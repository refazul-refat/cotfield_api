<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shipments extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$shipment_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		if($request_type=='POST'){
			if($shipment_id==0){
				$date=$this->input->post('shipment_date');
				$type=$this->input->post('shipment_type');
				$partial_shipment=$this->input->post('partial_shipment');
				$transshipment=$this->input->post('transshipment');
				$loading_port=$this->input->post('loading_port');
				$discharge_port=$this->input->post('discharge_port');
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_shipment',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				if(!in_array(strtolower($type),array('road','air')))$this->respond('400',array('error'=>'invalid_shipment_type'));
				if(!in_array(strtolower($partial_shipment),array('yes','no')))$this->respond('400',array('error'=>'invalid_partial_shipment'));
				if(!in_array(strtolower($transshipment),array('yes','no')))$this->respond('400',array('error'=>'invalid_transshipment'));
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$this->db->insert('shipments',array('date'=>$date,
											  'type'=>$type,
											  'partial_shipment'=>$partial_shipment,
											  'transshipment'=>$transshipment,
											  'loading_port'=>$loading_port,
											  'discharge_port'=>$discharge_port
											  ));
				$shipment_id=$this->db->insert_id();
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$this->db->select('*');
				$this->db->from('shipments');
				$this->db->where('id',$shipment_id);
				$shipment=$this->db->get()->row();
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_shipment',$token);
				/*****************************/
		
				$this->respond(201,$shipment);
			}
			else{
				
			}
		}
		else if($request_type=='GET'){
			if($shipment_id>0){
				$this->get_shipment($shipment_id);
			}
		}
	}
	private function new_shipment(){
		
		
	}
	private function get_shipment($shipment_id){
		
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_shipment',$token);
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
		$this->db->from('shipments');
		$this->db->where('id',$shipment_id);
		$shipment=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_shipment',$token);
		/*****************************/
		
		$this->respond(200,$shipment);
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
