/* =========================================================
   CATEGORIES
========================================================= */

INSERT INTO categories (id, name, description) VALUES
(1,'Guitares électriques','Guitares électriques solid body'),
(2,'Guitares acoustiques','Guitares folk et dreadnought'),
(3,'Amplificateurs','Amplificateurs guitare à lampes et transistors'),
(4,'Metal / High Gain','Instruments adaptés aux styles metal'),
(5,'Vintage','Modèles emblématiques et rééditions'),
(6,'Basse','Instruments basse électrique'),
(7,'Blues','Instruments adaptés au blues'),
(8,'Hard Rock','Instruments adaptés au hard rock'),
(9,'Pop Rock','Instruments adaptés au pop rock'),
(10,'Grunge','Instruments adaptés au grunge'),
(11,'Résonateur','Guitares à résonateur métal');


/* =========================================================
   PRODUCTS
========================================================= */

INSERT INTO products 
(id, created_at, name, short_description, long_description, img_file_path, price_htva, is_available, stock, display_priority) 
VALUES

(1,'2020-01-10 10:00:00','Fender Stratocaster',
'Corps aulne, micros V-Mod II, grande polyvalence blues/pop.',
'La Stratocaster American Ultra II combine tradition et modernité. Son corps en aulne offre équilibre et résonance, tandis que les micros V-Mod II délivrent des sons clairs précis et dynamiques. Le manche confortable favorise un jeu fluide sur toute la touche. Polyvalente, elle excelle en blues, pop, funk ou rock léger. Vibrato stable, finition soignée et grande expressivité en font un instrument fiable pour la scène comme pour le studio.',
'fender-american-ultra-ii-stratocaster-eb-texas-tea_1_GIT0061889-003.webp',
1499.00, TRUE, 12,1),

(2,'2020-02-12 11:00:00','Fender Telecaster',
'Twang précis, idéale country et rock.',
'Modèle emblématique, la Telecaster séduit par son attaque franche et son twang brillant. Son micro chevalet incisif traverse le mix avec clarté, tandis que le micro manche apporte rondeur et chaleur. Robuste et simple d’utilisation, elle convient parfaitement à la country, au rock et au blues. Manche confortable, stabilité d’accordage et grande réactivité en font un choix sûr pour les musiciens recherchant authenticité et efficacité.',
'fender-player-ii-telecaster-mn-butterscotch-blonde_1_GIT0061904-010.webp',
1299.00, TRUE,8,2),

(3,'2020-03-15 09:00:00','Fender Jaguar',
'Indémodable, idéale pour le grunge.',
'La Jaguar offre un caractère unique avec ses micros simples précis et son vibrato flottant expressif. Son diapason plus court favorise le confort et facilite les bends. Son grain brillant et légèrement mordant la rend idéale pour le surf, l’indie et le grunge. Son électronique polyvalente permet d’explorer différentes textures sonores. Un modèle au style affirmé et à la personnalité sonore marquée.',
'fender-player-ii-jaguar-rw-3-color-sunburst_1_GIT0061907-001.webp',
699.00, TRUE,15,5),

(4,'2020-04-10 14:00:00','Gibson SG Standard',
'Son puissant hard rock.',
'La Gibson SG Standard se distingue par son corps léger en acajou et ses doubles humbuckers puissants. Elle délivre un son riche, chaud et incisif, parfait pour le hard rock et le classic rock. Son accès facilité aux aigus favorise les solos expressifs. Équilibrée et confortable, elle combine sustain généreux et attaque précise pour une présence sonore affirmée.',
'gibson-sg-standard-61-vintage-cherry_1_GIT0062798-000.webp',
1399.00, TRUE,6,3),

(5,'2020-05-05 15:00:00','Gibson Les Paul Standard 60s',
'Sustain et chaleur vintage.',
'La Les Paul Standard 60s associe corps en acajou et table érable pour un sustain exceptionnel. Ses humbuckers délivrent un son dense, chaud et harmonique, idéal pour le rock, le blues et les solos chantants. Le manche profil 60s assure confort et précision. Instrument iconique, elle offre puissance, profondeur et caractère vintage inimitable.',
'gibson-les-paul-standard-60s-honey-amber_1_GIT0062796-002.webp',
2499.00, TRUE,4,4),

(6,'2020-06-01 10:00:00','Ibanez RG550',
'Manche Wizard, parfaite metal.',
'L’Ibanez RG550 est conçue pour la vitesse et la précision. Son manche Wizard ultra-fin facilite les techniques rapides et le shred. Les micros puissants assurent clarté et agressivité en saturation. Le vibrato stable permet des effets expressifs sans perte d’accordage. Idéale pour le metal et le rock technique, elle allie confort moderne et performance.',
'ibanez-genesis-rg550-bk-black_1_GIT0062189-000.webp',
999.00, TRUE,10,6),

