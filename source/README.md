# Product API

Welcome to the Product API! This API provides a set of endpoints to manage products, including creating, updating,
deleting, and listing products. The API is built with Docker, Laravel, Elasticsearch, Redis, RabbitMQ for enhanced
message queuing, and Supervisor for process management, leveraging MySQL for data management and Elasticsearch for
advanced search capabilities.

## Table of Contents

- [Setup Instructions](#setup-instructions)
- [Endpoints](#endpoints)
    - [Create Mapping for Elasticsearch](#create-mapping-for-elasticsearch)
    - [List Products](#list-products)
    - [Create a New Product](#create-a-new-product)
    - [Get Product by ID](#get-product-by-id)
    - [Update Product by ID](#update-product-by-id)
    - [Delete Product by ID](#delete-product-by-id)
    - [Index All Products in Elasticsearch](#index-all-products-in-elasticsearch)

## Setup Instructions

To get started with the Product API, follow these steps:

1. Clone the repository:
    ```sh
    git clone <repository_url>
    cd <repository_directory>
    ```

2. Build and run the Docker containers:
    ```sh
    docker-compose up -d --build
    ```

3. Navigate to the `src` directory:
    ```sh
    cd src
    ```

4. Rename the `env.example` file to `.env`:
    ```sh
    mv env.example .env
    ```

5. Composer install:
    ```sh
    docker-compose exec php-fpm sh -c "cd /app/src && composer install"
    ```

6. Generate the application key:
    ```sh
    docker-compose exec php-fpm php /app/src/artisan key:generate
    ```

7. Run the database migrations:
    ```sh
    docker-compose exec php-fpm php /app/src/artisan migrate
    ```

8. Seed the database:
    ```sh
    docker-compose exec php-fpm php /app/src/artisan db:seed
    ```

9. Access the API documentation:
   Open your browser and go to [http://localhost:8080/api/documentation](http://localhost:8080/api/documentation) to
   view the Swagger documentation.

## Endpoints

### Create Mapping for Elasticsearch

- **URL**: `/api/create-mapping`
- **Method**: `GET`
- **Summary**: Create mapping for Elasticsearch.
- **Description**: This endpoint creates the necessary mappings for Elasticsearch.
- **Response**:
    - **200**: Mapping created successfully.
      ```json
      {
        "message": "Mapping created successfully"
      }
      ```

### List Products

- **URL**: `/api/products`
- **Method**: `GET`
- **Summary**: List products.
- **Description**: Retrieve a list of products with optional filtering and pagination.
- **Parameters**:
    - `query` (optional): Search query to filter products by name.
    - `product_ids` (optional): Comma-separated list of product IDs to filter by.
    - `category` (optional): Comma-separated list of category IDs to filter by.
    - `page` (optional): Page number for pagination (default: 1).
    - `limit` (optional): Number of items per page (default: 10).
    - `sort` (optional): Field name to sort results by.
    - `order` (optional): Sort order ('asc' or 'desc').
- **Response**:
    - **200**: List of products matching the criteria.

### Create a New Product

- **URL**: `/api/products`
- **Method**: `POST`
- **Summary**: Create a new product.
- **Description**: Create a new product.
- **Request Body**:
    - **Required**: Yes
    - **Content**:
      ```json
      {
        "name": "deneme",
        "category": [1, 2],
        "price": 199.99,
        "stock": 55
      }
      ```
- **Response**:
    - **201**: Product created successfully.
      ```json
      {
        "message": "Product created successfully",
        "product": {
            "id": 100,
            "name": "deneme",
            "sku": "SKU6673957e4b2f4",
            "price": 199.99,
            "created_at": "2024-06-25T02:35:42.000000Z",
            "updated_at": "2024-06-25T02:35:42.000000Z",
            "categories": [
                {
                "id": 5,
                "name": "voluptas",
                "created_at": "2024-06-24T12:50:46.000000Z",
                "updated_at": "2024-06-24T12:50:46.000000Z",
                "pivot": {
                    "product_id": 100,
                    "category_id": 5
                }
                }
            ]
        } 
      }
      ```

### Get Product by ID

- **URL**: `/api/products/{id}`
- **Method**: `GET`
- **Summary**: Get product by ID.
- **Description**: Get product by ID.
- **Parameters**:
    - `id` (required): ID of the product to return.
- **Response**:
    - **200**: Product found.
    - **404**: Product not found.
      ```json
      {
        "message": "Product not found"
      }
      ```

### Update Product by ID

- **URL**: `/api/products/{id}`
- **Method**: `PUT`
- **Summary**: Update product by ID.
- **Description**: Update product by ID.
- **Parameters**:
    - `id` (required): ID of the product to update.
- **Request Body**:
    - **Required**: Yes
    - **Content**:
      ```json
      {
        "name": "deneme",
        "category": [1, 2],
        "price": 199.99,
        "stock": 55
      }
      ```
- **Response**:
    - **200**: Product updated successfully.
      ```json
      {
        "message": "Product updated successfully",
        "product": {
            "id": 100,
            "name": "deneme",
            "sku": "SKU6673957e4b2f4",
            "price": 199.99,
            "created_at": "2024-06-25T02:35:42.000000Z",
            "updated_at": "2024-06-25T02:35:42.000000Z",
            "categories": [
                {
                "id": 5,
                "name": "voluptas",
                "created_at": "2024-06-24T12:50:46.000000Z",
                "updated_at": "2024-06-24T12:50:46.000000Z",
                "pivot": {
                    "product_id": 100,
                    "category_id": 5
                }
                }
            ]
        }
      }
      ```

### Delete Product by ID

- **URL**: `/api/products/{id}`
- **Method**: `DELETE`
- **Summary**: Delete product by ID.
- **Description**: Delete product by ID.
- **Parameters**:
    - `id` (required): ID of the product to delete.
- **Response**:
    - **200**: Product deleted successfully.
      ```json
      {
        "message": "Product deleted successfully"
      }
      ```

### Index All Products in Elasticsearch

- **URL**: `/api/products-index`
- **Method**: `POST`
- **Summary**: Index all products in Elasticsearch.
- **Description**: Index all products in Elasticsearch.
- **Response**:
    - **200**: Products indexed successfully.
      ```json
      {
        "message": "Products indexed successfully"
      }
      ```

Enjoy using the Product API! 🚀

