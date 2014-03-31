<?php
// this file's duty include as followings:
//1.handle head image upload(only support 100*100 at the present)
//2.resize the 100*100 image to 50*50(named medium-*) and 20*20(named tiny-*)
//3.if resize success, write the image URL and update time to the database
require_once('vote_fns.php');
//error_reporting(EALL);
$max_size = 100000;

define('FULL_IMAG',1);
define('MEDIUM_IMAG',2);
define('TINY_IMAG',3);

$username=$_POST['username'];

$upload_dir = "/vote/upload/$username/";

if(!is_dir($upload_dir))
{
	$oldumask = umask(0);
	$res = mkdir($upload_dir, 0777);
	if(!$res)
	{
		$msg = "mkdir error for $upload_dir\n";
		echo "mkdir error for $upload_dir\n";
		//error_log($msg,3,"/alidate/log");
	}
	umask($oldumask);
}
//echo "$_FILES['usrfile']['name']";
//echo $_FILES['userfile']['name'];

if ( (!($_FILES['userfile']['name'])) &&
	 ($_FILES['userfile']['name'] =='none')) {
	
  echo "<p>Problem: ".$_FILES['userfile']['name'].
       " is null \n";
  exit;
}
if ($_FILES['userfile']['size']==0) {
  echo "<p>Problem: ".$_FILES['userfile']['name'].
       " is zero length";
  exit; 
}

if ($_FILES['userfile']['size']>$max_size) {
  echo "<p>Problem: ".$_FILES['userfile']['name']." is over "
        .$max_size." bytes";
  exit;
}

// we would like to check that the uploaded image is an image
// if getimagesize() can work out its size, it probably is.
if(!getimagesize($_FILES['userfile']['tmp_name'])) {
  echo "<p>Problem: ".$_FILES['userfile']['name'].
	   " is corrupt, or not a gif, jpeg or png.</p>";
  exit;
}

if (!is_uploaded_file($_FILES['userfile']['tmp_name'])) {
  // possible file upload attack detected
  echo "<p>Something funny happening with "
	   .$_FILES['userfile']['name'].", not uploading.";
  exit;
}
else
{
	
}

//echo $_FILES['userfile']['name'] . "\n";
//echo basename($_FILES['userfile']['name']) . "\n";

// determine the image type
//echo $_FILES['userfile']['type'];
//if ($_FILES['userfile']['type'] == "image/gif") { $ext = ".gif"; }
//if ($_FILES['userfile']['type'] == "image/jepg") { $ext = ".jpg"; }
//if ($_FILES['userfile']['type'] == "image/png") { $ext = ".png"; }
//echo $ext . "\n";
list($width, $height, $type) = getimagesize($_FILES['userfile']['tmp_name']);
//echo "type=" . $type . "\n";
switch ($type) 
{
	case 1: $ext = ".gif"; break;
	case 2: $ext = ".jpg"; break;
	case 3: $ext = ".png"; break;
}
//echo $ext;
$upload_file = $upload_dir . $username . $ext;
//echo $upload_file;
if(!move_uploaded_file($_FILES['userfile']['tmp_name'],
				   $upload_file))
{
	echo 'Problem: Could not move file to destination directory';
	exit;
}
else
{
	update_head_imag_db(FULL_IMAG,$upload_file);
	list($width, $height) = getimagesize($upload_file);
	if(($width==$height) && ($width == 100))
	{
		for($i=2;i<4;i++)
		{
			if(i == MEDIUM_IMAG)
			{
				$new_size = 50;
				$new_name = "medium-" . basename($upload_file); 
			}
			if(i == TINY_IMAG)
			{
				$new_size = 20;
				$new_name = "tiny-" . basename($upload_file); 
			}
			$ret = resize_image($upload_file,$newsize,$upload_dir,$new_name);
			if(!ret)
			{
				$url = $upload_dir . $new_name;
				update_head_imag_db(MEDIUM_IMAG,$url)
			}

		}	
	}
}

  
 
function resize_image($image_name,$newsize,$new_dir, $newfile_name) {
//echo "image_name=" . $image_name . "\n";
//echo "$newsize=" . $newsize. "\n";
//echo "new_file_name=" . $newfile_name . "\n";	  
list($width, $height, $type) = getimagesize($image_name);
thumb = imagecreatetruecolor($newsize, $newsize);
//echo "type=" . $type . "\n";
switch ($type) 
{
	case 1: $source = imagecreatefromgif($image_name); break;
	case 2: $source = imagecreatefromjpeg($image_name); break;
	case 3: $source = imagecreatefrompng($image_name); break;
	default:  $source = imagecreatefromjpeg($image_name);
}


// Resize
imagecopyresized($thumb, $source, 0, 0, 0, 0, $newsize, $newsize, $width, $height);

$output_name = $new_dir . $newfile_name;
// Output
imagejpeg($thumb,$output_name);
imagedestroy($thumb);
}


// part3: save the image URL in the database
// if (file existed)
//		write to database
update_head_imag_db($type,$url)
{
  $date = new DateTime();
  $timestamp = $date->getTimestamp();
  
  $conn = db_connect();
  if(!$conn){
	//$msg = "Function cookie_insert,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }

  // check if username is unique
  $result = $conn->query("select * from user
                         where username='".$username."'");
  if (!$result) {
    //$msg = "Function cookie_insert,db query failed!";
	//$auth_log->general($msg);
	return DB_QUERY_ERROR;
  }

  if ($result->num_rows>0) {

	switch($type)
    {
		case FULL_IMAG: $item = "head_imag_url"; break;
		case MEDIUM_IMAG: $item = "medium_imag_url"; break;
		case TINY_IMAG: $item = "tiny_imag_url"; break;
	}
	$result = $conn->query("update user_detail
							set $item = '".$url."', image_timestamp = '".$timestamp."'
							where username = '".$username."'");
	if (!$result) {
		//$msg = "Function cookie_insert,db insert cookie={$cookie} failed";
		//$auth_log->general($msg);
		return DB_INSERT_ERROR;
	 }
	 else{
		return COOKIE_SAVE_SUCCESS;
	 }		
  }
}

?>
