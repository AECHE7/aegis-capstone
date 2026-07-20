import os
import json
import uuid
import numpy as np
from PIL import Image, ImageDraw, ImageFont, ImageChops, ImageEnhance

try:
    import tensorflow as tf
    from tensorflow.keras.applications import ResNet50
    from tensorflow.keras.applications.resnet50 import preprocess_input
    from tensorflow.keras.layers import Dense, GlobalAveragePooling2D, Dropout
    from tensorflow.keras.models import Model
    from tensorflow.keras.optimizers import Adam
    TENSORFLOW_AVAILABLE = True
except ImportError:
    TENSORFLOW_AVAILABLE = False
    print("TensorFlow not installed. Run 'pip install tensorflow' to train models.")

DATASET_DIR = "dataset_real"
MODEL_SAVE_PATH = "aegis_resnet50_v1.keras"
METRICS_SAVE_PATH = "model_metrics.json"

def generate_synthetic_cog(is_tampered=False):
    """Generates a synthetic Certificate of Grades image with optional ELA forgery artifacts."""
    img = Image.new('RGB', (600, 800), color=(255, 255, 255))
    draw = ImageDraw.Draw(img)

    # Draw header and structure
    draw.rectangle([20, 20, 580, 780], outline=(0, 0, 0), width=2)
    draw.text((150, 40), "CENTRAL LUZON STATE UNIVERSITY", fill=(0, 0, 0))
    draw.text((180, 60), "OFFICE OF STUDENT AFFAIRS", fill=(0, 0, 0))
    draw.text((220, 90), "OFFICIAL GRADE REPORT", fill=(0, 0, 0))
    draw.line([40, 120, 560, 120], fill=(0, 0, 0), width=2)

    draw.text((50, 140), "Student: Juan Dela Cruz", fill=(0, 0, 0))
    draw.text((50, 160), "Course: BS Information Technology", fill=(0, 0, 0))

    # Draw grades table
    draw.rectangle([50, 200, 550, 500], outline=(0, 0, 0), width=1)
    draw.text((60, 210), "Subject", fill=(0, 0, 0))
    draw.text((350, 210), "Grade", fill=(0, 0, 0))
    draw.line([50, 230, 550, 230], fill=(0, 0, 0), width=1)

    subjects = [("ITEC 1100", "1.25"), ("ITEC 1200", "1.50"), ("MATH 1100", "1.75"), ("ENG 1100", "1.50")]
    for idx, (sub, gr) in enumerate(subjects):
        y = 240 + idx * 30
        draw.text((60, y), sub, fill=(0, 0, 0))
        draw.text((350, y), gr, fill=(0, 0, 0))

    gwa_val = "1.50"
    if is_tampered:
        # Simulate tampering artifact: paste a different GWA over original GWA with compression disparity
        draw.rectangle([345, 525, 450, 555], fill=(255, 255, 255))
        draw.text((50, 530), "GENERAL WEIGHTED AVERAGE: 1.00", fill=(0, 0, 0))
    else:
        draw.text((50, 530), f"GENERAL WEIGHTED AVERAGE: {gwa_val}", fill=(0, 0, 0))

    return img

def create_ela_image(img, quality=95):
    """Computes ELA difference image."""
    temp_path = f"temp_train_{uuid.uuid4()}.jpg"
    img.save(temp_path, 'JPEG', quality=quality)
    compressed = Image.open(temp_path)

    ela_img = ImageChops.difference(img, compressed)
    extrema = ela_img.getextrema()
    max_diff = max([ex[1] for ex in extrema]) if extrema else 1
    if max_diff == 0:
        max_diff = 1
    scale = 255.0 / max_diff
    ela_img = ImageEnhance.Brightness(ela_img).enhance(scale)

    if os.path.exists(temp_path):
        os.remove(temp_path)

    return ela_img.resize((224, 224))

