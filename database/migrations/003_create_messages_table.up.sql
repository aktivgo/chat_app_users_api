create table if not exists messages
(
    userId  int(11)      not null primary key,
    message varchar(1024) null,
    time    time null
);