(7,'2020-06-20 11:00:00','Martin D-28',
'Projection puissante.',
'La Martin D-28 est une référence des guitares acoustiques dreadnought. Sa projection puissante et son équilibre tonal en font un choix privilégié pour le jeu en picking comme en strumming. Les basses sont profondes, les médiums définis et les aigus clairs. Construction soignée et résonance ample garantissent richesse harmonique et dynamique exceptionnelle.',
'martin-guitars-d-28-new_1_GIT0062669-000.webp',
2599.00, TRUE,3,7),

(8,'2020-07-18 13:00:00','Taylor 214ce',
'Pan coupé, polyvalente.',
'La Taylor 214ce est une folk électro-acoustique polyvalente dotée d’un pan coupé facilitant l’accès aux aigus. Son système électronique intégré assure une amplification fidèle. Sonorité équilibrée, confort de jeu et finition soignée en font un excellent choix pour la scène et le studio. Adaptée aux auteurs-compositeurs comme aux musiciens polyvalents.',
'taylor-214ce_1_GIT0061604-000.webp',
899.00, TRUE,9,8),

(9,'2020-08-10 16:00:00','Fender Twin Reverb',
'Clair légendaire.',
'Le Twin Reverb 85W est célèbre pour son son clair ample et cristallin. Sa réserve de puissance importante garantit une grande dynamique, idéale pour les pédales d’effets. Les réverbérations et vibratos intégrés enrichissent le signal avec élégance. Parfait pour le rock, la country ou le jazz nécessitant précision et headroom conséquent.',
'fender-65-twin-reverb-combo-_1_GIT0000081-000.webp',
1699.00, TRUE,4,9),

(10,'2020-09-12 12:00:00','Marshall JTM45',
'Crunch british.',
'Le Marshall JTM45 délivre un crunch britannique chaud et organique. Sa conception tout lampes offre une compression naturelle et une réponse dynamique expressive. Idéal pour le blues rock et le classic rock, il réagit parfaitement au volume de la guitare. Un ampli de caractère, riche en harmoniques et en personnalité sonore.',
'marshall-jtm45-head-2245-_1_GIT0009522-000.webp',
1799.00, TRUE,3,10),

(11,'2020-10-02 09:30:00','Orange Micro Terror',
'Saturation moderne.',
'Compact mais puissant, l’Orange Micro Terror combine préampli à lampe et étage de puissance transistor. Il produit une saturation moderne, dense et agressive malgré son format réduit. Idéal pour la maison ou les petits concerts, il conserve la signature sonore Orange avec médiums prononcés et caractère affirmé.',
'orange-micro-terror-_1_GIT0023724-000.webp',
139.00, TRUE,2,11),

(12,'2021-01-15 10:00:00','Vox AC30',
'Chime britannique mythique.',
'Le Vox AC30 est reconnu pour son chime brillant et sa dynamique exceptionnelle. Ses lampes délivrent des sons clairs scintillants et un crunch progressif musical. Idéal pour la pop, le rock indépendant et le brit rock, il offre une excellente réponse aux nuances de jeu et aux pédales.',
'vox-ac30-c2-combo-_1_GIT0018374-000.webp',
1199.00, TRUE,5,12),

(13,'2021-02-10 11:00:00','Vox AC15 head',
'Parfait studio.',
'Le Vox AC15 propose le célèbre son Vox dans un format plus compact. Ses 15W tout lampes offrent un équilibre idéal entre puissance et maîtrise du volume. Sons clairs brillants, crunch chaleureux et excellente réactivité en font un ampli particulièrement adapté au studio et aux petites scènes.',
'vox-ac15-custom-head-limited-edition-white-bronco_1_GIT0040923-000.webp',
799.00, TRUE,6,13),

(14,'2021-03-05 14:00:00','Fender Precision Bass',
'Son rond et puissant.',
'La Precision Bass 4 cordes est une référence incontournable. Son micro split-coil délivre un son rond, profond et précis, parfaitement adapté au rock, à la pop et au funk. Construction robuste, manche confortable et grande stabilité d’accordage garantissent fiabilité et efficacité en toutes situations.',
'fender-american-vintage-ii-1960-precision-bass-rw-olympic-white_1_BAS0012908-000.webp',
749.00, TRUE,10,14),

