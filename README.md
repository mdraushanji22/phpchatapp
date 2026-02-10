# ChatRoom Application

A modern real-time PHP chat application with secure authentication, instant messaging capabilities, and responsive Bootstrap 5 UI.

## Technology Stack

| Technology | Version |
|------------|---------|
| PHP | 8.2+ |
| Bootstrap | 5.3.2 |
| jQuery | 3.7.1 |
| Pusher JS | 8.4.0 |
| Pusher PHP | ^7.2 |
| Bootstrap Icons | 1.11.1 |

## Features

### Authentication
- User login/registration with username and phone number
- Session-based authentication
- Secure logout functionality
- Client-side and server-side input validation
- Username: 3-20 alphanumeric characters
- Phone: 7-15 digits

### Messaging
- Real-time messaging using Pusher 8.4.0
- Message history (last 50 messages)
- Timestamps on messages
- Username display instead of phone numbers
- 500 character message limit with counter
- XSS protection with HTML escaping
- Message animations and hover effects

### UI/UX
- Modern Bootstrap 5.3 responsive design
- Gradient backgrounds and animations
- Loading states and spinners
- Character counter for messages
- Mobile-friendly interface
- Smooth scrolling and transitions

### Security
- SQL injection prevention using prepared statements
- XSS protection with htmlspecialchars()
- Input validation and sanitization
- Session security
- CSRF protection ready

## File Structure

```
├── css/
│   ├── login.css       # Login page styles with animations
│   └── style.css       # Main chat styles
├── js/
│   └── script.js       # jQuery + Pusher integration
├── addMsg.php          # Handle message sending
├── chatroom.sql        # Database schema
├── composer.json       # PHP 8.2+ dependencies
├── db.php              # Database connection
├── index.php           # Main chat page (Bootstrap 5)
├── login.php           # Login/registration (Bootstrap 5)
├── logout.php          # Logout handler
├── pusher.php          # Pusher configuration
├── readMsg.php         # Fetch messages
└── sendMsg.php         # Pusher trigger example
```

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uname VARCHAR(50) NOT NULL,
    phone VARCHAR(15) NOT NULL UNIQUE
);
```

### Messages Table
```sql
CREATE TABLE msg (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(15) NOT NULL,
    msg TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Dependencies

- PHP 8.2+
- MySQL/MariaDB 10.4+
- Pusher PHP Server SDK (^7.2)
- Composer 2.0+
- Modern web browser (Chrome, Firefox, Safari, Edge)

## Installation

1. Clone the repository to your web server directory
2. Run `composer install` to install dependencies
3. Import `chatroom.sql` into your database
4. Update `db.php` with your database credentials
5. Update `pusher.php` with your Pusher credentials
6. Access the application through your web browser

## Configuration

### Database (db.php)
```php
$db = mysqli_connect("localhost", "username", "password", "chatRoom");
```

### Pusher (pusher.php)
```php
$pusher = new Pusher\Pusher(
    'your-app-key',
    'your-app-secret',
    'your-app-id',
    $options
);
```

## Usage

1. Navigate to `login.php`
2. Enter a username (3-20 alphanumeric characters)
3. Enter a phone number (7-15 digits)
4. Click "Login / Register"
5. Start chatting in real-time!

## Security Features

- All database queries use prepared statements
- Input validation on all user inputs
- XSS protection with HTML escaping
- Session-based authentication
- Secure session destruction on logout

## Real-time Features

- Instant message delivery using Pusher
- No page refresh required
- Fallback polling every 10 seconds
- Message timestamps

## Browser Compatibility

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)

## License

This project is open source and available for educational purposes.