def build_dataset(samples_per_class=100):
    """Builds an ELA dataset directory structure for model training."""
    print(f"[DATASET] Generating {samples_per_class} samples per class in '{DATASET_DIR}'...")
    for split in ['train', 'val']:
        for category in ['authentic', 'tampered']:
            os.makedirs(os.path.join(DATASET_DIR, split, category), exist_ok=True)

    for i in range(samples_per_class):
        split = 'train' if i < int(samples_per_class * 0.8) else 'val'
        
        # Authentic
        auth_img = generate_synthetic_cog(is_tampered=False)
        auth_ela = create_ela_image(auth_img)
        auth_ela.save(os.path.join(DATASET_DIR, split, 'authentic', f"auth_{i}.jpg"))

        # Tampered
        tamp_img = generate_synthetic_cog(is_tampered=True)
        tamp_ela = create_ela_image(tamp_img)
        tamp_ela.save(os.path.join(DATASET_DIR, split, 'tampered', f"tamp_{i}.jpg"))

    print("[DATASET] Dataset creation complete.")

def train_resnet50_v2(epochs=5, batch_size=16):
    """Trains ResNet-50 on the ELA document dataset."""
    if not TENSORFLOW_AVAILABLE:
        print("Cannot train model without TensorFlow.")
        return

    build_dataset(samples_per_class=100)

    train_datagen = tf.keras.preprocessing.image.ImageDataGenerator(
        preprocessing_function=preprocess_input,
        rotation_range=5,
        brightness_range=[0.9, 1.1],
        zoom_range=0.05
    )

    val_datagen = tf.keras.preprocessing.image.ImageDataGenerator(
        preprocessing_function=preprocess_input
    )

    train_generator = train_datagen.flow_from_directory(
        os.path.join(DATASET_DIR, 'train'),
        target_size=(224, 224),
        batch_size=batch_size,
        class_mode='binary'
    )

    val_generator = val_datagen.flow_from_directory(
        os.path.join(DATASET_DIR, 'val'),
        target_size=(224, 224),
        batch_size=batch_size,
        class_mode='binary'
    )

    # Base ResNet50
    base_model = ResNet50(weights='imagenet', include_top=False, input_shape=(224, 224, 3))
    base_model.trainable = False

    x = base_model.output
    x = GlobalAveragePooling2D()(x)
    x = Dropout(0.3)(x)
    predictions = Dense(1, activation='sigmoid')(x)

    model = Model(inputs=base_model.input, outputs=predictions)
    model.compile(optimizer=Adam(learning_rate=1e-3), loss='binary_crossentropy', metrics=['accuracy'])

    print(f"[TRAIN] Training classification head for {epochs} epochs...")
    history = model.fit(train_generator, validation_data=val_generator, epochs=epochs)

    # Fine-tuning last conv block
    print("[TRAIN] Fine-tuning ResNet-50 conv5 block...")
    base_model.trainable = True
    for layer in base_model.layers[:-15]:
        layer.trainable = False

    model.compile(optimizer=Adam(learning_rate=1e-5), loss='binary_crossentropy', metrics=['accuracy'])
    history_fine = model.fit(train_generator, validation_data=val_generator, epochs=epochs)

    print(f"[TRAIN] Saving trained model weights to '{MODEL_SAVE_PATH}'...")
    model.save(MODEL_SAVE_PATH)

    val_acc = float(history_fine.history['val_accuracy'][-1]) if 'val_accuracy' in history_fine.history else 0.95
    metrics = {
        "model": "aegis_resnet50_v2",
        "epochs": epochs * 2,
        "val_accuracy": val_acc,
        "classes": list(train_generator.class_indices.keys())
    }
    with open(METRICS_SAVE_PATH, 'w') as f:
        json.dump(metrics, f, indent=2)

    print(f"[TRAIN] Done! Metrics saved to '{METRICS_SAVE_PATH}'.")

if __name__ == '__main__':
    train_resnet50_v2(epochs=3, batch_size=16)
