# CigBurger Backoffice Project

## ❓ What is this?

⚠️ Warning: This repository contains only a third of the course project. For the complete project, please check out CigBurger Kitchen and CigBurger Request in my profile.

This is my version of the CigBurger backoffice project built in the course "[2024] CodeIgniter 4 - 3 large PROFESSIONAL projects united by APIs | MVC | PHP8 | MySQL | All about one structure!"

## 🛠️ Setup

To set up the project, follow these steps:

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/joaotfrodrigues/cigburger_backoffice.git
2. **Navigate to the Project Directory:**
    ```bash
    cd cigburger_backoffice
3. **Install Dependencies:**
    ```bash
    composer install
4. **Database Setup**
    Before running the application, ensure your database is configured correctly:
    1. **Create a Database**
        - Update the Constants.php files with your database settings.
    2. **Initialize Database Structure:**
        - Open your terminal in the project directory.
        - Run the command:
            ```bash
            php spark setup:database
            ```
            This command sets up the required tables and populates them with initial data.
5. **Configure Environment Variables:**
    - Update the .env file with configuration settings.
6. **Start the Development Server:**
    ```bash
    php spark serve
7. **Access the Application:**
    - Open your web browser and go to http://localhost/cigburger_backoffice/public/

## API Endpoints

📡 **API Status**: 
- **Endpoint**: `/api/get_status`
- **Description**: Checks if the API is activated or not.

🏪 **Restaurant Details**: 
- **Endpoint**: `/api/get_restaurant_details`
- **Description**: Retrieves information from the restaurant, including details about products and product categories. The response includes metadata such as version, datetime, timestamp, and project ID, along with data about the restaurant, its products categories, and individual products. This endpoint provides comprehensive information about the restaurant's offerings, allowing clients to retrieve details about available items for ordering or browsing.

By following these steps, you should have your CigBurger Backoffice project set up and ready to use. Make sure to check out the other parts of the project, CigBurger Request and CigBurger Kitchen, to complete the full course project.