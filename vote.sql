create table usrinfo  (
  usrid int not null auto_increment primary key,
  passwd char(40) not null,
  email varchar(100) not null,
  cookie char(40),
  device_token varchar(100),
  usrname varchar(16) not null,
  gender char(1),
  signature varchar(300),
  screen_name varchar(40),
  screen_name_pinyin varchar(40),
  original_head_imag_url varchar(100),
  medium_head_imag_url varchar(100),
  thumbnails_head_imag_url varchar(100),
  usr_info_timestamp int default -1,
  head_imag_timestamp int default -1,
  friend_badge int unsigned default 0,
  vote_badge int unsigned default 0,
  active int default 0,
  participant_vote_id mediumtext,
  vote_notification mediumtext,
  vote_delete_forever mediumtext
);

create table friend (
  id int not null auto_increment primary key,
  usrid int not null,
  friend_id int not null,
  friend_group int,
  friend_remark varchar(40)
);

create table stranger (
  id int not null auto_increment primary key,
  usrid int not null,
  stranger_id int not null,
  status int default 0
);

create table unread_message(
  id int not null auto_increment primary key,
  usrid int not null,
  message mediumtext
);

create table vote_info(
  vote_id int not null auto_increment primary key,
  organizer varchar(16) not null,
  title varchar(40) not null,
  start_time double not null,
  end_time double not null,
  basic_timestamp int not null,
  vote_timestamp int not null,
  category varchar(20),
  max_choice int unsigned default 1,
  participants mediumtext not null,
  options mediumtext not null,
  vote_detail mediumtext,
  private bool default 0,
  anonymous bool default 1,
  the_public bool default 0,
  description varchar(100),
  image_url varchar(50),
  participants_number int unsigned
);

