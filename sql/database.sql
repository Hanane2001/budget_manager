-- Create database
CREATE DATABASE IF NOT EXISTS smart_wallet;
USE smart_wallet;

-- Create incomes table
CREATE TABLE IF NOT EXISTS incomes (
    idIn INT PRIMARY KEY AUTO_INCREMENT,
    amountIn DECIMAL(10,2) NOT NULL,
    dateIn DATE NOT NULL,
    descriptionIn VARCHAR(250) DEFAULT 'Unknown',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create expenses table
CREATE TABLE IF NOT EXISTS expenses (
    idEx INT PRIMARY KEY AUTO_INCREMENT,
    amountEx DECIMAL(10,2) NOT NULL,
    dateEx DATE NOT NULL,
    descriptionEx VARCHAR(250) DEFAULT 'Unknown',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO incomes (amountIn, dateIn, descriptionIn) VALUES 
(3000.00, '2024-01-15', 'Salary'),
(500.00, '2024-01-20', 'Freelance Work');

INSERT INTO expenses (amountEx, dateEx, descriptionEx) VALUES 
(800.00, '2024-01-05', 'Rent'),
(200.00, '2024-01-10', 'Groceries'),
(50.00, '2024-01-12', 'Utilities');

