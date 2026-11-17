
-- ============================================================
-- AFROCARE E-COMMERCE DATABASE
-- ShopPN foundation + normalized hair recommendations
-- Clean slate: drops existing tables and data
-- ============================================================

CREATE DATABASE IF NOT EXISTS `CrownCare_shoppn`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `CrownCare_shoppn`;


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

START TRANSACTION;

-- ============================================================
-- 1. DROP TABLES IN FOREIGN-KEY DEPENDENCY ORDER
-- ============================================================

DROP TABLE IF EXISTS `payment`;
DROP TABLE IF EXISTS `orderdetails`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `cart`;

DROP TABLE IF EXISTS `product_hair_attributes`;
DROP TABLE IF EXISTS `hair_profile_attributes`;
DROP TABLE IF EXISTS `hair_profiles`;
DROP TABLE IF EXISTS `hair_attributes`;

DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `brands`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `customer`;


-- ============================================================
-- 2. BRANDS
-- ============================================================

CREATE TABLE `brands` (
    `brand_id` INT NOT NULL AUTO_INCREMENT,
    `brand_name` VARCHAR(100) NOT NULL,

    PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 3. CATEGORIES
-- ============================================================

CREATE TABLE `categories` (
    `cat_id` INT NOT NULL AUTO_INCREMENT,
    `cat_name` VARCHAR(100) NOT NULL,

    PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 4. CUSTOMERS
-- ============================================================

CREATE TABLE `customer` (
    `customer_id` INT NOT NULL AUTO_INCREMENT,
    `customer_name` VARCHAR(100) NOT NULL,
    `customer_email` VARCHAR(50) NOT NULL,
    `customer_pass` VARCHAR(150) NOT NULL,
    `customer_country` VARCHAR(30) NOT NULL,
    `customer_city` VARCHAR(30) NOT NULL,
    `customer_contact` VARCHAR(15) NOT NULL,
    `customer_image` VARCHAR(100) DEFAULT NULL,
    `user_role` INT NOT NULL DEFAULT 2,

    PRIMARY KEY (`customer_id`),
    UNIQUE KEY `unique_customer_email` (`customer_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 5. PRODUCTS
-- ============================================================

CREATE TABLE `products` (
    `product_id` INT NOT NULL AUTO_INCREMENT,
    `product_cat` INT NOT NULL,
    `product_brand` INT NOT NULL,
    `product_title` VARCHAR(200) NOT NULL,
    `product_price` DOUBLE NOT NULL,
    `product_desc` VARCHAR(500) DEFAULT NULL,
    `product_image` VARCHAR(100) DEFAULT NULL,
    `product_keywords` VARCHAR(100) DEFAULT NULL,

    PRIMARY KEY (`product_id`),
    KEY `idx_product_cat` (`product_cat`),
    KEY `idx_product_brand` (`product_brand`),

    CONSTRAINT `fk_product_category`
        FOREIGN KEY (`product_cat`)
        REFERENCES `categories` (`cat_id`),

    CONSTRAINT `fk_product_brand`
        FOREIGN KEY (`product_brand`)
        REFERENCES `brands` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 6. HAIR ATTRIBUTES
-- One shared list of available hair attributes.
-- Types: texture, porosity, concern, goal, preference.
-- ============================================================

CREATE TABLE `hair_attributes` (
    `attribute_id` INT NOT NULL AUTO_INCREMENT,
    `attribute_name` VARCHAR(100) NOT NULL,
    `attribute_type` ENUM(
        'texture',
        'porosity',
        'concern',
        'goal',
        'preference'
    ) NOT NULL,

    PRIMARY KEY (`attribute_id`),

    UNIQUE KEY `unique_hair_attribute`
        (`attribute_name`, `attribute_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 7. HAIR PROFILES
-- One saved profile per customer.
-- ============================================================

CREATE TABLE `hair_profiles` (
    `profile_id` INT NOT NULL AUTO_INCREMENT,
    `customer_id` INT NOT NULL,
    `updated_at` TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`profile_id`),
    UNIQUE KEY `unique_customer_profile` (`customer_id`),

    CONSTRAINT `fk_hair_profile_customer`
        FOREIGN KEY (`customer_id`)
        REFERENCES `customer` (`customer_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 8. HAIR PROFILE ATTRIBUTES
-- Connects a saved profile to multiple hair attributes.
-- ============================================================

CREATE TABLE `hair_profile_attributes` (
    `profile_id` INT NOT NULL,
    `attribute_id` INT NOT NULL,

    PRIMARY KEY (`profile_id`, `attribute_id`),

    CONSTRAINT `fk_hpa_profile`
        FOREIGN KEY (`profile_id`)
        REFERENCES `hair_profiles` (`profile_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT `fk_hpa_attribute`
        FOREIGN KEY (`attribute_id`)
        REFERENCES `hair_attributes` (`attribute_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 9. PRODUCT HAIR ATTRIBUTES
-- Connects products to the textures, concerns and goals
-- they support.
-- ============================================================

CREATE TABLE `product_hair_attributes` (
    `product_id` INT NOT NULL,
    `attribute_id` INT NOT NULL,

    PRIMARY KEY (`product_id`, `attribute_id`),

    CONSTRAINT `fk_pha_product`
        FOREIGN KEY (`product_id`)
        REFERENCES `products` (`product_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT `fk_pha_attribute`
        FOREIGN KEY (`attribute_id`)
        REFERENCES `hair_attributes` (`attribute_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 10. CART
-- Each customer has a separate cart.
-- One row per customer/product combination.
-- No IP-based shared cart.
-- ============================================================

CREATE TABLE `cart` (
    `c_id` INT NOT NULL,
    `p_id` INT NOT NULL,
    `qty` INT NOT NULL DEFAULT 1,

    PRIMARY KEY (`c_id`, `p_id`),
    KEY `idx_cart_product` (`p_id`),

    CONSTRAINT `fk_cart_customer`
        FOREIGN KEY (`c_id`)
        REFERENCES `customer` (`customer_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT `fk_cart_product`
        FOREIGN KEY (`p_id`)
        REFERENCES `products` (`product_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 11. ORDERS
-- ============================================================

CREATE TABLE `orders` (
    `order_id` INT NOT NULL AUTO_INCREMENT,
    `customer_id` INT NOT NULL,
    `invoice_no` INT NOT NULL,
    `order_date` DATE NOT NULL,
    `order_status` VARCHAR(100) NOT NULL DEFAULT 'paid',

    PRIMARY KEY (`order_id`),
    KEY `idx_orders_customer` (`customer_id`),

    CONSTRAINT `fk_orders_customer`
        FOREIGN KEY (`customer_id`)
        REFERENCES `customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 12. ORDER DETAILS
-- One product entry per order.
-- ============================================================

CREATE TABLE `orderdetails` (
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `qty` INT NOT NULL,

    PRIMARY KEY (`order_id`, `product_id`),
    KEY `idx_orderdetails_product` (`product_id`),

    CONSTRAINT `fk_orderdetails_order`
        FOREIGN KEY (`order_id`)
        REFERENCES `orders` (`order_id`),

    CONSTRAINT `fk_orderdetails_product`
        FOREIGN KEY (`product_id`)
        REFERENCES `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 13. PAYMENTS
-- ============================================================

CREATE TABLE `payment` (
    `pay_id` INT NOT NULL AUTO_INCREMENT,
    `amt` DOUBLE NOT NULL,
    `customer_id` INT NOT NULL,
    `order_id` INT NOT NULL,
    `currency` VARCHAR(3) NOT NULL,
    `payment_date` DATE NOT NULL,

    PRIMARY KEY (`pay_id`),
    KEY `idx_payment_customer` (`customer_id`),
    KEY `idx_payment_order` (`order_id`),

    CONSTRAINT `fk_payment_customer`
        FOREIGN KEY (`customer_id`)
        REFERENCES `customer` (`customer_id`),

    CONSTRAINT `fk_payment_order`
        FOREIGN KEY (`order_id`)
        REFERENCES `orders` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


COMMIT;

-- ============================================================
-- DATABASE CREATED
-- Add your admin account, brands, categories, products,
-- hair attribute options, and product attribute mappings.
-- ============================================================
