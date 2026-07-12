-- Run this on your existing msmsdb database (phpMyAdmin or MySQL CLI)
-- Adds client login fields to tblcustomers

ALTER TABLE `tblcustomers`
  ADD COLUMN `UserName` varchar(100) DEFAULT NULL AFTER `Details`,
  ADD COLUMN `Password` varchar(200) DEFAULT NULL AFTER `UserName`;

-- Demo client accounts (password for all: client123)
UPDATE `tblcustomers` SET `UserName` = 'manish', `Password` = MD5('client123') WHERE `ID` = 6;
UPDATE `tblcustomers` SET `UserName` = 'rahul', `Password` = MD5('client123') WHERE `ID` = 2;
UPDATE `tblcustomers` SET `UserName` = 'anuj', `Password` = MD5('client123') WHERE `ID` = 7;
UPDATE `tblcustomers` SET `UserName` = 'testuser', `Password` = MD5('client123') WHERE `ID` = 5;
