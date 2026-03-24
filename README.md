# StayEase — Hotel Booking Website
### Complete Full-Stack PHP + MySQL Project

---

## 📁 Project Structure

```
stayease/
├── index.php                  # Home page
├── hotels.php                 # Hotel listing + filters
├── hotel-detail.php           # Hotel detail + booking form
├── booking.php                # Booking processor + confirmation
├── my-bookings.php            # User booking history
├── login.php                  # User login
├── register.php               # User registration
├── logout.php                 # Session destroy
├── generate-placeholders.php  # One-time image placeholder generator
├── config.php                 # DB config + helpers
├── stayease_db.sql            # Database + seed SQL
│
├── css/
│   └── style.css              # Main stylesheet
│
├── js/
│   ├── main.js                # Frontend interactions + validation
│   └── admin.js               # Admin panel enhancements
│
├── images/
│   └── hotels/                # Hotel images (auto-created)
│
├── includes/
│   ├── header.php             # Shared HTML head + navbar
│   └── footer.php             # Shared footer
│
└── admin/
    ├── dashboard.php          # Admin overview + stats
    ├── hotels.php             # List / delete hotels
    ├── add-hotel.php          # Add new hotel (with image upload)
    ├── edit-hotel.php         # Edit existing hotel
    ├── bookings.php           # View / manage / filter bookings
    └── users.php              # View / manage users + roles
```

---

## 🚀 How to Run Locally (XAMPP / WAMP)

### Prerequisites
- **XAMPP** (recommended) or **WAMP** installed
- PHP 8.0+ | MySQL 5.7+ | Apache

---

### Step 1 — Place the Project

**XAMPP:**
```
C:\xampp\htdocs\stayease\
```
**WAMP:**
```
C:\wamp64\www\stayease\
```
**macOS XAMPP:**
```
/Applications/XAMPP/htdocs/stayease/
```

---

### Step 2 — Start Servers

Open **XAMPP Control Panel** and start:
- ✅ Apache
- ✅ MySQL

---

### Step 3 — Create the Database

1. Open your browser → go to `http://localhost/phpmyadmin`
2. Click **"New"** on the left sidebar
3. Name the database: **`stayease_db`** → click **Create**
4. Click the **SQL** tab
5. Open `stayease_db.sql` from the project folder
6. Paste the entire content → click **Go**

This creates all tables and inserts sample data including 6 hotels and the admin account.

---

### Step 4 — Configure Database Connection

Open `config.php` and update if needed:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // Your MySQL username
define('DB_PASS', '');         // Your MySQL password (blank for XAMPP default)
define('DB_NAME', 'stayease_db');
define('SITE_URL', 'http://localhost/stayease');
```

---

### Step 5 — Generate Placeholder Images

Visit in your browser:
```
http://localhost/stayease/generate-placeholders.php
```

This creates SVG placeholder images for all 6 seed hotels. You can later replace them with real `.jpg` images in `/images/hotels/`.

---

### Step 6 — Open the Website

```
http://localhost/stayease/
```

---

## 🔑 Login Credentials

| Role  | Email                  | Password   |
|-------|------------------------|------------|
| Admin | admin@stayease.com     | Admin@123  |
| User  | Register a new account | (your own) |

> **Admin Panel:** `http://localhost/stayease/admin/dashboard.php`

---

## ✨ Features Summary

### Public Features
- 🏠 **Homepage** — Hero section, search bar, featured hotels, stats, testimonials
- 🔍 **Hotel Search** — Filter by location, price range, rating; sort by multiple criteria
- 🏨 **Hotel Detail** — Full description, amenities grid, policies, booking form with live total calculator
- 📅 **Booking Flow** — Date validation, guest selection, instant confirmation page
- 📋 **My Bookings** — View history, cancel upcoming bookings

### Authentication
- 🔐 **Register** — Email + password (bcrypt hashed), instant login on registration
- 🔑 **Login** — Session-based with redirect support
- 🚪 **Logout** — Clean session destruction

### Admin Panel
- 📊 **Dashboard** — Hotel / booking / user / revenue stats + recent bookings
- 🏨 **Hotels CRUD** — Add (with image upload), edit, delete hotels
- 📋 **Bookings** — View all, filter by status/search, confirm/cancel/delete
- 👥 **Users** — View all users, toggle admin role, delete users

---

## 🔒 Security Features

- ✅ PDO with prepared statements (SQL injection prevention)
- ✅ `password_hash()` / `password_verify()` (bcrypt)
- ✅ `htmlspecialchars()` output escaping (XSS prevention)
- ✅ Session-based authentication with `session_regenerate_id()`
- ✅ Admin route protection via `requireAdmin()`
- ✅ Input sanitization with `sanitize()` helper
- ✅ File upload validation (type + size checks)

---

## 🎨 Design Details

- **Typography:** Cormorant Garamond (headings) + DM Sans (body)
- **Color Palette:** Deep forest green `#1B4332` + gold `#C9A84C` + warm neutrals
- **Style:** Refined luxury — editorial, asymmetric, scroll-aware navbar
- **Responsive:** Mobile-first, works on all screen sizes
- **Animations:** CSS scroll-reveal, number counters, card hover effects

---

## 📤 Adding Real Hotel Images

1. Upload `.jpg` / `.png` images to `/images/hotels/`
2. Via Admin Panel → Edit Hotel → upload new image
3. Images are stored locally; filenames are hashed with `uniqid()`

---

## 🛠 Tech Stack

| Layer    | Technology                  |
|----------|-----------------------------|
| Frontend | HTML5, CSS3, Vanilla JS     |
| Backend  | PHP 8.x                     |
| Database | MySQL 5.7+ / MariaDB        |
| ORM/DB   | PDO with prepared statements|
| Fonts    | Google Fonts (self-hostable)|
| Icons    | Font Awesome 6              |
| Server   | Apache (XAMPP/WAMP)         |

---

## 🔧 Troubleshooting

**Blank page?** → Check `config.php` DB credentials. Enable error display with `ini_set('display_errors',1)`.

**Images not showing?** → Run `generate-placeholders.php` once, or check `/images/hotels/` folder permissions.

**Can't log in as admin?** → Make sure you ran the SQL script — it inserts the admin user. Re-import `stayease_db.sql` if needed.

**Upload not working?** → Check that `/images/hotels/` is writable (`chmod 755` on Linux/Mac).

**Session issues?** → Ensure `session.save_path` is configured correctly in your PHP.ini.
