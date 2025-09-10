# Notes System

A comprehensive note-taking web application built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

- 🔐 **User Authentication** - Secure login and registration system
- 📝 **Note Management** - Create, read, update, and delete notes
- 🎨 **Color Customization** - Change note colors with built-in color picker
- 👤 **User Profiles** - Manage account settings and upload profile pictures
- 📱 **Responsive Design** - Modern, mobile-friendly interface
- 🔒 **Security** - Password hashing, input validation, and session management

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP/WAMP/LAMP stack

## Installation

1. **Clone/Download** the project to your XAMPP htdocs folder:
   ```
   C:\xampp\htdocs\cilin-midterm-withdatabase\
   ```

2. **Start XAMPP** services:
   - Apache
   - MySQL

3. **Setup Database**:
   - Visit: `http://localhost/cilin-midterm-withdatabase/setup.php`
   - This will automatically create the database and tables

4. **Access the Application**:
   - Visit: `http://localhost/cilin-midterm-withdatabase/`

## Default Login

- **Username:** demo
- **Password:** password

## Project Structure

```
cilin-midterm-withdatabase/
├── api/
│   └── notes.php              # API endpoints for note operations
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   └── js/
│       └── dashboard.js       # JavaScript functionality
├── classes/
│   ├── Note.php               # Note class
│   └── User.php               # User class
├── config/
│   ├── config.php             # Application configuration
│   └── database.php           # Database connection
├── database/
│   └── schema.sql             # Database schema
├── uploads/
│   └── profiles/              # Profile image uploads
├── dashboard.php              # Main dashboard
├── index.php                  # Entry point
├── login.php                  # Login page
├── logout.php                 # Logout handler
├── profile.php                # Profile management
├── register.php               # Registration page
└── setup.php                  # Database setup script
```

## Database Schema

### Users Table
- `id` - Primary key
- `username` - Unique username
- `email` - User email
- `password_hash` - Hashed password
- `profile_image` - Profile image filename
- Timestamps

### Notes Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `title` - Note title
- `content` - Note content
- `color` - Note background color (hex)
- Timestamps

### User Sessions Table
- Session management (for future enhancements)

## Features in Detail

### User Authentication
- Secure registration with email validation
- Login with username or email
- Password hashing using PHP's password_hash()
- Session-based authentication

### Note Management
- Create notes with title and content
- Edit existing notes
- Delete notes with confirmation
- Real-time updates without page refresh

### Color Customization
- Quick color picker with preset colors
- Custom color selection
- Visual color grid interface

### Profile Management
- Update username and email
- Upload profile pictures
- Image validation and resizing
- Default avatar system

### Security Features
- Input sanitization
- SQL injection prevention with prepared statements
- Password strength requirements
- File upload validation
- Session security

## Customization

### Colors
Edit `assets/css/style.css` to customize the color scheme:
- Primary color: `#667eea`
- Secondary color: `#764ba2`

### Upload Settings
Modify `config/config.php` for upload settings:
- File size limits
- Allowed file types
- Upload directory

## Browser Support

- Chrome 60+
- Firefox 60+
- Safari 12+
- Edge 79+

## Contributing

1. Fork the project
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

## License

This project is open source and available under the [MIT License](LICENSE).
