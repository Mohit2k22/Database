-- IT Asset & Hardware Maintenance Log System
-- Source model: Users, Assets, Maintenance_Log from the project proposal.

CREATE DATABASE IF NOT EXISTS it_asset_10
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE it_asset_10;

DROP TABLE IF EXISTS Maintenance_Log;
DROP TABLE IF EXISTS Assets;
DROP TABLE IF EXISTS Users;

CREATE TABLE Users (
  UserID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  FullName VARCHAR(100) NOT NULL,
  Department VARCHAR(50) NOT NULL,
  Email VARCHAR(100) NOT NULL UNIQUE,
  CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE Assets (
  AssetID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  SerialNo VARCHAR(100) NOT NULL UNIQUE,
  DeviceType VARCHAR(50) NOT NULL,
  Brand VARCHAR(50) NOT NULL,
  Model VARCHAR(50) NOT NULL,
  PurchaseDate DATE NULL,
  WarrantyExpiry DATE NULL,
  AssignedUserID INT UNSIGNED NULL,
  Status ENUM('Operational','Maintenance Required','Under Repair','Retired')
    NOT NULL DEFAULT 'Operational',
  CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_assets_user
    FOREIGN KEY (AssignedUserID) REFERENCES Users(UserID)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE Maintenance_Log (
  MaintenanceID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  AssetID INT UNSIGNED NOT NULL,
  MaintenanceDate DATE NOT NULL,
  Technician VARCHAR(100) NOT NULL,
  Problem TEXT NOT NULL,
  RepairDetails TEXT NULL,
  Cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  CompletionDate DATE NULL,
  CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_maintenance_asset
    FOREIGN KEY (AssetID) REFERENCES Assets(AssetID)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_assets_status ON Assets(Status);
CREATE INDEX idx_assets_type ON Assets(DeviceType);
CREATE INDEX idx_assets_assigned_user ON Assets(AssignedUserID);
CREATE INDEX idx_maintenance_asset ON Maintenance_Log(AssetID);
CREATE INDEX idx_maintenance_date ON Maintenance_Log(MaintenanceDate);

INSERT INTO Users (FullName, Department, Email) VALUES
('Demo Administrator','IT Department','admin@example.com'),
('Rahim Ahmed','Computer Science','rahim@example.com'),
('Nusrat Jahan','Administration','nusrat@example.com');

INSERT INTO Assets
(SerialNo,DeviceType,Brand,Model,PurchaseDate,WarrantyExpiry,AssignedUserID,Status) VALUES
('DEMO-PC-001','Desktop computer','Dell','OptiPlex 7010','2025-01-15','2028-01-14',2,'Operational'),
('DEMO-LAP-002','Laptop','Lenovo','ThinkPad E14','2024-08-10','2027-08-09',3,'Maintenance Required'),
('DEMO-RTR-003','Router','MikroTik','RB4011','2024-03-20','2027-03-19',NULL,'Operational'),
('DEMO-PRN-004','Printer','HP','LaserJet Pro','2023-11-02','2025-11-01',NULL,'Under Repair');

INSERT INTO Maintenance_Log
(AssetID,MaintenanceDate,Technician,Problem,RepairDetails,Cost,CompletionDate) VALUES
(2,'2026-07-18','IT Support','Battery health degraded','Battery inspection and replacement scheduled',85.00,NULL),
(4,'2026-07-22','Network Technician','Paper feed error','Roller cleaned and pickup assembly inspected',25.00,'2026-07-23');
