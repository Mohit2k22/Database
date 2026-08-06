the complete, ready-to-run IT Asset Inventory System streamlined strictly for hardware asset tracking.
1. Database Setup (it_asset_db)
Open phpMyAdmin in XAMPP, create a database named it_asset_db, and run this SQL script in the SQL tab:
CREATE DATABASE IF NOT EXISTS it_asset_db;
USE it_asset_db;

CREATE TABLE IF NOT EXISTS assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_tag VARCHAR(50) NOT NULL UNIQUE,
    device_type VARCHAR(50) NOT NULL,
    brand_model VARCHAR(100) NOT NULL,
    department VARCHAR(50) NOT NULL,
    assigned_user VARCHAR(100) DEFAULT 'Unassigned',
    status ENUM('Assigned', 'In Stock', 'Disposed') DEFAULT 'In Stock',
    purchase_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Initial Sample Data
INSERT INTO assets (asset_tag, device_type, brand_model, department, assigned_user, status, purchase_date) 
VALUES 
('AST-1001', 'Laptop', 'Dell Latitude 3420', 'IT', 'Sajedul Hasan', 'Assigned', '2025-01-15'),
('AST-1002', 'Monitor', 'Dell P2419H', 'Accounts', 'Rahim Ahmed', 'Assigned', '2024-11-10'),
('AST-1003', 'Switch', 'Cisco Catalyst 2960', 'Network Ops', 'Unassigned', 'In Stock', '2023-06-20');



How to Run
 * Start Apache and MySQL in your XAMPP Control Panel.
 * Place style.css and index.php inside C:/xampp/htdocs/it-assets/.
 * Open your web browser and navigate to: 


 Create style.css in your project folder (C:/xampp/htdocs/it-assets/style.css):


    3. Application Script (index.php)
Create index.php in your project folder (C:/xampp/htdocs/it-assets/index.php):


                                <a href="index.php?delete=<?php echo $row['id']; ?>" class="action-link" onclick="return confirm('Delete this asset?');">Delete</a>
