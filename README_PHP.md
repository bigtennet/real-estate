# Premium Real Estate Website - PHP Version

A modern, professional real estate listing website built with PHP and MySQL, featuring a beautiful glassmorphism UI and WhatsApp integration for lead generation.

## 🚀 **Features**

- 🏠 **Property Listings**: Browse and search through premium properties
- 🎨 **Glassmorphism Design**: Modern, professional UI with Tailwind CSS
- 📱 **WhatsApp Integration**: Direct contact with property owners via WhatsApp
- 👨‍💼 **Admin Dashboard**: Manage properties, view analytics, and handle leads
- 🗄️ **MySQL Database**: Robust database with proper relationships
- 📱 **Responsive Design**: Works perfectly on all devices
- ⚡ **Fast Performance**: Built with PHP and optimized for speed

## 🛠️ **Tech Stack**

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Styling**: Tailwind CSS with custom glassmorphism components
- **Icons**: Lucide Icons
- **Server**: Apache/Nginx

## 📋 **Requirements**

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PDO MySQL extension

## 🚀 **Quick Setup**

### 1. **Download and Extract**
```bash
# Extract the files to your web server directory
# For XAMPP: C:\xampp\htdocs\real-estate
# For WAMP: C:\wamp64\www\real-estate
# For Linux: /var/www/html/real-estate
```

### 2. **Database Configuration**
Edit `config/database.php` with your MySQL credentials:
```php
private $host = 'localhost';
private $db_name = 'real_estate_db';
private $username = 'root';
private $password = 'your_password';
```

### 3. **Create Database**
```sql
CREATE DATABASE real_estate_db;
```

### 4. **Run Setup Script**
Visit: `http://localhost/real-estate/setup.php`

Or run via command line:
```bash
php setup.php
```

### 5. **Access the Website**
- **Main Website**: `http://localhost/real-estate/`
- **Admin Dashboard**: `http://localhost/real-estate/admin/`
- **Admin Login**: admin / admin123

## 📁 **Project Structure**

```
real-estate/
├── config/
│   └── database.php          # Database configuration
├── database/
│   └── schema.sql            # Database schema and sample data
├── includes/
│   ├── header.php            # Common header
│   └── footer.php            # Common footer
├── admin/
│   ├── index.php             # Admin dashboard
│   ├── login.php             # Admin login
│   ├── properties.php        # Manage properties
│   └── add-property.php      # Add new property
├── index.php                 # Homepage
├── properties.php            # Properties listing
├── about.php                 # About page
├── contact.php               # Contact page
├── setup.php                 # Database setup script
└── README_PHP.md            # This file
```

## 🎨 **Design Features**

### **Glassmorphism UI**
- Beautiful glassmorphism effects
- Gradient backgrounds
- Smooth animations
- Professional color scheme

### **Responsive Design**
- Mobile-first approach
- Tablet and desktop optimized
- Touch-friendly interface

### **WhatsApp Integration**
- Direct contact buttons on each property
- Pre-filled messages with property details
- Mobile-optimized WhatsApp links

## 🗄️ **Database Schema**

### **Tables**
- **properties**: Stores property information
- **owners**: Stores property owner information

### **Key Features**
- Proper foreign key relationships
- JSON fields for flexible data storage
- Timestamps for tracking
- Status management

## 👨‍💼 **Admin Dashboard**

### **Features**
- Property management (CRUD operations)
- Owner management
- Statistics dashboard
- Search and filtering
- Bulk operations

### **Access**
- URL: `/admin/`
- Username: `admin`
- Password: `admin123`

## 📱 **WhatsApp Integration**

Each property listing includes a WhatsApp contact button that:
- Opens WhatsApp with pre-filled message
- Includes property title and location
- Requests more information
- Mobile-optimized experience

## 🎯 **Key Pages**

### **Homepage** (`index.php`)
- Hero section with search
- Featured properties
- Statistics
- Property types
- Call-to-action sections

### **Properties** (`properties.php`)
- Advanced search and filtering
- Property grid layout
- WhatsApp contact buttons
- Responsive design

### **About** (`about.php`)
- Company information
- Team members
- Values and mission
- Statistics

### **Contact** (`contact.php`)
- Contact form
- Business information
- WhatsApp quick contact
- Office location

## 🔧 **Customization**

### **Styling**
- Edit CSS in `includes/header.php`
- Modify Tailwind classes
- Update color schemes
- Add custom animations

### **Database**
- Modify `database/schema.sql`
- Update `config/database.php`
- Add new fields as needed

### **WhatsApp Numbers**
- Update owner WhatsApp numbers in database
- Modify contact information in pages
- Customize message templates

## 🚀 **Deployment**

### **Shared Hosting**
1. Upload files via FTP
2. Create MySQL database
3. Update database configuration
4. Run setup script
5. Test all functionality

### **VPS/Dedicated Server**
1. Install PHP and MySQL
2. Configure web server
3. Upload files
4. Set up database
5. Configure SSL (recommended)

### **Docker** (Optional)
```dockerfile
FROM php:7.4-apache
COPY . /var/www/html/
RUN docker-php-ext-install pdo pdo_mysql
```

## 🔒 **Security Notes**

- Change default admin credentials
- Use strong passwords
- Enable HTTPS in production
- Regular database backups
- Keep PHP and MySQL updated

## 📞 **Support**

For support and customization:
- Check the code comments
- Review the database schema
- Test all functionality
- Customize as needed

## 🎉 **You're Ready!**

Your real estate website is now ready to use! The setup includes:
- ✅ Beautiful glassmorphism design
- ✅ WhatsApp integration
- ✅ Admin dashboard
- ✅ Property management
- ✅ Responsive design
- ✅ MySQL database
- ✅ Sample data

**Next Steps:**
1. Customize the design
2. Add your logo
3. Update contact information
4. Add your properties
5. Configure WhatsApp numbers
6. Deploy to production

---

Built with ❤️ using PHP, MySQL, and Tailwind CSS
