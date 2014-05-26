<?php
require_once("vote_fns.php");

//$name = "dingyi";
//update_friend_badge($name,ADD_BADGE);
//$usrinfo = query_badge($usrname);
//$friend_badge = $usrinfo['friend_badge'];
//echo $friend_badge;

function update_friend_badge($usrname,$action)
{
  $usrinfo = query_badge($usrname);
  $friend_badge = $usrinfo['friend_badge'];
  echo $friend_badge;

	if($action == ADD_BADGE){
		$friend_badge +=1;
	}else{
		$friend_badge -=1;
	}
	echo $usrname;
	$query = "update usrinfo set friend_badge = '".$friend_badge."'
							where usrname = '".$usrname."'";
	$result = vote_db_query($query);
	echo $result;
	if(!$result){
		return false;
	}else{
		return true;
	}
}

function query_badge($usrname)
{
  //save the usrid
  $query = "select * from usrinfo where usrname='".$usrname."'";
  $usrinfo = vote_get_array($query);
  return $usrinfo;
}

/*
function query_badge($usrname)
{
  //save the usrid
  $query = "select * from usrinfo where usrname='".$usrname."'";
  $badge = vote_get_array($query);
  return $badge;
}

*/
function get_user_badge($usrname)
{	
	$badge = query_badge($usrname);
	$total_badge = $badge['friend_badge'] +  $badge['usr_vote_badge'];
	return $total_badge;
}

?>

