-- Fix tbladmin errors — run in phpMyAdmin after selecting database msmsdb
--
-- Common errors this fixes:
--   #1050 Table 'tbladmin' already exists  → uses CREATE IF NOT EXISTS
--   #1062 Duplicate entry '1'             → uses INSERT IGNORE
--   Missing PRIMARY KEY / AUTO_INCREMENT  → correct table definition

CREATE DATABASE IF NOT EXISTS `msmsdb` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `msmsdb`;

CREATE TABLE IF NOT EXISTS `tbladmin` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `AdminName` char(50) DEFAULT NULL,
  `UserName` char(50) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Password` varchar(200) DEFAULT NULL,
  `AdminRegdate` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=2;

-- If table existed without PRIMARY KEY, run ONLY this line (ignore error if PK already exists):
-- ALTER TABLE `tbladmin` ADD PRIMARY KEY (`ID`);

-- If ID is not auto-increment yet, run ONLY this line:
-- ALTER TABLE `tbladmin` MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

INSERT IGNORE INTO `tbladmin` (`ID`, `AdminName`, `UserName`, `MobileNumber`, `Email`, `Password`, `AdminRegdate`) VALUES
(1, 'Admin', 'admin', 7898799798, 'tester1@gmail.com', MD5('admin'), NOW());

UPDATE `tbladmin` SET `Password` = MD5('admin') WHERE `UserName` = 'admin';
