# CigBurger Backoffice Project

## 📋 Table of Contents
- [❓ What is this?](#-what-is-this)
- [🚀 Prerequisites](#-prerequisites)
- [🛠️ Setup](#️-setup)
- [📡 API Endpoints](#api-endpoints)
- [🚦 API Status](#-api-status)
- [🏪 Restaurant Details](#-restaurant-details)
- [🛒 Request Checkout](#-request-checkout)

## ❓ What is this?

⚠️ Warning: This repository contains only a third of the course project. For the complete project, please check out CigBurger Kitchen and CigBurger Request in my profile.

This is my version of the CigBurger backoffice project built in the course "[2024] CodeIgniter 4 - 3 large PROFESSIONAL projects united by APIs | MVC | PHP8 | MySQL | All about one structure!"

## 🚀 Prerequisites
Before setting up the project, ensure you have the following installed:
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Composer
- Git

## 🛠️ Setup

To set up the project, follow these steps:

1. **Clone the Repository:**
    ```bash
    git clone https://github.com/joaotfrodrigues/cigburger_backoffice.git
    ```

2. **Navigate to the Project Directory:**
    ```bash
    cd cigburger_backoffice
    ```

3. **Install Dependencies:**
    ```bash
    composer install
    ```

4. **Database Setup**
    1. **Create a Database**
        - Update the `Constants.php` file with your database settings.
    2. **Initialize Database Structure:**
        - Open your terminal in the project directory.
        - Run the command:
            ```bash
            php spark setup:database
            ```
            This command sets up the required tables and populates them with initial data.

5. **Configure Environment Variables:**
    - Create a `.env` file in the project root.
    - Add the following configuration setting:
        ```dotenv
        CI_ENVIRONMENT=development
        ```

6. **Start the Development Server:**
    ```bash
    php spark serve
    ```

7. **Access the Application:**
    - Open your web browser and go to [http://localhost/cigburger_backoffice/public/](http://localhost/cigburger_backoffice/public/)

## 📡 API Endpoints

### 🚦 API Status
- **Endpoint**: `/api/get_status`
- **Method**: GET
- **Description**: Checks if the API is activated or not.
- **Response Example**:
    ```json
    {
      "status": 200,
      "message": "success",
      "info": {
          "version": "1.0.0",
          "datetime": "2024-05-21 21:07:46",
          "timestamp": 1716325666,
          "project_id": "100"
      },
      "data": []
    }
    ```

### 🏪 Restaurant Details
- **Endpoint**: `/api/get_restaurant_details`
- **Method**: GET
- **Description**: Retrieves information from the restaurant, including details about products and product categories.
- **Response Example**:
    ```json
    {
      "status": 200,
      "message": "success",
      "info": {
          "version": "1.0.0",
          "datetime": "2024-05-21 21:08:51",
          "timestamp": 1716325731,
          "project_id": "100"
      },
      "data": {
        "restaurant_details": {
          "id": "1",
          "name": "Restaurante 1",
          "address": "Rua do Restaurante 1",
          "phone": "990000100",
          "email": "restaurante_1@gmail.com",
          "project_id": "100",
          "api_key": "$2y$10$at6WTCo2IdSSQebn.q1KM.tHMvsdEEePFWlz69d2U4igMSICo5P.S",
          "created_at": "2024-04-21 15:42:46",
          "updated_at": null,
          "deleted_at": null
        },
        "products_categories": [
          {
              "category": "Hambúrgueres"
          },
          ...
        ],
        "products": [
          {
            "id": "1",
            "id_restaurant": "1",
            "name": "Cig Hamburger",
            "description": "O melhor hambúrguer pelo melhor preço.",
            "category": "Hambúrgueres",
            "price": "7.50",
            "availability": "1",
            "promotion": "10.00",
            "stock": "1100",
            "stock_min_limit": "100",
            "image": "rest_00001_burger_01.png",
            "created_at": "2024-04-27 23:54:22",
            "updated_at": "2024-05-12 19:44:32",
            "deleted_at": null
          },
          ...
        ]
      }
    }
    ```

### 🛒 Request Checkout
- **Endpoint**: `/api/request_checkout`
- **Method**: POST
- **Description**: Handles the checkout process.
- **Response Example**:
    ```json
    {
      "status": 200,
      "message": "success",
      "info": {
          "version": "1.0.0",
          "datetime": "2024-05-21 21:15:12",
          "timestamp": 1716326112,
          "project_id": 100
      },
      "data": {
          "restaurant_id": 100,
          "order": {
              "items": {
                  "1": {
                      "quantity": 2,
                      "price": 6.75
                  },
                  "15": {
                      "quantity": 2,
                      "price": 3.5
                  }
              },
              "status": "paid"
          },
          "machine_id": "XDFFGFRT"
      }
    }
    ```

By following these steps, you should have your CigBurger Backoffice project set up and ready to use. Make sure to check out the other parts of the project, CigBurger Request and CigBurger Kitchen, to complete the full course project.
