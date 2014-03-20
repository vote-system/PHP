<?php
require_once('vote_fns.php');

function db_connect() {
   $result = new mysqli('localhost', 'root', '841023', 'vote');
   if (!$result) {
	 $msg = "db connect error!";
	 $log->general($msg);
	 return DB_CONNECT_ERROR;
   } else {
     return $result;
   }
}
?>
