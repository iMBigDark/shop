CREATE DATABASE IF NOT EXISTS simple_shop;
USE simple_shop;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image VARCHAR(255)
);

INSERT INTO products (name, price, description, image) VALUES
('Laptop', 999.99, 'A powerful laptop for work and gaming', 'laptop.jpg'),
('Phone', 599.99, 'Latest smartphone with great features', 'phone.jpg'),
('Headphones', 149.99, 'High quality audio headphones', 'headphones.jpg'),
('Tablet', 399.99, 'Portable tablet for entertainment', 'tablet.jpg');