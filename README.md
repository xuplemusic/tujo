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

### Face Verification (Clarifai)

The application has been integrated with the [Clarifai AI Platform](https://clarifai.com/) for face verification. The logic is implemented in the `storeFace` method of the `CitizenController`.

**Note:** The implementation is a "best guess" based on Clarifai's Quick Start guides, as the detailed HTTP API documentation was inaccessible during development. You may need to adjust the endpoint path or request payload after consulting the official documentation.

#### Configuration Steps:
1.  **Sign up for Clarifai:** Create a free account on the Clarifai website.
2.  **Create an Application:** Inside the Clarifai platform, create a new application. This will give you an **App ID**.
3.  **Find Your User ID:** Your **User ID** is available in your account settings.
4.  **Create a Personal Access Token (PAT):** In your account's security settings, create a new Personal Access Token. This will be your API key.
5.  **Find a Model:** Clarifai has many pre-built models. Browse the "Community" section to find a suitable model for face detection or recognition. A good starting point might be the `face-detection` model. Copy the **Model ID**.
6.  **Update Your `.env` File:** Add the following keys to your `.env` file with the values you obtained:
    ```env
    CLARIFAI_PAT=your_personal_access_token
    CLARIFAI_USER_ID=your_user_id
    CLARIFAI_APP_ID=your_app_id
    CLARIFAI_MODEL_ID=the_model_id_you_chose
    ```

Once configured, the application will send captured face images to the Clarifai API for processing. You can then extend the functionality to store the results (like face embeddings) for duplicate checking.

## API Usage

This application provides a secure, token-based API to access citizen data. This is useful for integrating with other government services.

### 1. Creating an API Token
1.  Log in to your account.
2.  Click on your name in the top-right corner to go to your Profile page.
3.  Under the "Create API Token" section, give your token a descriptive name (e.g., "Health System integration").
4.  Click "Create".
5.  Your new API token will be displayed. **Copy this token immediately.** You will not be able to see it again for security reasons.

### 2. Authenticating Requests
To make an authenticated request to the API, you must include an `Authorization` header with your API token (as a Bearer token).

```
Authorization: Bearer <YOUR_API_TOKEN>
```

### 3. Available Endpoints

#### Get a Specific Citizen
- **Endpoint:** `GET /api/v1/citizens/{id}`
- **Description:** Retrieves the data for a single citizen by their ID.
- **Example Request (using curl):**
  ```bash
  curl -X GET http://localhost:8000/api/v1/citizens/1 \
  -H "Authorization: Bearer <YOUR_API_TOKEN>" \
  -H "Accept: application/json"
  ```
