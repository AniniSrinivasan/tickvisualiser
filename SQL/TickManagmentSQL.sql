CREATE TABLE IF NOT EXISTS Login
( 
adminID integer primary key,
password text not null
);

CREATE TABLE IF NOT EXISTS Species
(
speciesID integer primary key AUTO_INCREMENT,
speciesName text not null UNIQUE,
speciesLatinName text not null UNIQUE
);
CREATE TABLE IF NOT EXISTS Location
(
locationID integer primary key AUTO_INCREMENT,
locationName text not null UNIQUE,
county text not null
);
CREATE TABLE IF NOT EXISTS Sighting
(
sightingID  VARCHAR(20) primary key,
speciesID integer not null,
locationID integer not null,
date_time text not null,
FOREIGN KEY (speciesID) references Species(speciesID)ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (locationID) references Location(locationID) ON DELETE CASCADE ON UPDATE CASCADE 
);
