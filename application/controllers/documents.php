<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Documents extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$document_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		
		$this->load->model('document');
		if($request_type=='POST'){
			if($document_id==0){
				$document=new stdClass;
				$document->commercial_invoice=$this->input->post('document_commercial_invoice');
				$document->packing_list=$this->input->post('document_packing_list');
				$document->lading_bill=$this->input->post('document_lading_bill');
				$document->phytosanitary_certificate=$this->input->post('document_phytosanitary_certificate');
				$document->origin_certificate=$this->input->post('document_origin_certificate');
				$document->shipment_advice=$this->input->post('document_shipment_advice');
				$document->controller_letter=$this->input->post('document_controller_letter');
				$document->fumigation_letter=$this->input->post('document_fumigation_letter');
				
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_document',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				//if(!$document->no)$this->respond('400',array('error'=>'empty_no'));
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$document_id=$this->document->create($document);
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$document=$this->document->read($document_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_document',$token);
				/*****************************/
		
				$this->respond(201,$document);
			}
			else{
				if($this->input->post('method')=='update'){
					$document=new stdClass;
					if($this->input->post('document_commercial_invoice'))$document->commercial_invoice=$this->input->post('document_commercial_invoice');
					if($this->input->post('document_packing_list'))$document->packing_list=$this->input->post('document_packing_list');
					if($this->input->post('document_lading_bill'))$document->lading_bill=$this->input->post('document_lading_bill');
					if($this->input->post('document_phytosanitary_certificate'))$document->phytosanitary_certificate=$this->input->post('document_phytosanitary_certificate');
					if($this->input->post('document_origin_certificate'))$document->origin_certificate=$this->input->post('document_origin_certificate');
					if($this->input->post('document_shipment_advice'))$document->shipment_advice=$this->input->post('document_shipment_advice');
					if($this->input->post('document_controller_letter'))$document->controller_letter=$this->input->post('document_controller_letter');
					if($this->input->post('document_fumigation_letter'))$document->fumigation_letter=$this->input->post('document_fumigation_letter');
				
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('update_document',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$array=(array)$document;
					if(!empty($array))$this->document->update($document_id,$document);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					unset($document);
					$document=$this->document->read($document_id);
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('update_document',$token);
					/*****************************/
		
					$this->respond(200,$document);
				}
				else if($this->input->post('method')=='delete'){
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('delete_document',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$this->document->delete($document_id);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('delete_document',$token);
					/*****************************/
		
					$this->respond(204,array());
				}
			}
		}
		else if($request_type=='GET'){
			if($document_id>0){
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
				$document=$this->document->read($document_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('read_document',$token);
				/*****************************/
		
				$this->respond(200,$document);
			}
			else{
				$this->list_documents();
			}
		}
	}
	private function list_documents(){
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
		$this->db->select('id,name');
		$this->db->from('documents');
		$documents=$this->db->get()->result();
		$response=new stdClass;
		
		foreach($documents as &$document){
			$value=$document->id;
			$caption=new stdClass;
			$caption->caption=$document->name;
			$response->$value=$caption;
		}
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_document',$token);
		/*****************************/
		
		$this->respond(200,$response);
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