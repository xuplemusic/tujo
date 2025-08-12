# Adamawa State Citizen Datacenter

This is a Laravel and React (with Inertia.js) application designed to serve as a basic datacenter for managing citizen information for Adamawa State. It includes a full CRUD interface for citizen records and placeholder modules for biometric verification (face and fingerprint).

This project was bootstrapped by an AI agent. You will need to follow the installation instructions below to get it running.

## Features

- **Citizen Management:** Create, Read, Update, and Delete citizen records.
- **React Frontend:** A modern, single-page application experience powered by React and Inertia.js.
- **Face Verification (Placeholder):** A module for capturing a citizen's photo using a webcam. The backend logic for processing and storing face embeddings needs to be integrated.
- **Fingerprint Verification (Placeholder):** A placeholder module for capturing fingerprint data. This requires integration with a specific fingerprint scanner's Web SDK.

## Installation

Follow these steps to get the application up and running on your local development machine.

### 1. Prerequisites
- PHP >= 8.1
- Composer
- Node.js & NPM
- A database (e.g., MySQL, PostgreSQL)

### 2. Clone the Repository
```bash
git clone <repository-url>
cd <repository-directory>
```

### 3. Install Dependencies
Install both the PHP and JavaScript dependencies.
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 4. Environment Configuration
Create your local environment file and generate the application key.
```bash
# Copy the example environment file
cp .env.example .env

# Generate a new application key
php artisan key:generate
```
Next, open the `.env` file and configure your database connection settings (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

### 5. Run Database Migrations and Seeders
Create the necessary tables and default users by running the migrations and seeders.
```bash
# Run migrations
php artisan migrate

# Run seeders to create default users
php artisan db:seed
```

### 6. Create Storage Link
To make the uploaded face images publicly accessible, create the symbolic link from `public/storage` to `storage/app/public`.
```bash
php artisan storage:link
```

### 7. Compile Frontend Assets
Compile the JavaScript and CSS files using Vite. For development, you can use `npm run dev`.
```bash
# For development (with hot-reloading)
npm run dev

# For production
npm run build
```

### 8. Start the Development Server
You can now start the Laravel development server.
```bash
php artisan serve
```
The application should now be accessible at `http://localhost:8000`.

## Authentication and User Roles

This application includes a basic authentication system with two user roles:
- **Admin:** Can access all features, including the admin dashboard and can manage all citizen records (Create, Read, Update, Delete).
- **Registrar:** Can create and update citizen records.

### Default Users
The database seeder (`php artisan db:seed`) will create two default users for you to use:

- **Admin User**
  - **Email:** `admin@example.com`
  - **Password:** `password`

- **Registrar User**
  - **Email:** `registrar@example.com`
  - **Password:** `password`

## Biometric API Configuration

The application is configured to send biometric data to an external API. You must set the URL for this API in your `.env` file.

```
BIOMETRIC_API_URL=https://your-biometric-api.com/api
```

If this URL is not set, the application will still save the captured images and fingerprint templates locally, but it will not attempt to send them to an external service.

## Completing the Implementation

The core CRUD functionality is in place, but the biometric verification features require further integration with third-party services and hardware.

### Face Verification

The frontend component for capturing a face image is located at `resources/js/Components/FaceCapture.jsx`. It uses `react-webcam` to access the camera and upload a photo.

The backend logic to handle this photo is in `app/Http/Controllers/CitizenController.php`, within the `storeFace` method.

**Your Task:**
1.  Choose a face recognition service (e.g., AWS Rekognition, Azure Face API, or an open-source library like `face-recognition`).
2.  In the `storeFace` method, after the image is saved, send the image to your chosen service to get a facial embedding (a unique vector representation of the face).
3.  You should add a new column to the `citizens` table (e.g., `face_embedding` of type `TEXT` or `JSON`) to store this value.
4.  Modify the controller to save this embedding to the database. This embedding is what you will use for searching and matching, not the raw image.

### Fingerprint Verification

This feature is highly dependent on the specific fingerprint scanner hardware you choose.

The frontend placeholder component is at `resources/js/Components/FingerprintCapture.jsx`. The backend placeholder method is `storeFingerprint` in `CitizenController`.

**Your Task:**
1.  Select a fingerprint scanner that provides a Web SDK.
2.  Replace the placeholder content in `FingerprintCapture.jsx` with the actual integration code from your SDK. This will involve triggering a scan and getting the fingerprint template data (often a Base64 string).
3.  The component will then submit this data to the `storeFingerprint` method. The current code saves the raw template to the `fingerprint_template` column, which you can adjust as needed based on your SDK's requirements.
4.  For verification, you will need to implement a matching algorithm, which is also typically provided by the hardware's SDK.
