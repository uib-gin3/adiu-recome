# Recome database for practice 1 of 21724 ADIU
# Import this file in phpMyAdmin on XAMPP or let Docker load it automatically

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS recome CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE recome;

CREATE TABLE units (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(40) NOT NULL,
  type ENUM('weight', 'volume', 'unit') NOT NULL,
  factor DECIMAL(10,4) NOT NULL
) ENGINE=InnoDB;

# One unit of each row equals the factor in grams, liters or pieces
INSERT INTO units (id, name, type, factor) VALUES
(1, 'Cullerada', 'volume', 0.0150),
(2, 'Culleradeta', 'volume', 0.0050),
(3, 'Polsim', 'weight', 1.0000),
(4, 'Tassó', 'volume', 0.2000),
(5, 'Tassa', 'volume', 0.2500),
(6, 'Tasseta', 'volume', 0.1000),
(7, 'g', 'weight', 1.0000),
(8, 'l', 'volume', 1.0000),
(9, 'ml', 'volume', 0.0010),
(10, 'cl', 'volume', 0.0100),
(11, 'kg', 'weight', 1000.0000),
(12, 'unitat', 'unit', 1.0000);

CREATE TABLE ingredients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  family VARCHAR(40) NOT NULL,
  purchase_quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
  purchase_unit_id INT NOT NULL,
  is_infinite TINYINT(1) NOT NULL DEFAULT 0,
  always_buy TINYINT(1) NOT NULL DEFAULT 0,
  CONSTRAINT fk_ingredient_unit FOREIGN KEY (purchase_unit_id) REFERENCES units(id)
) ENGINE=InnoDB;

CREATE TABLE pantry (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ingredient_id INT NOT NULL,
  quantity DECIMAL(10,2) NOT NULL,
  unit_id INT NOT NULL,
  CONSTRAINT fk_pantry_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id),
  CONSTRAINT fk_pantry_unit FOREIGN KEY (unit_id) REFERENCES units(id)
) ENGINE=InnoDB;

CREATE TABLE recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  base_people INT NOT NULL DEFAULT 2,
  instructions TEXT
) ENGINE=InnoDB;

CREATE TABLE recipe_ingredients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT NOT NULL,
  ingredient_id INT NOT NULL,
  quantity DECIMAL(10,2) NOT NULL,
  unit_id INT NOT NULL,
  CONSTRAINT fk_recipe_line_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
  CONSTRAINT fk_recipe_line_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id),
  CONSTRAINT fk_recipe_line_unit FOREIGN KEY (unit_id) REFERENCES units(id)
) ENGINE=InnoDB;

