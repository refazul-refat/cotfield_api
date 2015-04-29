<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Documents extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$document_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		if($request_type=='POST'){
			if($document_id==0){
				$commercial_invoice=$this->input->post('commercial_invoice');
				$packing_list=$this->input->post('packing_list');
				$lading_bill=$this->input->post('lading_bill');
				$phytosanitary_certificate=$this->input->post('phytosanitary_certificate');
				$origin_certificate=$this->input->post('origin_certificate');
				$shipment_advice=$this->input->post('shipment_advice');
				$controller_letter=$this->input->post('controller_letter');
				$fumigation_letter=$this->input->post('fumigation_letter');
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_document',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$this->db->insert('documents',array('commercial_invoice'=>$commercial_invoice,
													'packing_list'=>$packing_list,
													'lading_bill'=>$lading_bill,
													'phytosanitary_certificate'=>$phytosanitary_certificate,
													'origin_certificate'=>$origin_certificate,
													'shipment_advice'=>$shipment_advice,
													'controller_letter'=>$controller_letter,
													'fumigation_letter'=>$fumigation_letter
											  ));
				$document_id=$this->db->insert_id();
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$this->db->select('*');
				$this->db->from('documents');
				$this->db->where('id',$document_id);
				$document=$this->db->get()->row();
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_document',$token);
				/*****************************/
		
				$this->respond(201,$document);
			}
			else{
				
			}
		}
		else if($request_type=='GET'){
			if($document_id>0){
				$this->get_document($document_id);
			}
		}
	}
	private function new_document(){
		
		
	}
	private function get_document($document_id){
		
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_document',$token);
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
		$this->db->from('documents');
		$this->db->where('id',$document_id);
		$document=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_document',$token);
		/*****************************/
		
		$this->respond(200,$document);
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
