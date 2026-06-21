import os
import uuid
import cv2
import numpy as np
import tensorflow as tf
from flask import Flask, request, jsonify
from PIL import Image, ImageChops, ImageEnhance

app = Flask(__name__)

UPLOAD_FOLDER = 'temp_uploads'
ELA_FOLDER = 'ela_outputs'
HEATMAP_FOLDER = 'heatmap_outputs'
MODEL_PATH = 'aegis_resnet50_v1.h5'

for folder in [UPLOAD_FOLDER, ELA_FOLDER, HEATMAP_FOLDER]:
    os.makedirs(folder, exist_ok=True)

# 1. Load the Trained Model Globally
try:
    print("Loading A.E.G.I.S. ResNet-50 Model...")
    model = tf.keras.models.load_model(MODEL_PATH)
    # The last conv layer in ResNet50 is 'conv5_block3_out'
    last_conv_layer_name = "conv5_block3_out" 
    print("Model Loaded Successfully!")
except Exception as e:
    print(f"Warning: Model not found. Please run train_model.py first. Error: {e}")
    model = None

def generate_ela(img_path, output_path, quality=95):
    """Stage 1: Error Level Analysis (ELA) Preprocessing."""
    original = Image.open(img_path).convert('RGB')
    temp_filename = f'temp_{uuid.uuid4()}.jpg'
    original.save(temp_filename, 'JPEG', quality=quality)
    compressed = Image.open(temp_filename)
    
    ela_image = ImageChops.difference(original, compressed)
    extrema = ela_image.getextrema()
    max_diff = max([ex[1] for ex in extrema]) if extrema else 1
    if max_diff == 0: max_diff = 1
    
    scale = 255.0 / max_diff
    ela_image = ImageEnhance.Brightness(ela_image).enhance(scale)
    ela_image.save(output_path)
    os.remove(temp_filename)
    return output_path

def get_gradcam_heatmap(img_array, model, last_conv_layer_name):
    """Generates a Grad-CAM heatmap highlighting tampered regions."""
    grad_model = tf.keras.models.Model(
        [model.inputs], [model.get_layer(last_conv_layer_name).output, model.output]
    )

    with tf.GradientTape() as tape:
        last_conv_layer_output, preds = grad_model(img_array)
        class_channel = preds[:, 0]

    # Calculate gradients
    grads = tape.gradient(class_channel, last_conv_layer_output)
    pooled_grads = tf.reduce_mean(grads, axis=(0, 1, 2))
    
    # Multiply feature map by gradients
    last_conv_layer_output = last_conv_layer_output[0]
    heatmap = last_conv_layer_output @ pooled_grads[..., tf.newaxis]
    heatmap = tf.squeeze(heatmap)
    
    # Apply ReLU (only care about positive features)
    heatmap = tf.maximum(heatmap, 0) / tf.math.reduce_max(heatmap)
    return heatmap.numpy()

def run_cnn_inference_and_gradcam(original_path, ela_path, heatmap_output_path):
    """Stage 2: Real ResNet-50 Inference and Grad-CAM Generation"""
    if model is None:
        raise Exception("AI Model is not loaded. Train the model first.")

    # Prepare image for model (ResNet50 expects 224x224)
    ela_img = cv2.imread(ela_path)
    ela_img = cv2.cvtColor(ela_img, cv2.COLOR_BGR2RGB)
    ela_resized = cv2.resize(ela_img, (224, 224))
    img_array = np.expand_dims(ela_resized, axis=0) / 255.0 # Normalize

    # 1. Real Inference
    prediction = model.predict(img_array)[0][0]
    # Keras typically outputs probability of class 1.
    # Assuming class 0 = Authentic, class 1 = Tampered (alphabetical order in flow_from_directory)
    fraud_probability = round(float(prediction) * 100, 2)
    classification = "Tampered" if fraud_probability >= 40.0 else "Authentic"

    # 2. Real Grad-CAM
    heatmap = get_gradcam_heatmap(img_array, model, last_conv_layer_name)

    # 3. Superimpose heatmap over the original image
    original_img = cv2.imread(original_path)
    heatmap_resized = cv2.resize(heatmap, (original_img.shape[1], original_img.shape[0]))
    heatmap_resized = np.uint8(255 * heatmap_resized)
    jet_heatmap = cv2.applyColorMap(heatmap_resized, cv2.COLORMAP_JET)
    
    superimposed_img = cv2.addWeighted(original_img, 0.6, jet_heatmap, 0.4, 0)
    cv2.imwrite(heatmap_output_path, superimposed_img)
    
    return fraud_probability, classification

@app.route('/analyze-document', methods=['POST'])
def analyze_document():
    if 'file' not in request.files:
        return jsonify({"error": "No file part"}), 400
        
    file = request.files['file']
    if file.filename == '':
        return jsonify({"error": "No file selected"}), 400
        
    if file:
        file_uuid = str(uuid.uuid4())
        original_ext = file.filename.rsplit('.', 1)[1].lower()
        original_path = os.path.join(UPLOAD_FOLDER, f"{file_uuid}.{original_ext}")
        ela_path = os.path.join(ELA_FOLDER, f"{file_uuid}_ela.jpg")
        heatmap_path = os.path.join(HEATMAP_FOLDER, f"{file_uuid}_heatmap.jpg")
        
        file.save(original_path)
        
        try:
            generate_ela(original_path, ela_path)
            score, label = run_cnn_inference_and_gradcam(original_path, ela_path, heatmap_path)
            
            return jsonify({
                "status": "success",
                "fraud_probability": score,
                "classification": label,
                "paths": {
                    "heatmap_path": heatmap_path,
                    "ela_path": ela_path
                }
            }), 200
        except Exception as e:
            return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)