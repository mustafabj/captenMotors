# Capten Motors - Car Management System

A comprehensive car dealership management system built with Laravel, featuring user management, car inventory, and advanced JavaScript architecture.

## 🚀 Features

### Core Functionality
- **Car Management**: Complete CRUD operations for car inventory
- **User Management**: Role-based user system with soft deletes
- **Bulk Deals**: Manage multiple car purchases and sales
- **Responsive Design**: Mobile-first design with KtUI and Tailwind CSS
- **Advanced Search**: Real-time search and filtering capabilities

### Technical Features
- **App Namespace Architecture**: Organized JavaScript with modular structure
- **Form Helpers**: Reusable form validation and AJAX submission
- **Asset Versioning**: Cache busting system for optimal performance
- **Dynamic Loading**: Smart JavaScript file loading based on routes
- **Auto-save**: Form data persistence with localStorage

## 📁 Project Structure

```
captenMotors/
├── app/
│   ├── Console/Commands/
│   │   └── UpdateAssetVersions.php
│   ├── Http/Controllers/
│   │   ├── Auth/
│   │   ├── CarController.php
│   │   └── UserController.php
│   ├── Models/
│   │   ├── Car.php
│   │   └── User.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── Helpers/
│       └── AssetHelper.php
├── config/
│   └── assets.php
├── public/js/
│   ├── app.js
│   ├── pages/
│   │   ├── users/
│   │   │   ├── users-index.js
│   │   │   ├── users-create.js
│   │   │   └── users-edit.js
│   │   └── cars/
│   │       ├── cars-index.js
│   │       ├── cars-form.js
│   │       └── cars-show.js
│   ├── utils/
│   │   └── form-helpers.js
│   ├── components/
│   │   └── modal.js
│   └── config/
│       ├── loader.js
│       └── version.js
└── resources/views/
    ├── layouts/
    │   └── app.blade.php
    ├── users/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── edit.blade.php
    └── cars/
        ├── index.blade.php
        ├── create.blade.php
        └── show.blade.php
```

## 🛠️ Installation

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL/PostgreSQL

### Setup
```bash
# Clone the repository
git clone <repository-url>
cd captenMotors

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
# Run migrations
php artisan migrate

# Seed the database
php artisan db:seed

# Build assets
npm run build

# Start the development server
php artisan serve
```

## 🎨 Frontend Architecture

### App Namespace Pattern
The application uses a modular JavaScript architecture with the App namespace:

```javascript
window.App = {
    config: { /* global configuration */ },
    utils: { /* utility functions */ },
    pages: { /* page-specific modules */ },
    components: { /* reusable components */ }
};
```

### File Organization
- **`app.js`**: Core application setup and utilities
- **`pages/`**: Page-specific JavaScript modules
- **`utils/`**: Reusable utility functions
- **`components/`**: Reusable UI components
- **`config/`**: Configuration files

### Dynamic Loading System
The loader automatically loads JavaScript files based on the current route:

```javascript
// Loader configuration
pages: {
    'users.index': ['pages/users/users-index.js'],
    'users.create': ['pages/users/users-create.js', 'utils/form-helpers.js'],
    'users.edit': ['pages/users/users-edit.js', 'utils/form-helpers.js']
}
```

## 📝 Form System

### Form Helpers
Reusable form utilities for consistent behavior across all forms:

```javascript
// Auto-save form data
App.utils.forms.autoSave(form, 'form-key');

// AJAX form submission
App.utils.forms.submitAjax(form, {
    onSuccess: (data) => handleSuccess(data),
    onError: (error) => handleError(error)
});

// Button loading states
App.utils.forms.showButtonLoading(form);
App.utils.forms.hideButtonLoading(form);
```

### Features
- **Auto-save**: Form data persists in localStorage
- **Validation**: Real-time client-side validation
- **AJAX Submission**: No page reloads
- **Error Handling**: Consistent error display
- **Loading States**: Visual feedback during submission

## 🔄 Asset Versioning System

### Configuration
Asset versions are managed in `config/assets.php`:

```php
return [
    'version' => env('ASSET_VERSION', '1.0.0'),
    'versions' => [
        'js/app.js' => '1.0.0',
        'js/pages/users/users-create.js' => '1.0.0',
        // ... more assets
    ]
];
```

