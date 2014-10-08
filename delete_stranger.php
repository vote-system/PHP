<?php

require_once("db_fns.php");
require_once("vote_fns.php");
require_once("usrinfo_fns.php");
//require_once("time.php");

header('Content-Type: application/json');

$usrname = $_POST['usrname'];
$stranger_name = $_POST['stranger_name'];
//$old_selections = $_POST['old_selections'];
//$new_selections = $_POST['new_selections'];

$usrid = usrname_to_usrid($usrname);
$stranger_id = usrname_to_usrid($stranger_name);

$query = "delete from stranger where usrid = '".$usrid."' and stranger_id = '".$stranger_id."'";
$ret = vote_db_query($query);

if($ret)
{
	$del_stranger['del_stranger'] = 1;
	$del_stranger['del_stranger_name'] = $stranger_name;
}	
else
{ 
	$del_stranger['del_stranger'] = 0;
	$del_stranger['del_stranger_name'] = $stranger_name;
}

echo json_encode($del_stranger);

?>