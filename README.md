# 🏦 BMMB Digital Forms System

A comprehensive Laravel-based digital forms platform for Brunei Darussalam financial institutions, designed for streamlined data access requests, corrections, service requests, and remittance applications.

## 📋 Overview

BMMB Digital Forms is a modern web application built with Laravel that provides secure, user-friendly interfaces for processing various types of requests and applications. The system features interactive multi-step forms with real-time validation and a beautiful, responsive UI.

## ✨ Features

### 🎯 Core Functionality

- **Multi-Step Forms**: Interactive stepper navigation with Alpine.js
- **Four Form Types**:
  - **DAR** (Data Access Request) - GDPR-compliant data access requests
  - **DCR** (Data Correction Request) - Data correction and rectification
  - **RAF** (Remittance Application Form) - Foreign remittance applications
  - **SRF** (Service Request Form) - General service requests

### 🎨 UI/UX Features

- **Modern Stepper Component**: Fully interactive step navigation
- **Real-time Progress Indicators**: Visual feedback on form completion
- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **Smooth Animations**: Alpine.js transitions between steps
- **Form Validation**: Client-side validation with server-side security

### 🔐 Security

- **CSRF Protection**: Built-in Laravel security
- **Data Encryption**: Secure form data handling
- **GDPR Compliance**: Data protection regulation adherence
- **Input Sanitization**: XSS and injection protection

## 🛠️ Tech Stack

- **Backend**: Laravel 11.x
- **Frontend**: Blade Templates + Tailwind CSS
- **JavaScript**: Alpine.js for reactive components
- **Icons**: Boxicons
- **Database**: SQLite (development) / MySQL (production)

## 📁 Project Structure

```
bmmb_digital_form/
├── app/
│   ├── Http/Controllers/    # Form controllers
│   └── Models/               # Eloquent models
│       ├── DarFormSubmission.php
│       ├── DcrFormSubmission.php
│       ├── RafFormSubmission.php
│       └── SrfFormSubmission.php
├── resources/
│   └── views/
│       ├── public/forms/     # Public form views
│       │   ├── dar.blade.php
│       │   ├── dcr.blade.php
│       │   ├── raf.blade.php
│       │   └── srf.blade.php
│       └── layouts/          # Blade layouts
├── database/
│   └── migrations/          # Database migrations
└── routes/
    └── web.php              # Route definitions
```

## 🚀 Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/bmmb_digital_form.git
   cd bmmb_digital_form
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install frontend dependencies**
   ```bash
   npm install
   ```

4. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Update `.env` with your database credentials:
   ```env
   DB_CONNECTION=sqlite
   # or for MySQL
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_DATABASE=bmmb_digital_form
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed database (optional)**
   ```bash
   php artisan db:seed
   ```

8. **Compile assets**
   ```bash
   npm run dev
   # or for production
   npm run build
   ```

9. **Start development server**
   ```bash
   php artisan serve
   ```

10. **Access the application**
    Open your browser and visit: `http://localhost:8000`

## 📝 Available Forms

### 1. Data Access Request (DAR)
- Multi-step form for requesting personal data access
- GDPR compliant processing
- Legal basis validation
- Purpose and justification requirements

### 2. Data Correction Request (DCR)
- Request data corrections or updates
- Priority-based processing
- Supporting document uploads
- Verification workflow

### 3. Remittance Application Form (RAF)
- Foreign remittance applications
- Multi-step verification process
- Document management
- Status tracking

### 4. Service Request Form (SRF)
- General service requests
- Category-based routing
- Service description and requirements
- Legal basis and documentation

## 🎨 Stepper Component

The forms feature an interactive stepper component built with Alpine.js:

- **Click navigation**: Click any step to jump directly to that step
- **Visual feedback**: Active step highlighted with gradient styling
- **Progress tracking**: Completed steps show checkmarks
- **Smooth transitions**: Animated transitions between steps

### Recent Improvements

✅ Fixed stepper synchronization across all forms  
✅ Converted from JavaScript-based navigation to Alpine.js  
✅ Added smooth transitions and animations  
✅ Unified navigation experience across DAR, RAF, DCR, and SRF forms

## 🔧 Development

### Running Tests
```bash
php artisan test
```

### Generating Assets
```bash
npm run dev    # Development build
npm run build  # Production build
npm run watch  # Watch for changes
```

### Database Management
```bash
php artisan migrate        # Run migrations
php artisan migrate:fresh  # Fresh migration with seeding
php artisan db:seed        # Seed database
```

## 📊 Database Structure

For detailed database schema information, see [DATABASE_STRUCTURE.md](DATABASE_STRUCTURE.md)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is proprietary software for BMMB Digital Forms System.

## 👥 Support

For issues, questions, or support requests, please contact the development team.

## 🙏 Acknowledgments

- Laravel Framework
- Alpine.js for reactive components
- Tailwind CSS for styling
- Boxicons for icons

---

**Built with ❤️ for BMMB Digital Forms System**
