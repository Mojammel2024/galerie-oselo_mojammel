CREATE DATABASE galerie_oselo;
USE galerie_oselo;

CREATE TABLE artworks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist_name VARCHAR(255),
    year INT,
    width INT,
    height INT
);

CREATE TABLE warehouses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255)
);

ALTER TABLE artworks ADD warehouse_id INT;
ALTER TABLE artworks ADD CONSTRAINT fk_artworks_warehouse FOREIGN KEY (warehouse_id) 
REFERENCES warehouses(id) 
ON DELETE CASCADE
ON UPDATE CASCADE; 


