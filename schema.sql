CREATE DATABASE IF NOT EXISTS gemFin;
USE gemFin;


CREATE TABLE `user` (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    time_stamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `balance` (
    balance_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    current_balance DECIMAL(15, 2) DEFAULT 0.00,
    time_stamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES `user`(user_id) ON DELETE CASCADE
);

CREATE TABLE `category` (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    description VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES `user`(user_id)
);

CREATE TABLE `budget` (
    budget_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    month VARCHAR(20) NOT NULL, 
    FOREIGN KEY (user_id) REFERENCES `user`(user_id),
    FOREIGN KEY (category_id) REFERENCES `category`(category_id)
);

CREATE TABLE `saving_goals` (
    goal_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    description VARCHAR(255),
    target_amount DECIMAL(15, 2) NOT NULL,
    current_amount DECIMAL(15, 2) DEFAULT 0.00,
    target_date DATE,
    FOREIGN KEY (user_id) REFERENCES `user`(user_id)
);

CREATE TABLE `transaction` (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    time_stamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES `user`(user_id),
    FOREIGN KEY (category_id) REFERENCES `category`(category_id)
);


CREATE TABLE `expense` (
    expense_id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    merchant VARCHAR(100),
    FOREIGN KEY (transaction_id) REFERENCES `transaction`(transaction_id) ON DELETE CASCADE
);

CREATE TABLE `income` (
    income_id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    source VARCHAR(100),
    FOREIGN KEY (transaction_id) REFERENCES `transaction`(transaction_id) ON DELETE CASCADE
);

CREATE TABLE `reports` (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50),
    start_date DATE,
    total_income DECIMAL(15, 2),
    FOREIGN KEY (user_id) REFERENCES `user`(user_id)
);
