<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contracts extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$contract_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		if($request_type=='POST'){
			if($contract_id==0)
				$this->new_contract();
			else{
				
			}
		}
		else if($request_type=='GET'){
			if($contract_id>0){
				$this->get_contract($contract_id);
			}
		}
	}
	private function new_contract(){
		
		/*
		Fields: no,initiate_date,agreement_date,commission_rate,commission_rate_unit,copy
		*/
		
		$contract_no=$this->input->post('contract_no');
		$contract_initiate_date=$this->input->post('contract_initiate_date');
		$contract_agreement_date=$this->input->post('contract_agreement_date');
		$contract_commission_rate=$this->input->post('contract_commission_rate');
		$contract_commission_rate_unit=$this->input->post('contract_commission_rate_unit');
		$contract_copy=$this->input->post('contract_copy');
		$token=$this->input->post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('create_contract',$token);
		if($status!='authorized')$this->respond('400',array('error'=>$status));
		/*************************/
		
		/******************************/
		/* Section 2 - Validate Input */
		if(!$contract_no)$this->respond('400',array('error'=>'empty_contract_no'));
		if(!is_numeric($contract_commission_rate))$this->respond(400,array('error'=>'invalid_commission_rate'));
		if(!in_array($contract_commission_rate_unit,array('lbs','kg','kgs')))$this->respond(400,array('error'=>'invalid_commission_rate_unit'));
		/******************************/
		
		/**********************************/
		/* Section 3 - Database Operation */
		$this->db->insert('contracts',array('no'=>$contract_no,
											'initiate_date'=>$contract_initiate_date,
											'agreement_date'=>$contract_agreement_date,
											'commission_rate'=>$contract_commission_rate,
											'commission_rate_unit'=>$contract_commission_rate_unit,
											'copy'=>$contract_copy
											));
		$contract_id=$this->db->insert_id();
		/**********************************/
		
		/********************************/
		/* Section 4 - Prepare Response */
		$this->db->select('*');
		$this->db->from('contracts');
		$this->db->where('id',$contract_id);
		$contract=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('create_contract',$token);
		/*****************************/
		
		$this->respond(201,$contract);
	}
	private function get_contract($contract_id){
		
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_contract',$token);
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
		$this->db->from('contracts');
		$this->db->where('id',$contract_id);
		$contract=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_contract',$token);
		/*****************************/
		
		$this->respond(200,$contract);
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
