# Restaurant Order System - Database Documentation

## Overview

In-restaurant ordering system where customers scan QR codes at tables to place orders. Each restaurant has one admin account to manage their dashboard.

## Database Tables

### 1. RESTAURANT

Stores restaurant information and admin login credentials.

| Column       | Type        | Description                   |
| ------------ | ----------- | ----------------------------- |
| id           | bigint (PK) | Primary key                   |
| name         | string(255) | Restaurant name               |
| username     | string(255) | Admin login username (unique) |
| password     | string(255) | Hashed password               |
| address      | text        | Restaurant address            |
| phone        | string(20)  | Contact number                |
| logo_url     | string(255) | Logo image path (nullable)    |
| opening_time | time        | Daily opening time            |
| closing_time | time        | Daily closing time            |
| is_active    | boolean     | Restaurant status             |
| timestamps   | -           | created_at, updated_at        |

### 2. MENU

Menu groups for organizing items (e.g., Breakfast, Lunch, Dinner).

| Column        | Type        | Description                        |
| ------------- | ----------- | ---------------------------------- |
| id            | bigint (PK) | Primary key                        |
| restaurant_id | bigint (FK) | References restaurants             |
| name          | string(255) | Menu name                          |
| type          | string(50)  | Menu type (breakfast/lunch/dinner) |
| is_active     | boolean     | Menu visibility                    |
| timestamps    | -           | created_at, updated_at             |

### 3. CATEGORY

Food categories within a menu (e.g., Appetizers, Main Course, Drinks).

| Column        | Type        | Description                     |
| ------------- | ----------- | ------------------------------- |
| id            | bigint (PK) | Primary key                     |
| menu_id       | bigint (FK) | References menus                |
| name          | string(255) | Category name                   |
| description   | text        | Category description (nullable) |
| display_order | integer     | Sort order                      |
| timestamps    | -           | created_at, updated_at          |

### 4. MENU_ITEM

Individual food items that customers can order.

| Column           | Type          | Description                 |
| ---------------- | ------------- | --------------------------- |
| id               | bigint (PK)   | Primary key                 |
| category_id      | bigint (FK)   | References categories       |
| name             | string(255)   | Item name                   |
| description      | text          | Item description (nullable) |
| price            | decimal(10,2) | Item price                  |
| image_url        | string(255)   | Item image (nullable)       |
| is_available     | boolean       | Availability status         |
| preparation_time | integer       | Time in minutes             |
| timestamps       | -             | created_at, updated_at      |

### 5. RESTAURANT_TABLE

Physical tables in the restaurant with QR codes.

| Column           | Type        | Description                       |
| ---------------- | ----------- | --------------------------------- |
| id               | bigint (PK) | Primary key                       |
| restaurant_id    | bigint (FK) | References restaurants            |
| table_number     | string(50)  | Table identifier                  |
| qr_code          | string(255) | QR code data (unique)             |
| seating_capacity | integer     | Number of seats                   |
| section          | string(100) | Table section/location (nullable) |
| status           | enum        | available/occupied/reserved       |
| timestamps       | -           | created_at, updated_at            |

### 6. ORDER

Customer orders placed from tables.

| Column               | Type          | Description                                        |
| -------------------- | ------------- | -------------------------------------------------- |
| id                   | bigint (PK)   | Primary key                                        |
| table_id             | bigint (FK)   | References restaurant_tables                       |
| order_number         | string(50)    | Unique order number                                |
| subtotal             | decimal(10,2) | Subtotal before tax                                |
| tax_amount           | decimal(10,2) | Tax amount                                         |
| total_amount         | decimal(10,2) | Final total                                        |
| status               | enum          | pending/preparing/ready/served/completed/cancelled |
| special_instructions | text          | General order notes (nullable)                     |
| timestamps           | -             | created_at, updated_at                             |

### 7. ORDER_ITEM

Individual items within an order.

| Column       | Type          | Description                              |
| ------------ | ------------- | ---------------------------------------- |
| id           | bigint (PK)   | Primary key                              |
| order_id     | bigint (FK)   | References orders                        |
| menu_item_id | bigint (FK)   | References menu_items                    |
| quantity     | integer       | Item quantity                            |
| unit_price   | decimal(10,2) | Price at time of order                   |
| notes        | text          | Item special requests (nullable)         |
| status       | enum          | pending/preparing/ready/served/cancelled |
| timestamps   | -             | created_at, updated_at                   |

### 8. ORDER_STATUS_HISTORY

Tracks all status changes for orders.

| Column     | Type        | Description                 |
| ---------- | ----------- | --------------------------- |
| id         | bigint (PK) | Primary key                 |
| order_id   | bigint (FK) | References orders           |
| status     | string(50)  | Status at this point        |
| notes      | text        | Additional notes (nullable) |
| changed_at | timestamp   | When status changed         |
| timestamps | -           | created_at, updated_at      |

### 9. PAYMENT

Payment records for orders.

| Column         | Type          | Description                       |
| -------------- | ------------- | --------------------------------- |
| id             | bigint (PK)   | Primary key                       |
| order_id       | bigint (FK)   | References orders                 |
| restaurant_id  | bigint (FK)   | References restaurants            |
| payment_method | enum          | cash/card/qr_code/other           |
| amount         | decimal(10,2) | Amount paid                       |
| transaction_id | string(255)   | Payment transaction ID (nullable) |
| status         | enum          | pending/completed/failed/refunded |
| timestamps     | -             | created_at, updated_at            |

### 10. KITCHEN_DISPLAY_QUEUE

Items sent to kitchen for preparation.

| Column       | Type        | Description                        |
| ------------ | ----------- | ---------------------------------- |
| id           | bigint (PK) | Primary key                        |
| order_id     | bigint (FK) | References orders                  |
| menu_item_id | bigint (FK) | References menu_items              |
| quantity     | integer     | Quantity to prepare                |
| status       | enum        | pending/preparing/completed        |
| priority     | integer     | Priority level (default: 0)        |
| completed_at | timestamp   | When item was completed (nullable) |
| timestamps   | -           | created_at, updated_at             |

## Relationships

- **restaurants** has many **menus**, **restaurant_tables**, **payments**
- **menus** has many **categories**
- **categories** has many **menu_items**
- **restaurant_tables** has many **orders**
- **orders** has many **order_items**, **order_status_histories**, **kitchen_display_queues**, one **payment**
- **order_items** belongs to **menu_items**

## Status Flow

1. Order: pending → preparing → ready → served → completed (or cancelled at any point)
2. Kitchen Queue: pending → preparing → completed
3. Payment: pending → completed (or failed/refunded)
