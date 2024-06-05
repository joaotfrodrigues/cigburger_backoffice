# CigBurger Backoffice Project

## 📋 Table of Contents
- [❓ What is this?](#-what-is-this)
- [🚀 Prerequisites](#-prerequisites)
- [🛠️ Setup](#️-setup)
- [📡 API Endpoints](#-api-endpoints)

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
            ```php
            // MYSQL
            define('DB_HOST', 'localhost');
            define('DB_NAME', 'db_cig_burger');
            define('DB_USER', 'user_db_cig_burger');
            define('DB_PASS', 'ren7hICOqosAqu8Es7wagIjuD6KI4o');
            ```
    2. **Initialize Database Structure:**
        - Open your terminal in the project directory.
        - Run the command:
            ```bash
            php spark setup:database
            ```
            This command sets up the required tables and populates them with initial data.

5. **Configure Environment Variables:**
    - Edit the `.env` file in the project root. If it does not exist, create it.
    - Modify or add the following configuration setting:
        ```dotenv
        CI_ENVIRONMENT=development
        ```
      The `CI_ENVIRONMENT` variable can be set to either `development` or `production` depending on your needs.
    - Update the `APP_BASEURL` variable in the `.env` file to reflect the correct base URL for the application.

6. **Configure Email Settings:**
    - Edit the `Constants.php` file in the `app/Config` directory.
    - Set up the following email variables:
        ```php
        // SMTP
        define('MAIL_FROM',       'cigburger@cigburger.com');
        define('MAIL_FROM_NAME',  'CigBurger');

        define('MAIL_PROTOCOL',   'smtp');
        define('MAIL_HOST',       'localhost');
        define('MAIL_USER',       '');
        define('MAIL_PASS',       '');
        define('MAIL_PORT',       1025);
        define('MAIL_ENCRYPTION', '');
        ```
    - If you're developing locally and don't have an SMTP server configured, you can use software like MailCatcher to catch emails as if they were sent.

7. **Start the Development Server:**
    ```bash
    php spark serve
    ```

8. **Access the Application:**
    - Open your web browser and go to [http://localhost/cigburger_backoffice/public/](http://localhost/cigburger_backoffice/public/)

### Default Backoffice Login Credentials

**Restaurant:** `Restaurante 1`  
**Username:** `admin_rest1`  
**Password:** `Aa123456`

**Restaurant:** `Restaurante 2`  
**Username:** `admin_rest2`  
**Password:** `Aa123456`

## 📡 API Endpoints<a name="api-endpoints"></a>

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

### 🕒 Get Pending Orders
- **Endpoint**: `/api/get_pending_orders`
- **Method**: GET
- **Description**: Retrieves a list of pending orders for the restaurant.
- **Response Example**:
    ```json
    {
      "status": 200,
      "message": "success",
      "info": {
          "version": "1.0.0",
          "datetime": "2024-05-26 11:13:14",
          "timestamp": 1716721994,
          "project_id": "100"
      },
      "data": [
        {
          "id": "1",
          "id_restaurant": "1",
          "machine_id": "XDFFGFRT",
          "order_number": null,
          "order_date": "2024-05-24 23:33:38",
          "order_status": "paid",
          "total_price": "12.25",
          "created_at": "2024-05-24 23:33:38",
          "updated_at": null,
          "deleted_at": null
        },
        ...
      ]
    }
    ```

### 🛒 Request Checkout
- **Endpoint**: `/api/request_checkout`
- **Method**: POST
- **Description**: Handles the checkout process.
- **Request Payload**:
    ```json
    {
      "id_restaurant": "100",
      "machine_id": "XDFFGFRT",
      "order": {
          "items": {
              "1": {
                  "quantity": 2
              },
              "15": {
                  "quantity": 2
              }
          }
      }
    }
    ```
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
          "id_restaurant": "100",
          "machine_id": "XDFFGFRT",
          "order": {
              "items": {
                  "1": {
                      "quantity": 2
                  },
                  "15": {
                      "quantity": 2
                  }
              }
          }
      }
    }
    ```

### 🛒 Request Final Confirmation
- **Endpoint**: `/api/request_final_confirmation`
- **Method**: POST
- **Description**: Handles the final confirmation of an order request.
- **Request Payload**:
    ```json
    {
      "id_restaurant": "100",
      "machine_id": "XDFFGFRT",
      "total_price": 25.50,
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
      }
    }
    ```
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
          "id_order": 12345,
          "order_number": 43
      }
    }
    ```
### 🕵️‍♂️ Get Order Details
- **Endpoint**: `/api/get_order_details`
- **Method**: POST
- **Description**: Retrieves details of a specific order based on its ID.
- **Request Payload**:
    ```json
    {
      "id": "123"
    }
    ```
- **Response Example**:
    ```json
    {
      "status": 200,
      "message": "success",
      "info": {
          "version": "1.0.0",
          "datetime": "2024-05-26 17:01:43",
          "timestamp": 1716742903,
          "project_id": "100"
      },
      "data": {
        "id": "2",
        "id_restaurant": "1",
        "machine_id": "XDFFGFRT",
        "order_number": "2",
        "order_date": "2024-05-26 16:09:02",
        "order_status": "paid",
        "total_price": "28.50",
        "created_at": "2024-05-26 16:09:02",
        "updated_at": null,
        "deleted_at": null,
        "total_items": "5"
      }
    }
    ```

### 🗑️ Delete Order
- **Endpoint**: `/api/delete_order`
- **Method**: POST
- **Description**: Deletes a specific order based on its ID.
- **Request Payload**:
    ```json
    {
      "id": "123"
    }
    ```
- **Response Example**:
    ```json
    {
      "status": 200,
      "message": "success",
      "info": {
          "version": "1.0.0",
          "datetime": "2024-05-26 17:17:00",
          "timestamp": 1716743820,
          "project_id": "100"
      },
      "data": []
    }
    ```

### 📦 Get Order Details with Products
- **Endpoint**: `/api/get_order_details_with_products`
- **Method**: POST
- **Description**: Retrieves details of a specific order along with the associated products.
- **Request Payload**:
    ```json
    {
      "id": "123"
    }
    ```
- **Response Example**:
    ```json
    {
      "status": 200,
      "message": "success",
      "info": {
          "version": "1.0.0",
          "datetime": "2024-05-26 19:00:00",
          "timestamp": 1716750000,
          "project_id": "100"
      },
      "data": {
        "order_details": {
          "id": "2",
          "id_restaurant": "1",
          "machine_id": "XDFFGFRT",
          "order_number": "2",
          "order_date": "2024-05-26 16:09:02",
          "order_status": "paid",
          "total_price": "28.50",
          "created_at": "2024-05-26 16:09:02",
          "updated_at": null,
          "deleted_at": null,
          "total_items": "5"
        },
        "order_products": [
          {
            "id": "4",
            "id_order": "2",
            "id_product": "6",
            "price_per_unit": "13.00",
            "quantity": "1",
            "created_at": "2024-05-26 16:09:02",
            "updated_at": null,
            "deleted_at": null,
            "product_name": "Cig Royale",
            "product_category": "Hambúrgueres"
          },
          ...
        ]
      }
    }
    ```

### 🏁 Finish Order
- **Endpoint**: `/api/finish_order`
- **Method**: POST
- **Description**: Marks a specific order as finished based on its ID.
- **Request Payload**:
    ```json
    {
      "id": "123"
    }
    ```
- **Response Example**:
    ```json
    {
      "status": 200,
      "message": "success",
      "info": {
          "version": "1.0.0",
          "datetime": "2024-05-26 21:49:37",
          "timestamp": 1716760177,
          "project_id": "100"
      },
      "data": []
    }
    ```

By following these steps, you should have your CigBurger Backoffice project set up and ready to use. Make sure to check out the other parts of the project, CigBurger Request and CigBurger Kitchen, to complete the full course project.
