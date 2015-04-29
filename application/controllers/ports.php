<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ports extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$port_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		if($request_type=='POST'){
			if($port_id==0){
				$buyer_cnf=$this->input->post('buyer_cnf');
				$buyer_clearance=$this->input->post('buyer_clearance');
				$clearance_document=$this->input->post('clearance_document');
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_port',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$this->db->insert('ports',array('buyer_cnf'=>$buyer_cnf,
												'buyer_clearance'=>$buyer_clearance,
												'clearance_document'=>$clearance_document
											  ));
				$port_id=$this->db->insert_id();
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$this->db->select('*');
				$this->db->from('ports');
				$this->db->where('id',$port_id);
				$port=$this->db->get()->row();
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_port',$token);
				/*****************************/
		
				$this->respond(201,$port);
			}
			else{
				
			}
		}
		else if($request_type=='GET'){
			if($port_id>0){
				$this->get_port($port_id);
			}
		}
	}
	private function new_port(){
		
		
	}
	private function get_port($port_id){
		
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_port',$token);
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
		$this->db->from('ports');
		$this->db->where('id',$port_id);
		$port=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_port',$token);
		/*****************************/
		
		$this->respond(200,$port);
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
