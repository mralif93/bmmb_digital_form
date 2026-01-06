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

### 🔑 Important Environment Variables

### 🔑 Configuration Guide

#### 1. eForm Configuration (`.env`)
These variables must be set in the **eForm** application's `.env` file:

| Variable | Description | Example |
|----------|-------------|---------|
| **APP_URL** | **Critical.** Base URL for Nginx proxying. | `http://localhost/eform` |
| **MAP_DATABASE_PATH** | Absolute path to MAP's SQLite DB (for user sync). | `/var/www/FinancingApp/db.sqlite3` |
| **MAP_SSO_SECRET** | Shared secret for token verification (Must match MAP). | `my-secure-secret-key` |
| **MAP_LOGIN_URL** | MAP's login page URL. | `http://192.168.1.10:8000/pengurusan/login/` |
| **MAP_LOGOUT_URL** | MAP's logout endpoint. | `http://192.168.1.10:8000/pengurusan/logout/` |
| **MAP_VERIFY_URL** | MAP's API endpoint to verify tokens. | `http://192.168.1.10:8000/api/eform/verify/` |

#### 2. MAP (FinancingApp) Configuration
Ensure the following configurations are set in the **MAP** application (`FinancingApp`) to allow eForm integration:

- **SSO Secret**: The `MAP_SSO_SECRET` in eForm must match the secret key defined in MAP's `settings.py` or `.env`.
- **Redirect Whitelist**: Ensure MAP trusts the eForm redirect URL (e.g., `http://localhost/eform/login/msg`).
- **CORS Headers**: If API calls are client-side, ensure MAP allows requests from the eForm domain.

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


### 🔄 Recent Updates & Changelog

Here is a summary of the latest changes and features implemented in the project:

#### 1. Data Consistency & Localization
- **Timezone Standardization**: All dates and times across the system (Views, PDFs, Exports, Audit Trails) now strictly follow the system settings (Default: `Asia/Kuala_Lumpur`, Format: `d M Y, h:i A`).
- **Date Formatting**: Fixed inconsistent date formats in dashboards, submission lists, details modals, and PDF reports.
- **Null Handling**: Improved robustness against null dates in user and submission data.

#### 2. Admin Scheduler & Sync Settings
- **New Scheduler Tab**: Added a dedicated "Scheduler" tab in Admin Settings.
- **Dynamic Scheduling**: Admins can now configure the MAP database sync frequency (`Daily`, `Hourly`, `Every 30 Minutes`, etc.) without code changes.
- **Last Sync Indicator**: Displays the timestamp of the last successful MAP sync to ensure visibility.

#### 3. "Trashed" Functionality (Soft Deletes)
- **Extended Soft Deletes**: Implemented Soft Deletes for **Branches**, **States**, and **Regions** (previously only available for Users).
- **Dedicated Trashed Views**: Created separate "Trashed" views for all modules to keep the main index clean.
- **Restore & Force Delete**: Added functionality to restore soft-deleted items or permanently remove them.
- **Audit Logging**: All restore and force-delete actions are logged in the Audit Trail.

#### 4. UI/UX Enhancements
- **Search UI alignment**: Standardized search bars, buttons, and layouts across Users, Branches, States, and Regions modules.
- **Color Standardization**: Updated UI colors for States (formerly Purple) and Regions (formerly Teal) to match the primary **Orange** branding.
- **Mobile Responsive Menu**: Fixed mobile menu layout and responsiveness.
- **Consistent Icons**: Removed redundant action icons and standardized button styles.

#### 5. User Management Improvements
- **Staff ID Field**: Renamed "Username" to "Staff ID" in the UI to match business terminology.
- **Staff ID Column**: Added a dedicated column for Staff ID in the User Index.
- **Read-Only Fields**: prevented manual editing of MAP-synced fields (Staff ID, Email) to avoid data conflicts.

#### 6. Bug Fixes & Technical Improvements
- **Nginx & Routing**: Fixed Nginx proxy and URL generation issues for `/eform` path prefix.
- **Migration Fixes**: Resolved schema constraints in database migrations.
- **Role & Access Control**: Refined logic for User Roles and permissions.

#### 7. Recent Quality of Life Improvements (Q1 2026)
- **Enhanced QR Code Scheduler**: Unified QR code regeneration logic. Manual regeneration now respects the "Frequency" setting (Daily/Weekly) defined in Scheduler settings, ensuring consistent expiration times.
- **Universal Dark Mode**: Comprehensive dark theme audit and fixes across all Admin pages, ensuring text legibility and UI consistency.
- **PDF Optimization**: Refined PDF layouts for all forms (Portrait A4), standardized font sizes (Outfit), and fixed color branding for professional printing.
- **Role Access Fixes**: Resolved "Access Denied" issues for Operation Officers (OO) and Assistant Branch Managers (ABM), granting them proper processing rights.
- **Form Submission Refinements**: Fixed radio button reset issues and array display errors in submission details.
- **Robust Syncing**: Hardened MAP synchronization commands against `STDIN` errors and schema conflicts during automated runs.


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

## ⏰ Scheduler Setup

The application relies on Laravel's Task Scheduler for the MAP database synchronization.

### Local Development
To run the scheduler locally without Docker:
```bash
php artisan schedule:work
```

### 🐳 Docker Configuration
We have included a dedicated `scheduler` service in `docker-compose.yml` that runs `php artisan schedule:work` automatically.

To start it:
```bash
docker-compose up -d scheduler
```
(It will start automatically with `docker-compose up -d`)

### Production
For production environments, add the following Cron entry to your server:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Configuration
You can configure the synchronization frequency in **System Settings > Scheduler**.
Available options:
- **Standard**: Daily, Every 4 Hours, Hourly
- **Critical**: Every 5 Minutes, Every Minute

## 🐳 Docker & Permissions

If you are running the application using Docker, you may encounter permission issues after restarting containers (e.g., `docker-compose down` followed by `up`).

To ensure the application functions correctly, you must run the permissions fix script:

```bash
chmod +x fix-permissions.sh
./fix-permissions.sh
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
