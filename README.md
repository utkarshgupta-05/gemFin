<h1 align="center">💎 gemFin</h1>
<h3 align="center">Personal Finance Tracker</h3>

<p align="center">
  <b>Track Income · Manage Budgets · Set Goals · Visualize Data</b>
</p>

<br>
**gemFin** is a comprehensive, web-based personal finance management application built to help users track their income, expenses, budgets, and savings goals. It features a modern, glassmorphism-inspired UI, real-time balance calculations, and dynamic data visualization.

---

## 🚀 Key Features

### 1. 📊 Smart Dashboard

* **Live Balance:** Automatically updates based on income and expenses.
* **Recent Activity:** Shows the last 10 transactions with color-coded amounts (Green for Income, Black for Expense).
* **Smart Validation:** Prevents users from adding transactions for future dates.

### 2. 💰 Budget Management

* **Monthly Limits:** Set specific spending limits for different categories (e.g., Food, Travel).
* **Visual Progress Bars:**
* 🟢 **Green:** On Track
* 🟠 **Orange:** Warning (>75% spent)
* 🔴 **Red:** Over Budget (>100% spent)


* **Auto-Filtering:** Automatically switches views based on the selected month.

### 3. 🎯 Gamified Savings Goals

* Create specific saving targets (e.g., "Buy a Bike").
* **Completion Logic:** When the target is met, the card turns Gold 🏆 and further inputs are locked to prevent errors.
* Shows percentage progress bars for every goal.

### 4. 📈 Dynamic Reports

* **Interactive Charts:** Powered by **Chart.js**.
* **Trend Analysis:** Line chart comparing Income vs. Expense vs. Net Balance.
* **Expense Breakdown:** Doughnut chart showing spending distribution by category.
* **Time Filters:** Filter data by "This Year", "Last Year", "Last 6 Months", or Custom Date Ranges.

---

## 🛠️ Tech Stack

* **Frontend:** HTML5, CSS3 (Modern Light Theme), JavaScript (Vanilla).
* **Backend:** PHP (Procedural & Object-Oriented).
* **Database:** MySQL (Relational Schema).
* **Libraries:**
* [Chart.js](https://www.chartjs.org/) (Data Visualization)
* [FontAwesome](https://fontawesome.com/) (Icons)


* **Server Environment:** XAMPP (Apache/MySQL).

---

## 📸 Screenshots


| <img width="1919" height="909" alt="image" src="https://github.com/user-attachments/assets/000b6995-2a2b-47bf-b938-120b057ba31c" />
 | <img width="1919" height="907" alt="image" src="https://github.com/user-attachments/assets/84a13cf9-4656-43b0-a72b-c71268c13cfb" />
|
| <img width="1917" height="906" alt="image" src="https://github.com/user-attachments/assets/45d860ea-2e3c-4e1a-a538-2728d7baa89a" />
 | <img width="1919" height="910" alt="image" src="https://github.com/user-attachments/assets/97cf52ae-55ee-4814-8660-65ed45201a72" />
|

---

## ⚙️ Installation & Setup Guide

Follow these steps to run the project locally on your machine.

### Prerequisites

* Install **XAMPP** (or WAMP/MAMP).
* A web browser.

### Step 1: Clone the Repository

```bash
git clone https://github.com/utkarshgupta-05/gemFin.git

```

*Alternatively, download the ZIP file and extract it.*

### Step 2: Move Files

Move the `gemFin` folder into your local server directory:

* **XAMPP:** `C:\xampp\htdocs\`
* **WAMP:** `C:\wamp64\www\`

### Step 3: Database Setup

1. Open **XAMPP Control Panel** and start **Apache** and **MySQL**.
2. Go to `http://localhost/phpmyadmin` in your browser.
3. Create a new database named **`gemFin`**.
4. Click **Import** tab.
5. Choose the `gemFin.sql` file provided in this repository and click **Import**.

### Step 4: Configuration (Optional)

If your MySQL root password is not empty (default in XAMPP is empty), open `db_connect.php` and update the password:

```php
$username = "root";
$password = "YOUR_PASSWORD"; // Update this if needed

```

### Step 5: Run the App

Open your browser and visit:
`http://localhost/gemFin`

---

## 📂 Project Structure

```
gemFin/
├── css/
│   └── style.css       # Global styling
├── php/                # (Optional folder if you organized scripts)
├── db_connect.php      # Database connection configuration
├── index.php           # Login Page
├── register.php        # Registration Page
├── dashboard.php       # Main User Dashboard
├── budgets.php         # Budgeting Logic
├── goals.php           # Savings Goals Logic
├── reports.php         # Analytics & Charts
├── sidebar.php         # Navigation Component
├── logout.php          # Session destruction
└── gemFin.sql          # Database Import File

```

---

## 🛡️ Security Features

* **Password Hashing:** Uses PHP `password_hash()` for secure user authentication.
* **Prepared Statements:** All SQL queries use prepared statements to prevent SQL Injection attacks.
* **Session Management:** Protected pages redirect unauthenticated users to the login screen.

---

## 🤝 Contributing

Feel free to fork this repository and submit pull requests. For major changes, please open an issue first to discuss what you would like to change.

## 📄 License

This project is open-source and available for educational purposes.
