-- Client stylist choice + accept/reject workflow
-- Run on msmsdb AFTER stylist_panel_update.sql

USE `msmsdb`;

-- Link stylists to services they are expert in
CREATE TABLE IF NOT EXISTS `tblstylist_services` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `StylistId` int(10) NOT NULL,
  `ServiceId` int(10) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `stylist_service` (`StylistId`,`ServiceId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- StylistStatus is added in stylist_panel_update.sql (line 25).
-- If you did NOT run that file yet, run fix_stylist_columns.sql instead of the line below.

-- Run only if StylistStatus column is missing (ignore "Duplicate column"):
-- ALTER TABLE `tblappointment` ADD COLUMN `StylistStatus` varchar(10) NOT NULL DEFAULT '' AFTER `StylistRemark`;

-- Demo: link demo stylist to common services
INSERT IGNORE INTO `tblstylist_services` (`StylistId`, `ServiceId`)
SELECT s.ID, srv.ID
FROM `tblstylists` s
CROSS JOIN `tblservices` srv
WHERE s.UserName = 'stylist'
  AND srv.ServiceName IN ('Hair Cut', 'Style Haircut', 'MUSTACHE TRIM', 'Beard Trim');
