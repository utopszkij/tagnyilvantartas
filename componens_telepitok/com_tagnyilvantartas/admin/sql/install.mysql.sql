CREATE TABLE IF NOT EXISTS `#__tny_cimkek` (
  `cimke_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'belső egyedfi azonosító',
  `szoveg` varchar(32) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'szöveg',
  PRIMARY KEY (`cimke_id`)
) ENGINE=InnoDB COLLATE=utf8_hungarian_ci;

CREATE TABLE IF NOT EXISTS `#__tny_felhcsoportok` (
  `fcsop_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'belső egyedi azonosító',
  `kod` char(6) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'rövid kód',
  `nev` varchar(40) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'megnevezés',
  `jog_felhasznalok` tinyint(1) NOT NULL COMMENT 'felhasználókat kezelhet',
  `jog_terszerv` tinyint(1) NOT NULL COMMENT 'területi szervezeteket kezelhet',
  `jog_kategoriak` tinyint(1) NOT NULL COMMENT 'kapcsolat kategoriákat kezelhet',
  `jog_cimkek` tinyint(1) NOT NULL COMMENT 'cimkeket kezelhet',
  `jog_csoportos` tinyint(1) NOT NULL COMMENT 'csoportos módositási jog',
  `jog_hirlevel` tinyint(1) NOT NULL COMMENT 'hirlevél küldés, kezelés',
  `jog_csv` tinyint(1) NOT NULL COMMENT 'csv export/import jog',
  `jog_email` char(2) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'email cimek kezelése',
  `jog_nev` char(2) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'nevek kezelése',
  `jog_telefonszam` char(2) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'telefonszámok kezelése',
  `jog_lakcim` char(2) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'lakcím adatok kezelése',
  `jog_tarthely` char(2) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'tartozkodási hely',
  `jog_oevk` char(2) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'oevk kezelése',
  `jog_szev` char(2) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'születési év kezelése',
  `jog_kapcsolat` char(2) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'kapcsolat felvétel adatok kezelése',
  `jog_kapcskat` char(2) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'kapcsolat kategórájának kezelése',
  `jog_kapcster` char(2) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'kapcsolat területi szervezet kezelése',
  `jog_kapcscimkek` char(2) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'kapcsolat cimkék kezelése',
  `jog_kapcshirlevel` char(2) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'kapcsolat hirlevelet kér kezelése',
  `jog_ellenorzott` char(2) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'hirlevelet kér adat kezelése',
  PRIMARY KEY (`fcsop_id`)
) ENGINE=InnoDB COLLATE=utf8_hungarian_ci;

CREATE TABLE IF NOT EXISTS `#__tny_kapcsolatok` (
  `kapcs_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi rekord azonositó',
  `email` varchar(40) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'e-mail (kódolt)',
  `nev1` varchar(40) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'vezetéknév (kódolt)',
  `nev2` varchar(40) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'középső név (kódolt)',
  `nev3` varchar(40) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'utónév (kodolt)',
  `titulus` varchar(10) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'titulus (kódolt)',
  `nem` varchar(3) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'nem (kódolt)',
  `email2` varchar(40) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'másodlagos email (kódolt)',
  `telefon` varchar(60) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'telefonszámok (kódolt)',
  `irsz` varchar(10) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'allandó lakcim irsz (kódolt)',
  `telepules` varchar(40) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'allando lakcim település (kódolt)',
  `kerulet` int(2) NOT NULL COMMENT 'allando lakcim kerület (kódolt)',
  `utca` varchar(40) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'allando lakcim közterület neve (kódolt)',
  `kjelleg` varchar(10) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'allando lakcim közterület jellege (kodolt)',
  `hazszam` int(4) NOT NULL COMMENT 'allando lakcim hazszam (kodolt)',
  `cimkieg` varchar(20) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'allando lakcim cimkieg (kodolt)',
  `tirsz` varchar(10) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'tart.hely irsz (kódolt)',
  `ttelepules` varchar(40) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'tart.hely település (kódolt)',
  `tkerulet` int(2) NOT NULL COMMENT 'tart.hely kerület (kódolt)',
  `tutca` varchar(40) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'tart.hely közter neve (kódolt)',
  `tkjelleg` varchar(10) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'tart.hely közterület jellege (kódolt)',
  `thazszam` int(3) NOT NULL COMMENT 'tart.hely házszám (kódolt)',
  `tcimkieg` varchar(20) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'tart.hely cimkieg (kódolt)',
  `oevk` varchar(20) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'orsz.egyéni vállasztó kerület (kódolt)',
  `szev` int(4) NOT NULL COMMENT 'születési év (kódolt)',
  `kapcsnev` varchar(40) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'Kapcsolat felvevő neve',
  `kapcsid` int(11) NOT NULL COMMENT 'kapcsolat felvevő id (kódolt)',
  `kapcsdatum` date NOT NULL COMMENT 'kapcsolat felvétel dátuma (kódolt)',
  `kategoria_id` int(11) NOT NULL COMMENT 'kategória id (kodolt)',
  `terszerv_id` int(11) NOT NULL COMMENT 'területi szervezet id',
  `cimkek` varchar(80) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'cimkék (kódolt)',
  `belsoemail` varchar(40) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'belső email (kódolt)',
  `hirlevel` tinyint(1) NOT NULL COMMENT 'hírlevelet kér',
  `ellenorzott` tinyint(1) NOT NULL COMMENT 'ellenörzött rekord',
  `zarol_user_id` int(11) NOT NULL COMMENT 'zárolt rekord zároló user id',
  `zarol_time` bigint(20) NOT NULL COMMENT 'zárolás időpontja',
  `lastaction` varchar(20) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'utolsó akció',
  `lastact_user_id` int(11) NOT NULL COMMENT 'utolsó akció user id',
  `lastact_time` datetime NOT NULL COMMENT 'utolsó akció időpontja',
  `lastact_info` varchar(80) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'kiegészitő infó az utolsó akciohoz',
  PRIMARY KEY (`kapcs_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci


CREATE TABLE IF NOT EXISTS `#__tny_kategoriak` (
  `kategoria_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'belső egyedi azonosító',
  `szoveg` varchar(40) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'kapcsolat kategória megnevezése',
  PRIMARY KEY (`kategoria_id`)
) ENGINE=InnoDB COLLATE=utf8_hungarian_ci;

CREATE TABLE IF NOT EXISTS `#__tny_teruletiszervezetek` (
  `terszerv_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'egyedi belső azonosító',
  `nev` varchar(40) COLLATE utf8_hungarian_ci NOT NULL COMMENT 'rövid név',
  `leiras` text COLLATE utf8_hungarian_ci NOT NULL COMMENT 'leírás',
  `tulaj_id` int(11) NOT NULL COMMENT 'főlérendelt szervezet',
  PRIMARY KEY (`terszerv_id`)
) ENGINE=InnoDB COLLATE=utf8_hungarian_ci;

