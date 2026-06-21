import os
import shutil
import random
import numpy as np
import tensorflow as tf
from PIL import Image, ImageDraw, ImageFont, ImageChops, ImageEnhance
from tensorflow.keras.applications import ResNet50
from tensorflow.keras.layers import Dense, GlobalAveragePooling2D, Dropout
from tensorflow.keras.models import Model
from tensorflow.keras.preprocessing.image import ImageDataGenerator
from tensorflow.keras.callbacks import EarlyStopping, ModelCheckpoint

# --- CONFIGURATION ---
DATASET_DIR = 'dataset_advanced'
IMG_SIZE = (224, 224)
BATCH_SIZE = 16
EPOCHS = 10 # Increased for better accuracy
SAMPLES_PER_CLASS = 200 # Generates 400 total images

print("=== Phase 1: Synthetic COG Forgery Generation ===")
shutil.rmtree(DATASET_DIR, ignore_errors=True)
os.makedirs(os.path.join(DATASET_DIR, 'authentic'), exist_ok=True)
os.makedirs(os.path.join(DATASET_DIR, 'tampered'), exist_ok=True)

def generate_synthetic_cog(is_forged=False):
    """Generates a realistic academic document and intentionally forges it if requested."""
    # 1. Create a blank white document
    img = Image.new('RGB', (800, 1000), color=(255, 255, 255))
    draw = ImageDraw.Draw(img)
    
    # 2. Add realistic document features (Header, Lines, Text)
    draw.text((300, 50), "CENTRAL LUZON STATE UNIVERSITY", fill=(0, 0, 0))
    draw.text((320, 80), "CERTIFICATE OF GRADES", fill=(0, 0, 0))
    draw.line((50, 120, 750, 120), fill=(0, 0, 0), width=2)
    
    # Add dummy grades
    for y in range(200, 800, 40):
        draw.text((100, y), f"COURSE {y}", fill=(0, 0, 0))
        grade = random.choice(["1.00", "1.25", "1.50", "2.00", "2.50", "3.00"])
        draw.text((650, y), grade, fill=(0, 0, 0))
        
    # Original GWA
    original_gwa = "2.75"
    draw.text((100, 850), "GENERAL WEIGHTED AVERAGE:", fill=(0, 0, 0))
    draw.text((650, 850), original_gwa, fill=(0, 0, 0))

    if not is_forged:
        # Authentic: Save once at standard quality
        return img
    else:
        # Tampered: Save to simulate the first compression (the original scan)
        temp_path = 'temp_original.jpg'
        img.save(temp_path, 'JPEG', quality=90)
        
        # Re-open the image to simulate a student editing it in Photoshop
        forged_img = Image.open(temp_path)
        forged_draw = ImageDraw.Draw(forged_img)
        
        # 1. "Erase" the old GWA by drawing a white box over it
        forged_draw.rectangle([640, 840, 700, 870], fill=(255, 255, 255))
        
        # 2. Write a fake "better" GWA over it (simulating forgery)
        fake_gwa = "1.00"
        forged_draw.text((650, 850), fake_gwa, fill=(10, 10, 10)) # Slight color mismatch
        
        os.remove(temp_path)
        return forged_img

# Generate the dataset
for i in range(SAMPLES_PER_CLASS):
    auth_img = generate_synthetic_cog(is_forged=False)
    # Save authentic at 90 quality
    auth_img.save(os.path.join(DATASET_DIR, 'authentic', f'auth_{i}.jpg'), 'JPEG', quality=90)
    
    tamp_img = generate_synthetic_cog(is_forged=True)
    # Save tampered at 85 quality (The mismatch in compression is what ELA catches!)
    tamp_img.save(os.path.join(DATASET_DIR, 'tampered', f'tamp_{i}.jpg'), 'JPEG', quality=85)

print(f"Generated {SAMPLES_PER_CLASS * 2} documents for training.")

# --- ELA PREPROCESSING ---
print("\n=== Phase 2: Error Level Analysis Pipeline ===")
def ela_preprocessing(image_array):
    original = Image.fromarray(np.uint8(image_array)).convert('RGB')
    temp_filename = 'temp_train_compress.jpg'
    original.save(temp_filename, 'JPEG', quality=95)
    compressed = Image.open(temp_filename)
    
    ela_image = ImageChops.difference(original, compressed)
    extrema = ela_image.getextrema()
    max_diff = max([ex[1] for ex in extrema]) if extrema else 1
    if max_diff == 0: max_diff = 1
    
    scale = 255.0 / max_diff
    ela_image = ImageEnhance.Brightness(ela_image).enhance(scale)
    
    os.remove(temp_filename)
    return np.array(ela_image) / 255.0

datagen = ImageDataGenerator(preprocessing_function=ela_preprocessing, validation_split=0.2)

train_generator = datagen.flow_from_directory(
    DATASET_DIR, target_size=IMG_SIZE, batch_size=BATCH_SIZE, class_mode='binary', subset='training'
)
val_generator = datagen.flow_from_directory(
    DATASET_DIR, target_size=IMG_SIZE, batch_size=BATCH_SIZE, class_mode='binary', subset='validation'
)

# --- NEURAL NETWORK ARCHITECTURE ---
print("\n=== Phase 3: Compiling ResNet-50 Architecture ===")
base_model = ResNet50(weights='imagenet', include_top=False, input_shape=(224, 224, 3))

# Freeze the bottom layers, but leave the top few layers un-frozen so it learns document features
for layer in base_model.layers[:-10]:
    layer.trainable = False

x = base_model.output
x = GlobalAveragePooling2D()(x)
x = Dense(256, activation='relu')(x)
x = Dropout(0.5)(x)
predictions = Dense(1, activation='sigmoid')(x)

model = Model(inputs=base_model.input, outputs=predictions)

model.compile(
    optimizer=tf.keras.optimizers.Adam(learning_rate=0.0001),
    loss='binary_crossentropy',
    metrics=['accuracy', tf.keras.metrics.Precision(name='precision'), tf.keras.metrics.Recall(name='recall')]
)

# Callbacks to stop training when accuracy peaks and save the best version
callbacks = [
    EarlyStopping(monitor='val_accuracy', patience=3, restore_best_weights=True),
    ModelCheckpoint('aegis_resnet50_v1.h5', monitor='val_accuracy', save_best_only=True)
]

print("\n=== Phase 4: Commencing Deep Learning Training ===")
history = model.fit(
    train_generator,
    validation_data=val_generator,
    epochs=EPOCHS,
    callbacks=callbacks
)

print("\nTraining Complete! High-Accuracy Model saved as 'aegis_resnet50_v1.h5'")