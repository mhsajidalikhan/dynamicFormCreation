/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : dynamicformsolution

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2023-10-17 22:45:29
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tbl_forms`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_forms`;
CREATE TABLE `tbl_forms` (
  `formID` int(11) NOT NULL AUTO_INCREMENT,
  `formName` varchar(100) NOT NULL,
  `formData` text NOT NULL,
  `formRecipientEmail` varchar(100) NOT NULL,
  PRIMARY KEY (`formID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tbl_forms
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_submitform`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_submitform`;
CREATE TABLE `tbl_submitform` (
  `submitFormID` int(11) NOT NULL AUTO_INCREMENT,
  `recipientEmail` varchar(100) NOT NULL,
  `submitFormData` text NOT NULL,
  `dynamicFormId` int(11) NOT NULL,
  PRIMARY KEY (`submitFormID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of tbl_submitform
-- ----------------------------
