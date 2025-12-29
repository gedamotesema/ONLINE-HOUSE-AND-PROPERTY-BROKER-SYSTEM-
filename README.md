# GNK Housing - House Rental Broker System

**Web Design Project - Third Year Software Engineering**

---

## Project Information

**Course:** Web Design  
**Academic Year:** 2025/26  
**Section:** A  
**Instructor:** Mr. Wondimagegn  
**Submission Date:** january 1, 2026

---

## Group Members (Section A)

| Name | ID |
|------|---------|
| Gedamo Tesema | 1600179 |
| Naod Hailu | 1600278 |
| Kidst Zewde | 1601298 |
| Yoseph Elias | 1600407 |
| Amir Detamo | 1600045 |
| Adimasu Robito | 1500628 |

---

## Project Description

GNK Housing is a web-based house rental broker platform that connects landlords and tenants. The system allows landlords to list their properties, tenants to browse and request viewings, and facilitates communication between both parties through an integrated messaging system.

---

## Technologies Used

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Server:** Apache (WAMP/XAMPP)
- **Architecture:** Multi-Page Application (MPA)

---

## System Features

### 1. User Authentication & Authorization
- User registration with role selection (Tenant/Landlord)
- Secure login system with password hashing
- Password reset functionality
- Role-based access control
- Session management
- CSRF protection

### 2. Property Management (CRUD Operations)
- Landlords can create new property listings
- Upload up to 5 images per property
- Edit existing properties
- Delete properties
- Toggle property status (Available/Rented/Pending)
- Property types: Apartment, House, Studio, Villa

### 3. Property Browsing & Searching
- Browse all available properties
- Search by title or location
- Filter by property type
- Filter by maximum price
- Combined filtering options
- Responsive property cards with images

### 4. Messaging System
- Real-time messaging between tenants and landlords
- Messages linked to specific properties
- Conversation history
- User avatars in messages

### 5. Viewing Request Management
- Tenants can request property viewings
- Schedule preferred date and time
- Landlords can accept or reject requests
- Status tracking (Pending/Accepted/Rejected)
- Dashboard notifications for new requests

### 6. User Profile Management
- Edit personal information
- Upload profile picture
- Change password securely
- Update bio

### 7. Admin Dashboard
- View system logs
- Monitor user activity
- Oversee properties
- Platform statistics

### 8. Responsive Design
- Mobile-friendly interface
- Modern glassmorphism UI design
- Smooth animations and transitions
- Auto-dismissing alerts

---

## Security Features

1. **SQL Injection Prevention** - Using PDO prepared statements
2. **XSS Protection** - All inputs are sanitized
3. **CSRF Protection** - CSRF tokens on all forms
4. **Password Security** - Passwords hashed with bcrypt
5. **File Upload Validation** - Type and size checking
6. **Session Security** - Proper session handling
7. **Access Control** - Role-based permissions

---

## Database Structure

The system uses 7 main tables:

1. **users** - Stores user accounts (tenants, landlords, admins)
2. **properties** - Property listings with details and images
3. **conversations** - Chat conversations between users
4. **messages** - Individual messages within conversations
5. **viewing_requests** - Property viewing appointments
6. **favorites** - User's saved favorite properties
7. **system_logs** - Activity logging for security

---

## Installation Instructions

### Prerequisites
- WAMP/XAMPP installed
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Step 1: Setup Project Files
1. Copy the project folder to `C:\wamp64\www\final` (or your web server directory)
2. Make sure `uploads` folder has write permissions

### Step 2: Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `rental_broker`
3. Import the `database.sql` file into the database

### Step 3: Configuration
1. Open `config.php`
2. Update database credentials if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'rental_broker');
```

### Step 4: Access the Application
1. Start WAMP/XAMPP server
2. Open browser and go to: `http://localhost/final`

---

## File Structure

```
final/
├── includes/
│   ├── auth.php              # Authentication functions
│   ├── functions.php         # Utility functions
│   ├── header.php            # Header component
│   └── footer.php            # Footer component
├── uploads/
│   └── profiles/             # User profile pictures
├── config.php                # Database configuration
├── database.sql              # Database schema
├── index.php                 # Landing page
├── login.php                 # Login page
├── register.php              # Registration page
├── dashboard.php             # User dashboard
├── properties.php            # Browse properties
├── property_details.php      # Property details
├── property_manage.php       # Create/Edit property
├── messages.php              # Messaging system
├── profile.php               # Profile management
├── admin_dashboard.php       # Admin panel
├── style.css                 # All styles
└── main.js                   # JavaScript functionality
```

---

## User Guide

### For Tenants:
1. Register as a Tenant
2. Browse available properties
3. Search and filter properties
4. View property details
5. Request viewings
6. Send messages to landlords
7. Manage your profile

### For Landlords:
1. Register as a Landlord
2. List new properties with images
3. Manage your property listings
4. View and respond to viewing requests
5. Chat with interested tenants
6. Update property status
7. Edit or delete properties

### For Admins:
1. Access admin dashboard
2. View system logs
3. Monitor user activities
4. Oversee all properties

---

## Testing

### Test Accounts
Create test accounts through the registration system:

**Tenant:**
- Email: tenant@test.com
- Password: Test123!

**Landlord:**
- Email: landlord@test.com
- Password: Test123!

**Admin:**
- Email: admin@test.com
- Password: Admin123!

---

## Key Pages

| Page | URL | Access |
|------|-----|--------|
| Landing Page | `/index.php` | Public |
| Login | `/login.php` | Public |
| Register | `/register.php` | Public |
| Browse Properties | `/properties.php` | Public |
| Property Details | `/property_details.php?id=X` | Public |
| Dashboard | `/dashboard.php` | Authenticated Users |
| Messages | `/messages.php` | Authenticated Users |
| Profile | `/profile.php` | Authenticated Users |
| Manage Property | `/property_manage.php` | Landlords Only |
| Admin Panel | `/admin_dashboard.php` | Admins Only |

---

## Troubleshooting

**Problem:** Can't connect to database  
**Solution:** Check that MySQL is running and credentials in `config.php` are correct

**Problem:** Images not uploading  
**Solution:** Make sure `uploads` folder has write permissions

**Problem:** Session errors  
**Solution:** Check that PHP sessions are enabled

**Problem:** Page not found errors  
**Solution:** Verify Apache mod_rewrite is enabled

---

## Future Improvements

- Email notifications for messages and requests
- Map integration for property locations
- Payment integration for rent payments
- Property comparison feature
- Reviews and ratings
- Mobile app version
- Advanced search with more filters

---

## References

- PHP Documentation: https://www.php.net/docs.php
- MySQL Documentation: https://dev.mysql.com/doc/
- Web Design Course Materials
- Modern Real Estate Platform Designs

---

## Declaration

We declare that this project is our own work and has been developed as part of the Web Design course requirements for Third Year Software Engineering, Section A.

**Submitted to:** Mr. Wondimagegn  
**Submission Date:** january 1 , 2026

---

**© 2025 GNK Housing - Section A Group Project**
