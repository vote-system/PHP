<?php
require_once("friend_fns.php");

$usrname=$_GET['usrname'];

if($usrname)
{
  $usrid = usrname_to_usrid($usrname);
	
  if(FRIEND_DEBUG){
	echo "usrid = " . $usrid;
  }

  $query = "select * from friend where usrid='".$usrid."'";
  $rows = vote_get_assoc($query);
  foreach($rows as $row) 
  {
	 $friend_id = $row['friend_id'];
	 if(FRIEND_DEBUG){
		echo "friend_id = " . $friend_id;
	 }
	 get_usrdetail($friendid);
  }
}

?>