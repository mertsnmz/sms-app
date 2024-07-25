# SMS App

This project is designed to send SMS messages during specific periods of the day, taking into account timezones. The project is developed using PHP and the Yii2 framework.

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- XAMPP (or similar tool to run PHP and MySQL)

## Installation

### 1. Clone the Repository
git clone https://github.com/mertsonmezz/sms-app.git
cd sms-app

### 2. Install Dependencies
composer install

### 3. Configure the Database
Open the config/db.php file and enter your database connection details

### 4. Run Migrations
php yii migrate

### 5. Populate the Database with Random Data
php yii mobile/populate-random-data

### 6. Get Messages to Send
php yii mobile/get-messages-to-send
# sms-app
