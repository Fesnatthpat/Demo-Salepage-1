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

 Date: 27/02/2026 09:17:41
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for product_salepage
-- ----------------------------
DROP TABLE IF EXISTS `product_salepage`;
CREATE TABLE `product_salepage`  (
  `pd_sp_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `pd_sp_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pd_sp_SKU` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pd_sp_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pd_sp_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `pd_sp_price` decimal(10, 2) NOT NULL,
  `pd_sp_price2` decimal(10, 2) NULL DEFAULT NULL,
  `pd_sp_discount` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `pd_sp_stock` int NOT NULL DEFAULT 0,
  `pd_sp_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_recommended` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'สินค้าแนะนำ',
  `is_bogo_active` tinyint(1) NOT NULL DEFAULT 0,
  `pd_sp_display_location` enum('homepage','general') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`pd_sp_id`) USING BTREE,
  UNIQUE INDEX `product_salepage_pd_sp_code_unique`(`pd_sp_code` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
