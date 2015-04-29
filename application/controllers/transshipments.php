<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transshipments extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$transshipment_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		if($request_type=='POST'){
			if($transshipment_id==0){
				$original_document_arrival=$this->input->post('original_document_arrival');
				$payment_notification=$this->input->post('payment_notification');
				$vessel_track_no=$this->input->post('vessel_track_no');
				$date=$this->input->post('transshipment_date');
				$port=$this->input->post('transshipment_port');
				$buyer_notification=$this->input->post('buyer_notification');
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_transshipment',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$this->db->insert('transshipments',array('original_document_arrival'=>$original_document_arrival,
													'payment_notification'=>$payment_notification,
													'vessel_track_no'=>$vessel_track_no,
													'date'=>$date,
													'port'=>$port,
													'buyer_notification'=>$buyer_notification
											  ));
				$transshipment_id=$this->db->insert_id();
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$this->db->select('*');
				$this->db->from('transshipments');
				$this->db->where('id',$transshipment_id);
				$transshipment=$this->db->get()->row();
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_transshipment',$token);
				/*****************************/
		
				$this->respond(201,$transshipment);
			}
			else{
				
			}
		}
		else if($request_type=='GET'){
			if($transshipment_id>0){
				$this->get_transshipment($transshipment_id);
			}
		}
	}
	private function new_transshipment(){
		
		
	}
	private function get_transshipment($transshipment_id){
		
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_transshipment',$token);
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
		$this->db->from('transshipments');
		$this->db->where('id',$transshipment_id);
		$transshipment=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_transshipment',$token);
		/*****************************/
		
		$this->respond(200,$transshipment);
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
