-- Fix StylistStatus / StylistRemark errors on tblappointment
-- In phpMyAdmin: select database msmsdb, open SQL tab, run ONE block at a time.
-- Skip any line that says "Duplicate column" (column already exists = OK).

USE `msmsdb`;

-- 1) StylistId (skip if duplicate)
ALTER TABLE `tblappointment`
  ADD COLUMN `StylistId` int(10) DEFAULT NULL AFTER `Status`;

-- 2) StylistRemark (skip if duplicate)
ALTER TABLE `tblappointment`
  ADD COLUMN `StylistRemark` varchar(250) DEFAULT NULL AFTER `StylistId`;

-- 3) StylistStatus — run ONLY the first line that succeeds (do not run all three)

-- Try A (when StylistRemark exists):
ALTER TABLE `tblappointment`
  ADD COLUMN `StylistStatus` varchar(10) NOT NULL DEFAULT '' AFTER `StylistRemark`;

-- If A failed: "Unknown column StylistRemark" → run B instead:
-- ALTER TABLE `tblappointment`
--   ADD COLUMN `StylistStatus` varchar(10) NOT NULL DEFAULT '' AFTER `StylistId`;

-- If B failed: "Unknown column StylistId" → run C instead:
-- ALTER TABLE `tblappointment`
--   ADD COLUMN `StylistStatus` varchar(10) NOT NULL DEFAULT '' AFTER `Status`;

-- If you get "Duplicate column name StylistStatus" → column is already there; you are done.
