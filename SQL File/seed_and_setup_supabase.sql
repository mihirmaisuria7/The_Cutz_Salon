-- Setup and Seed Script for Online Saloon Management System (Supabase / PostgreSQL)
-- Run in: Supabase Dashboard -> SQL Editor -> New query -> Paste & Run

-- --------------------------------------------------------
-- 1. Disable Row Level Security (RLS) on all tables
-- This allows the traditional PHP server backend to perform queries via the anon key
-- --------------------------------------------------------
ALTER TABLE IF EXISTS public.tbladmin DISABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.tblappointment DISABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.tblcustomers DISABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.tblinvoice DISABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.tblpage DISABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.tblservices DISABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.tblstylists DISABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.tblstylist_services DISABLE ROW LEVEL SECURITY;
ALTER TABLE IF EXISTS public.tblsubscriber DISABLE ROW LEVEL SECURITY;

-- --------------------------------------------------------
-- 2. Seed Default Admin
-- Default credentials: Username: admin | Password: Test@123 (MD5 hashed)
-- --------------------------------------------------------
INSERT INTO public.tbladmin (id, adminname, username, mobilenumber, email, password, adminregdate)
VALUES (1, 'Admin', 'admin', 7898799798, 'tester1@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2019-07-25 06:21:50')
ON CONFLICT (id) DO NOTHING;

-- --------------------------------------------------------
-- 3. Seed Informational Pages (About Us and Contact Us)
-- --------------------------------------------------------
INSERT INTO public.tblpage (id, pagetype, pagetitle, pagedescription, email, mobilenumber, updationdate, timing) 
VALUES
(1, 'aboutus', 'About Us', 'Our main focus is on quality and hygiene. Our Parlour is well equipped with advanced technology equipments and provides best quality services.', NULL, NULL, NULL, ''),
(2, 'contactus', 'Contact Us', '890,Sector 62, Gyan Sarovar, GAIL Noida(Delhi/NCR)', 'info@gmail.com', 7896541236, NULL, '10:30 am to 8:30 pm')
ON CONFLICT (id) DO NOTHING;

-- --------------------------------------------------------
-- 4. Seed Services List
-- --------------------------------------------------------
INSERT INTO public.tblservices (id, servicename, description, cost, creationdate) 
VALUES
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
ON CONFLICT (id) DO NOTHING;

-- --------------------------------------------------------
-- 5. Seed Stylist Account
-- Default credentials: Username: stylist | Password: stylist123 (MD5 hashed)
-- --------------------------------------------------------
INSERT INTO public.tblstylists (id, stylistname, username, email, mobilenumber, specialty, password, creationdate)
VALUES (1, 'Priya Sharma', 'stylist', 'stylist@msms.com', 9876543210, 'Hair Cut & Styling', MD5('stylist123'), '2021-07-19 07:38:20')
ON CONFLICT (id) DO NOTHING;

-- --------------------------------------------------------
-- 6. Link Stylist with Services Expertises
-- --------------------------------------------------------
INSERT INTO public.tblstylist_services (id, stylistid, serviceid)
VALUES 
(1, 1, 8),   -- Hair Cut
(2, 1, 9),   -- Style Haircut
(3, 1, 17),  -- MUSTACHE TRIM
(4, 1, 18)   -- Beard Trim
ON CONFLICT (stylistid, serviceid) DO NOTHING;

-- --------------------------------------------------------
-- 7. Seed Demo Customers
-- Default credentials: Password: client123 (MD5 hashed)
-- --------------------------------------------------------
INSERT INTO public.tblcustomers (id, name, email, mobilenumber, gender, details, username, password, creationdate)
VALUES 
(2, 'Rahul Singh', 'singh@gmail.com', 5565565656, 'Male', 'Taken haircut by him', 'rahul', MD5('client123'), '2019-07-26 11:10:02'),
(6, 'Manish', 'manish@gmail.com', 9879879798, 'Male', 'vjhgjhghg', 'manish', MD5('client123'), '2021-07-21 07:42:54')
ON CONFLICT (id) DO NOTHING;

-- --------------------------------------------------------
-- 8. Reset Auto-Increment Sequences
-- Ensures the auto-generating identity counter starts after the seeded IDs
-- --------------------------------------------------------
SELECT setval(pg_get_serial_sequence('public.tbladmin', 'id'), GREATEST((SELECT MAX(id) FROM public.tbladmin), 1));
SELECT setval(pg_get_serial_sequence('public.tblappointment', 'id'), GREATEST((SELECT MAX(id) FROM public.tblappointment), 1));
SELECT setval(pg_get_serial_sequence('public.tblcustomers', 'id'), GREATEST((SELECT MAX(id) FROM public.tblcustomers), 1));
SELECT setval(pg_get_serial_sequence('public.tblinvoice', 'id'), GREATEST((SELECT MAX(id) FROM public.tblinvoice), 1));
SELECT setval(pg_get_serial_sequence('public.tblpage', 'id'), GREATEST((SELECT MAX(id) FROM public.tblpage), 1));
SELECT setval(pg_get_serial_sequence('public.tblservices', 'id'), GREATEST((SELECT MAX(id) FROM public.tblservices), 1));
SELECT setval(pg_get_serial_sequence('public.tblstylists', 'id'), GREATEST((SELECT MAX(id) FROM public.tblstylists), 1));
SELECT setval(pg_get_serial_sequence('public.tblstylist_services', 'id'), GREATEST((SELECT MAX(id) FROM public.tblstylist_services), 1));
SELECT setval(pg_get_serial_sequence('public.tblsubscriber', 'id'), GREATEST((SELECT MAX(id) FROM public.tblsubscriber), 1));