(15,'2021-03-20 14:00:00','Fender Jazz Bass V',
'Grande polyvalence.',
'La Jazz Bass V offre cinq cordes pour une plage étendue et une grande polyvalence. Ses deux micros simples permettent d’ajuster précisément le caractère sonore, du rond chaleureux au son plus mordant. Confortable et équilibrée, elle s’adapte aux styles modernes, jazz, funk ou rock.',
'fender-american-professional-ii-jazz-bass-mn-mystic-surf-green-_1_BAS0011244-000.webp',
899.00, TRUE,7,15),

(16,'2021-04-15 15:00:00','Gibson J-45 Studio',
'Chaleur roots.',
'La Gibson J-45 Studio propose une sonorité chaleureuse et équilibrée grâce à sa construction en acajou. Les basses sont présentes sans excès, les médiums riches et les aigus doux. Idéale pour l’accompagnement vocal et le folk, elle offre confort de jeu et projection maîtrisée.',
'gibson-j-45-standard-vs_1_GIT0046968-000.webp',
1499.00, TRUE,4,16),

(17,'2021-05-10 12:00:00','Gibson Hummingbird',
'Projection riche.',
'La Gibson Hummingbird se distingue par son esthétique iconique et sa projection ample. Son timbre riche et équilibré convient parfaitement au folk et au rock acoustique. Les accords résonnent avec profondeur tandis que les notes individuelles restent définies. Un instrument expressif au caractère affirmé.',
'gibson-1960-hummingbird-hcs-fixed-bridge_1_GIT0051872-000.webp',
2299.00, TRUE,3,17),

(18,'2021-06-01 11:00:00','Gretsch G9200 Boxcar',
'Parfaite slide blues.',
'La Gretsch G9200 Boxcar est une guitare résonateur au caractère métallique distinctif. Son cône en aluminium produit un son brillant, puissant et idéal pour le slide blues. Construction robuste et projection directe en font un instrument expressif pour le blues traditionnel et le roots.',
'gretsch-g9200-boxcar-round-neck-resonator-natural-_1_GIT0044292-000.webp',
599.00, TRUE,5,18),

(19,'2021-06-15 11:00:00','Red Hill Resonator',
'Delta blues authentique.',
'La Red Hill Resonator délivre un timbre brut et authentique typique du delta blues. Sa construction en acier accentue la brillance et la projection. Idéale pour le slide, elle offre une réponse immédiate et un caractère vintage marqué, parfait pour les styles roots et traditionnels.',
'red-hill-resonator-guitar-black_1_GIT0050864-000.webp',
699.00, TRUE,4,19),

(20,'2021-07-01 09:00:00','Ibanez RGA42FM',
'Micros haute sortie.',
'L’Ibanez RGA42FM propose une lutherie moderne et des micros haute sortie adaptés au metal et au rock puissant. Son manche confortable facilite le jeu rapide et précis. Les graves sont serrés, les aigus définis et la saturation dense. Un excellent choix pour les guitaristes recherchant performance et accessibilité.',
'ibanez-standard-rga42fm-blf-blue-lagoon-flat_1_GIT0040964-000.webp',
449.00, TRUE,20,20),

(21,'2021-08-01 10:00:00','Ampeg SVT Classic',
'Tête basse légendaire tout lampes 300W, référence rock et hard rock.',
'L’Ampeg SVT Classic est une tête basse tout lampes de 300W réputée pour sa puissance et sa profondeur. Son grain chaud, riche en harmoniques, traverse le mix avec autorité. Idéale pour le rock et le hard rock, elle offre une dynamique impressionnante et une réserve de puissance adaptée aux grandes scènes.',
'ampeg-svt-classic-head-_1_BAS0000425-000.webp',
2199.00, TRUE, 4, 21);


/* =========================================================
   PRODUCT_CATEGORY
========================================================= */

INSERT INTO product_category VALUES
(1,1),(1,7),(1,9),
(2,1),(2,7),(2,9),
(3,1),(3,10),
(4,1),(4,8),
(5,1),(5,5),(5,8),
(6,1),(6,4),(6,8),
(7,2),(7,5),
(8,2),(8,9),
(9,3),(9,7),
(10,3),(10,8),
(11,3),(11,4),
(12,3),(12,9),
(13,3),
(14,6),(14,7),
(15,6),(15,8),
(16,2),(16,7),
(17,2),(17,9),
(18,2),(18,11),(18,7),
(19,2),(19,11),(19,7),
(20,1),(20,4),
(21,3),(21,6),(21,8);


/* =========================================================
   CUSTOMERS
========================================================= */

