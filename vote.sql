create table usrinfo  (
  usrid int not null auto_increment primary key,
  passwd char(40) not null,
  email varchar(100) not null,
  cookie char(40),
  device_token varchar(100),
  usrname varchar(16) not null,
  gender char(1),
  signature varchar(100),
  screen_name varchar(40),
  screen_name_pinyin varchar(40),
  original_head_imag_url varchar(40),
  medium_head_imag_url varchar(40),
  thumbnails_head_imag_url varchar(40),
  usr_info_timestamp int default -1,
  head_image_timestamp int default -1
);

create table friend (
  id int not null auto_increment primary key,
  usrid int not null,
  friendid int not null,
  friend_group int,
  friend_remark varchar(40)
);