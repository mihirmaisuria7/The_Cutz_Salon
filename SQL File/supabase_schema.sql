-- Supabase (PostgreSQL) schema for Salon Management System
-- Run in: Supabase Dashboard → SQL Editor → New query → Paste & Run
-- Project: https://supabase.com/dashboard/project/umbwlifaxyqagafihpcm

-- --------------------------------------------------------
-- tbladmin
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS tbladmin (
  "ID" SERIAL PRIMARY KEY,
  "AdminName" VARCHAR(50),
  "UserName" VARCHAR(50),
  "MobileNumber" BIGINT,
  "Email" VARCHAR(200),
  "Password" VARCHAR(200),
  "AdminRegdate" TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tbladmin ("ID", "AdminName", "UserName", "MobileNumber", "Email", "Password", "AdminRegdate")
VALUES (1, 'Admin', 'admin', 7898799798, 'tester1@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2019-07-25 06:21:50')
ON CONFLICT ("ID") DO NOTHING;

SELECT setval(pg_get_serial_sequence('tbladmin', 'ID'), GREATEST((SELECT MAX("ID") FROM tbladmin), 1));

-- --------------------------------------------------------
-- tblappointment
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS tblappointment (
  "ID" SERIAL PRIMARY KEY,
  "AptNumber" VARCHAR(80),
  "Name" VARCHAR(120),
  "Email" VARCHAR(120),
  "PhoneNumber" BIGINT,
  "AptDate" VARCHAR(120),
  "AptTime" VARCHAR(120),
  "Services" VARCHAR(120),
  "ApplyDate" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "Remark" VARCHAR(250) DEFAULT '',
  "Status" VARCHAR(50) DEFAULT '',
  "RemarkDate" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "StylistId" INTEGER,
  "StylistRemark" VARCHAR(250),
  "StylistStatus" VARCHAR(10) DEFAULT ''
);

INSERT INTO tblappointment ("ID", "AptNumber", "Name", "Email", "PhoneNumber", "AptDate", "AptTime", "Services", "ApplyDate", "Remark", "Status", "RemarkDate") VALUES
(4, '578797544', 'Anuj Kumar', 'phpgurukulofficial@gmail.com', 123456789, '8/30/2019', '1:30am', 'Test', '2019-08-21 16:13:13', 'Selected', '1', '2021-07-19 12:24:48'),
(5, '899118550', 'bb', 'bgfdh@fdfdsf.com', 4234235423, '8/27/2019', '1:30am', 'Loreal Hair Color(Full)', '2019-08-21 16:14:14', '', '', '2019-08-21 16:14:14'),
(6, '621107928', 'ABC', 'abc@gmail.com', 1234567890, '8/27/2019', '1:30am', 'Rebonding', '2019-08-21 16:22:25', 'Testing', '2', '2019-08-21 16:24:10'),
(7, '184242778', 'Harish', 'har@gmail.com', 4654646546, '2021-07-23', '10:38', 'MUSTACHE TRIM', '2021-07-20 06:40:43', 'selected', '1', '2021-07-21 07:40:06'),
(8, '777343097', 'Manish', 'manish@gmail.com', 2678979789, '2021-07-24', '13:23', 'Hair Cut', '2021-07-20 06:52:33', 'selected', '1', '2021-07-25 17:32:06'),
(9, '290594099', 'Yash', 'yash@gmail.com', 4654654654, '2021-07-24', '14:36', 'Hair Cut', '2021-07-21 08:05:47', '', '', '2021-07-25 17:32:08'),
(10, '128617343', 'Dinesh', 'dinesh@gmail.com', 6876876868, '2021-07-25', '15:30', 'Hair Cut', '2021-07-23 04:56:47', '', '', '2021-07-25 17:32:11'),
(11, '600991456', 'Test', 'test@gmail.com', 7987987897, '2021-07-24', '15:40', 'Hair Cut', '2021-07-23 05:10:56', 'Selected', '1', '2021-07-25 17:32:14'),
(12, '336388269', 'Anuj', 'ak@gmail.com', 1234569870, '2021-07-30', '15:52', 'Hair Cut', '2021-07-25 17:22:37', 'Confirmed', '1', '2021-07-25 17:32:16')
ON CONFLICT ("ID") DO NOTHING;

SELECT setval(pg_get_serial_sequence('tblappointment', 'ID'), GREATEST((SELECT MAX("ID") FROM tblappointment), 1));

-- --------------------------------------------------------
-- tblcustomers
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS tblcustomers (
  "ID" SERIAL PRIMARY KEY,
  "Name" VARCHAR(120),
  "Email" VARCHAR(200),
  "MobileNumber" BIGINT,
  "Gender" VARCHAR(20) CHECK ("Gender" IN ('Female', 'Male', 'Transgender')),
  "Details" TEXT,
  "CreationDate" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "UpdationDate" TIMESTAMP,
  "UserName" VARCHAR(100),
  "Password" VARCHAR(200)
);

INSERT INTO tblcustomers ("ID", "Name", "Email", "MobileNumber", "Gender", "Details", "CreationDate", "UpdationDate", "UserName", "Password") VALUES
(2, 'Rahul Singh', 'singh@gmail.com', 5565565656, 'Male', 'Taken haircut by him', '2019-07-26 11:10:02', NULL, 'rahul', MD5('client123')),
(5, 'Test user', 'testuser@gmail.com', 1234567890, 'Female', 'Test', '2019-08-21 16:24:53', '2019-08-21 16:25:11', 'testuser', MD5('client123')),
(6, 'Manish', 'manish@gmail.com', 9879879798, 'Male', 'vjhgjhghg', '2021-07-21 07:42:54', NULL, 'manish', MD5('client123')),
(7, 'Anuj kumar', 'ak@gmail.com', 1234567899, 'Transgender', 'Test', '2021-07-25 17:25:54', '2021-07-25 17:26:31', 'anuj', MD5('client123'))
ON CONFLICT ("ID") DO NOTHING;

SELECT setval(pg_get_serial_sequence('tblcustomers', 'ID'), GREATEST((SELECT MAX("ID") FROM tblcustomers), 1));

-- --------------------------------------------------------
-- tblinvoice
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS tblinvoice (
  id SERIAL PRIMARY KEY,
  "Userid" INTEGER,
  "ServiceId" INTEGER,
  "BillingId" INTEGER,
  "PostingDate" TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tblinvoice (id, "Userid", "ServiceId", "BillingId", "PostingDate") VALUES
(1, 2, 7, 600922156, '2021-07-21 07:48:58'),
(2, 2, 9, 600922156, '2021-07-21 07:48:58'),
(3, 5, 7, 777590972, '2021-07-23 05:16:41'),
(4, 5, 9, 777590972, '2021-07-23 05:16:41'),
(6, 7, 9, 631074383, '2021-07-25 17:26:51'),
(7, 7, 15, 631074383, '2021-07-25 17:26:51')
ON CONFLICT (id) DO NOTHING;

SELECT setval(pg_get_serial_sequence('tblinvoice', 'id'), GREATEST((SELECT MAX(id) FROM tblinvoice), 1));

-- --------------------------------------------------------
-- tblpage
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS tblpage (
  "ID" SERIAL PRIMARY KEY,
  "PageType" VARCHAR(200),
  "PageTitle" TEXT,
  "PageDescription" TEXT,
  "Email" VARCHAR(200),
  "MobileNumber" BIGINT,
  "UpdationDate" DATE,
  "Timing" VARCHAR(200) DEFAULT ''
);

INSERT INTO tblpage ("ID", "PageType", "PageTitle", "PageDescription", "Email", "MobileNumber", "UpdationDate", "Timing") VALUES
(1, 'aboutus', 'About Us', 'Our main focus is on quality and hygiene. Our Parlour is well equipped with advanced technology equipments and provides best quality services.', NULL, NULL, NULL, ''),
(2, 'contactus', 'Contact Us', '890,Sector 62, Gyan Sarovar, GAIL Noida(Delhi/NCR)', 'info@gmail.com', 7896541236, NULL, '10:30 am to 8:30 pm')
ON CONFLICT ("ID") DO NOTHING;

SELECT setval(pg_get_serial_sequence('tblpage', 'ID'), GREATEST((SELECT MAX("ID") FROM tblpage), 1));

-- --------------------------------------------------------
-- tblservices
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS tblservices (
  "ID" SERIAL PRIMARY KEY,
  "ServiceName" VARCHAR(200),
  "Description" TEXT,
  "Cost" INTEGER,
  "CreationDate" TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tblservices ("ID", "ServiceName", "Description", "Cost", "CreationDate") VALUES
(1, 'O3 Facial', 'Activated charcoal draws bacteria, toxins, dirt and oil from the skin.', 120, '2019-07-25 11:22:38'),
(2, 'Fruit Facial', 'If its a peel-off mask, it also works as an excellent exfoliator.', 500, '2019-07-25 11:22:53'),
(3, 'Charcol Facial', 'The end result is skin that is clean and clear.', 1000, '2019-07-25 11:23:10'),
(4, 'Deluxe Menicure', 'The end result is skin that is clean and clear.', 500, '2019-07-25 11:23:34'),
(5, 'Deluxe Pedicure', 'A pedicure is a therapeutic treatment for your feet.', 600, '2019-07-25 11:23:47'),
(6, 'Normal Menicure', 'A pedicure is a therapeutic treatment for your feet.', 300, '2019-07-25 11:24:01'),
(7, 'Normal Pedicure', 'A pedicure is a therapeutic treatment for your feet.', 400, '2019-07-25 11:24:19'),
(8, 'Hair Cut', 'A hairstyle, hairdo, or haircut refers to the styling of hair.', 250, '2019-07-25 11:24:38'),
(9, 'Style Haircut', 'A hairstyle, hairdo, or haircut refers to the styling of hair.', 550, '2019-07-25 11:24:53'),
(10, 'Hair Wash', 'A hairstyle, hairdo, or haircut refers to the styling of hair.', 3999, '2019-07-25 11:25:08'),
(11, 'Loreal Hair Color(Full)', 'hgfhgj', 1200, '2019-07-25 11:25:35'),
(12, 'Body Spa', 'It is full body spa including hair wash', 1500, '2019-08-19 13:36:27'),
(14, 'Test', 'test test', 100, '2019-08-21 15:45:50'),
(15, 'ABC', 'gjhgjhgbkhhioljhoioi', 200, '2019-08-21 16:23:23'),
(16, 'Tradinational Cut', 'Traditional cut service', 45, '2021-07-19 07:37:40'),
(17, 'MUSTACHE TRIM', 'Trim Trim Trim', 85, '2021-07-19 07:38:02'),
(18, 'Beard Trim', 'Beard Trim', 10, '2021-07-19 07:38:20')
ON CONFLICT ("ID") DO NOTHING;

SELECT setval(pg_get_serial_sequence('tblservices', 'ID'), GREATEST((SELECT MAX("ID") FROM tblservices), 1));

-- --------------------------------------------------------
-- tblsubscriber
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS tblsubscriber (
  "ID" SERIAL PRIMARY KEY,
  "Email" VARCHAR(200),
  "DateofSub" TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tblsubscriber ("ID", "Email", "DateofSub") VALUES
(1, 'ani@gmail.com', '2021-07-16 07:32:33'),
(2, 'rahul@gmail.com', '2021-07-16 07:32:33'),
(3, 'ganesh@gmail.com', '2021-07-21 07:36:46'),
(4, 'ak@gmail.com', '2021-07-25 17:25:29')
ON CONFLICT ("ID") DO NOTHING;

SELECT setval(pg_get_serial_sequence('tblsubscriber', 'ID'), GREATEST((SELECT MAX("ID") FROM tblsubscriber), 1));

-- --------------------------------------------------------
-- tblstylists
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS tblstylists (
  "ID" SERIAL PRIMARY KEY,
  "StylistName" VARCHAR(120),
  "UserName" VARCHAR(100),
  "Email" VARCHAR(200),
  "MobileNumber" BIGINT,
  "Specialty" VARCHAR(200),
  "Password" VARCHAR(200),
  "CreationDate" TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tblstylists ("StylistName", "UserName", "Email", "MobileNumber", "Specialty", "Password")
SELECT 'Priya Sharma', 'stylist', 'stylist@msms.com', 9876543210, 'Hair Cut & Styling', MD5('stylist123')
WHERE NOT EXISTS (SELECT 1 FROM tblstylists WHERE "UserName" = 'stylist');

-- --------------------------------------------------------
-- tblstylist_services
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS tblstylist_services (
  "ID" SERIAL PRIMARY KEY,
  "StylistId" INTEGER NOT NULL,
  "ServiceId" INTEGER NOT NULL,
  UNIQUE ("StylistId", "ServiceId")
);

INSERT INTO tblstylist_services ("StylistId", "ServiceId")
SELECT s."ID", srv."ID"
FROM tblstylists s
CROSS JOIN tblservices srv
WHERE s."UserName" = 'stylist'
  AND srv."ServiceName" IN ('Hair Cut', 'Style Haircut', 'MUSTACHE TRIM', 'Beard Trim')
ON CONFLICT ("StylistId", "ServiceId") DO NOTHING;
