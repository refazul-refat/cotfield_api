<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payments extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$payment_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		if($request_type=='POST'){
			if($payment_id==0){
				$supplier_clearance=$this->input->post('supplier_clearance');
				$commission_amount=$this->input->post('commission_amount');
				$commission_amount_unit=$this->input->post('commission_amount_unit');
				$receiving_date=$this->input->post('receiving_date');
				$late_payment=$this->input->post('late_payment');
				$buyer_bank_payment=$this->input->post('buyer_bank_payment');
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_payment',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$this->db->insert('payments',array('supplier_clearance'=>$supplier_clearance,
												'commission_amount'=>$commission_amount,
												'commission_amount_unit'=>$commission_amount_unit,
												'receiving_date'=>$receiving_date,
												'late_payment'=>$late_payment,
												'buyer_bank_payment'=>$buyer_bank_payment
											  ));
				$payment_id=$this->db->insert_id();
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$this->db->select('*');
				$this->db->from('payments');
				$this->db->where('id',$payment_id);
				$payment=$this->db->get()->row();
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_payment',$token);
				/*****************************/
		
				$this->respond(201,$payment);
			}
			else{
				
			}
		}
		else if($request_type=='GET'){
			if($payment_id>0){
				$this->get_payment($payment_id);
			}
		}
	}
	private function new_payment(){
		
		
	}
	private function get_payment($payment_id){
		
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_payment',$token);
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
		$this->db->from('payments');
		$this->db->where('id',$payment_id);
		$payment=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_payment',$token);
		/*****************************/
		
		$this->respond(200,$payment);
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
