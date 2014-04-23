<?php 
//Multiline error log class 
// ersin güvenç 2008 eguvenc@gmail.com 
//For break use "\n" instead '\n' 

class vote_log { 
  // 
  //const USER_ERROR_DIR = '/alidata/log/httpd/vote/usr.log'; 
  //const GENERAL_ERROR_DIR = '/alidata/log/httpd/vote/gen.log'; 
  const USER_ERROR_DIR = 'usr.log'; 
  const GENERAL_ERROR_DIR = 'gen.log'; 

  /* 
   User Errors... 
  */ 
    public function user($msg,$username) 
    { 
    $date = date('d.m.Y h:i:s'); 
    $log = $msg."   |  Date:  ".$date."  |  User:  ".$username."\n"; 
    error_log($log, 3, self::USER_ERROR_DIR); 
    } 
    /* 
   General Errors... 
  */ 
    public function general($msg) 
    { 
    $date = date('d.m.Y h:i:s'); 
    $log = $msg."   |  Date:  ".$date."\n"; 
    error_log($log, 3, self::GENERAL_ERROR_DIR); 
    } 
} 
?> 
