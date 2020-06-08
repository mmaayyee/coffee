/*
Navicat MySQL Data Transfer

Source Server         : localhost2
Source Server Version : 50540
Source Host           : 127.0.0.1:3306
Source Database       : cfframe

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2016-01-25 16:34:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for auth_assignment
-- ----------------------------
DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_assignment
-- ----------------------------
INSERT INTO `auth_assignment` VALUES ('超级管理员', '1', '1453710132');

-- ----------------------------
-- Table structure for auth_item
-- ----------------------------
DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE `auth_item` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_item
-- ----------------------------
INSERT INTO `auth_item` VALUES ('修改密码', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('删除管理员', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('删除角色', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('操作日志', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('查看管理员', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('查看系统设置', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('查看角色', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('添加管理员', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('添加角色', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('管理员管理', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('系统设置', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('系统设置列表', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('编辑管理员', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('编辑系统设置', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('编辑角色', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('角色管理', '2', null, null, null, '1453709548', '1453709548');
INSERT INTO `auth_item` VALUES ('超级管理员', '1', '超级管理员', null, null, '1453709843', '1453710083');
INSERT INTO `auth_item` VALUES ('首页管理', '2', null, null, null, '1453709548', '1453709548');

-- ----------------------------
-- Table structure for auth_item_child
-- ----------------------------
DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_item_child
-- ----------------------------
INSERT INTO `auth_item_child` VALUES ('超级管理员', '修改密码');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '删除管理员');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '删除角色');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '操作日志');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '查看管理员');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '查看系统设置');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '查看角色');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '添加管理员');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '添加角色');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '管理员管理');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '系统设置');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '系统设置列表');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '编辑管理员');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '编辑系统设置');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '编辑角色');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '角色管理');
INSERT INTO `auth_item_child` VALUES ('超级管理员', '首页管理');

-- ----------------------------
-- Table structure for auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_rule
-- ----------------------------

-- ----------------------------
-- Table structure for manager
-- ----------------------------
DROP TABLE IF EXISTS `manager`;
CREATE TABLE `manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '角色',
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `mobile` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '手机号',
  `branch` int(11) NOT NULL COMMENT '分公司',
  `realname` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '姓名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of manager
-- ----------------------------
INSERT INTO `manager` VALUES ('1', 'admin', 'Fz1HqYF28mDEk2xLyc4X3cadEM4UFxq_', '$2y$13$aJsVwo.LXTIE0fhh.z6pEeL793ZcUE1yJNRm4.rLaprK.INESbYxi', null, 'jiayi.song@coffee08.com', '超级管理员', '10', '1419381685', '1453710385', '18301129220', '1', '超级管理员');

-- ----------------------------
-- Table structure for manager_log
-- ----------------------------
DROP TABLE IF EXISTS `manager_log`;
CREATE TABLE `manager_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `manager_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `module_name` varchar(50) NOT NULL COMMENT '模块名称',
  `operate_type` tinyint(2) DEFAULT '0' COMMENT '操作类型（0添加，1编辑,2删除）',
  `operate_content` varchar(50) NOT NULL COMMENT '操作内容（如产品名称）',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`log_id`),
  KEY `manager_fk` (`manager_id`),
  CONSTRAINT `manager_fk` FOREIGN KEY (`manager_id`) REFERENCES `manager` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='管理员操作日志表';

-- ----------------------------
-- Records of manager_log
-- ----------------------------
INSERT INTO `manager_log` VALUES ('1', '1', '角色管理', '2', '设备管理员', '1453709399');
INSERT INTO `manager_log` VALUES ('2', '1', '角色管理', '2', '物流配送管理员', '1453709403');
INSERT INTO `manager_log` VALUES ('3', '1', '角色管理', '2', '数据管理员', '1453709407');
INSERT INTO `manager_log` VALUES ('4', '1', '角色管理', '2', '市场管理员', '1453709410');
INSERT INTO `manager_log` VALUES ('5', '1', '角色管理', '2', '客服管理员', '1453709413');
INSERT INTO `manager_log` VALUES ('6', '1', '角色管理', '2', '全功能管理员', '1453709416');
INSERT INTO `manager_log` VALUES ('7', '1', '角色管理', '1', '超级管理员', '1453709421');
INSERT INTO `manager_log` VALUES ('8', '1', '角色管理', '0', '超级管理员', '1453709844');
INSERT INTO `manager_log` VALUES ('9', '1', '角色管理', '1', '超级管理员', '1453710084');
INSERT INTO `manager_log` VALUES ('10', '1', '管理员管理', '1', '超级管理员', '1453710132');
INSERT INTO `manager_log` VALUES ('11', '1', '角色管理', '0', 'test', '1453710448');
INSERT INTO `manager_log` VALUES ('12', '1', '管理员管理', '0', '111111', '1453710480');
INSERT INTO `manager_log` VALUES ('13', '1', '管理员管理', '2', '111111', '1453710535');
INSERT INTO `manager_log` VALUES ('14', '1', '角色管理', '2', 'test', '1453710540');

-- ----------------------------
-- Table structure for migration
-- ----------------------------
DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of migration
-- ----------------------------
INSERT INTO `migration` VALUES ('m000000_000000_base', '1419381634');
INSERT INTO `migration` VALUES ('m130524_201442_init', '1419381638');

-- ----------------------------
-- Table structure for organization
-- ----------------------------
DROP TABLE IF EXISTS `organization`;
CREATE TABLE `organization` (
  `org_id` int(11) NOT NULL AUTO_INCREMENT,
  `org_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '机构名称',
  `parent_id` int(11) NOT NULL COMMENT '机构父ID',
  `parent_path` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '机构路径',
  `org_pass` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '机构设备密码',
  PRIMARY KEY (`org_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='机构表';

-- ----------------------------
-- Records of organization
-- ----------------------------
INSERT INTO `organization` VALUES ('1', '北京总部', '0', '-1-', '2222222');
INSERT INTO `organization` VALUES ('2', '北京分公司', '1', '-1-2-', '222222');
INSERT INTO `organization` VALUES ('3', '成都分公司', '1', '-1-3-', '');
INSERT INTO `organization` VALUES ('4', '广州分公司', '1', '-1-4-', '33333');

-- ----------------------------
-- Table structure for sysconfig
-- ----------------------------
DROP TABLE IF EXISTS `sysconfig`;
CREATE TABLE `sysconfig` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `config_key` varchar(50) NOT NULL DEFAULT '0' COMMENT '配置键',
  `config_value` varchar(255) NOT NULL DEFAULT '0' COMMENT '配置值',
  `config_desc` varchar(255) NOT NULL DEFAULT '0' COMMENT '配置描述',
  `config_edit` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否可编辑 1不可编辑',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统配置表';

-- ----------------------------
-- Records of sysconfig
-- ----------------------------

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '邮件',
  `role` smallint(6) NOT NULL DEFAULT '10',
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `openid` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '微信OPENID',
  `nickname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '昵称',
  `realname` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '姓名',
  `sex` tinyint(11) DEFAULT '1' COMMENT '姓别',
  `province` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '省份',
  `mobile` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '手机号',
  `is_master` tinyint(2) DEFAULT '0' COMMENT '是否包养主 1是',
  `belong` int(11) DEFAULT '0' COMMENT '所属包养主',
  `interest_balance` float(11,2) DEFAULT '0.00' COMMENT '红利余额',
  `points` int(11) DEFAULT '0' COMMENT '用户积分',
  `user_group_id` int(11) NOT NULL DEFAULT '0' COMMENT '红利套餐ID',
  `interest_start` int(11) DEFAULT '0' COMMENT '包养开始时间',
  `interest_total` float(11,2) DEFAULT '0.00' COMMENT '红利收入总额',
  `interest_draw` float(11,2) DEFAULT '0.00' COMMENT '红利提现总额',
  `head_avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '头像',
  `equipment_id` int(11) DEFAULT '0' COMMENT '设备主键',
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid_idx` (`openid`)
) ENGINE=InnoDB AUTO_INCREMENT=72506 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

