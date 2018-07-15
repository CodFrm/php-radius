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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pr_auth`
--

LOCK TABLES `pr_auth` WRITE;
/*!40000 ALTER TABLE `pr_auth` DISABLE KEYS */;
INSERT INTO `pr_auth` VALUES (1,'adminAuthController','后台admin管理权限');
/*!40000 ALTER TABLE `pr_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pr_group`
--

DROP TABLE IF EXISTS `pr_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pr_group` (
  `group_id` int(11) unsigned NOT NULL,
  `name` varchar(32) NOT NULL COMMENT '组名字',
  `auth_id` varchar(255) NOT NULL COMMENT '用户组拥有权限id',
  `description` varchar(255) NOT NULL COMMENT '用户组描述',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pr_group`
--

LOCK TABLES `pr_group` WRITE;
/*!40000 ALTER TABLE `pr_group` DISABLE KEYS */;
INSERT INTO `pr_group` VALUES (1,'管理员','1','后台管理员权限');
/*!40000 ALTER TABLE `pr_group` ENABLE KEYS */;
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
  `expire` bigint(20) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pr_user_group`
--

LOCK TABLES `pr_user_group` WRITE;
/*!40000 ALTER TABLE `pr_user_group` DISABLE KEYS */;
INSERT INTO `pr_user_group` VALUES (1,1,0,-1);
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
INSERT INTO `pr_user_tokens` VALUES ('7ljgjSiij4BpP0zW',1,1,1531623773);
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
  PRIMARY KEY (`uid`),
  UNIQUE KEY `user_UNIQUE` (`user`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pr_users`
--

LOCK TABLES `pr_users` WRITE;
/*!40000 ALTER TABLE `pr_users` DISABLE KEYS */;
INSERT INTO `pr_users` VALUES (1,'farmer','4c103a5fd57c98c18bb2b918e57a0fe568afd12d52ef25257e795bb291d44a4f','code.farmer@qq.com');
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

-- Dump completed on 2018-07-15 11:05:20
