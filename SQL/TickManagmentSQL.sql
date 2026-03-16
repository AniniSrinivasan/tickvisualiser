-- CREATE TABLE IF NOT EXISTS Role
-- (
-- roleID INTEGER PRIMARY KEY auto_increment,
-- roleName TEXT NOT NULL
-- )

-- CREATE TABLE IF NOT EXISTS User
-- ( 
-- userEmail email primary key,
-- password text not null,
-- fName TEXT NOT NULL,
-- lName TEXT NOT NULL,
-- roleID INTEGER NOT NULL
-- FOREIGN KEY (roleID) references Role(roleID) ON DELETE CASCADE ON UPDATE CASCADE
-- );

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
-- county text
);

-- CREATE TABLE IF NOT EXISTS Upload
-- (
-- uploadID integer primary key auto_increment
-- );

CREATE TABLE IF NOT EXISTS Sighting
(
sightingID  VARCHAR(20) primary key,
speciesID integer not null,
locationID integer not null,
date_time text not null,
-- uploadID integer not null,
-- foreign key (uploadID) references Upload(uploadID),
FOREIGN KEY (speciesID) references Species(speciesID)ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (locationID) references Location(locationID) ON DELETE CASCADE ON UPDATE CASCADE 
);
