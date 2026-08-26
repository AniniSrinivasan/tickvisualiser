# Tick Visualiser

Tick Visualiser is a PHP and MySQL web application for uploading, validating, browsing, and visualising UK tick sighting data. It is designed to run locally with XAMPP and uses the uploaded sightings to generate a dashboard, map, charts, regional risk levels, and species insights.

## What You Can Do

- Register and log in as a public user or admin.
- View a dashboard with:
  - total validated tick sightings
  - UK tick distribution map
  - monthly sighting trend chart
  - top national risk areas
- Search dashboard data by city, species name, or Latin name.
- Use advanced dashboard search such as:

```text
@location_name:Manchester AND @species_name:Marsh Tick
```

- View general tick insights, prevention advice, removal guidance, and species information.
- Upload CSV tick sighting files as an admin.
- Browse previous uploads.
- Edit or delete uploaded sighting records.
- Review inaccurate uploaded rows separately and approve corrected rows into the main sightings table.
- Manage registered users as an admin.

## Project Structure

```text
tickvisualiser/
├── functions/          PHP database, upload, search, chart, map, and user helpers
├── pages/              Main application pages
├── script/             Frontend JavaScript
├── style/              CSS files
├── SQL/                Database schema, seed snippets, and ERD files
├── tickCSV/            Sample CSV data
├── TickImages/         Tick species images used by the insights page
├── cache/              Cached map boundary GeoJSON
└── upload-files/       Uploaded CSV files
```

## Requirements

- XAMPP with Apache and MySQL/MariaDB enabled
- PHP with `mysqli` support
- A browser
- Internet access for CDN assets:
  - Chart.js
  - Leaflet

The app database connection is currently configured in `functions/db_connect.php`:

```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "TickDB";
```

This matches a default local XAMPP MySQL setup.

## Setup Steps

1. Place the project in the XAMPP web root:

```text
{xampp path}/htdocs/tickvisualiser
```

2. Start XAMPP.

Start both:

- Apache
- MySQL

3. Create the database.

Open phpMyAdmin or a MySQL client and create a database named:

```sql
CREATE DATABASE TickDB;
USE TickDB;
```

4. Create the tables.

Import or run:

```text
SQL/TickManagmentSQL.sql
```

This creates these tables:

- `roles`
- `users`
- `species`
- `location`
- `upload`
- `sighting`
- `inaccurate_sighting`

5. Seed the required roles and supported locations.

Important: do not blindly import the whole `SQL/TickSQL.sql` file after creating the tables, because the lower section contains `DROP TABLE` statements.

Use only the insert statements from `SQL/TickSQL.sql` for:

- `location`
- `roles`

The required role values are:

```sql
INSERT INTO roles(role_name)
VALUES ("user"), ("admin");
```

The predefined locations are:

```sql
INSERT INTO location(location_name)
VALUES
("Birmingham"),
("Bristol"),
("Cardiff"),
("Edinburgh"),
("Glasgow"),
("Leeds"),
("Leicester"),
("Liverpool"),
("London"),
("Manchester"),
("Newcastle"),
("Nottingham"),
("Sheffield"),
("Southampton");
```

6. Open the application.

Use:

```text
http://localhost/tickvisualiser/pages/login.php
```

or:

```text
http://localhost/tickvisualiser/pages/dashboard.php
```

7. Create the first admin account.

Go to:

```text
http://localhost/tickvisualiser/pages/admin-register.php
```

Create an admin user, then log in with that account. The admin registration page currently has `adminCheck()` commented out for testing, which allows the first admin account to be created.

After the first admin exists, you should re-enable the admin check in `pages/admin-register.php` if you want only existing admins to create new admin users.

8. Upload sample data.

Log in as an admin, open Browse Data, and upload:

```text
tickCSV/Tick_Sightings.csv
```

After upload, the dashboard, map, charts, insights, and risk level pages will have data to display.

## CSV Format

The sample CSV uses:

```csv
id,date,location,species,latinName
```

The upload parser also accepts these equivalent column names:

- Date: `date_time` or `date`
- Location: `location_name`, `city`, or `location`
- Species: `species_name` or `species`
- Latin name: `species_latin_name`, `latin_name`, or `latinName`

A row is treated as valid when:

- `id` is present
- `id` is exactly 20 characters long
- species name is present
- Latin name is present
- location is present and exists in the `location` table
- date/time can be parsed

Rows that do not pass validation are stored in `inaccurate_sighting`. Admins can view them by selecting `Show only inaccurate data`, edit them, and approve corrected rows into the main `sighting` table.

## User Roles

Role IDs are:

```text
1 = user
2 = admin
```

Public users can access the dashboard, insights, contact page, login, logout, and registration flow.

Admins can additionally access:

- Browse Data
- Upload CSV data
- Edit/delete sighting rows
- Approve corrected inaccurate rows
- Manage users
- Add user accounts
- Add admin accounts

## Main Pages

- `pages/login.php` - login page
- `pages/register.php` - public user registration
- `pages/admin-register.php` - admin account creation
- `pages/dashboard.php` - main dashboard
- `pages/insights.php` - species and tick information
- `pages/regional-risk-levels.php` - regional risk breakdown
- `pages/browse-data.php` - admin CSV upload and data browsing
- `pages/manage-user.php` - admin user management
- `pages/contact.php` - contact information

## Troubleshooting

If the app cannot connect to the database, check:

- MySQL is running in XAMPP
- the database is named `TickDB`
- `functions/db_connect.php` has the correct username and password

If pages show empty charts or maps:

- upload valid CSV data first
- check that Chart.js and Leaflet can load from the CDN
- check browser console errors

If uploaded rows appear as inaccurate:

- confirm the `id` value is exactly 20 characters
- confirm the location already exists in the `location` table
- confirm the date can be parsed
- confirm species and Latin name fields are not empty

If CSV upload fails:

- upload one file at a time
- make sure the file extension is `.csv`
- make sure `upload-files/` exists and is writable by the web server

## Notes for Developers

- The app uses plain PHP pages with shared helper files in `functions/`.
- Sessions and role checks are handled in `functions/session.php`.
- The dashboard search parser is in `functions/search-functions.php`.
- CSV upload, validation, inline editing, deletion, and approval actions are handled by `functions/upload-functions.php`.
- Frontend behaviours are in `script/script.js`.
- The map uses local boundary data from `cache/map-boundary.json`.
