/*
Navicat MySQL Data Transfer

Source Server         : Eric
Source Server Version : 50553
Source Host           : 127.0.0.1:3306
Source Database       : sub_table

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-01-16 11:28:17
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for data2018-10
-- ----------------------------
DROP TABLE IF EXISTS `data2018-10`;
CREATE TABLE `data2018-10` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `visit_time` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of data2018-10
-- ----------------------------
INSERT INTO `data2018-10` VALUES ('1', 'zhangsan', '2018-10-03');
INSERT INTO `data2018-10` VALUES ('2', 'zhangsan', '2018-10-03');

-- ----------------------------
-- Table structure for data2018-11
-- ----------------------------
DROP TABLE IF EXISTS `data2018-11`;
CREATE TABLE `data2018-11` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `visit_time` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of data2018-11
-- ----------------------------
INSERT INTO `data2018-11` VALUES ('1', 'zhangsan', '2018-11-03');
INSERT INTO `data2018-11` VALUES ('2', 'zhangsan', '2018-11-03');
INSERT INTO `data2018-11` VALUES ('3', 'zhangsan', '2018-11-03');
INSERT INTO `data2018-11` VALUES ('4', 'zhangsan', '2018-11-03');
INSERT INTO `data2018-11` VALUES ('5', 'zhangsan', '2018-11-03');
INSERT INTO `data2018-11` VALUES ('6', 'zhangsan', '2018-11-03');
INSERT INTO `data2018-11` VALUES ('7', 'zhangsan', '2018-11-03');
INSERT INTO `data2018-11` VALUES ('8', 'zhangsan', '2018-11-03');
INSERT INTO `data2018-11` VALUES ('9', 'zhangsan', '2018-11-03');

-- ----------------------------
-- Table structure for data2018-12
-- ----------------------------
DROP TABLE IF EXISTS `data2018-12`;
CREATE TABLE `data2018-12` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `visit_time` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of data2018-12
-- ----------------------------
INSERT INTO `data2018-12` VALUES ('1', 'zhangsan', '2018-12-03');
INSERT INTO `data2018-12` VALUES ('2', 'zhangsan', '2018-12-03');
INSERT INTO `data2018-12` VALUES ('3', 'zhangsan', '2018-12-03');
INSERT INTO `data2018-12` VALUES ('4', 'zhangsan', '2018-12-03');
INSERT INTO `data2018-12` VALUES ('5', 'zhangsan', '2018-12-03');

-- ----------------------------
-- Table structure for data2019-01
-- ----------------------------
DROP TABLE IF EXISTS `data2019-01`;
CREATE TABLE `data2019-01` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `visit_time` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of data2019-01
-- ----------------------------
INSERT INTO `data2019-01` VALUES ('1', 'zhangsan', '2019-01-03');
INSERT INTO `data2019-01` VALUES ('2', 'zhangsan', '2019-01-16');
