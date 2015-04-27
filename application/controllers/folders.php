<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Folders extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$folder_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		if($request_type=='POST'){
			if($folder_id==0)
				$this->new_folder();
			else{
				
			}
		}
		else if($request_type=='GET'){
			if($folder_id>0){
				$this->get_folder($folder_id);
			}
		}
	}
	private function new_folder(){
		
		$name=$this->input->post('folder_name');
		$token=$this->input->post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('create_folder',$token);
		if($status!='authorized')$this->respond('400',array('error'=>$status));
		/*************************/
		
		/******************************/
		/* Section 2 - Validate Input */
		if(!$name)$this->respond('400',array('error'=>'empty_name'));
		/******************************/
		
		/**********************************/
		/* Section 3 - Database Operation */
		$this->db->insert('folders',array('name'=>$name,
											'description'=>$this->input->post('folder_description')?$this->input->post('folder_description'):''
											));
		$folder_id=$this->db->insert_id();
		$this->db->insert('tree',array('item_id'=>$folder_id,
										'item_type'=>'folder',
										'parent'=>$this->input->post('folder_parent')?$this->input->post('folder_parent'):0
										));
		/**********************************/
		
		/********************************/
		/* Section 4 - Prepare Response */
		$this->db->select('*');
		$this->db->from('folders');
		$this->db->where('id',$folder_id);
		$folder=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('create_folder',$token);
		/*****************************/
		
		$this->respond(201,$folder);
	}
	private function get_folder($folder_id){
		
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_folder',$token);
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
		$this->db->from('folders');
		$this->db->where('id',$folder_id);
		$folder=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_folder',$token);
		/*****************************/
		
		$this->respond(200,$folder);
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
