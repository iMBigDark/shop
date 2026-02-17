# Simple Shop - E-Commerce System

A complete, simple e-commerce shop with authentication, admin dashboard, and customer shopping interface.

## Features

### 1. **Authentication System**
- User registration with validation
- Secure login with password hashing
- User roles: Customer and Admin
- Session management

### 2. **Admin Dashboard**
- **Product Management**
  - Add new products
  - Edit product details (name, price, description, stock)
  - Delete products
  - View all products in a table

- **Order Management**
  - View recent orders
  - Track order statuses (pending, completed, cancelled)
  - Customer information
  - Order details

### 3. **Customer Features**
- Browse available products
- Add products to shopping cart (with localStorage persistence)
- Adjust quantities in cart
- View cart total
- Place orders
- View order history and status
- Order confirmation page

### 4. **Database Schema**
- Users table (with roles)
- Products table (with stock management)
- Orders table (with status tracking)
- Order items table (for cart records)

## Setup Instructions

### 1. Database Setup
```bash
# Import the database schema
mysql -u root -p < shop.sql
```

Or manually:
- Create a database named `simple_shop`
- Run the SQL queries from `shop.sql`

### 2. Configuration
The database connection is configured in `config.php`:
- Host: `localhost`
- Database: `simple_shop`
- Username: `root`
- Password: `` (empty)

Update these credentials if needed.

### 3. Default Admin Account
- **Username:** admin
- **Password:** admin
- **Role:** Admin

## File Structure

```
shop/
├── config.php           # Database configuration & helper functions
├── index.php           # Landing page (redirects based on role)
├── login.php           # Login page
├── register.php        # Registration page
├── logout.php          # Logout handler
├── customer.php        # Customer shopping page
├── admin.php           # Admin dashboard
├── checkout.php        # Order processing
├── order-success.php   # Order confirmation
├── my-orders.php       # Customer order history
├── style.css           # Responsive styling
├── script.js           # Frontend functionality
└── shop.sql            # Database schema
```

## How to Use

### For Customers
1. Go to `index.php` → Redirects to login
2. Click "Register here" to create new account
3. Login with credentials
4. Browse products on the shop page
5. Add products to cart
6. Click cart button to view and manage items
7. Click "Checkout" to place order
8. View order confirmation with order ID
9. Check "My Orders" to see order history

### For Admin
1. Login with admin credentials (username: `admin`, password: `admin`)
2. Go to Products tab to:
   - Add new products (fill form and submit)
   - Edit products (click Edit button)
   - Delete products (click Delete button)
3. Go to Orders tab to view all customer orders with status

## Key Features Explained

### Shopping Cart
- Uses browser's localStorage for persistence
- Items remain in cart even after page refresh
- Quantities can be adjusted
- Shows real-time total

### Product Management
- Admin can manage full inventory
- Stock levels are tracked and updated on purchase
- Products with 0 stock won't show to customers

### Order System
- Orders are automatically marked as completed after checkout
- Stock is automatically reduced when order is placed
- Customers can track all their orders with timestamps

### Security
- Passwords are hashed using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- HTML entity encoding for XSS prevention

## Styling
- Modern gradient design with purple theme
- Fully responsive (desktop, tablet, mobile)
- Clean, intuitive UI
- Smooth animations and transitions

## Technologies Used
- PHP 7.4+
- MySQL/MariaDB
- HTML5
- CSS3
- JavaScript (Vanilla)
- localStorage API

## Notes
- Demo admin account is available for testing
- Cart data is stored locally in browser
- All passwords are securely hashed
- Multiple simultaneous users are supported