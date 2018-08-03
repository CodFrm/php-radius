-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: php-radius
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.33-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `pr_account`
--

DROP TABLE IF EXISTS `pr_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pr_account` (
  `account_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `server_id` int(10) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `beg_time` bigint(20) unsigned NOT NULL,
  `end_time` bigint(20) unsigned NOT NULL DEFAULT '0',
  `client_ip` varchar(45) DEFAULT NULL COMMENT '客户端信息',
  `input_octets` bigint(20) unsigned DEFAULT '0',
  `output_octets` bigint(20) unsigned DEFAULT '0',
  `session` varchar(45) NOT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='radius计费表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pr_account`
--

LOCK TABLES `pr_account` WRITE;
/*!40000 ALTER TABLE `pr_account` DISABLE KEYS */;
INSERT INTO `pr_account` VALUES (1,4,3,'PI3B','192.168.1.20',1532579977,0,NULL,0,0,'7B2D805D2D16B4683BCA0D60D030391A'),(2,4,3,'PI3B','192.168.1.20',1532579999,0,NULL,0,0,'5053908251C00D62250CC0D4ADBA9D09'),(3,4,3,'PI3B','192.168.1.20',1532580084,0,NULL,0,0,'E2D637987004E6203E1D27C48DD2BA8A'),(4,4,3,'PI3B','192.168.1.20',1532580172,0,NULL,0,0,'A187166E88D8034092014CE226093B62'),(5,4,3,'PI3B','192.168.1.20',1532580206,1532580212,'10.8.0.6',0,0,'9A1E10D7038851DD2C03994168138D2A'),(6,4,3,'PI3B','192.168.1.20',1532581247,123,'10.8.0.6',0,0,'6D85D902EB200BAB869C4060057E0065'),(7,4,3,'PI3B','192.168.1.20',1532581347,1234,'10.8.0.6',0,0,'BD42000E09AE5E5BDD27085A0ED47A70'),(8,4,3,'PI3B','192.168.1.20',1532581632,124124,'10.8.0.6',0,0,'5E4C987AEDA7DC858485669861EA668A'),(9,4,3,'PI3B','192.168.1.20',1532581784,1532581841,'10.8.0.6',0,0,'5C0B96EB394056D401A766738785E5BC'),(10,4,1,'test','192.168.1.10',1532581857,0,NULL,0,0,'4020E7B48452001E71600A1B8596DA0E'),(11,4,1,'test','192.168.1.10',1532581860,0,NULL,0,0,'4020E7B48452001E71600A1B8596DA0E'),(12,4,3,'PI3B','192.168.1.20',1532582096,1532582161,'10.8.0.6',7972,2295,'E6882C6BE676274D0A2B093908606C28');
/*!40000 ALTER TABLE `pr_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pr_auth`
--

DROP TABLE IF EXISTS `pr_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pr_auth` (
  `auth_id` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pr_auth`
--

LOCK TABLES `pr_auth` WRITE;
/*!40000 ALTER TABLE `pr_auth` DISABLE KEYS */;
INSERT INTO `pr_auth` VALUES (1,'adminAuthController','后台admin管理权限'),(2,'userAuthController','普通用户信息设置'),(3,'radiusAuth','radius验证通过权限');
/*!40000 ALTER TABLE `pr_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pr_group`
--

DROP TABLE IF EXISTS `pr_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pr_group` (
  `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '组名字',
  `auth_id` varchar(255) NOT NULL COMMENT '用户组拥有权限id',
  `description` varchar(255) NOT NULL COMMENT '用户组描述',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pr_group`
--

LOCK TABLES `pr_group` WRITE;
/*!40000 ALTER TABLE `pr_group` DISABLE KEYS */;
INSERT INTO `pr_group` VALUES (1,'管理员','1,2','管理员,拥有至高的权限'),(2,'普通用户','2','底层愚民'),(3,'VIP1','2,3','尊贵的vip用户，等级1');
/*!40000 ALTER TABLE `pr_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pr_server`
--

DROP TABLE IF EXISTS `pr_server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pr_server` (
  `server_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL COMMENT '服务器名字',
  `ip` varchar(45) NOT NULL COMMENT '服务器ip',
  `config` text NOT NULL COMMENT '连接配置',
  `secret` varchar(45) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '服务器状态 0 关闭 1 运行中',
  PRIMARY KEY (`server_id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pr_server`
--

LOCK TABLES `pr_server` WRITE;
/*!40000 ALTER TABLE `pr_server` DISABLE KEYS */;
INSERT INTO `pr_server` VALUES (1,'test','192.168.1.10','12312341231234123123412312341231234123123412312341231234123123412312341231234123123412312341231234123123412312341231234123123412312341231234123123412312341231234123123412312341231234','test123',0),(3,'PI3B','192.168.1.20','请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问请问','qwe123',0);
/*!40000 ALTER TABLE `pr_server` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pr_user_authority`
--

DROP TABLE IF EXISTS `pr_user_authority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pr_user_authority` (
  `uid` bigint(20) NOT NULL,
  `group_id` varchar(255) NOT NULL COMMENT '拥有的用户组id',
  `auth_id` varchar(255) NOT NULL COMMENT '拥有的权限id',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pr_user_authority`
--

LOCK TABLES `pr_user_authority` WRITE;
/*!40000 ALTER TABLE `pr_user_authority` DISABLE KEYS */;
/*!40000 ALTER TABLE `pr_user_authority` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pr_user_group`
--

DROP TABLE IF EXISTS `pr_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pr_user_group` (
  `uid` bigint(20) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  `expire` bigint(20) NOT NULL DEFAULT '-1',
  UNIQUE KEY `uid,group` (`uid`,`group_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pr_user_group`
--

LOCK TABLES `pr_user_group` WRITE;
/*!40000 ALTER TABLE `pr_user_group` DISABLE KEYS */;
INSERT INTO `pr_user_group` VALUES (1,1,1532071668,-1),(1,2,1532073940,1533024333),(4,3,1532243956,1534676912);
/*!40000 ALTER TABLE `pr_user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pr_user_tokens`
--

DROP TABLE IF EXISTS `pr_user_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pr_user_tokens` (
  `token` varchar(64) NOT NULL,
  `uid` bigint(20) unsigned NOT NULL,
  `type` tinyint(4) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pr_user_tokens`
--

LOCK TABLES `pr_user_tokens` WRITE;
/*!40000 ALTER TABLE `pr_user_tokens` DISABLE KEYS */;
INSERT INTO `pr_user_tokens` VALUES ('gvfqtau3EeewqLsr',4,1,1532238110),('hq0yhScmnkff2m0q',4,1,1532578329),('lKJzpwg1fZEGNWjv',4,1,1532085224),('Lp18RWcL2Wddrbyi',1,1,1533264169),('QCFD4xeB1I6ZLSYL',4,1,1532085075),('qE05tSfc6Wkn0ahd',1,1,1532574648),('TrfDpAtALgnJyK6K',1,1,1532485073),('u7BESXE4NAVrsgv3',4,1,1532337756),('XGn4kk820IB2Yj8G',1,1,1533264398),('zE7vOwvL2OLRUffe',4,1,1532339019),('ZmT5vPApVpgG8DVw',1,1,1531798165);
/*!40000 ALTER TABLE `pr_user_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pr_users`
--

DROP TABLE IF EXISTS `pr_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pr_users` (
  `uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(32) NOT NULL,
  `passwd` varchar(128) NOT NULL,
  `email` varchar(45) NOT NULL,
  `reg_ip` varchar(45) NOT NULL,
  `reg_time` bigint(20) unsigned NOT NULL,
  `last_login_time` bigint(20) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '用户状态,0 正常用户,1 用户禁封',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `user_UNIQUE` (`user`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `regip` (`reg_ip`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pr_users`
--

LOCK TABLES `pr_users` WRITE;
/*!40000 ALTER TABLE `pr_users` DISABLE KEYS */;
INSERT INTO `pr_users` VALUES (1,'farmer','4c103a5fd57c98c18bb2b918e57a0fe568afd12d52ef25257e795bb291d44a4f','code.farmer@qq.com','',1531792947,1533264170,0),(4,'codfrm','6e65b091ceca96a5de80041afdd1f5f580cfa3fb399b996ff13c6242432bee3b','958139621@qq.com','',1531792947,1532582096,0);
/*!40000 ALTER TABLE `pr_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-08-03 10:47:31
