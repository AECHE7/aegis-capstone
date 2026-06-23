# A.E.G.I.S. (Academic Evaluation & Grade Integrity System)

A.E.G.I.S. is a modern, full-stack capstone web application integrated with a deep learning python microservice. It is designed to secure, streamline, and automate the student scholarship application and evaluation process for Central Luzon State University (CLSU).

---

## 🚀 Core Features

- **Double-Layer Forensics Grade Verification:** 
  - **Error Level Analysis (ELA):** Preprocesses Certificates of Grades (COGs) to isolate digital compression mismatches.
  - **ResNet-50 CNN:** Classifies processed ELA images to compute fraud probabilities.
  - **Grad-CAM Heatmaps:** Generates visual activation heatmaps showing administrators exactly where grades have been altered.
- **Normalized Scholarship Management:** Admins can configure minimum General Weighted Average (GWA) thresholds, monitor real-time queue volumes, and generate compliance reports (CSV/PDF).
- **Institutional Email Security:** Restricts student registration to verified CLSU email domains (`@clsu.edu.ph` / `@clsu2.edu.ph`) with active polling redirect support.
- **Ethical Privacy Standards:** Enforces AES-256 database column encryption on student identifiers and contact numbers, alongside SHA-256 UUID filename renaming to guarantee student anonymity at-rest.
- **Staff Lifecycle Invitations:** Allows the Super Admin to dispatch token-based invites to personnel with secure expiration setups.

---

## 🛠️ System Architecture

```mermaid
graph TB
    subgraph Laravel Portal (Port 8000)
        router[web.php Router] --> auth[Auth Middleware]
        auth --> student[Student Dashboard]
        auth --> admin[Admin Review Console]
        auth --> super[SuperAdmin Analytics]
        db[(SQLite Database)] <--> ORM[Eloquent Models]
    end

    subgraph Python Forensics Microservice (Port 5000)
        flask[app.py API] --> ela[ELA Engine]
        ela --> resnet[ResNet-50 Model]
        resnet --> gradcam[Grad-CAM Generator]
    end

    admin -->|Queued Job Scan| flask
    flask -->|Forensics Scores & Heatmaps| admin
```

---

## 💻 Technical Stack

- **Backend:** Laravel 12.0, PHP 8.2+
- **Frontend:** Blade Templating, Tailwind CSS, Alpine.js, Bootstrap 5
- **Database:** SQLite (local development) / MySQL 8.0 (production-ready)
- **AI Microservice:** Python 3.11, TensorFlow 2.16.1, Keras 3.3.3, OpenCV, Pillow

---

## ⚙️ Setup & Installation

### 1. Clone & Install PHP Dependencies
```bash
git clone https://github.com/AECHE7/aegis-capstone.git
cd aegis-capstone
composer install
```

### 2. Configure Environment Suffixes
Duplicate `.env.example` as `.env` and configure:
- DB configurations (`DB_CONNECTION=sqlite`)
- Mail server SMTP settings (e.g. Brevo SMTP host and keys)
- Run key generation:
```bash
php artisan key:generate
```

### 3. Setup SQLite Database
Create `database/database.sqlite` (if using SQLite) and run migrations:
```bash
php artisan migrate:fresh --seed
```

### 4. Setup Python AI Forensics Environment
```bash
cd aegis-ai
# Create a virtual environment using Python 3.11
C:\Python311\python.exe -m venv venv
# Activate environment (Windows PowerShell)
.\venv\Scripts\Activate.ps1
# Install packages
pip install --upgrade pip
pip install -r requirements.txt
```

### 5. Train & Run the AI Microservice
Generate synthetic datasets and train the ResNet-50 network:
```bash
python train_model.py
# Start the Flask service
python app.py
```

### 6. Start the Web Portal & Queue Worker
In separate terminals from the project root:
```bash
# Laravel server
php artisan serve
# Queue worker
php artisan queue:work
# Vite hot reload
npm install
npm run dev
```
Alternatively, execute `start-all.bat` on Windows to launch all services concurrently.

---

## 🧪 Automated Testing
Run the complete testing suite to verify system integrity across 37 features (145 assertions):
```bash
php artisan test
```

---

## 📄 License
This system is developed for academic purposes as a Capstone project for Central Luzon State University (CLSU).
