<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suppliers extends CI_Controller {
	
	public function respond($http_response_code,$message){
		header("Content-Type: application/json");
		header("Access-Control-Allow-Origin: *");
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$supplier_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		if($request_type=='POST'){
			if($supplier_id==0)
				$this->new_supplier();
			else{
				
			}
		}
		else if($request_type=='GET'){
			if($supplier_id>0){
				$this->get_supplier($supplier_id);
			}
		}
	}
	private function new_supplier(){
		
		$name=$this->input->post('name');
		$token=$this->input->post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('create_supplier',$token);
		if($status!='authorized')$this->respond('400',array('error'=>$status));
		/*************************/
		
		/******************************/
		/* Section 2 - Validate Input */
		if(!$name)$this->respond('400',array('error'=>'empty_name'));
		/******************************/
		
		/**********************************/
		/* Section 3 - Database Operation */
		$this->db->insert('suppliers',array('name'=>$name,
											'description'=>$this->input->post('description')?$this->input->post('description'):''
											));
		$supplier_id=$this->db->insert_id();
		/**********************************/
		
		/********************************/
		/* Section 4 - Prepare Response */
		$this->db->select('*');
		$this->db->from('suppliers');
		$this->db->where('id',$supplier_id);
		$supplier=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('create_supplier',$token);
		/*****************************/
		
		$this->respond(201,$supplier);
	}
	private function get_supplier($supplier_id){
		
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_supplier',$token);
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
		$this->db->from('suppliers');
		$this->db->where('id',$supplier_id);
		$supplier=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_supplier',$token);
		/*****************************/
		
		$this->respond(200,$supplier);
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
