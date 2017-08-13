/*
SQLyog Community v9.02 
MySQL - 5.6.12 : Database - lmp
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `lmp_tny_cimkek` */

DROP TABLE IF EXISTS `lmp_tny_cimkek`;

CREATE TABLE `lmp_tny_cimkek` (
  `cimke_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `szoveg` varchar(32) COLLATE utf8_hungarian_ci NOT NULL,
  PRIMARY KEY (`cimke_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_extrafields` */

DROP TABLE IF EXISTS `lmp_tny_extrafields`;

CREATE TABLE `lmp_tny_extrafields` (
  `field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_name` varchar(32) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `field_label` varchar(40) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `field_type` varchar(16) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_felh_terhat` */

DROP TABLE IF EXISTS `lmp_tny_felh_terhat`;

CREATE TABLE `lmp_tny_felh_terhat` (
  `felh_id` int(11) unsigned NOT NULL,
  `fcsoport_id` int(11) unsigned NOT NULL,
  `terszerv_id` int(11) NOT NULL,
  `commentemail` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_felhcsoportok` */

DROP TABLE IF EXISTS `lmp_tny_felhcsoportok`;

CREATE TABLE `lmp_tny_felhcsoportok` (
  `fcsop_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kod` char(6) COLLATE utf8_hungarian_ci NOT NULL,
  `nev` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `jog_felhasznalok` tinyint(1) NOT NULL,
  `jog_terszerv` tinyint(1) NOT NULL,
  `jog_kategoriak` tinyint(1) NOT NULL,
  `jog_cimkek` tinyint(1) NOT NULL,
  `jog_csoportos` tinyint(1) NOT NULL,
  `jog_hirlevel` tinyint(1) NOT NULL,
  `jog_csv` tinyint(1) NOT NULL,
  `jog_email` char(2) COLLATE utf8_hungarian_ci NOT NULL,
  `jog_nev` char(2) COLLATE utf8_hungarian_ci NOT NULL,
  `jog_telefonszam` char(2) COLLATE utf8_hungarian_ci NOT NULL,
  `jog_lakcim` char(2) COLLATE utf8_hungarian_ci NOT NULL,
  `jog_tarthely` char(2) COLLATE utf8_hungarian_ci NOT NULL,
  `jog_oevk` char(2) COLLATE utf8_hungarian_ci NOT NULL,
  `jog_szev` char(2) COLLATE utf8_hungarian_ci NOT NULL,
  `jog_kapcsolat` char(2) COLLATE utf8_hungarian_ci NOT NULL,
  `jog_kapcskat` char(2) COLLATE utf8_hungarian_ci NOT NULL,
  `jog_kapcster` char(2) COLLATE utf8_hungarian_ci NOT NULL,
  `jog_kapcscimkek` char(2) COLLATE utf8_hungarian_ci NOT NULL,
  `jog_kapcshirlevel` char(2) COLLATE utf8_hungarian_ci NOT NULL,
  `jog_ellenorzott` char(2) COLLATE utf8_hungarian_ci NOT NULL,
  PRIMARY KEY (`fcsop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_javaslat` */

DROP TABLE IF EXISTS `lmp_tny_javaslat`;

CREATE TABLE `lmp_tny_javaslat` (
  `kapcs_id` int(11) NOT NULL DEFAULT '0',
  `javaslo_id` int(11) NOT NULL DEFAULT '0',
  `idopont` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mezo` varchar(32) COLLATE utf8_hungarian_ci NOT NULL,
  `ertek` varchar(128) COLLATE utf8_hungarian_ci NOT NULL,
  `allapot` varchar(10) COLLATE utf8_hungarian_ci NOT NULL DEFAULT 'javaslat',
  `megjegyzes` varchar(80) COLLATE utf8_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_kampany` */

DROP TABLE IF EXISTS `lmp_tny_kampany`;

CREATE TABLE `lmp_tny_kampany` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `megnev` varchar(128) COLLATE utf8_hungarian_ci NOT NULL,
  `leiras` text COLLATE utf8_hungarian_ci NOT NULL,
  `helyszin` varchar(128) COLLATE utf8_hungarian_ci NOT NULL,
  `idopont` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `eloado` varchar(250) COLLATE utf8_hungarian_ci NOT NULL,
  `meghivott` varchar(250) COLLATE utf8_hungarian_ci NOT NULL,
  `kezdet` date NOT NULL,
  `vege` date NOT NULL,
  `allapot` int(1) NOT NULL,
  `megjegyzes` text COLLATE utf8_hungarian_ci NOT NULL,
  `szures` text COLLATE utf8_hungarian_ci NOT NULL,
  `kerdes` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `valaszok` text COLLATE utf8_hungarian_ci,
  `kerdestipus` tinyint(4) DEFAULT NULL,
  `kerdes1` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `valaszok1` text COLLATE utf8_hungarian_ci,
  `kerdestipus1` tinyint(4) DEFAULT NULL,
  `kerdes2` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `valaszok2` text COLLATE utf8_hungarian_ci,
  `kerdestipus2` tinyint(4) DEFAULT NULL,
  `kerdes3` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `valaszok3` text COLLATE utf8_hungarian_ci,
  `kerdestipus3` tinyint(4) DEFAULT NULL,
  `kerdes4` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `valaszok4` text COLLATE utf8_hungarian_ci,
  `kerdestipus4` tinyint(4) DEFAULT NULL,
  `hirlevel_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_kampany_kapcs` */

DROP TABLE IF EXISTS `lmp_tny_kampany_kapcs`;

CREATE TABLE `lmp_tny_kampany_kapcs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kampany_id` int(11) NOT NULL,
  `kapcs_id` int(11) NOT NULL,
  `hivasido` datetime DEFAULT NULL,
  `valasz` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `valasz1` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `valasz2` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `valasz3` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `valasz4` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kacs_id_i` (`kapcs_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_kampany_terszerv` */

DROP TABLE IF EXISTS `lmp_tny_kampany_terszerv`;

CREATE TABLE `lmp_tny_kampany_terszerv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kampany_id` int(11) NOT NULL,
  `terszerv_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_kapcsolatok` */

DROP TABLE IF EXISTS `lmp_tny_kapcsolatok`;

CREATE TABLE `lmp_tny_kapcsolatok` (
  `kapcs_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `nev1` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `nev2` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `nev3` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `titulus` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `nem` varchar(3) COLLATE utf8_hungarian_ci NOT NULL,
  `email2` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `telefon` varchar(60) COLLATE utf8_hungarian_ci NOT NULL,
  `irsz` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `telepules` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `kerulet` int(2) NOT NULL,
  `utca` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `kjelleg` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `hazszam` int(4) NOT NULL,
  `cimkieg` varchar(20) COLLATE utf8_hungarian_ci NOT NULL,
  `tirsz` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `ttelepules` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `tkerulet` int(2) NOT NULL,
  `tutca` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `tkjelleg` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `thazszam` int(3) NOT NULL,
  `tcimkieg` varchar(20) COLLATE utf8_hungarian_ci NOT NULL,
  `oevk` varchar(20) COLLATE utf8_hungarian_ci NOT NULL,
  `szev` int(4) NOT NULL,
  `kapcsnev` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `kapcsid` int(11) NOT NULL,
  `kapcsdatum` date NOT NULL,
  `kategoria_id` int(11) NOT NULL,
  `terszerv_id` int(11) NOT NULL,
  `cimkek` varchar(80) COLLATE utf8_hungarian_ci NOT NULL,
  `belsoemail` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `hirlevel` tinyint(1) NOT NULL,
  `ellenorzott` tinyint(1) NOT NULL,
  `zarol_user_id` int(11) NOT NULL,
  `zarol_time` bigint(20) NOT NULL,
  `lastaction` varchar(20) COLLATE utf8_hungarian_ci NOT NULL,
  `lastact_user_id` int(11) NOT NULL,
  `lastact_time` datetime NOT NULL,
  `lastact_info` varchar(80) COLLATE utf8_hungarian_ci NOT NULL,
  `megjegyzes` text COLLATE utf8_hungarian_ci NOT NULL,
  `telefon2` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `telszammegj` varchar(255) COLLATE utf8_hungarian_ci NOT NULL,
  `hogyan_csatlakozott` varchar(128) COLLATE utf8_hungarian_ci NOT NULL,
  `parttagstart` varchar(128) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
  `parttagend` varchar(128) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
  `telmegj2` varchar(128) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
  `orszag` char(3) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `torszag` char(3) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`kapcs_id`),
  KEY `lmp_tny_kapcsolatoki` (`telepules`,`kerulet`),
  KEY `email` (`email`),
  KEY `email2` (`email2`)
) ENGINE=InnoDB AUTO_INCREMENT=113529 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_kategoriak` */

DROP TABLE IF EXISTS `lmp_tny_kategoriak`;

CREATE TABLE `lmp_tny_kategoriak` (
  `kategoria_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `szoveg` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  PRIMARY KEY (`kategoria_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_keresztnevek` */

DROP TABLE IF EXISTS `lmp_tny_keresztnevek`;

CREATE TABLE `lmp_tny_keresztnevek` (
  `nev` varchar(40) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `nem` varchar(6) COLLATE utf8_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_kommentek` */

DROP TABLE IF EXISTS `lmp_tny_kommentek`;

CREATE TABLE `lmp_tny_kommentek` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kapcs_id` int(11) DEFAULT NULL,
  `idopont` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `kommentszoveg` text COLLATE utf8_hungarian_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=654 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_kommentolvasasok` */

DROP TABLE IF EXISTS `lmp_tny_kommentolvasasok`;

CREATE TABLE `lmp_tny_kommentolvasasok` (
  `kapcs_id` int(11) NOT NULL DEFAULT '0',
  `komment_id` int(11) NOT NULL DEFAULT '0',
  `olvaso_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`kapcs_id`,`komment_id`,`olvaso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_messages` */

DROP TABLE IF EXISTS `lmp_tny_messages`;

CREATE TABLE `lmp_tny_messages` (
  `recipient` int(11) DEFAULT NULL,
  `tipus` varchar(10) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `txt` text COLLATE utf8_hungarian_ci,
  `created` datetime DEFAULT NULL,
  `creator` int(11) DEFAULT NULL,
  `read` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_naplo` */

DROP TABLE IF EXISTS `lmp_tny_naplo`;

CREATE TABLE `lmp_tny_naplo` (
  `kapcs_id` int(11) unsigned NOT NULL,
  `email` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `nev1` varchar(40) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `nev2` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `nev3` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `titulus` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `nem` varchar(3) COLLATE utf8_hungarian_ci NOT NULL,
  `email2` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `telefon` varchar(60) COLLATE utf8_hungarian_ci NOT NULL,
  `irsz` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `telepules` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `kerulet` int(2) NOT NULL,
  `utca` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `kjelleg` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `hazszam` int(4) NOT NULL,
  `cimkieg` varchar(20) COLLATE utf8_hungarian_ci NOT NULL,
  `tirsz` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `ttelepules` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `tkerulet` int(2) NOT NULL,
  `tutca` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `tkjelleg` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `thazszam` int(3) NOT NULL,
  `tcimkieg` varchar(20) COLLATE utf8_hungarian_ci NOT NULL,
  `oevk` varchar(20) COLLATE utf8_hungarian_ci NOT NULL,
  `szev` int(4) NOT NULL,
  `kapcsnev` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `kapcsid` int(11) NOT NULL,
  `kapcsdatum` date NOT NULL,
  `kategoria_id` int(11) NOT NULL,
  `terszerv_id` int(11) NOT NULL,
  `cimkek` varchar(80) COLLATE utf8_hungarian_ci NOT NULL,
  `belsoemail` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `hirlevel` tinyint(1) NOT NULL,
  `ellenorzott` tinyint(1) NOT NULL,
  `zarol_user_id` int(11) NOT NULL,
  `zarol_time` bigint(20) NOT NULL,
  `lastaction` varchar(20) COLLATE utf8_hungarian_ci NOT NULL,
  `lastact_user_id` int(11) NOT NULL,
  `lastact_time` datetime NOT NULL,
  `lastact_info` varchar(80) COLLATE utf8_hungarian_ci NOT NULL,
  `megjegyzes` text COLLATE utf8_hungarian_ci,
  `telefon2` varchar(40) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `telszammegj` varchar(255) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `hogyan_csatlakozott` varchar(128) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `parttagstart` varchar(128) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
  `parttagend` varchar(128) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
  `telmegj2` varchar(128) COLLATE utf8_hungarian_ci NOT NULL DEFAULT '',
  `orszag` char(3) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `torszag` char(3) COLLATE utf8_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_oevk_torzs` */

DROP TABLE IF EXISTS `lmp_tny_oevk_torzs`;

CREATE TABLE `lmp_tny_oevk_torzs` (
  `ev` int(4) DEFAULT NULL,
  `OEVK` varchar(20) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `telepules` varchar(80) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `kerulet` char(2) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `kozterulet` varchar(80) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'Ha üres akkor ez atelepülés default',
  `kozterjellege` varchar(20) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `hazszamtol` char(4) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `hazszamig` char(4) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `paros` varchar(20) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `lmp_tny_oevk_torzsi` (`telepules`,`kerulet`),
  KEY `lmp_tny_oevk_torzsi1` (`telepules`)
) ENGINE=InnoDB AUTO_INCREMENT=15973 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_orszkod` */

DROP TABLE IF EXISTS `lmp_tny_orszkod`;

CREATE TABLE `lmp_tny_orszkod` (
  `ORSZKOD` varchar(6) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `MEGN` varchar(150) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `EU` varchar(3) COLLATE utf8_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_terszerv_map` */

DROP TABLE IF EXISTS `lmp_tny_terszerv_map`;

CREATE TABLE `lmp_tny_terszerv_map` (
  `telepules` varchar(60) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'település neve',
  `kerulet` char(2) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'kerület (csak Bp -nél)',
  `terszerv_nev` varchar(60) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'ter.szerv megnevezése',
  `terszerv_id` int(3) DEFAULT NULL COMMENT 'ter.szerv kódja',
  KEY `cimi` (`telepules`,`kerulet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_teruletiszervezetek` */

DROP TABLE IF EXISTS `lmp_tny_teruletiszervezetek`;

CREATE TABLE `lmp_tny_teruletiszervezetek` (
  `terszerv_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nev` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `leiras` text COLLATE utf8_hungarian_ci NOT NULL,
  `tulaj_id` int(11) NOT NULL,
  PRIMARY KEY (`terszerv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Table structure for table `lmp_tny_utkozesek` */

DROP TABLE IF EXISTS `lmp_tny_utkozesek`;

CREATE TABLE `lmp_tny_utkozesek` (
  `kapcs_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `info` varchar(20) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `email` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `nev1` varchar(40) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `nev2` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `nev3` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `titulus` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `nem` varchar(3) COLLATE utf8_hungarian_ci NOT NULL,
  `email2` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `telefon` varchar(60) COLLATE utf8_hungarian_ci NOT NULL,
  `irsz` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `telepules` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `kerulet` int(2) NOT NULL,
  `utca` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `kjelleg` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `hazszam` int(4) NOT NULL,
  `cimkieg` varchar(20) COLLATE utf8_hungarian_ci NOT NULL,
  `tirsz` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `ttelepules` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `tkerulet` int(2) NOT NULL,
  `tutca` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `tkjelleg` varchar(10) COLLATE utf8_hungarian_ci NOT NULL,
  `thazszam` int(3) NOT NULL,
  `tcimkieg` varchar(20) COLLATE utf8_hungarian_ci NOT NULL,
  `oevk` varchar(20) COLLATE utf8_hungarian_ci NOT NULL,
  `szev` int(4) NOT NULL,
  `kapcsnev` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `kapcsid` int(11) NOT NULL,
  `kapcsdatum` date NOT NULL,
  `kategoria_id` int(11) NOT NULL,
  `terszerv_id` int(11) NOT NULL,
  `cimkek` varchar(80) COLLATE utf8_hungarian_ci NOT NULL,
  `belsoemail` varchar(40) COLLATE utf8_hungarian_ci NOT NULL,
  `hirlevel` tinyint(1) NOT NULL,
  `ellenorzott` tinyint(1) NOT NULL,
  `zarol_user_id` int(11) NOT NULL,
  `zarol_time` bigint(20) NOT NULL,
  `lastaction` varchar(20) COLLATE utf8_hungarian_ci NOT NULL,
  `lastact_user_id` int(11) NOT NULL,
  `lastact_time` datetime NOT NULL,
  `lastact_info` varchar(80) COLLATE utf8_hungarian_ci NOT NULL,
  `megjegyzes` text COLLATE utf8_hungarian_ci,
  PRIMARY KEY (`kapcs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/* Function  structure for function  `SPLIT_STRING` */

/*!50003 DROP FUNCTION IF EXISTS `SPLIT_STRING` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` FUNCTION `SPLIT_STRING`(delim VARCHAR(12), str VARCHAR(255), pos INT) RETURNS varchar(255) CHARSET utf8 COLLATE utf8_hungarian_ci
    DETERMINISTIC
RETURN
    TRIM(REPLACE(
        SUBSTRING(
            SUBSTRING_INDEX(str, delim, pos),
            LENGTH(SUBSTRING_INDEX(str, delim, pos-1)) + 1
        ),
        delim, ''
    )) */$$
DELIMITER ;

/*Table structure for table `lmp_pu_ervenyes_eloirasok` */

DROP TABLE IF EXISTS `lmp_pu_ervenyes_eloirasok`;

/*!50001 DROP VIEW IF EXISTS `lmp_pu_ervenyes_eloirasok` */;
/*!50001 DROP TABLE IF EXISTS `lmp_pu_ervenyes_eloirasok` */;

/*!50001 CREATE TABLE  `lmp_pu_ervenyes_eloirasok`(
 `id` int(11) unsigned ,
 `kapcs_id` int(11) ,
 `datum` date ,
 `nyito` int(1) ,
 `leiras` varchar(40) ,
 `tagdij` bigint(11) ,
 `uvegzseb` int(11) ,
 `modusr` int(11) ,
 `moddat` datetime 
)*/;

/*Table structure for table `lmp_pu_folyoszamla` */

DROP TABLE IF EXISTS `lmp_pu_folyoszamla`;

/*!50001 DROP VIEW IF EXISTS `lmp_pu_folyoszamla` */;
/*!50001 DROP TABLE IF EXISTS `lmp_pu_folyoszamla` */;

/*!50001 CREATE TABLE  `lmp_pu_folyoszamla`(
 `terszerv_id` int(11) ,
 `datum` date ,
 `tagdij_eloiras` bigint(20) ,
 `tagdij_befizetes` bigint(11) ,
 `uvegzseb_eloiras` bigint(20) ,
 `uvegzseb_befizetes` bigint(11) ,
 `tamogatas` bigint(11) ,
 `tulaj_id` int(11) ,
 `kapcs_id` int(11) unsigned ,
 `kapcsnev` varchar(122) 
)*/;

/*Table structure for table `lmp_tny_hirlevel_csatlakozas` */

DROP TABLE IF EXISTS `lmp_tny_hirlevel_csatlakozas`;

/*!50001 DROP VIEW IF EXISTS `lmp_tny_hirlevel_csatlakozas` */;
/*!50001 DROP TABLE IF EXISTS `lmp_tny_hirlevel_csatlakozas` */;

/*!50001 CREATE TABLE  `lmp_tny_hirlevel_csatlakozas`(
 `kapcs_id` int(11) unsigned ,
 `hirlevel_csatlakozas` varchar(10) 
)*/;

/*Table structure for table `lmp_tny_kapcsolatnevek` */

DROP TABLE IF EXISTS `lmp_tny_kapcsolatnevek`;

/*!50001 DROP VIEW IF EXISTS `lmp_tny_kapcsolatnevek` */;
/*!50001 DROP TABLE IF EXISTS `lmp_tny_kapcsolatnevek` */;

/*!50001 CREATE TABLE  `lmp_tny_kapcsolatnevek`(
 `kapcs_id` int(11) unsigned ,
 `nev` varchar(122) 
)*/;

/*View structure for view lmp_pu_ervenyes_eloirasok */

/*!50001 DROP TABLE IF EXISTS `lmp_pu_ervenyes_eloirasok` */;
/*!50001 DROP VIEW IF EXISTS `lmp_pu_ervenyes_eloirasok` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `lmp_pu_ervenyes_eloirasok` AS (select distinct `e`.`id` AS `id`,`e`.`kapcs_id` AS `kapcs_id`,`e`.`datum` AS `datum`,`e`.`nyito` AS `nyito`,`e`.`leiras` AS `leiras`,if((isnull(`m`.`kapcs_id`) or (`e`.`nyito` = 1)),`e`.`tagdij`,0) AS `tagdij`,`e`.`uvegzseb` AS `uvegzseb`,`e`.`modusr` AS `modusr`,`e`.`moddat` AS `moddat` from (`lmp_pu_eloirasok` `e` left join `lmp_pu_tagdij_mentessegek` `m` on(((`m`.`kapcs_id` = `e`.`kapcs_id`) and (`m`.`datumtol` <= `e`.`datum`) and (`m`.`datumig` >= `e`.`datum`))))) */;

/*View structure for view lmp_pu_folyoszamla */

/*!50001 DROP TABLE IF EXISTS `lmp_pu_folyoszamla` */;
/*!50001 DROP VIEW IF EXISTS `lmp_pu_folyoszamla` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `lmp_pu_folyoszamla` AS select `k`.`terszerv_id` AS `terszerv_id`,`e`.`datum` AS `datum`,`e`.`tagdij` AS `tagdij_eloiras`,0 AS `tagdij_befizetes`,`e`.`uvegzseb` AS `uvegzseb_eloiras`,0 AS `uvegzseb_befizetes`,0 AS `tamogatas`,`ter`.`tulaj_id` AS `tulaj_id`,`k`.`kapcs_id` AS `kapcs_id`,concat(`k`.`nev1`,' ',`k`.`nev2`,' ',`k`.`nev3`) AS `kapcsnev` from ((`lmp_pu_ervenyes_eloirasok` `e` left join `lmp_tny_kapcsolatok` `k` on((`k`.`kapcs_id` = `e`.`kapcs_id`))) left join `lmp_tny_teruletiszervezetek` `ter` on((`ter`.`terszerv_id` = `k`.`terszerv_id`))) union all select `k`.`terszerv_id` AS `terszerv_id`,`b`.`datum` AS `datum`,0 AS `0`,`b`.`tagdij` AS `tagdij`,0 AS `0`,`b`.`uvegzseb` AS `uvegzseb`,`b`.`tamogatas` AS `tamogatas`,`ter`.`tulaj_id` AS `tulaj_id`,`k`.`kapcs_id` AS `kapcs_id`,concat(`k`.`nev1`,' ',`k`.`nev2`,' ',`k`.`nev3`) AS `kapcsnev` from ((`lmp_pu_befizetesek` `b` left join `lmp_tny_kapcsolatok` `k` on((`k`.`kapcs_id` = `b`.`kapcs_id`))) left join `lmp_tny_teruletiszervezetek` `ter` on((`ter`.`terszerv_id` = `k`.`terszerv_id`))) where (`b`.`megbizo` = 0) union all select `k`.`terszerv_id` AS `terszerv_id`,`b`.`datum` AS `datum`,0 AS `0`,`b`.`tagdij` AS `tagdij`,0 AS `0`,`b`.`uvegzseb` AS `uvegzseb`,`b`.`tamogatas` AS `tamogatas`,`ter`.`tulaj_id` AS `tulaj_id`,`k`.`kapcs_id` AS `kapcs_id`,concat(`k`.`nev1`,' ',`k`.`nev2`,' ',`k`.`nev3`) AS `kapcsnev` from ((`lmp_pu_befizetesek` `b` left join `lmp_tny_kapcsolatok` `k` on((`k`.`kapcs_id` = `b`.`megbizo`))) left join `lmp_tny_teruletiszervezetek` `ter` on((`ter`.`terszerv_id` = `k`.`terszerv_id`))) where (`b`.`megbizo` > 0) */;

/*View structure for view lmp_tny_hirlevel_csatlakozas */

/*!50001 DROP TABLE IF EXISTS `lmp_tny_hirlevel_csatlakozas` */;
/*!50001 DROP VIEW IF EXISTS `lmp_tny_hirlevel_csatlakozas` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `lmp_tny_hirlevel_csatlakozas` AS (select `k`.`kapcs_id` AS `kapcs_id`,date_format(from_unixtime(max(`uc`.`date`)),'%Y-%m-%d') AS `hirlevel_csatlakozas` from (((`lmp_acymailing_url` `u` join `lmp_acymailing_urlclick` `uc` on((`uc`.`urlid` = `u`.`urlid`))) join `lmp_acymailing_subscriber` `s` on((`s`.`subid` = `uc`.`subid`))) join `lmp_tny_kapcsolatok` `k` on(((`k`.`email` = `s`.`email`) or (`k`.`email2` = `s`.`email`)))) where (`u`.`name` = 'http://lehetmas.hu/tejossz/') group by `k`.`kapcs_id`) */;

/*View structure for view lmp_tny_kapcsolatnevek */

/*!50001 DROP TABLE IF EXISTS `lmp_tny_kapcsolatnevek` */;
/*!50001 DROP VIEW IF EXISTS `lmp_tny_kapcsolatnevek` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `lmp_tny_kapcsolatnevek` AS (select `lmp_tny_kapcsolatok`.`kapcs_id` AS `kapcs_id`,trim(replace(concat(trim(`lmp_tny_kapcsolatok`.`nev1`),' ',trim(`lmp_tny_kapcsolatok`.`nev2`),' ',trim(`lmp_tny_kapcsolatok`.`nev3`)),'  ',' ')) AS `nev` from `lmp_tny_kapcsolatok`) */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
