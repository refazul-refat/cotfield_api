<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Controllers extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$controller_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		if($request_type=='POST'){
			if($controller_id==0){
				$company=$this->input->post('controller_company');
				$weight_finalization_area=$this->input->post('weight_finalization_area');
				$final_weight=$this->input->post('final_weight');
				$final_weight_unit=$this->input->post('final_weight_unit');
				$weight_claim=$this->input->post('weight_claim');
				$weight_claim_unit=$this->input->post('weight_claim_unit');
				$unit_price=$this->input->post('unit_price');
				$unit_price_currency=$this->input->post('unit_price_currency');
				$claim_amount=$this->input->post('claim_amount');
				$claim_amount_unit=$this->input->post('claim_amount_unit');
				$landing_report=$this->input->post('landing_report');
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_controller',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$this->db->insert('controllers',array('company'=>$company,
												'weight_finalization_area'=>$weight_finalization_area,
												'final_weight'=>$final_weight,
												'final_weight_unit'=>$final_weight_unit,
												'weight_claim'=>$weight_claim,
												'weight_claim_unit'=>$weight_claim_unit,
												'unit_price'=>$unit_price,
												'unit_price_currency'=>$unit_price_currency,
												'claim_amount'=>$claim_amount,
												'claim_amount_unit'=>$claim_amount_unit,
												'landing_report'=>$landing_report
											  ));
				$controller_id=$this->db->insert_id();
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$this->db->select('*');
				$this->db->from('controllers');
				$this->db->where('id',$controller_id);
				$controller=$this->db->get()->row();
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_controller',$token);
				/*****************************/
		
				$this->respond(201,$controller);
			}
			else{
				
			}
		}
		else if($request_type=='GET'){
			if($controller_id>0){
				$this->get_controller($controller_id);
			}
		}
	}
	private function new_controller(){
		
		
	}
	private function get_controller($controller_id){
		
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_controller',$token);
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
		$this->db->from('controllers');
		$this->db->where('id',$controller_id);
		$controller=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_controller',$token);
		/*****************************/
		
		$this->respond(200,$controller);
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
