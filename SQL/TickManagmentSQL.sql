CREATE TABLE IF NOT EXISTS roles (
    role_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(40) NOT NULL
);

CREATE TABLE IF NOT EXISTS users ( 
    user_email VARCHAR(100) PRIMARY KEY,
    user_hash_password VARCHAR(255) NOT NULL,
    f_name VARCHAR(40) NOT NULL,
    l_name VARCHAR(40) NOT NULL,
    role_id INTEGER NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS species (
    species_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    species_name VARCHAR(40) NOT NULL UNIQUE,
    species_latin_name VARCHAR(40) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS location (
    location_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    location_name VARCHAR(40) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS upload (
    upload_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    upload_name VARCHAR(255) NOT NULL,
    upload_date DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS sighting (
    row_num INTEGER PRIMARY KEY AUTO_INCREMENT,
    id VARCHAR(20),
    species_id INTEGER NOT NULL,
    location_id INTEGER NOT NULL,
    date_time DATETIME,
    upload_id INTEGER NOT NULL,
    FOREIGN KEY (species_id) REFERENCES species(species_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (location_id) REFERENCES location(location_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (upload_id) REFERENCES upload(upload_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS inaccurate_sighting (
	row_num INT AUTO_INCREMENT PRIMARY KEY,
    id VARCHAR(255),
    species VARCHAR(40),
    latin_name VARCHAR(40),
    city VARCHAR(40),
    date_time VARCHAR(40),
    upload_id INTEGER NOT NULL,
    FOREIGN KEY (upload_id) REFERENCES upload(upload_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);
