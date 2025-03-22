# thriveCart Test

This is a backend project implementing a simple **basket pricing API** for an online store.  
It supports adding products, calculating totals with discounts, and applying delivery charges.

## 📌 Features Implemented

### 1. Basket Management
- **Add a Product to Basket** (`POST /basket/add/{code}`)
  - Stores a product in the user's session-based basket.
  - Uses **Slim Framework** for routing.

- **Get Basket Total** (`GET /basket/total`)
  - Returns the total price, including:
    - **Subtotal (before discounts)**
    - **Applied Discounts**
    - **Delivery Cost**
    - **Final Total**

### 2. Pricing Rules
- **Delivery Cost Rules**
  - Orders **under $50** → `$4.95`
  - Orders **$50 - $89.99** → `$2.95`
  - Orders **$90 or more** → **Free delivery**

- **Discounts**
  - "Buy One Red Widget (R01), Get Second One Half Price."

### 3. Architecture
- **Slim Framework 4** – Lightweight and efficient routing.
- **Dependency Injection (`PHP-DI`)** – Decouples services.
- **Session-based Storage** – No database required.
- **Strategy Pattern** – Encapsulates pricing rules as strategies.

## 🚀 How to Run the Project

### 1. Prerequisites
- **docker**: Make sure Docker and Docker Compose are installed.
- **Python and Python Requests**: Not mandatory, only if you want to test the API with the provided Python script.

### 2. Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/Menelion/thrive-cart-test.git
   cd thrive-cart-test
   ```

2. Run docker containers:

   ```bash
   docker compose up --build -d
   ```

3. Install dependencies:

   ```bash
   docker compose run --rm composer install
   ```

The API must be available under http://localhost.

### 3. Running Tests

  ```bash
  docker compose exec php ./vendor/bin/phpunit
  ```

## Possible Improvements

### 1. Database

Currently the whole database infrastructure is in place, and if you run Docker Compose, you’ll get warnings about empty environment variables. Please copy `.env.sample` to `.env` and optionally set environment variables, thus MariaDB won't complain anymore. Although it is never used, it is good to use a DB for such tasks.

### 2. Validation

Currently there is almost no validation, except for the negative price in the value object. It would be nice to have a more robust solution.

### 3. Error Handling

Basic error handling is in place, but it would be better to have more granular exceptions.

### 4. Authentication

There is no authentication whatsoever, it would require user management.

### 5. Logging

A robust logger like Monolog would be a good improvement.

### 6. Swagger

Currently Swagger is set up and even the UI is available, but the routes are not properly documented.

### 7. SSL/TLS

Currently there is no ecnryption, everything runs under HTTP, not HTTPS.