INSERT INTO customers (id, created_at, email, first_name, last_name) VALUES
(1,'2020-01-05 09:00:00','jimi.hendrix@email.com','Jimi','Hendrix'),
(2,'2020-02-07 10:00:00','jimmy.page@email.com','Jimmy','Page'),
(3,'2020-03-10 11:00:00','eric.clapton@email.com','Eric','Clapton'),
(4,'2020-04-11 12:00:00','david.gilmour@email.com','David','Gilmour'),
(5,'2020-05-01 13:00:00','angus.young@email.com','Angus','Young'),
(6,'2020-06-03 14:00:00','stevie.vaughan@email.com','Stevie Ray','Vaughan'),
(7,'2020-07-04 15:00:00','kirk.hammett@email.com','Kirk','Hammett'),
(8,'2020-08-06 16:00:00','kurt.cobain@email.com','Kurt','Cobain'),
(9,'2020-09-08 17:00:00','paul.mccartney@email.com','Paul','McCartney'),
(10,'2020-10-09 18:00:00','chuck.berry@email.com','Chuck','Berry'),
(11,'2020-09-08 17:00:00','sting.sumner@email.com','Sting','Sumner');


/* =========================================================
   ADDRESSES
========================================================= */

INSERT INTO addresses (id, customer_id, street, zip_code, city, country) VALUES
(1,1,'12 rue des Lilas','75015','Paris','France'),
(2,2,'8 avenue Victor Hugo','69002','Lyon','France'),
(3,3,'45 boulevard National','13001','Marseille','France'),
(4,4,'22 rue Sainte-Catherine','33000','Bordeaux','France'),
(5,5,'10 rue du Molinel','59000','Lille','France'),
(6,6,'3 rue Oberkampf','75011','Paris','France'),
(7,7,'15 quai des Chartrons','33000','Bordeaux','France'),
(8,8,'6 rue Foch','34000','Montpellier','France'),
(9,9,'18 rue de Metz','31000','Toulouse','France'),
(10,10,'9 rue d’Italie','06000','Nice','France');


/* =========================================================
   ORDERS
========================================================= */

INSERT INTO orders (id, created_at, customer_id, delivery_address_id, total_htva, total_tvac) VALUES
(1,'2021-01-10 10:00:00',1,1,3248.00,3920.48),
(2,'2021-03-15 11:00:00',1,1,1799.00,2177.79),
(3,'2021-02-05 12:00:00',2,2,4298.00,5200.58),
(4,'2021-04-20 14:00:00',2,2,1399.00,1694.79),
(5,'2021-01-20 09:00:00',3,3,3198.00,3874.58),
(6,'2021-05-05 10:00:00',3,3,599.00,724.79),
(7,'2021-02-15 15:00:00',4,4,4198.00,5081.58),
(8,'2021-03-10 16:00:00',5,5,3198.00,3874.58),
(9,'2021-06-01 10:00:00',5,5,1799.00,2177.79),
(10,'2021-04-01 11:00:00',6,6,3198.00,3874.58),
(11,'2021-05-15 12:00:00',7,7,2998.00,3638.58),
(12,'2021-06-10 09:00:00',8,8,699.00,847.79),
(13,'2021-07-15 10:00:00',8,8,1699.00,2060.79),
(14,'2021-03-05 13:00:00',9,9,2948.00,3575.08),
(15,'2021-05-10 14:00:00',10,10,3898.00,4726.58);


/* =========================================================
   ORDER_LINES
========================================================= */

INSERT INTO order_lines (order_id, product_id, quantity, unit_price_htva, line_total_htva) VALUES
(1,1,1,1499.00,1499.00),
(1,10,1,1799.00,1799.00),
(2,9,1,1699.00,1699.00),
(3,5,1,2499.00,2499.00),
(3,10,1,1799.00,1799.00),
(4,4,1,1399.00,1399.00),
(5,1,1,1499.00,1499.00),
(5,9,1,1699.00,1699.00),
(6,18,1,599.00,599.00),
(7,1,1,1499.00,1499.00),
(7,12,1,1199.00,1199.00),
(7,10,1,1499.00,1499.00),
(8,4,1,1399.00,1399.00),
(8,10,1,1799.00,1799.00),
(9,10,1,1799.00,1799.00),
(10,1,1,1499.00,1499.00),
(10,9,1,1699.00,1699.00),
(11,6,1,999.00,999.00),
(11,11,1,1999.00,1999.00),
(12,3,1,699.00,699.00),
(13,9,1,1699.00,1699.00),
(14,14,1,749.00,749.00),
(14,21,1,2199.00,2199.00),
(15,5,1,2499.00,2499.00),
(15,12,1,1199.00,1199.00),
(15,10,1,1799.00,1799.00);
