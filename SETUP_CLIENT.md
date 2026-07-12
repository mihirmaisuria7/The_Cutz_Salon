# MSMS Salon — Client Side Setup

## 1. Import database
Import `SQL File/msmsdb.sql` into MySQL (database name: **msmsdb**).

## 2. Run client login update (required)
In phpMyAdmin, run **`SQL File/client_auth_update.sql`**  
This adds `UserName` and `Password` columns to `tblcustomers` and creates demo client accounts.

## 3. Configure connection
Edit `includes/dbconnection.php` if needed (host, user, password, database).

## 4. Open in browser
- **Home + Login:** `http://localhost/Online-Saloon-Management-system-main/`
- **Admin panel:** unchanged at `admin/` (also reachable after admin login from home)

## Login (merged index)

| Role   | Username | Password   | Goes to              |
|--------|----------|------------|----------------------|
| Admin  | admin    | (your DB)  | `admin/dashboard.php` |
| Client | manish   | client123  | `client/dashboard.php` |
| Client | rahul    | client123  | `client/dashboard.php` |

Default admin password is whatever you set in `tbladmin` (original project often uses **admin** / **Test@123** or check your import).

## Client features
- Dashboard with stats
- Book appointments (saved to `tblappointment` — visible in admin)
- View appointment status
- Browse services & prices
- View invoices (from admin billing)
- Edit profile & change password
- Self-registration at `client/register.php`

## Admin panel
**Not modified.** Admins can still use `admin/index.php` or the main home login.
