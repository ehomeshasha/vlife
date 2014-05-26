<?php
error_reporting(E_ALL ^ E_NOTICE);
$username = $_GET['username'];
define('ROOT_PATH',str_replace('\\','/',substr(dirname(__FILE__),0,-9)));
define('UPLOAD_DIR', 'data/upload/');


$verifyToken = md5('unique_salt' . $_POST['timestamp']);

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
	
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = ROOT_PATH.UPLOAD_DIR.$username."/";
	if (!file_exists($targetPath)) {
		if(!mkdir($targetPath,0777)) {
			exit("File Created Failed");
		}
	}
	$filename = date('His').strtolower(random(16));
	
	
	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png','bmp'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	$targetFile = rtrim($targetPath,'/') . '/' . $filename.'.'.$fileParts['extension'];
	if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
		
		move_uploaded_file($tempFile,$targetFile);
		echo rtrim(UPLOAD_DIR.$username."/",'/') . '/' . $filename.'.'.$fileParts['extension'];
	} else {
		echo 'invalid filetype';
	}
}


function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}


?>