<?php

/****************************************
Example of how to use this uploader class...
You can uncomment the following lines (minus the require) to use these as your defaults.

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array();
// max file size in bytes
$sizeLimit = 10 * 1024 * 1024;
//the input name set in the javascript
$inputName = 'fpfile'

require('valums-file-uploader/server/php.php');
$uploader = new fpFileUploader($allowedExtensions, $sizeLimit, $inputName);

// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
$result = $uploader->handleUpload('uploads/');

// to pass data through iframe you will need to encode all html tags
header("Content-Type: text/plain");
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

/******************************************/



/**
 * Handle file uploads via XMLHttpRequest
 */
class fpUploadedFileXhr {
    private $inputName;
    
    /**
     * @param string $inputName; defaults to the javascript default: 'fpfile'
     */
    public function __construct($inputName = 'fpfile'){
        $this->inputName = $inputName;
    }
    
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    public function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if ($realSize != $this->getSize()){            
            return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }

    /**
     * Get the original filename
     * @return string filename
     */
    public function getName() {
        return $_GET[$this->inputName];
    }
    
    /**
     * Get the file size
     * @return integer file-size in byte
     */
    public function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class fpUploadedFileForm {
    private $inputName;
	
    /**
     * @param string $inputName; defaults to the javascript default: 'fpfile'
     */
    public function __construct($inputName = 'fpfile'){
        $this->inputName = $inputName;
    }
    
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    public function save($path) {
        return move_uploaded_file($_FILES[$this->inputName]['tmp_name'], $path);
    }
    
    /**
     * Get the original filename
     * @return string filename
     */
    public function getName() {
        return $_FILES[$this->inputName]['name'];
    }
    
    /**
     * Get the file size
     * @return integer file-size in byte
     */
    public function getSize() {
        return $_FILES[$this->inputName]['size'];
    }
}

/**
 * Class that encapsulates the file-upload internals
 */
class fpFileUploader {
    private $allowedExtensions;
    private $sizeLimit;
    private $file;
    private $uploadName;

    /**
     * @param array $allowedExtensions; defaults to an empty array
     * @param int $sizeLimit; defaults to the server's upload_max_filesize setting
     * @param string $inputName; defaults to the javascript default: 'fpfile'
     */
    function __construct(array $allowedExtensions = null, $sizeLimit = null, $inputName = 'fpfile'){
        if($allowedExtensions===null) {
            $allowedExtensions = array();
    	}
    	if($sizeLimit===null) {
    	    $sizeLimit = $this->toBytes(ini_get('upload_max_filesize'));
    	}
    	        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        
        $this->checkServerSettings();       

        if(!isset($_SERVER['CONTENT_TYPE'])) {
            $this->file = false;	
        } else if (strpos(strtolower($_SERVER['CONTENT_TYPE']), 'multipart/') === 0) {
            $this->file = new fpUploadedFileForm($inputName);
        } else {
            $this->file = new fpUploadedFileXhr($inputName);
        }
    }
    
    /**
     * Get the name of the uploaded file
     * @return string
     */
    public function getUploadName(){
        if( isset( $this->uploadName ) )
            return $this->uploadName;
    }
	
    /**
     * Get the original filename
     * @return string filename
     */
    public function getName(){
        if ($this->file)
            return $this->file->getName();
    }
    
    /**
     * Internal function that checks if server's may sizes match the
     * object's maximum size for uploads
     */
    private function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die(json_encode(array('error'=>'increase post_max_size and upload_max_filesize to ' . $size)));    
        }        
    }
    
    /**
     * Convert a given size with units to bytes
     * @param string $str
     */
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
    
    /**
     * Handle the uploaded file
     * @param string $uploadDirectory
     * @param string $replaceOldFile=true
     * @returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
		global $config,$db;
		$addr = mt_rand(10,99);
        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
		$filename = substr(md5(md5($filename).mt_rand(1,999)),0,8);
		$uploadDirectory = $config['uploadPath'].'/';
		$previewDirectory = $config['previewPath'].'/';
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable.");
        }
        
        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }

        $ext = strtolower(@$pathinfo['extension']);

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
        
        $ext = ($ext == '') ? $ext : '.'.$ext;
        
        $this->uploadName = $filename.$ext;
		include_once($config['SITE_DIR'].'/engine/functions.php');
		$delDate = time()+86400;
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$ip = $_SERVER['REMOTE_ADDR'];
        if ($this->file->save($uploadDirectory.$filename.$ext) && img_resize_clip($uploadDirectory.$filename.$ext,270,152,$previewDirectory.$filename.'.jpg') && $db->query("INSERT INTO `ss_files` SET `name`='".$filename."',`ext`='".$ext."',`upload_date`='".time()."';")){
			$id = $db->insertid();
			$url1 = $config['SITE_URL'].'/f/'.$addr.'/'.$filename.$ext;
			$url2 = '[img]'.$config['SITE_URL'].'/f/'.$addr.'/'.$filename.$ext.'[/img]';
			$url3 = '<img src="'.$config['SITE_URL'].'/f/'.$addr.'/'.$filename.$ext.'" alt="" />';
			$url4 = '[url='.$config['SITE_URL'].'/f/'.$addr.'/'.$filename.$ext.'][img]'.$config['SITE_URL'].'/m/'.$addr.'/'.$filename.$ext.'[/img][/url]';
			$url5 = '<a href="'.$config['SITE_URL'].'/f/'.$addr.'/'.$filename.$ext.'"><img src="'.$config['SITE_URL'].'/m/'.$addr.'/'.$filename.$ext.'" alt="" /></a>';
			$url6 = $config['SITE_URL'].'/file/'.$addr.'/'.$filename.'/';
			$url7 = '/m/'.$addr.'/'.$filename.'.jpg';
            return array('success'=>true,'src'=>$url7,'url1'=>$url1,'url2'=>$url2,'url3'=>$url3,'url4'=>$url4,'url5'=>$url5,'url6'=>$url6,'url7'=>$url7,'id'=>$id);
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
        
    }    
}
