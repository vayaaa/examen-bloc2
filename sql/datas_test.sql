-- Base de données fictives pour la démonstration

-- Roles
INSERT INTO roles (name) 
VALUES ('admin'), ('preparation'), ('accueil');

-- Comptes 
INSERT INTO users (role_id, first_name, last_name, email, password) 
VALUES 
(1, 'Marie', 'Cooper', 'admin@wacdo.fr','$2y$12$Nd59LnuwJ/fQjhiF1ydDg.KdLC3jz.V1VGFU4eB512DpIyG8Lg7Ci' ),
(2, 'Laura', 'Romano', 'prep@wacdo.fr', '$2y$12$aLWeCewzQhA1T93yqi4eQel1ZGZMjdLWAnOohkZ1IeQbQd8T/tEta'),
(3, 'Jean', 'Gris', 'accueil@wacdo.fr','$2y$12$YiyMe5a0IGQjD9yyBwlzXuvVnZnRgQ9N1xe13GPnXq/Eb4NrB.DZK' );