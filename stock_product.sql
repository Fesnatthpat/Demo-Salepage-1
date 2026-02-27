/*
 Navicat Premium Dump SQL

 Source Server         : salepage_demo
 Source Server Type    : MySQL
 Source Server Version : 100432 (10.4.32-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : salepage_demo

 Target Server Type    : MySQL
 Target Server Version : 100432 (10.4.32-MariaDB)
 File Encoding         : 65001

 Date: 27/02/2026 09:26:07
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for stock_product
-- ----------------------------
DROP TABLE IF EXISTS `stock_product`;
CREATE TABLE `stock_product`  (
  `stock_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `pd_sp_id` bigint UNSIGNED NOT NULL COMMENT 'รหัสอ้างอิงสินค้าจาก product_salepage',
  `quantity` int NOT NULL DEFAULT 0 COMMENT 'จำนวนสต๊อกสินค้า',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stock_id`) USING BTREE,
  INDEX `fk_product_stock`(`pd_sp_id` ASC) USING BTREE,
  CONSTRAINT `fk_product_stock` FOREIGN KEY (`pd_sp_id`) REFERENCES `product_salepage` (`pd_sp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
