-- Base de données fictives pour la démonstration

-- Roles____________________________________________________
INSERT INTO roles (name) 
VALUES ('admin'), ('preparation'), ('accueil');

-- Comptes____________________________________________________
INSERT INTO users (role_id, first_name, last_name, email, password) 
VALUES 
(1, 'Marie', 'Cooper', 'admin@wacdo.fr','$2y$12$Nd59LnuwJ/fQjhiF1ydDg.KdLC3jz.V1VGFU4eB512DpIyG8Lg7Ci' ),
(2, 'Laura', 'Romano', 'prep@wacdo.fr', '$2y$12$aLWeCewzQhA1T93yqi4eQel1ZGZMjdLWAnOohkZ1IeQbQd8T/tEta'),
(3, 'Jean', 'Gris', 'accueil@wacdo.fr','$2y$12$YiyMe5a0IGQjD9yyBwlzXuvVnZnRgQ9N1xe13GPnXq/Eb4NrB.DZK' );




-- Commandes____________________________________________________
DELETE FROM order_items;
DELETE FROM orders;


INSERT INTO orders(user_id, ticket_number, source, status, order_date, delivery_time, total_price) 
VALUES
-- À préparer
((SELECT id FROM users WHERE email = 'accueil@wacdo.fr'), 'A102', 'comptoir', 'to_prepare', NOW() - INTERVAL 5 MINUTE, NOW() + INTERVAL 5 MINUTE, 12.80),

--Préparées
(NULL, 'A103', 'borne', 'prepared', NOW() - INTERVAL 20 MINUTE, NOW() + INTERVAL 2 MINUTE, 11.50),
((SELECT id FROM users WHERE email = 'accueil@wacdo.fr'), 'A104', 'comptoir', 'prepared', NOW() - INTERVAL 25 MINUTE, NOW() + INTERVAL 1 MINUTE, 8.45),

-- Livrées
(NULL, 'A105', 'borne', 'delivered', NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY, 12.40),
((SELECT id FROM users WHERE email = 'accueil@wacdo.fr'), 'A106', 'telephone', 'delivered', NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY, 9.70);


INSERT INTO order_items (order_id, product_id, menu_id, name, quantity, price) VALUES
-- A102 : 12,80 
((SELECT id FROM orders WHERE ticket_number = 'A102'), NULL, (SELECT id FROM menus WHERE name = 'Menu Royal Cheese'), 'Menu Royal Cheese', 2, 6.40),

-- A103 : 11,50 €
((SELECT id FROM orders WHERE ticket_number = 'A103'), (SELECT id FROM products WHERE name = 'Nuggets x4'), NULL, 'Nuggets x4', 1, 4.20),
((SELECT id FROM orders WHERE ticket_number = 'A103'), (SELECT id FROM products WHERE name = 'Grande Frite'), NULL, 'Grande Frite', 1, 3.50),
((SELECT id FROM orders WHERE ticket_number = 'A103'), (SELECT id FROM products WHERE name = 'Coca Cola'), NULL, 'Coca Cola', 2, 1.90),

-- A104 : 8,45 €
((SELECT id FROM orders WHERE ticket_number = 'A104'), (SELECT id FROM products WHERE name = 'Big Mac'), NULL, 'Big Mac', 1, 6.00),
((SELECT id FROM orders WHERE ticket_number = 'A104'), (SELECT id FROM products WHERE name = 'Petite Frite'), NULL, 'Petite Frite', 1, 1.45),
((SELECT id FROM orders WHERE ticket_number = 'A104'), (SELECT id FROM products WHERE name = 'Eau'), NULL, 'Eau', 1, 1.00),

-- A105 : 12,40 €
((SELECT id FROM orders WHERE ticket_number = 'A105'), NULL, (SELECT id FROM menus WHERE name = 'Menu MC Chicken'), 'Menu MC Chicken', 1, 9.30),
((SELECT id FROM orders WHERE ticket_number = 'A105'), (SELECT id FROM products WHERE name = 'Cheesecake Fraise'), NULL, 'Cheesecake Fraise', 1, 3.10),

-- A106 : 9,70 €
((SELECT id FROM orders WHERE ticket_number = 'A106'), (SELECT id FROM products WHERE name = 'Cheeseburger'), NULL, 'Cheeseburger', 3, 2.60),
((SELECT id FROM orders WHERE ticket_number = 'A106'), (SELECT id FROM products WHERE name = 'Ice Tea Pêche'), NULL, 'Ice Tea Pêche', 1, 1.90);



-- Vérification d'ajout des commandes____________________________________________________
SELECT id, ticket_number, source, status, order_date, delivery_time, total_price, user_id
FROM orders
ORDER BY id DESC
LIMIT 3;