### Usage
```html
<!-- Blade directives -->
<script src="@versionedJs('app.js')"></script>
<link href="@versionedCss('app.css')" rel="stylesheet">
<img src="@versioned('images/logo.png')" alt="Logo">
```

### Commands
```bash
# Update all asset versions
php artisan assets:version 2.0.0 --all

# Update specific file
php artisan assets:version 1.1.0 --file=js/pages/users/users-create.js
```

## 👥 User Management

### Features
- **Role-based Access**: Admin, Manager, User roles
- **Soft Deletes**: Users can be restored after deletion
- **Search & Filter**: Real-time search by name, email, role
- **Responsive Design**: Works on desktop and mobile

### User Roles
- **Admin**: Full system access
- **Manager**: Car and user management
- **User**: Basic access and viewing

### API Endpoints
```
GET    /users              - List users
GET    /users/create       - Create user form
POST   /users              - Store new user
GET    /users/{id}/edit    - Edit user form
PUT    /users/{id}         - Update user
DELETE /users/{id}         - Soft delete user
POST   /users/{id}/restore - Restore deleted user
DELETE /users/{id}/force   - Permanently delete user
```

## 🚗 Car Management

### Features
- **Complete CRUD**: Create, read, update, delete cars
- **Multi-step Forms**: Wizard-style car creation
- **File Uploads**: License and car images
- **Status Tracking**: Car status history
- **Equipment Costs**: Track additional costs
- **Bulk Operations**: Manage multiple cars

### Car Statuses
- **Available**: Ready for sale
- **Sold**: Car has been sold
- **In Transit**: Car is being transported
- **Under Inspection**: Car is being inspected

### Form Steps
1. **Basic Information**: Model, category, plate number
2. **Specifications**: Year, engine, keys
3. **Options**: Car features and accessories
4. **Pricing**: Purchase and sale prices
5. **Inspection**: Condition and notes
6. **Images**: License and car photos

## 🎯 JavaScript Patterns

### Page Modules
Each page has its own JavaScript module:

```javascript
App.pages.usersCreate = {
    _form: null,
    _autoSaveKey: 'user-create-form',

    init: function() {
        this._form = document.getElementById('user-form');
        this._setupForm();
        this._bindEvents();
    },

    _handleFormSubmit: function(e) {
        e.preventDefault();
        App.utils.forms.submitAjax(this._form, {
            onSuccess: (data) => this._handleSuccess(data)
        });
    }
};
```

### Component Pattern
Reusable components follow the same pattern:

```javascript
App.components.modal = {
    _activeModal: null,

    init: function() {
        this._bindEvents();
    },

    open: function(modalId) {
        // Modal opening logic
    },

    close: function() {
        // Modal closing logic
    }
};
```

## 🎨 Styling

### Design System
- **KtUI**: Modern UI components
- **Tailwind CSS**: Utility-first CSS framework
- **Responsive**: Mobile-first design approach
- **Consistent**: Unified design language

### Color Scheme
- **Primary**: Blue (#3B82F6)
- **Success**: Green (#10B981)
- **Warning**: Yellow (#F59E0B)
- **Error**: Red (#EF4444)

## 🔧 Development

### Environment Variables
```env
APP_NAME="Capten Motors"
APP_ENV=local
APP_DEBUG=true
ASSET_VERSION=1.0.0
ASSET_CACHE_STRATEGY=version
```

### Development Commands
```bash
# Watch for changes
npm run dev

# Build for production
npm run build

# Run tests
php artisan test

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Code Style
- **PHP**: PSR-12 coding standards
- **JavaScript**: ES6+ with consistent naming
- **CSS**: Tailwind utility classes
- **Blade**: Clean, readable templates

## 🚀 Deployment

### Production Setup
```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update asset versions
php artisan assets:version 2.0.0 --all
```

### Server Requirements
- **PHP**: 8.1+
- **Extensions**: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Web Server**: Apache/Nginx

## 📚 API Documentation

### Authentication
All API endpoints require authentication via Laravel Sanctum.

### Response Format
```json
{
    "success": true,
    "data": { /* response data */ },
    "message": "Operation successful"
}
```

### Error Format
```json
{
    "success": false,
    "message": "Error description",
    "errors": { /* validation errors */ }
}
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support, email support@captenmotors.com or create an issue in the repository.

---

**Built with ❤️ using Laravel, KtUI, and Tailwind CSS**
