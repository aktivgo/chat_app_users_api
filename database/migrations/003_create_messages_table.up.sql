create table if not exists messages
(
    id       int(11)       not null primary key,
    userName varchar(255)  not null,
    message  varchar(1024) null
);