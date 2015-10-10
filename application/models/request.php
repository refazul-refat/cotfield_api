<?php
class Request extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function dispatch($action,$token){

      return true;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,base_url()."o/oauth2/consume");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query(array('action' => $action,'token'=>$token)));

		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$result = json_decode(curl_exec ($ch));

		if($result->status=='consumed' || $result->status=='savored'){
			return true;
		}
		else{
			return false;
		}
	}
}
