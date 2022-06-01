DELETE FROM targets;
DELETE FROM events;
DELETE FROM categories;
DELETE FROM calendars;
DELETE FROM users;
DELETE FROM roles;

ALTER TABLE categories AUTO_INCREMENT = 1;
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE calendars AUTO_INCREMENT = 1;
ALTER TABLE targets AUTO_INCREMENT = 1;
ALTER TABLE calendar_user AUTO_INCREMENT = 1;
ALTER TABLE roles AUTO_INCREMENT = 1;
ALTER TABLE events AUTO_INCREMENT = 1;


INSERT INTO roles VALUES (NULL, 'ADMIN');
INSERT INTO roles VALUES (NULL, 'CUSTOMER');

INSERT INTO users VALUES (NULL, 'Gerard','admin@admin.com', '$2a$10$8lMtqu7E3veYGcm1bHId5uvLKJTXBsVBcnFP5sMNVxOXsc2n4YM4O',
'Casas', 'Serarols', 0, date '2002-10-03', NULL, 'M', 1 ,193, NULL, NULL, now() ,now(), now());

INSERT INTO users VALUES (NULL, 'Albert','albert@gmail.com', '$2a$10$8lMtqu7E3veYGcm1bHId5uvLKJTXBsVBcnFP5sMNVxOXsc2n4YM4O',
'Casas', 'Montagut', 0, date '2002-11-23', NULL, 'M', 2 ,193, NULL, NULL, now() ,now(), now());

INSERT INTO users VALUES (NULL, 'Janma','janma@gmail.com', '$2a$10$8lMtqu7E3veYGcm1bHId5uvLKJTXBsVBcnFP5sMNVxOXsc2n4YM4O',
'Managut', 'Urlan', 0, date '2001-02-23', NULL, 'O', 2 ,193, NULL, NULL, now() ,now(), now());

insert into categories values (null, 2, 'Esports');
insert into categories values (null, 2, 'Cinema');
insert into categories values (null, 2, 'Beisbol');
insert into categories values (null, 2, 'Caminates');
insert into categories values (null, 3, 'Alcoholics anonims');
insert into categories values (null, 3, 'Atenció communitaria');
insert into categories values (null, 3, 'Grup ajuda drogadictes');

insert into calendars values (null,1, 'Esportiueig 2022', 'Calendari d''events esportius per l''estiu del 2022' ,NULL, '2022-06-01',  '2023-06-01');
insert into calendars values(null,2, 'Ajudes Communitaries', 'Calendari de les sessions d''ajuda del 2022' ,NULL, '2022-06-01',  '2023-06-01');

insert into targets values (1, 'servergerard@gmail.com', 0);
insert into targets values (1, 'g3casas@gmail.com', 0);
insert into targets values (2, 'g3casas@gmail.com', 0);
insert into targets values (2, 'servergerard@gmail.com', 0);

insert into calendar_user values (2, 1);
insert into calendar_user values (3, 1);

insert into events values (null,1, 1, 2, 'Partit Futbol', 'Partit de futobol igualada vs lleida bengemins',
     'Les Comes (Pavelló d''Hoquei Patins de Les Comes)', 0, '#ff0000', '2022-06-01 18:30:00','2022-06-01 20:30:00', NULL);

insert into events values (null,1, 4, 3, 'Caminata esgabetalls', 'Caminata pensda per tota la família per el magnific paisatge dels esgavetalls',
     'Vilanova del Camí', 0, '#ff0000', '2022-06-07 08:30:00','2022-06-07 10:30:00', NULL);

insert into events values (null,2, 6, 2, 'Reunió d''ajuda comunitaria', 'Reunió per ajuda generica a la communitat de veïns d''Igualada',
    'Plaça Calfont Igualada', 0, '#ff0000', '2022-06-03 17:30:00','2022-06-03 20:30:00', NULL);

insert into events values (null,2, 5, 3, 'Reunó d''ajuda a persones alcoholiques', 'Reunió on es posen en comú coses d''alcholics',
    'C/ Anglí, 54, 08017 Barcelona – España', 0, '#ff0000', '2022-06-23 08:30:00','2022-06-23 23:30:00', NULL);
