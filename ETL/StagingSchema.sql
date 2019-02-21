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

-- TABLES --


--
-- Table structure for table `pentaho_log``
--

DROP TABLE IF EXISTS `pentaho_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pentaho_log` (
  `ID_JOB` int(11) DEFAULT NULL,
  `ID_BATCH` int(11) DEFAULT NULL,
  `CHANNEL_ID` varchar(255) DEFAULT NULL,
  `JOBNAME` varchar(255) DEFAULT NULL,
  `TRANSNAME` varchar(255) DEFAULT NULL,
  `STATUS` varchar(15) DEFAULT NULL,
  `ERRORS` bigint(20) DEFAULT NULL,
  `STARTDATE` datetime DEFAULT NULL,
  `ENDDATE` datetime DEFAULT NULL,
  `LOGDATE` datetime DEFAULT NULL,
  `DEPDATE` datetime DEFAULT NULL,
  `REPLAYDATE` datetime DEFAULT NULL,
  `LOG_FIELD` mediumtext,
  `EXECUTING_SERVER` varchar(255) DEFAULT NULL,
  `EXECUTING_USER` varchar(255) DEFAULT NULL,
  KEY `IDX_etl_errors_1` (`ID_BATCH`),
  KEY `IDX_etl_errors_2` (`ERRORS`,`STATUS`,`TRANSNAME`),
  KEY `IDX_pentaho_log_1` (`ID_JOB`),
  KEY `IDX_pentaho_log_2` (`ERRORS`,`STATUS`,`JOBNAME`),
  KEY `pnth_lg_chnnl_idx` (`CHANNEL_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `pentaho_details_log``
--

DROP TABLE IF EXISTS `pentaho_details_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE pentaho_details_log
( ID_BATCH INT
, CHANNEL_ID VARCHAR(255)
, LOG_DATE DATETIME
, LOGGING_OBJECT_TYPE VARCHAR(255)
, OBJECT_NAME VARCHAR(255)
, OBJECT_COPY VARCHAR(255)
, REPOSITORY_DIRECTORY VARCHAR(255)
, FILENAME VARCHAR(255)
, OBJECT_ID VARCHAR(255)
, OBJECT_REVISION VARCHAR(255)
, PARENT_CHANNEL_ID VARCHAR(255)
, ROOT_CHANNEL_ID VARCHAR(255)
, KEY `pnth_dtls_lg_chnnl_idx` (`CHANNEL_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `transformation_errors`
--

DROP TABLE IF EXISTS `transformation_errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transformation_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trans_name` varchar(255) DEFAULT NULL,
  `trans_batch_id` int(11) DEFAULT NULL, 
  `job_name` varchar(255) DEFAULT NULL,
  `log_date` datetime DEFAULT NULL,
  `error` varchar(1024) DEFAULT NULL,
  `error_field` varchar(255) DEFAULT NULL,
  `error_stack_trace` MEDIUMTEXT DEFAULT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iisda_batch_ids``
--

DROP TABLE IF EXISTS `iisda_batch_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iisda_batch_ids` (
  `iisda_batch_id` int(11) NOT NULL,
  `administration_name` varchar(1024) NOT NULL,
  `administration_UIC` varchar(255) DEFAULT NULL,
  `administration_status` varchar(255) NOT NULL,
   PRIMARY KEY (`iisda_batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `administration`
--

DROP TABLE IF EXISTS `administration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `administration` ( 
  `iisda_id` int(11) NOT NULL,
  `iisda_batch_id` int(11) NOT NULL,
  `batch_type` varchar(255) NOT NULL,
  `name` varchar(1024) NOT NULL,
  `UIC` varchar(255) DEFAULT NULL,
  `adm_struct_kind` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `district_ekatte_code` varchar(255) DEFAULT NULL,
  `district_name` varchar(255) DEFAULT NULL,
  `municipality_ekatte_code` varchar(255) DEFAULT NULL,
  `municipality_name` varchar(255) DEFAULT NULL,
  `settlement_ekatte_code` varchar(255) DEFAULT NULL,
  `settlement_name` varchar(255) DEFAULT NULL,
  `area_ekatte_code` varchar(255) DEFAULT NULL,
  `area_name` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`iisda_id`),
  KEY `admnstrtn_iisda_btch_ids_fk_idx` (`iisda_batch_id`),
  CONSTRAINT `admnstrtn_iisda_btch_ids_fk` FOREIGN KEY (`iisda_batch_id`) REFERENCES `iisda_batch_ids` (`iisda_batch_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `administration_units`
--

DROP TABLE IF EXISTS `administration_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `administration_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_pos_id` int(11) NOT NULL,
  `name` varchar(4096) NOT NULL,
  `kind` varchar(255) NOT NULL,
  `adm_type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `adm_iisda_id` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `district_ekatte_code` varchar(255) DEFAULT NULL,
  `district_name` varchar(255) DEFAULT NULL,
  `municipality_ekatte_code` varchar(255) DEFAULT NULL,
  `municipality_name` varchar(255) DEFAULT NULL,
  `settlement_ekatte_code` varchar(255) DEFAULT NULL,
  `settlement_name` varchar(255) DEFAULT NULL,
  `area_ekatte_code` varchar(255) DEFAULT NULL,
  `area_name` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admnstrtn_unts_idx` (`unit_pos_id`),
  KEY `admnstrtn_unts_admnstrtn_fk_idx` (`adm_iisda_id`),
  KEY `admnstrtn_unts_dstrcts_cd_idx` (`district_ekatte_code`),
  KEY `admnstrtn_unts_mncplt_cd_idx` (`municipality_ekatte_code`),
  KEY `admnstrtn_unts_cts_cd_idx` (`settlement_ekatte_code`), 
  KEY `admnstrtn_unts_rgns_cd_idx` (`area_ekatte_code`), 
  KEY `admnstrtn_unts_orgnztn_unt_tp_cd_idx` (`adm_type`),
  CONSTRAINT `admnstrtn_unts_admnstrtn_fk` FOREIGN KEY (`adm_iisda_id`) REFERENCES `administration` (`iisda_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `administration_units_remote_workplace`
--

DROP TABLE IF EXISTS `administration_units_remote_workplace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `administration_units_remote_workplace` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remote_workplace_id` int(11) NOT NULL,
  `name` varchar(1024) NOT NULL,
  `unit_pos_id` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `district_ekatte_code` varchar(255) DEFAULT NULL,
  `district_name` varchar(255) DEFAULT NULL,
  `municipality_ekatte_code` varchar(255) DEFAULT NULL,
  `municipality_name` varchar(255) DEFAULT NULL,
  `settlement_ekatte_code` varchar(255) DEFAULT NULL,
  `settlement_name` varchar(255) DEFAULT NULL,
  `area_ekatte_code` varchar(255) DEFAULT NULL,
  `area_name` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admnstrtn_unts_rmt_wrkplc_admnstrtn_unts_fk_idx` (`unit_pos_id`),
  CONSTRAINT `admnstrtn_unts_rmt_wrkplc_admnstrtn_unts_fk` FOREIGN KEY (`unit_pos_id`) REFERENCES `administration_units` (`unit_pos_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `administration_town_halls`
--
DROP TABLE IF EXISTS `administration_town_halls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `administration_town_halls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `town_hall_id` int(11) NOT NULL,
  `name` varchar(1024) NOT NULL,
  `adm_iisda_id` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `district_ekatte_code` varchar(255) DEFAULT NULL,
  `district_name` varchar(255) DEFAULT NULL,
  `municipality_ekatte_code` varchar(255) DEFAULT NULL,
  `municipality_name` varchar(255) DEFAULT NULL,
  `settlement_ekatte_code` varchar(255) DEFAULT NULL,
  `settlement_name` varchar(255) DEFAULT NULL,
  `area_ekatte_code` varchar(255) DEFAULT NULL,
  `area_name` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admnstrtn_twn_hlls_admnstrtn_fk_idx` (`adm_iisda_id`),
  CONSTRAINT `admnstrtn_twn_hlls_admnstrtn_fk` FOREIGN KEY (`adm_iisda_id`) REFERENCES `administration` (`iisda_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `administration_services`
--
DROP TABLE IF EXISTS `administration_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `administration_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adm_service_id` int(11) NOT NULL,
  `name` varchar(1024) NOT NULL,
  `adm_iisda_id` int(11) NOT NULL,
  `section_name` varchar(255) DEFAULT NULL,
   PRIMARY KEY (`id`),
  KEY `admnstrtn_srvcs_admnstrtn_fk_idx` (`adm_iisda_id`),
  CONSTRAINT `admnstrtn_srvcs_admnstrtn_fk` FOREIGN KEY (`adm_iisda_id`) REFERENCES `administration` (`iisda_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `apache_log`
--
DROP TABLE IF EXISTS `apache_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apache_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_datetime` datetime,
  `http_method` varchar(32) DEFAULT NULL,
  `itop_object` MEDIUMTEXT DEFAULT NULL,
  `client_ip` varchar(32) DEFAULT NULL,
  `client_browser` varchar(1024) DEFAULT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `pentaho_user`@`%` 
    SQL SECURITY DEFINER
VIEW `pentaho_log_vw` AS
    SELECT 
        `pl`.`ID_JOB` AS `ID_JOB`,
        IF(ISNULL(`pl`.`ID_BATCH`),
            `pl`.`ID_BATCH`,
            `pl1`.`ID_JOB`) AS `ID_BATCH`,
        IF(ISNULL(`pl`.`JOBNAME`),
            `pl1`.`JOBNAME`,
            `pl`.`JOBNAME`) AS `JOBNAME`,
        `pl`.`TRANSNAME` AS `TRANSNAME`,
        `pl`.`STATUS` AS `STATUS`,
        `pl`.`ERRORS` AS `ERRORS`,
        `pl`.`LOGDATE` AS `LOGDATE`,
        `pl`.`LOG_FIELD` AS `LOG_FIELD`,
        IF((CHAR_LENGTH(SUBSTR(`pl`.`LOG_FIELD`,
                        LOCATE('ERROR (version 7.1.0.0-12,',
                                `pl`.`LOG_FIELD`),
                        128)) = 0),
            'OK',
            SUBSTR(`pl`.`LOG_FIELD`,
                LOCATE('ERROR (version 7.1.0.0-12,',
                        `pl`.`LOG_FIELD`),
                128)) AS `LOG_FIELD_EXCERPT`
    FROM
        ((`pentaho_log` `pl`
        JOIN `pentaho_details_log` `pdl` ON ((`pl`.`CHANNEL_ID` = `pdl`.`CHANNEL_ID`)))
        JOIN `pentaho_log` `pl1` ON ((`pdl`.`ROOT_CHANNEL_ID` = `pl1`.`CHANNEL_ID`)))
    ORDER BY `pl1`.`ID_JOB` , `pl`.`ID_BATCH` , `pl`.`LOGDATE` DESC;
	
	
--
-- Table structure for table `justice_init``
--

DROP TABLE IF EXISTS `justice_init`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_init` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emf_id` varchar(64) NOT NULL,
  `object_title` varchar(1024) NOT NULL,
  `object_type` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `justice_organization``
--

DROP TABLE IF EXISTS `justice_organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_objects_id` int(11) NOT NULL,
  `justice_objects_parent_id` int(11) NOT NULL,
  `title` varchar(1024) NOT NULL,
  `administration_type` varchar(255) NOT NULL,
  `administration_subtype` varchar(255) DEFAULT NULL,
  `address` varchar(1024) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `webSite` varchar(255) DEFAULT NULL,
  `company_number` varchar(255) DEFAULT NULL,
  `status` varchar(32) NOT NULL,
  `modified_on` datetime NOT NULL,
   PRIMARY KEY (`id`),
   KEY `jstc_orgnztn_jstc_objcts_fk_idx` (`justice_objects_id`),
   CONSTRAINT `jstc_orgnztn_jstc_objcts_fk` FOREIGN KEY (`justice_objects_id`) REFERENCES `justice_objects` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
   KEY `jstc_orgnztn_jstc_objcts_fk2_idx` (`justice_objects_parent_id`),
   CONSTRAINT `jstc_orgnztn_jstc_objcts_fk2` FOREIGN KEY (`justice_objects_parent_id`) REFERENCES `justice_objects` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `justice_building``
--

DROP TABLE IF EXISTS `justice_building`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_building` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_objects_id` int(11) NOT NULL,
  `justice_organization_id` int(11) DEFAULT NULL,
  `title` varchar(1024) NOT NULL,
  `building_type` varchar(255) NOT NULL,
  `address` varchar(1024) DEFAULT NULL,
  `GPS_coordinates` varchar(254) DEFAULT NULL,
  `building_details` varchar(1024) DEFAULT NULL,
  `status` varchar(32) NOT NULL,
  `modified_on` datetime NOT NULL,
   PRIMARY KEY (`id`),
   KEY `jstc_bldng_jstc_objcts_fk_idx` (`justice_objects_id`),
   CONSTRAINT `jstc_bldng_jstc_objcts_fk` FOREIGN KEY (`justice_objects_id`) REFERENCES `justice_objects` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
   KEY `jstc_bldng_jstc_orgnztn_fk_idx` (`justice_objects_parent_id`),
   CONSTRAINT `jstc_bldng_jstc_orgnztn_fk` FOREIGN KEY (`justice_organization_id`) REFERENCES `justice_organization` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `justice_itop_object_status_mapping`
--

DROP TABLE IF EXISTS `justice_itop_object_status_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_itop_object_status_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_code` varchar(64) NOT NULL,
  `justice_name_bg` varchar(255) NOT NULL,
  `itop_code` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `staging`.`justice_itop_object_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('INIT', 'Начално', 'inactive');
INSERT INTO `staging`.`justice_itop_object_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('DRAFT', 'Работно копие', 'inactive');
INSERT INTO `staging`.`justice_itop_object_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('UNDER_REVIEW', 'В процес на удобрение', 'inactive');
INSERT INTO `staging`.`justice_itop_object_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('ACTIVE', 'Активен', 'active');
INSERT INTO `staging`.`justice_itop_object_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('INACTIVE', 'Неактивен', 'inactive');
INSERT INTO `staging`.`justice_itop_object_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('DELETED', 'Изтрит', 'inactive');

--
-- Table structure for table `justice_itop_asset_status_mapping`
--

DROP TABLE IF EXISTS `justice_itop_asset_status_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_itop_asset_status_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_code` varchar(64) NOT NULL,
  `justice_name_bg` varchar(255) NOT NULL,
  `itop_code` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `staging`.`justice_itop_asset_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('INIT', 'Начален', 'started');
INSERT INTO `staging`.`justice_itop_asset_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('IN_OPERATION', 'В експлоатация', 'active');
INSERT INTO `staging`.`justice_itop_asset_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('OUT_OF_OPERATION', 'Извън експлоатация', 'inactive');
INSERT INTO `staging`.`justice_itop_asset_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('TO_BE_RECALLED', 'За брак', 'for scrap');
INSERT INTO `staging`.`justice_itop_asset_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('RECALLED', 'Бракуван актив', 'scrap');
INSERT INTO `staging`.`justice_itop_asset_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('OBSOLETE', 'Морално остарял актив', 'old');
INSERT INTO `staging`.`justice_itop_asset_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('CURRENT', 'Актуален', 'actual');
INSERT INTO `staging`.`justice_itop_asset_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('OUTDATED', 'Неактуален', 'unactual');
INSERT INTO `staging`.`justice_itop_asset_status_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('DELETED', 'Изтрит', 'deleted');

--
-- Table structure for table `justice_itop_wayOfAcquisition_mapping`
--

DROP TABLE IF EXISTS `justice_itop_wayOfAcquisition_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_itop_wayOfAcquisition_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_code` varchar(64) NOT NULL,
  `justice_name_bg` varchar(255) NOT NULL,
  `itop_code` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `staging`.`justice_itop_wayOfAcquisition_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('WA_INV', 'Закупуване', 'purchased');
INSERT INTO `staging`.`justice_itop_wayOfAcquisition_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('WA_CON', 'Наем', 'rented');
INSERT INTO `staging`.`justice_itop_wayOfAcquisition_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('WA_CH', 'Дарение', 'donation');
INSERT INTO `staging`.`justice_itop_wayOfAcquisition_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('WA_NA', 'Непредоставена информация', 'no_informatio');
INSERT INTO `staging`.`justice_itop_wayOfAcquisition_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('WA_OTH', 'Друг', 'other');

--
-- Table structure for table `justice_itop_boolean_mapping`
--

DROP TABLE IF EXISTS `justice_itop_boolean_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_itop_boolean_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_code` varchar(64) NOT NULL,
  `justice_name_bg` varchar(255) NOT NULL,
  `itop_code` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `staging`.`justice_itop_boolean_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('BOOL_YES', 'да', 'yes');
INSERT INTO `staging`.`justice_itop_boolean_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('BOOL_NO', 'не', 'no');
INSERT INTO `staging`.`justice_itop_boolean_mapping` (`justice_code`, `justice_name_bg`) VALUES ('BOOL_NA', 'няма информация');

--
-- Table structure for table `justice_itop_dataUpdate_mapping`
--

DROP TABLE IF EXISTS `justice_itop_dataUpdate_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_itop_dataUpdate_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_code` varchar(64) NOT NULL,
  `justice_name_bg` varchar(255) NOT NULL,
  `itop_code` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `staging`.`justice_itop_dataUpdate_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('DTUPD01', 'Не се обновяват', 'No_Update');
INSERT INTO `staging`.`justice_itop_dataUpdate_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('DTUPD02', 'Ръчно', 'Manual');
INSERT INTO `staging`.`justice_itop_dataUpdate_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('DTUPD03', 'Автоматизирано', 'Automatic');

--
-- Table structure for table `justice_itop_dataStructure_mapping`
--

DROP TABLE IF EXISTS `justice_itop_dataStructure_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_itop_dataStructure_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_code` varchar(64) NOT NULL,
  `justice_name_bg` varchar(255) NOT NULL,
  `itop_code` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `staging`.`justice_itop_dataStructure_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('DSTR01', 'Структурирани', 'Structured');
INSERT INTO `staging`.`justice_itop_dataStructure_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('DSTR02', 'Неструктурирани', 'Unstructured');
INSERT INTO `staging`.`justice_itop_dataStructure_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('DSTR03', 'Полу-структурирани', 'Halfstructured');
INSERT INTO `staging`.`justice_itop_dataStructure_mapping` (`justice_code`, `justice_name_bg`) VALUES ('DSTR04', 'С конкретно описание');

--
-- Table structure for table `justice_itop_access_mapping`
--

DROP TABLE IF EXISTS `justice_itop_access_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_itop_access_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_code` varchar(64) NOT NULL,
  `justice_name_bg` varchar(255) NOT NULL,
  `itop_code` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `staging`.`justice_itop_access_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('ACCSS01', 'Служебен', 'Work');
INSERT INTO `staging`.`justice_itop_access_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('ACCSS02', 'Свободен', 'Free');
INSERT INTO `staging`.`justice_itop_access_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('ACCSS03', 'Публичен', 'Public');

--
-- Table structure for table `justice_itop_personalDetails_mapping`
--

DROP TABLE IF EXISTS `justice_itop_personalDetails_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_itop_personalDetails_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_code` varchar(64) NOT NULL,
  `justice_name_bg` varchar(255) NOT NULL,
  `itop_code` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `staging`.`justice_itop_personalDetails_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('CPD01', 'да', 'yes');
INSERT INTO `staging`.`justice_itop_personalDetails_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('CPD02', 'не', 'no');
INSERT INTO `staging`.`justice_itop_personalDetails_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('CPD03', 'частично', 'partly');

--
-- Table structure for table `justice_itop_telephoneExchange_mapping`
--

DROP TABLE IF EXISTS `justice_itop_telephoneExchange_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_itop_telephoneExchange_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_code` varchar(64) NOT NULL,
  `justice_name_bg` varchar(255) NOT NULL,
  `itop_code` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `staging`.`justice_itop_telephoneExchange_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('TET_ANLG', 'Аналогова', 'Analog');
INSERT INTO `staging`.`justice_itop_telephoneExchange_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('TET_ISDN', 'ISDN', 'ISDN');
INSERT INTO `staging`.`justice_itop_telephoneExchange_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('TET_VOIP', 'VoIP', 'VoIP');
INSERT INTO `staging`.`justice_itop_telephoneExchange_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('TET_OTH', 'Друг', 'Other');

--
-- Table structure for table `justice_itop_telephoneType_mapping`
--

DROP TABLE IF EXISTS `justice_itop_telephoneType_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_itop_telephoneType_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_code` varchar(64) NOT NULL,
  `justice_name_bg` varchar(255) NOT NULL,
  `itop_code` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `staging`.`justice_itop_telephoneType_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('TT_NVOIP', 'non VoIP', 'non VoIP');
INSERT INTO `staging`.`justice_itop_telephoneType_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('TT_VOIP', 'VoIP', 'VoIP');
INSERT INTO `staging`.`justice_itop_telephoneType_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('TT_OTH', 'Друг', 'Other');

--
-- Table structure for table `justice_itop_providerStatus_mapping`
--

DROP TABLE IF EXISTS `justice_itop_providerStatus_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_itop_providerStatus_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_code` varchar(64) NOT NULL,
  `justice_name_bg` varchar(255) NOT NULL,
  `itop_code` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `staging`.`justice_itop_providerStatus_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('INIT', 'Начално', 'inactive');
INSERT INTO `staging`.`justice_itop_providerStatus_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('CURRENT', 'Действащ', 'active');
INSERT INTO `staging`.`justice_itop_providerStatus_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('OUTDATED', 'Изтекъл', 'inactive');
INSERT INTO `staging`.`justice_itop_providerStatus_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('DELETED', 'Изтрит', 'inactive');

--
-- Table structure for table `justice_itop_personStatus_mapping`
--

DROP TABLE IF EXISTS `justice_itop_personStatus_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `justice_itop_personStatus_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `justice_code` varchar(64) NOT NULL,
  `justice_name_bg` varchar(255) NOT NULL,
  `itop_code` varchar(255) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `staging`.`justice_itop_personStatus_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('ACTIVE', 'Активен', 'active');
INSERT INTO `staging`.`justice_itop_personStatus_mapping` (`justice_code`, `justice_name_bg`, `itop_code`) VALUES ('INACTIVE', 'Неактивен', 'inactive');


