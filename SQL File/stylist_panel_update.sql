-- Run on existing msmsdb database (phpMyAdmin or MySQL CLI)
-- Stylist panel: accounts + appointment assignment

CREATE TABLE IF NOT EXISTS `tblstylists` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `StylistName` varchar(120) DEFAULT NULL,
  `UserName` varchar(100) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `MobileNumber` bigint(11) DEFAULT NULL,
  `Specialty` varchar(200) DEFAULT NULL,
  `Password` varchar(200) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Demo stylist (password: stylist123)
INSERT INTO `tblstylists` (`StylistName`, `UserName`, `Email`, `MobileNumber`, `Specialty`, `Password`)
SELECT 'Priya Sharma', 'stylist', 'stylist@msms.com', 9876543210, 'Hair Cut & Styling', MD5('stylist123')
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tblstylists` WHERE `UserName` = 'stylist');

-- Run each ALTER once (ignore error if column already exists)
ALTER TABLE `tblappointment` ADD COLUMN `StylistId` int(10) DEFAULT NULL AFTER `Status`;
ALTER TABLE `tblappointment` ADD COLUMN `StylistRemark` varchar(250) DEFAULT NULL AFTER `StylistId`;
ALTER TABLE `tblappointment` ADD COLUMN `StylistStatus` varchar(10) NOT NULL DEFAULT '' AFTER `StylistRemark`;
