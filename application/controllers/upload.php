<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends CI_Controller {

	public function index()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Cache-control, Origin, X-Requested-With, Content-Type, Accept, Key");
		$storeFolder = FCPATH.'uploads';
		if(!empty($_FILES)){
			$tempFile = $_FILES['file']['tmp_name'];
			$fileName = time().'-'.$_FILES['file']['name'];
			$targetFile =  $storeFolder.DIRECTORY_SEPARATOR.$fileName;
			echo base_url().'uploads/'.$fileName;
			move_uploaded_file($tempFile,$targetFile);
		}
	}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */