SELECT * FROM location;
SELECT * FROM sighting;
SELECT * FROM inaccurate_sighting;
SELECT * FROM species;
SELECT * FROM users;
SELECT * FROM roles;
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

INSERT INTO users(user_email,user_hash_password,f_name,l_name,role_id)
VALUES
("Admin@example.com","adminuser","admin","user","2"),
("user@example.com","useruser","user","user","1");