# One row per meal, day 1 is Monday and slot 3 is lunch
CREATE TABLE menu_slots (
  day TINYINT NOT NULL,
  slot TINYINT NOT NULL,
  recipe_id INT NOT NULL,
  people INT NOT NULL DEFAULT 2,
  PRIMARY KEY (day, slot),
  CONSTRAINT fk_menu_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

# Balanced diet criteria, portions per family in one week
CREATE TABLE criteria (
  family VARCHAR(40) PRIMARY KEY,
  portions INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE shopping_list (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ingredient_id INT NULL,
  custom_name VARCHAR(80) NULL,
  quantity DECIMAL(10,2) NOT NULL,
  unit_id INT NULL,
  status ENUM('pending', 'bought', 'discarded') NOT NULL DEFAULT 'pending',
  is_manual TINYINT(1) NOT NULL DEFAULT 0,
  CONSTRAINT fk_list_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredients(id),
  CONSTRAINT fk_list_unit FOREIGN KEY (unit_id) REFERENCES units(id)
) ENGINE=InnoDB;

INSERT INTO ingredients (id, name, family, purchase_quantity, purchase_unit_id, is_infinite, always_buy) VALUES
(1, 'Sal fina', 'Espècies', 1, 11, 1, 0),
(2, 'Pebre negre', 'Espècies', 100, 7, 1, 0),
(3, 'Aigua de l''aixeta', 'Begudes', 1, 8, 1, 0),
(4, 'Oli d''oliva verge extra', 'Olis', 1, 8, 0, 0),
(5, 'Arròs blanc', 'Cereals', 1, 11, 0, 0),
(6, 'Macarrons', 'Cereals', 500, 7, 0, 0),
(7, 'Pa de barra', 'Cereals', 2, 12, 0, 1),
(8, 'Patates', 'Verdura', 2, 11, 0, 0),
(9, 'Tomàquets madurs', 'Verdura', 1, 11, 0, 0),
(10, 'Ceba', 'Verdura', 1, 11, 0, 0),
(11, 'All', 'Verdura', 100, 7, 0, 0),
(12, 'Escarola', 'Verdura', 1, 12, 0, 0),
(13, 'Espinacs frescos', 'Verdura', 300, 7, 0, 0),
(14, 'Pèsols congelats', 'Verdura', 500, 7, 0, 0),
(15, 'Pomes', 'Fruita', 1, 11, 0, 0),
(16, 'Plàtans', 'Fruita', 1, 11, 0, 0),
(17, 'Pollastre', 'Carn', 1, 11, 0, 0),
(18, 'Lluç', 'Peix', 1, 11, 0, 0),
(19, 'Tonyina en llauna', 'Peix', 3, 12, 0, 0),
(20, 'Cigrons cuits', 'Llegums', 1, 11, 0, 0),
(21, 'Ous', 'Ous', 12, 12, 0, 0),
(22, 'Llet sencera', 'Làctius', 1, 8, 0, 0),
(23, 'Formatge fresc', 'Làctius', 250, 7, 0, 0),
(24, 'Iogurt natural', 'Làctius', 4, 12, 0, 0);

INSERT INTO recipes (id, name, base_people, instructions) VALUES
(1, 'Arròs a la cassola amb pollastre', 4, 'Sofregiu la ceba i el tomàquet picats amb oli. Afegiu el pollastre a trossos i daureu-lo. Poseu l''arròs, remeneu i afegiu l''aigua calenta. Coeu vint minuts a foc suau i deixeu reposar cinc minuts abans de servir.'),
(2, 'Macarrons amb tonyina', 2, 'Coceu els macarrons en aigua amb sal i escorreu-los. En una cassola, sofregiu l''all amb oli, afegiu el tomàquet i la tonyina escorreguda. Barregeu-ho tot amb la pasta i serveixeu calent.'),
(3, 'Truita de patates', 2, 'Talleu les patates i la ceba a làmines i coeu-les amb oli a foc suau. Bateu els ous amb sal, barregeu-ho tot i coeu la truita amb una mica més d''oli.'),
(4, 'Lluç al forn amb pèsols', 2, 'Poseu el lluç, les patates a làmines i els pèsols en una safata de forn. Afegiu l''all picat i l''oli. Coeu al forn a dos-cents graus durant vint minuts.'),
(5, 'Amanida d''escarola', 2, 'Renteu i talleu l''escarola. Afegiu-hi el tomàquet, la ceba i la tonyina. Amaniu amb oli i sal.'),
(6, 'Cigrons saltats amb espinacs', 2, 'Sofregiu l''all amb oli, afegiu els espinacs i després els cigrons escorreguts. Salteu cinc minuts i poseu-hi pebre negre.');

INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity, unit_id) VALUES
(1, 5, 320, 7),
(1, 17, 500, 7),
(1, 9, 200, 7),
(1, 10, 150, 7),
(1, 4, 3, 1),
(1, 1, 1, 2),
(1, 3, 600, 9),
(2, 6, 250, 7),
(2, 19, 2, 12),
(2, 9, 300, 7),
(2, 11, 10, 7),
(2, 4, 4, 1),
(2, 1, 1, 2),
(3, 21, 6, 12),
(3, 8, 500, 7),
(3, 10, 150, 7),
(3, 4, 5, 1),
(3, 1, 1, 2),
(4, 18, 600, 7),
(4, 14, 300, 7),
(4, 8, 400, 7),
(4, 11, 10, 7),
(4, 4, 4, 1),
(4, 1, 1, 2),
(5, 12, 1, 12),
(5, 9, 200, 7),
(5, 10, 100, 7),
(5, 19, 1, 12),
(5, 4, 3, 1),
(5, 1, 1, 3),
(6, 20, 400, 7),
(6, 13, 300, 7),
(6, 11, 10, 7),
(6, 4, 3, 1),
(6, 2, 1, 3);

INSERT INTO pantry (ingredient_id, quantity, unit_id) VALUES
(4, 1, 8),
(1, 1, 11),
(5, 500, 7),
(6, 500, 7),
(21, 6, 12),
(22, 2, 8),
(9, 800, 7),
(10, 500, 7),
(7, 1, 12),
(20, 400, 7),
(24, 4, 12);

INSERT INTO menu_slots (day, slot, recipe_id, people) VALUES
(1, 3, 1, 4),
(1, 5, 5, 2),
(2, 3, 2, 2),
(2, 5, 3, 2),
(3, 3, 4, 2),
(4, 3, 6, 2),
(5, 3, 1, 2),
(6, 3, 2, 4);

INSERT INTO criteria (family, portions) VALUES
('Cereals', 10),
('Verdura', 7),
('Fruita', 7),
('Làctius', 7),
('Carn', 3),
('Peix', 3),
('Llegums', 2),
('Ous', 4);
