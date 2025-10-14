# Custom Laravel Login System
A fully custom login system built in **Laravel** without using any pre-built authentication libraries like Laravel Breeze or Jetstream. This project demonstrates manual authentication handling, security features, and user experience enhancements.

## Features

### 1. Component-Based Views
- The front-end is broken down into reusable **Blade components**:
  - **Home Page**
  - **Login Page**
  - **Registration Page**
  - **Forgot Password Page**
  - ** Dashboard Page**
- Promotes **clean, maintainable code** and easy scalability.

### 2. Registration & Login Logic
- **Registration**: Users can create an account with validation for required fields, unique email, and strong password.
- **Login**: Users can authenticate with email and password.
- Passwords are securely hashed using **bcrypt** before storing in the database.
- Invalid login attempts return user-friendly messages.

### 3. CAPTCHA
- Implemented a **CAPTCHA** on the login and registration forms to prevent automated bots from spamming the system.

### 4. Password Hide/Show
- Added a **toggle icon** for password input fields to show or hide the password for better UX.

### 5. Forgot Password
- Users can request a **password reset link** if they forget their password.
- Reset link is sent via **Mailtrap** (SMTP) for testing purposes.
- Secure token-based password reset ensures **temporary and safe access**.

### 6. Remember Me
- Users can select **"Remember Me"** during login to maintain authentication across browser sessions.
- Implemented using **secure cookies**.

### 7. Force Logout for Multiple Devices
- Users are **forced to log out** if they attempt to log in from a different device or browser.
- Ensures **single-session enforcement**, improving account security.

### 8. Rate Limiting
- Implemented **rate limiting** to prevent brute-force attacks.
- Limits the number of login attempts within a given timeframe.
- Provides user-friendly error messages after exceeding the limit.

### 9. Password Encrypted
- Passwords are first hashed with SHA-512 in the frontend so the raw password never travels over the network.
- The Laravel backend then applies bcrypt (Hash::make()) to the received SHA-512 hash before storing it in the database.
- During login, the same SHA-512 process runs in the frontend, and Laravel verifies it using Hash::check().
- password → SHA-512 (frontend) → bcrypt (Laravel) → stored in DB
- This ensures the raw password is never exposed while still maintaining strong server-side security.

### 10. Password and confirm pasword must be 1 Uppercase, 1 lowercase, special charecter, 1 number, and lenght must be min 8 charecters.

## Tech Stack

- **Backend**: Laravel (PHP)
- **Frontend**: Blade Templates, HTML, CSS, JavaScript
- **Database**: MySQL
- **Mail**: Mailtrap (for testing email features)

## Setup Instructions

1. Clone the repository:
- git clone <repository-url>
- cd <project-directory>

2. Install dependencies
- composer install
- npm install
- npm run dev

3. Configure .env file:
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=your_database
- DB_USERNAME=your_username
- DB_PASSWORD=your_password

- MAIL_MAILER=smtp
- MAIL_HOST=smtp.mailtrap.io
- MAIL_PORT=2525
- MAIL_USERNAME=your_mailtrap_username
- MAIL_PASSWORD=your_mailtrap_password
- MAIL_ENCRYPTION=null

4. Run migrations:
- php artisan migrate

5. Start the server:
- php artisan serve

## Security Considerations
- Password Hashing: All passwords are hashed with bcrypt.
- CAPTCHA: Prevents automated login/registration attacks.
- Rate Limiting: Protects against brute-force attacks.
- Single Session Enforcement: Prevents concurrent logins from multiple devices.
- Secure Password Reset: Tokens expire after a set time to prevent unauthorized access.

## Future Enhancements
- Two-factor authentication (2FA) for additional security.
- Logging and monitoring failed login attempts.
- Implementing social logins (Google, Facebook, etc.).
- Enhanced front-end UI with Vue.js or React.
