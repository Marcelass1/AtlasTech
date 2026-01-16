CREATE DATABASE IF NOT EXISTS rh_db;
USE rh_db;

CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    position VARCHAR(50) NOT NULL,
    department VARCHAR(50) NOT NULL,
    salary DECIMAL(10, 2),
    hired_date DATE DEFAULT CURRENT_DATE
);

INSERT INTO employees (first_name, last_name, email, position, department, salary) VALUES 
('John', 'Doe', 'john@atlastech.com', 'Developer', 'IT', 4500.00),
('Jane', 'Smith', 'jane@atlastech.com', 'HR Manager', 'HR', 5000.00),
('Alice', 'Johnson', 'alice@atlastech.com', 'Accountant', 'Finance', 4800.00);
