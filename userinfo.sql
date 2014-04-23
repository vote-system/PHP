create database vote;
use vote;

create table user  (
  username varchar(16) primary key,
  passwd char(40) not null,
  email varchar(100) not null,
  cookie char(40)
);

create table user_detail(
  username varchar(16) primary key,
  gender varchar(20),
  signature varchar(100),
  screen_name varchar(40),
  screen_name_pinyin varchar(40),
  head_imag_url varchar(40)
);

grant insert, update, delete,select
on cwork.*
to bo@localhost identified by 'password';
