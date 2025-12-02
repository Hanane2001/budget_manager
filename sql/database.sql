CREATE DATABASE IF NOT EXISTS smart_wallet;

USE DATABASE smart_wallet;

CREATE TABLE IF NOT EXISTS incomes(
    idIn int primary key Auto_INCREMENT,
    amountIn DECIMAL(10,2) NOT NULL,
    -- dateIn DATE DEFAULT (CURRENT_TIME),
    dateIn DATE NOT NULL,
    descriptionIn varchar(250) DEFAULT 'Unknown'
);

CREATE TABLE IF NOT EXISTS expenses(
    idEx int primary key Auto_INCREMENT,
    amountEx DECIMAL(10,2) NOT NULL,
    -- dateEx DATE DEFAULT (CURRENT_TIME),
    dateEx DATE NOT NULL,
    descriptionEx varchar(250) DEFAULT 'Unknown'
);

SELECT * FROM incomes;

SELECT * FROM expenses;
