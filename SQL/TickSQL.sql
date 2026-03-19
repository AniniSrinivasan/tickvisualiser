SELECT * FROM imported_data;
SELECT * FROM location;
SELECT * FROM sighting;
SELECT * FROM species;
SELECT * FROM login;

ALTER TABLE imported_data MODIFY COLUMN id VARCHAR(255);
ALTER TABLE imported_data CHANGE COLUMN `date` date_time VARCHAR(40);
ALTER TABLE imported_data MODIFY COLUMN location VARCHAR(40);
ALTER TABLE imported_data MODIFY COLUMN species VARCHAR(40);
ALTER TABLE imported_data MODIFY COLUMN latinName VARCHAR(40);

-- INSERTING UNIQUE DATA INTO species AND location
INSERT IGNORE INTO species (species_name, species_latin_name)
SELECT species, latin_name
FROM imported_data;

INSERT IGNORE INTO location (location_name)
SELECT location
FROM imported_data;

insert into upload(upload_id,upload_name,upload_date)
values ("1","imported_data","2026-02-20");

-- INSERTING INTO sighting WHERE id IS CORRECT LENGTH AND date_time IS VALID
INSERT INTO sighting  (species_id, location_id, date_time, upload_id)
SELECT s.species_id, l.location_id, i.date_time, 1
FROM imported_data i
JOIN species s ON i.species = s.species_name
JOIN location l ON i.location = l.location_name
WHERE (LENGTH(i.id) = 20) 
AND STR_TO_DATE(i.date_time, '%Y-%m-%dT%H:%i:%s') IS NOT NULL;

-- drop table sighting;

-- DATA THAT WASN'T ENTERED INTO sighting DUE TO ERRORS
SELECT * FROM imported_data i
WHERE NOT EXISTS (
    SELECT 1 
    FROM sighting s 
    WHERE s.sighting_id = i.id
);

-- SELECT ALL WHERE date_time IS NOT VALID
SELECT * FROM imported_data
WHERE STR_TO_DATE(date_time, '%Y-%m-%dT%H:%i:%s') IS NULL;