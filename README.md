# CigBurger Backoffice Project

## ❓ What is this?

⚠️ Warning: This repository contains only a third of the course project. For the complete project, please check out CigBurger Kitchen and CigBurger Request in my profile.

This is my version of the CigBurger backoffice project built in the course "[2024] CodeIgniter 4 - 3 large PROFESSIONAL projects united by APIs | MVC | PHP8 | MySQL | All about one structure!"

## 🛠 Setup the Environment

**📦 Database:**
1. Create a database called "db_cig_burger".
2. Create a user called "user_db_cig_burger" with the password "ren7hICOqosAqu8Es7wagIjuD6KI4o".
3. Open the terminal in your local development environment and run the following command to set up the database structure and populate it:

```bash
php spark setup:database
```

Please ensure that you have PHP and CodeIgniter 4 installed and configured properly before running the setup command.

## API Endpoints

📡 **API Status**: 
- **Endpoint**: `/api/get_status`
- **Description**: Checks if the API is activated or not.

🏪 **Restaurant Details**: 
- **Endpoint**: `/api/get_restaurant_details`
- **Description**: Retrieves information from the restaurant, including details about products and product categories. The response includes metadata such as version, datetime, timestamp, and project ID, along with data about the restaurant, its products categories, and individual products. This endpoint provides comprehensive information about the restaurant's offerings, allowing clients to retrieve details about available items for ordering or browsing.

## 🌐 Accessing the Server

Once the environment is set up and the server is running, you can access the application by visiting [http://localhost/cigburger_backoffice/public/](http://localhost/cigburger_backoffice/public/)