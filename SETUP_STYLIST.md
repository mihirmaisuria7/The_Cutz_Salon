# Stylist Panel & Client Booking Setup

## 1. Run SQL (in order)

In phpMyAdmin, database **`msmsdb`**:

1. `SQL File/stylist_panel_update.sql` — stylist accounts + `StylistId`, `StylistRemark`, `StylistStatus`  
2. `SQL File/stylist_booking_update.sql` — service expertise table + demo links  

If `StylistStatus` fails, use **`SQL File/fix_stylist_columns.sql`** (run steps in order; skip “Duplicate column”).

**Common errors**

| Message | Fix |
|---------|-----|
| Unknown column **StylistRemark** | Run step 1–2 in `fix_stylist_columns.sql` first, then add `StylistStatus`. |
| Duplicate column **StylistStatus** | Column already exists — skip that line; you are done. |

## 2. Demo logins

| Role    | Username | Password     |
|---------|----------|--------------|
| Stylist | stylist  | stylist123   |
| Admin   | admin    | (your admin) |
| Client  | manish   | client123    |

## 3. Admin setup

1. **Stylists → Add Stylist** — check which **services** they are expert in.  
2. Or **Manage Stylists → Services** to edit expertise later.  
3. **Appointments → View** — see **client requested stylist**, accept/reject with remark.

## 4. Client flow

1. **Services** or **Stylists** — see who does which service.  
2. **Book** — pick service → stylist (filtered by expertise) → date & time.  
3. **My Appointments** — see admin status, stylist status, and overall result.

## 5. Stylist flow

1. **Dashboard / My Schedule** — client requests that chose you.  
2. **Respond** — accept or reject (with optional message).  
3. Booking is **confirmed** when **admin accepts** and **stylist accepts** (if a stylist was requested).  
4. Either side can **reject** — booking is rejected.

## 6. Status rules

- **Admin `Status`**: empty = pending, `1` = accepted, `2` = rejected  
- **Stylist `StylistStatus`**: same for the requested stylist  
- **Overall confirmed**: admin accepted AND (no stylist OR stylist accepted)  
- **Rejected**: admin rejected OR stylist rejected  
