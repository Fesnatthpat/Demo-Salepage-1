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

 Date: 27/02/2026 09:23:55
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for product_options
-- ----------------------------
DROP TABLE IF EXISTS `product_options`;
CREATE TABLE `product_options`  (
  `option_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` bigint UNSIGNED NOT NULL,
  `option_price` decimal(10, 2) NULL DEFAULT 0.00,
  `option_price2` decimal(10, 2) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `option_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `option_stock` int NOT NULL DEFAULT 0,
  `option_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`option_id`) USING BTREE,
  INDEX `product_options_parent_id_foreign`(`parent_id` ASC) USING BTREE,
  CONSTRAINT `product_options_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `product_salepage` (`pd_sp_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 35 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
