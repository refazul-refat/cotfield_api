<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lcs extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$lc_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		if($request_type=='POST'){
			if($lc_id==0)
				$this->new_lc();
			else{
				
			}
		}
		else if($request_type=='GET'){
			if($lc_id>0){
				$this->get_lc($lc_id);
			}
		}
	}
	private function new_lc(){
		
		$no=$this->input->post('lc_no');
		$issue_date=$this->input->post('lc_issue_date');
		$type=$this->input->post('lc_type');
		$opening_bank=$this->input->post('lc_opening_bank');
		$receiving_bank=$this->input->post('lc_receiving_bank');
		$token=$this->input->post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('create_lc',$token);
		if($status!='authorized')$this->respond('400',array('error'=>$status));
		/*************************/
		
		/******************************/
		/* Section 2 - Validate Input */
		if(!$no)$this->respond('400',array('error'=>'empty_lc_no'));
		/******************************/
		
		/**********************************/
		/* Section 3 - Database Operation */
		$this->db->insert('lcs',array('no'=>$no,
									  'issue_date'=>$issue_date,
									  'type'=>$type,
									  'opening_bank'=>$opening_bank,
									  'receiving_bank'=>$receiving_bank
									  ));
		$lc_id=$this->db->insert_id();
		/**********************************/
		
		/********************************/
		/* Section 4 - Prepare Response */
		$this->db->select('*');
		$this->db->from('lcs');
		$this->db->where('id',$lc_id);
		$lc=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('create_lc',$token);
		/*****************************/
		
		$this->respond(201,$lc);
	}
	private function get_lc($lc_id){
		
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_lc',$token);
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
		$this->db->from('lcs');
		$this->db->where('id',$lc_id);
		$lc=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_lc',$token);
		/*****************************/
		
		$this->respond(200,$lc);
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
