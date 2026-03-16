select * from importeddata;
select * from location;
select * from sighting;
select * from species;
select * from login;
-- DROP TABLE sighting;
-- DROP TABLE species;
-- DROP TABLE location;
-- DROP TABLE importeddata;

-- Inserting unique data into species and location
INSERT IGNORE INTO species (speciesName, speciesLatinName)
select species, latinName
from importeddata;
INSERT IGNORE INTO location (locationName)
select location
from importeddata;

-- ALTER TABLE importeddata MODIFY COLUMN id VARCHAR(255);
-- ALTER TABLE importeddata CHANGE COLUMN `date` `date_time` TEXT;

-- Inserting into sighting where id is correct length and date/time is valid - using id's instead of names
INSERT INTO sighting(sightingID,speciesID,locationID,date_time)
SELECT i.id,s.speciesID,l.locationID,i.date_time
FROM importeddata i 
JOIN species s on i.species = s.speciesName
join location l on i.location = l.locationName
WHERE (LENGTH(i.id) = 20) AND STR_TO_DATE(i.date_time, '%Y-%m-%dT%H:%i:%s') IS NOT NULL;

-- Data that wasnt entered into sighting due to errors in data
SELECT * FROM importeddata i
WHERE NOT EXISTS(SELECT 1 FROM sighting s WHERE s.sightingID = i.id);

-- Select all Where date_time is not valid
SELECT * FROM importeddata
WHERE STR_TO_DATE(date_time, '%Y-%m-%dT%H:%i:%s') IS NULL;