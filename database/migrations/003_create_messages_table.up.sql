create table if not exists messages
(
    id       int(11)        not null primary key auto_increment,
    userName varchar(255)   null,
    message  varchar(10000) null,
    time     varchar(255)   null
);