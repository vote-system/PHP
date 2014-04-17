<?php
require_once("vote_fns.php");

function update_badge($badge_name,$usrname,$action)
{
  $badge = query_badge("friend_badge",$usrname);
  if(!$badge){
	  return false;
  }else{
	if($action == ADD_BADGE)
		$badge +=1;
	else
		$badge -=1;

	$query = "update usrinfo set '".$badge_name."' = '".$badge."'
							where usrname = '".$usrname."'";
	$result = vote_db_query($query);
	if(!$result){
		return false;
	}else{
		return true;
	}
  }
  return false;
}

function query_badge($badge_name,$usrname)
{
  //save the usrid
  $query = "select '".$badge_name."' from usrinfo where usrname='".$usrname."'";
  $badge = vote_get_assoc($query);
  return $badge;
}

function get_user_badge($usrname)
{
	$friend_badge = query_badge("friend_badge",$usrname);
	$vote_badge = query_badge("vote_badge",$usrname);
	$total_badge = $friend_badge + $vote_badge;
	return $total_badge;
}

?>