SELECT * FROM imported_data;
SELECT COUNT(*) FROM imported_data;
SELECT * FROM location;
SELECT * FROM sighting;
SELECT * FROM inaccurate_sighting;
SELECT * FROM species;
SELECT * FROM login;
SELECT * FROM users;
SELECT * FROM upload;

-- for dropping the tables
SET FOREIGN_KEY_CHECKS = 0;
-- drop table if exists user;
-- drop table if exists login;
drop table if exists location;
drop table if exists sighting;
drop table if exists species;
drop table if exists upload;
drop table if exists Tick_Sightings;
drop table if exists inaccurate_sighting;
SET FOREIGN_KEY_CHECKS = 1;

-- shows the names of the existing tables
SHOW TABLES;


-- INSERTING UNIQUE DATA INTO species AND location
INSERT IGNORE INTO species (species_name, species_latin_name)
SELECT species, latinName
FROM imported_data;

INSERT IGNORE INTO location (location_name)
SELECT location
FROM imported_data;


-- Insert into location citys currently beeing used , future city will be added after aproval from admin to check for mistakes
insert into location(location_name)
Values("Birmingham"),
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

-- inserting into roles table
INSERT INTO 
roles(role_name) 
VALUES 
("user"),
("admin");

-- INSERTING INTO sighting WHERE id IS CORRECT LENGTH AND date_time IS VALID
INSERT INTO sighting  (species_id, location_id, date_time, upload_id)
SELECT s.species_id, l.location_id, i.date_time, 1
FROM imported_data i
JOIN species s ON i.species = s.species_name
JOIN location l ON i.location = l.location_name
WHERE (LENGTH(i.id) = 20) 
AND STR_TO_DATE(i.date_time, '%Y-%m-%dT%H:%i:%s') IS NOT NULL;

-- DATA THAT WASN'T ENTERED INTO sighting DUE TO ERRORS
SELECT * FROM imported_data i
WHERE NOT EXISTS (
    SELECT 1 
    FROM sighting s 
    WHERE s.id = i.id
);

-- SELECT ALL WHERE date_time IS NOT VALID
SELECT * FROM imported_data
WHERE STR_TO_DATE(date_time, '%Y-%m-%dT%H:%i:%s') IS NULL;


-- to check the specific field which is edited 
SELECT *
FROM tick_sightings
WHERE upload_id = 17
AND ID="02WNholuSg6ndCk4c1dA"
ORDER BY row_num;

-- to check view the specific uplaoded data
SELECT *
FROM tick_sightings
WHERE upload_id = 3
ORDER BY row_num;

-- to make the upload_id start from 1
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE upload;
SET FOREIGN_KEY_CHECKS = 1;

select upload_name from upload;

-- gives the create statement of the table
SHOW CREATE TABLE tick_sightings;
SHOW CREATE TABLE upload;

