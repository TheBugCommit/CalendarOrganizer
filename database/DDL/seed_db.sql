START TRANSACTION;
insert into users values (
    null,'Gerard','g3casas@gmail.com', 'admin', 'Casas', 'Serarosls',
    0,  curdate(), null, 'O', 1, null ,sysdate(), sysdate()
);

insert into categories values (
    null,1, 'Esports'
);

insert into calendars values (
    null,1, 'Titulo',  curdate(),  curdate()
);

insert into targets values (
    1, 'gonzalo@gmail.com'
);

insert into calendar_user values (
    1, 1
);

insert into events values (
    null,1, 1, 1, 'tupak', 'si', 'si', 1, 'red', sysdate(),sysdate()
);

COMMIT;