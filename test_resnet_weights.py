import os
import cv2
import numpy as np

try:
    import tensorflow as tf
    from tensorflow.keras.applications.resnet50 import preprocess_input
    print(f"[TEST] TensorFlow Version: {tf.__version__}")
except ImportError:
    print("[TEST] TensorFlow is NOT installed in this environment.")
    exit(1)

model_path = "aegis_resnet50_v1.keras"
if not os.path.exists(model_path):
    print(f"[TEST] Error: Model file '{model_path}' not found.")
    exit(1)

try:
    print(f"[TEST] Loading model weights from '{model_path}'...")
    model = tf.keras.models.load_model(model_path)
    print("[TEST] Model loaded successfully!")
    model.summary()
    
    # Test dummy prediction tensor
    dummy_input = np.random.randint(0, 256, (1, 224, 224, 3), dtype=np.uint8)
    preprocessed = preprocess_input(dummy_input.astype(np.float32))
    
    raw_pred = model.predict(preprocessed, verbose=0)
    print(f"[TEST] Raw Prediction Output: {raw_pred}")
    print(f"[TEST] Predicted Fraud Probability: {float(raw_pred[0][0]) * 100:.2f}%")

except Exception as e:
    print(f"[TEST] Error loading or running model: {e}")
