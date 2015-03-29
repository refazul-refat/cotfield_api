<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import_permits extends CI_Controller {
	
	public function respond($http_response_code,$message){
		header("Content-Type: application/json");
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$import_permit_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		if($request_type=='POST'){
			if($import_permit_id==0)
				$this->new_import_permit();
			else{
				
			}
		}
		else if($request_type=='GET'){
			if($import_permit_id>0){
				$this->get_import_permit($import_permit_id);
			}
		}
	}
	private function new_import_permit(){
		
		$no=$this->input->post('no');
		$date=$this->input->post('date');
		$token=$this->input->post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('create_import_permit',$token);
		if($status!='authorized')$this->respond('400',array('error'=>$status));
		/*************************/
		
		/******************************/
		/* Section 2 - Validate Input */
		if(!$no)$this->respond('400',array('error'=>'empty_import_permit_no'));
		/******************************/
		
		/**********************************/
		/* Section 3 - Database Operation */
		$this->db->insert('import_permits',array('no'=>$no,
												'date'=>$date
											));
		$import_permit_id=$this->db->insert_id();
		/**********************************/
		
		/********************************/
		/* Section 4 - Prepare Response */
		$this->db->select('*');
		$this->db->from('import_permits');
		$this->db->where('id',$import_permit_id);
		$import_permit=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('create_import_permit',$token);
		/*****************************/
		
		$this->respond(201,$import_permit);
	}
	private function get_import_permit($import_permit_id){
		
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_import_permit',$token);
		if($status!='authorized')$this->respond('400',array('error'=>$status));
		/*************************/
		
		/******************************/
		/* Section 2 - Validate Input */
		/******************************/
		
		/**********************************/
		/* Section 3 - Database Operation */
		/**********************************/
		
		/********************************/
		/* Section 4 - Prepare Response */
		$this->db->select('*');
		$this->db->from('import_permits');
		$this->db->where('id',$import_permit_id);
		$import_permit=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_import_permit',$token);
		/*****************************/
		
		$this->respond(200,$import_permit);
	}
	private function skeleton(){
		
		/*************************/
		/* Section 1 - Authorize */
		/*************************/
		
		/******************************/
		/* Section 2 - Validate Input */
		/******************************/
		
		/**********************************/
		/* Section 3 - Database Operation */
		/**********************************/
		
		/********************************/
		/* Section 4 - Prepare Response */
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		/*****************************/
	}
	
	public function _remap(){
		
		$this->index();
	}
}
