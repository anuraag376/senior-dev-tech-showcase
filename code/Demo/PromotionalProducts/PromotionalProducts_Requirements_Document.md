
# Requirements Document for Promotional Products Module

## Overview
This document outlines the detailed functional requirements and design specifications for the **PromotionalProducts** Magento 2 module. The module includes features for managing promotional products from the admin panel, displaying promotional products on the frontend, and integrating RabbitMQ and Elasticsearch. Unit test coverage is provided to ensure the reliability of key features. The document addresses each section of the module, including admin, frontend, RabbitMQ, and Elasticsearch integration, based on the provided code and functionality overview.

---

## Admin Panel Features

The **Promotional Products** module integrates into the Magento admin panel under the **Marketing** section with the following capabilities:

### 1. Menu and Grid Display
- **New Menu Item**: A new menu item labeled **Promotional Products** is added under the **Marketing** menu.
- **Grid Display**: This menu links to a grid that displays promotional products with the following columns:
  - **ID**
  - **SKU**
  - **Name**
  - **Special Price**
  - **Price**
  - **Promotion Status** (based on whether a product is actively on promotion).
- The grid supports **filters**, **pagination**, and **sorting** using Magento UI components.

### 2. Edit Promotional Products
- **Edit Functionality**: Each promotional product in the grid includes an "Edit" action button.
- **Fields for Editing**:
  - **Promotion Start Date**: Specifies when the promotion begins.
  - **Promotion End Date**: Specifies when the promotion ends.
  - **Discount Percentage**: Dynamically calculated, Represents the discount applied to the product.
- **Special Price Attribute**: Products are considered promotional if they have a special price and the current date falls within the promotion start and end dates.

### 3. Mass Actions
- **Mass Enable**: The admin can select multiple products to enable them.
- **Mass Disable**: The admin can select multiple products to disable them.

---

## Frontend Display Requirements

The module provides two ways to display promotional products on the frontend:

### 1. Promotional Products Page
- A **custom page** accessible at `/promotionalproducts` is created. This page displays all products eligible for promotion (products with a special price, and promotion dates that include the current date).
- **Product Listing**: The products are displayed in a grid or list view, showing the **product image**, **name**, and **final price**.

### 2. CMS Widget
- A **widget** with the ID `demo_promotionalproducts_promotionalproducts` is created, which can be embedded into any CMS page via the admin panel.
- The widget displays promotional products using the same criteria as the custom page, making it easy to integrate into other parts of the site.

---

## Unit Test Coverage Expectations

### 1. Core Functionality Tests
Unit tests are implemented to ensure the reliability of the core functionality:
- **Discount Calculation**: Tests validate the correctness of the discount percentage calculations by comparing the original price and special price.
- **Promotion Date Validation**: Tests ensure that a product's promotional status is correctly determined based on its start and end dates.
- **Product Promotion Eligibility**: Tests confirm that only products with valid promotion dates and special prices are considered promotional products.

### 2. Mocking Dependencies
- The tests mock services like `ProductRepositoryInterface`, `DateTime`, and `LoggerInterface` to isolate and verify the functionality of the module.

---

## RabbitMQ Integration Specifications

### 1. Producer
- When a product’s promotion details are edited in the admin panel, the module uses a **RabbitMQ producer** to send the product data to a RabbitMQ queue.
- The message payload includes:
  - **Product ID**
  - **SKU**
  - **Name**
  - **Promotion details** (start date, end date, discount percentage).
- The message is placed in the **promotion_update_consumer** queue.

### 2. Consumer
- A RabbitMQ **consumer** processes messages from the queue.
- When the command `php bin/magento queue:consumers:start promotion_update_consumer` is run, the consumer fetches the message from RabbitMQ and:
  1. Updates the product’s promotional data.
  2. Calls the method `updateProductInElasticsearch` to update the promotional status in Elasticsearch.

---

## Elasticsearch Integration Specifications

### 1. Indexing Promotional Products
- The module integrates with **Elasticsearch** (7.17.4) to store promotional product data for efficient searching and filtering.
- Products are indexed in Elasticsearch with an additional attribute **promotional_status**, which is dynamically calculated based on whether the product is on promotion.
- The custom plugin ensures that during reindexing (`php bin/magento indexer:reindex catalogsearch_fulltext`), the promotional status is updated based on the product's **special price** and the current date.

### 2. Searching Promotional Products
- The promotional products can be queried through Elasticsearch based on the `promotional_status` attribute.
- The integration ensures that only products with active promotions are returned when the promotional status is true.

---

## Summary of Key Features

### Admin Panel:
- **Menu**: Added under Marketing with a grid to manage promotional products.
- **Actions**: Supports mass enable/disable and individual product editing.
- **Attributes**: Uses the **special price** attribute for promotions.

### Frontend:
- **Page**: Custom `/promotionalproducts` page for displaying promotional products.
- **Widget**: Allows embedding of promotional products on CMS pages.

### Unit Testing:
- Tests validate the correct calculation of discount percentages, promotional status based on dates, and RabbitMQ message processing.

### RabbitMQ:
- **Producer**: Sends promotional updates to the RabbitMQ queue when admin changes occur.
- **Consumer**: Updates product data based on RabbitMQ messages.

### Elasticsearch:
- **Promotional Status**: Indexed in Elasticsearch for searching promotional products.
- **Custom Plugin**: Ensures accurate promotional status is indexed dynamically.

---

## Conclusion

This module provides a complete system for managing, displaying, and indexing promotional products in Magento 2. It includes features for both admin and frontend, with comprehensive RabbitMQ and Elasticsearch integrations.
