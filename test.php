<?php
//require_once("friend_fns.php");
//require_once("vote_fns.php");

$usrid = 2;
$stranger_id = 4;
//phpinfo();


$line = "insert into stranger values
                           (NULL, '".$usrid."', '".$stranger_id."',NULL)";
insert_item($line);


function db_connect() {
   $result = new mysqli('localhost', 'root', '841023', 'vote');
   if (!$result) {
	 echo "DB_CONNECT_ERROR\n";
   } else {
     return $result;
   }
}

function insert_item($line)
{
  $conn = db_connect();
  if(!$conn)
  {
	echo "DB_CONNECT_ERROR\n";
  }
  $result = $conn->query($line);
  if (!$result) {
	echo "DB_INSERT_ERROR\n";
  }
  return true;
}

?>