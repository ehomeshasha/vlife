<?php
class upload {
	
	public function __construct() {
		
	}
	
	public function upload_files() {
		
		global $_G;
		$curdate = date("Y-m-d", $_G['timestamp']);
		print_r($_FILES['file_upload']);
		$filepath = "";
		$uploadDir = ROOT_PATH.UPLOAD_DIR.$_G['username']."/";
		$filesname = $_FILES['file_upload']['name'];
		$filetypeerror = $filesizeerror = $filesmoveerror = array();
		for ($i = 0; $i < count($filesname); $i++) {
			$ext = addslashes(strtolower(substr(strrchr($filesname[$i], '.'), 1, 10)));
			if(!in_array($ext,array('jpg','jpeg','png','gif','bmp','pdf','doc','docx','xls','csv'))) {
				array_merge($filetypeerror, array($filesname[$i]));
				continue;	
			}
			if($_FILES['file_upload']['size'][$i] > 10000000) {
				array_merge($filesizeerror, array($filesname[$i]));
				continue;
			}
			$fPath = $_G['username']."-".$curdate."-".random(8).$ext;
			if(!move_uploaded_file($_FILES['file_upload']['tmp_name'][$i], $uploadDir . $fPath)) {
				array_merge($filesmoveerror, array($filesname[$i]));
				continue;
			}
			$filepath .= $filesname[$i]."^".$fPath.",";
			
		}
		$filepath = substr($filepath,0,-1);
		$_G['upload']['error'] = array(
			'filetype' => $filetypeerror,
			'filesize' => $filesizeerror,
			'filemove' => $filesmoveerror
		);
		$_G['upload']['filepath'] = $filepath;
	}
	
	public function getresult() {
		global $_G;
		$message = "";
		$code = "1";
		if(!empty($_G['upload']['error']['filetype']) || !empty($_G['upload']['error']['filesize']) || !empty($_G['upload']['error']['filemove'])) {
			if(!empty($_G['upload']['error']['filetype'])) {
				$typeerrorcount = count($_G['upload']['error']['filetype']);
				$message .= "<h4>文件类型错误：</h4><p>数量：{$typeerrorcount} 文件名：".implode(",", $_G['upload']['error']['filetype'])."</p>";	
			}
			if(!empty($_G['upload']['error']['filesize'])) {
				$sizeerrorcount = count($_G['upload']['error']['filesize']);
				$message .= "<h4>文件类型错误：</h4><p>数量：{$sizeerrorcount} 文件名：".implode(",", $_G['upload']['error']['filesize'])."</p>";	
			}
			if(!empty($_G['upload']['error']['filemove'])) {
				$moveerrorcount = count($_G['upload']['error']['filemove']);
				$message .= "<h4>文件类型错误：</h4><p>数量：{$moveerrorcount} 文件名：".implode(",", $_G['upload']['error']['filemove'])."</p>";	
			}
			$code = "-1";
		}
		$filepathArr = explode(",", $_G['upload']['filepath']);
		$successcount = count($filepathArr);
		if($successcount == 0) $code = "-1";
		$message .= "成功上传的文件数量：".$successcount;
		$_SESSION['message'] = array('code' => $code, 'content' => array($message));
		
	}
}