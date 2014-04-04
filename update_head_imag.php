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

//definition for image type returned by getimagsize()
define('GIF',1);
define('JPG',2);
define('PNG',3);
define('SWF',4);
define('PSD',5);
define('BMP',6);
define('TIFF',7);

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
    //echo "<p>Problem: ".$_FILES['userfile']['name'].
    //   " is null \n";	
    $upload_file_resp['up_code'] = FILE_NAME_NULL; 
	header('Content-Type: application/json');
	echo json_encode($upload_file_resp);
    exit;
}
if ($_FILES['userfile']['size']==0) {
  //echo "<p>Problem: ".$_FILES['userfile']['name'].
  //     " is zero length";
  $upload_file_resp['up_code'] = FILE_SIZE_NULL; 
  header('Content-Type: application/json');
  echo json_encode($upload_file_resp);
  exit; 
}

if ($_FILES['userfile']['size']>$max_size) {
  //echo "<p>Problem: ".$_FILES['userfile']['name']." is over "
  //      .$max_size." bytes";
  $upload_file_resp['up_code'] = FILE_SIZE_OVER; 
  header('Content-Type: application/json');
  echo json_encode($upload_file_resp);
  exit;
}

// we would like to check that the uploaded image is an image
// if getimagesize() can work out its size, it probably is.
if(!getimagesize($_FILES['userfile']['tmp_name'])) {
  //echo "<p>Problem: ".$_FILES['userfile']['name'].
  //	   " is corrupt, or not a gif, jpeg or png.</p>";
  $upload_file_resp['up_code'] = UPLOAD_CORRUPT; 
  header('Content-Type: application/json');
  echo json_encode($upload_file_resp);
  exit;
}

if (!is_uploaded_file($_FILES['userfile']['tmp_name'])) {
  // possible file upload attack detected
  //echo "<p>Something funny happening with "
  //	   .$_FILES['userfile']['name'].", not uploading.";
  $upload_file_resp['up_code'] = UPLOAD_CORRUPT; 
  header('Content-Type: application/json');
  echo json_encode($upload_file_resp);
  exit;
}

//echo $_FILES['userfile']['name'] . "\n";
//echo basename($_FILES['userfile']['name']) . "\n";

// determine the image type
list($width, $height, $type) = getimagesize($_FILES['userfile']['tmp_name']);
//echo "type=" . $type . "\n";
switch ($type) 
{
	case GIF: $ext = ".gif"; break;
	case JPG: $ext = ".jpg"; break;
	case PNG: $ext = ".png"; break;
	default: $ext = ".jpg";
}
//echo $ext;
$upload_file = $upload_dir . $username . $ext;
//echo $upload_file;
if(!move_uploaded_file($_FILES['userfile']['tmp_name'],
				   $upload_file))
{
	//echo 'Problem: Could not move file to destination directory';
	$upload_file_resp['up_code'] = MV_FILE_FAIL; 
    header('Content-Type: application/json');
    echo json_encode($upload_file_resp);
	exit;
}
else
{
	$ret = update_head_imag_db(FULL_IMAG,$upload_file);
	if($ret != UPDATE_IMAGE_SUCC )
	{
		$upload_file_resp['up_code'] = UPDATE_IMAGE_FAIL; 
		header('Content-Type: application/json');
		echo json_encode($upload_file_resp);
		exit;
	}
	
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
				$upload_file_resp['up_code'] = RESIZE_IMAGE_FAIL; 
				header('Content-Type: application/json');
				echo json_encode($upload_file_resp);
			}
			else
			{
				$url = $upload_dir . $new_name;
				update_head_imag_db(MEDIUM_IMAG,$url)
				if($ret != UPDATE_IMAGE_SUCC )
				{
					$upload_file_resp['up_code'] = UPDATE_IMAGE_FAIL; 
					header('Content-Type: application/json');
					echo json_encode($upload_file_resp);
					exit;
				}
			}
		}
	}
	$upload_file_resp['up_code'] = UPDATE_IMAGE_SUCC; 
	header('Content-Type: application/json');
	echo json_encode($upload_file_resp);
}

function resize_image($image_name,$newsize,$new_dir, $newfile_name) {
//echo "image_name=" . $image_name . "\n";
//echo "$newsize=" . $newsize. "\n";
//echo "new_file_name=" . $newfile_name . "\n";	  
list($width, $height, $type) = getimagesize($image_name);
thumb = imagecreatetruecolor($newsize, $newsize);
if(!thumb)
	return false;
//echo "type=" . $type . "\n";
switch ($type) 
{
	case 1: $source = imagecreatefromgif($image_name); break;
	case 2: $source = imagecreatefromjpeg($image_name); break;
	case 3: $source = imagecreatefrompng($image_name); break;
	default:  $source = imagecreatefromjpeg($image_name);
}

// Resize
$ret = imagecopyresized($thumb, $source, 0, 0, 0, 0, $newsize, $newsize, $width, $height);
if(!$ret)
	return false;

$output_name = $new_dir . $newfile_name;
// Output
$ret = imagejpeg($thumb,$output_name);
if(!$ret)
	return false;
$ret = imagedestroy($thumb);
if(!$ret)
	return false;
else
	return true;
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
		case FULL_IMAG: $item = "original_head_image_url"; break;
		case MEDIUM_IMAG: $item = "medium_head_image_url"; break;
		case TINY_IMAG: $item = "thumbnails_head_image_url"; break;
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
		return UPDATE_IMAGE_SUCC;
	 }		
  }
}

?>
