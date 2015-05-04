<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transshipments extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$transshipment_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		
		$this->load->model('transshipment');
		if($request_type=='POST'){
			if($transshipment_id==0){
				$transshipment=new stdClass;
				$transshipment->original_document_arrival=$this->input->post('transshipment_original_document_arrival');
				$transshipment->payment_notification=$this->input->post('transshipment_payment_notification');
				$transshipment->vessel_track_no=$this->input->post('transshipment_vessel_track_no');
				$transshipment->date=$this->input->post('transshipment_date');
				$transshipment->port=$this->input->post('transshipment_port');
				$transshipment->buyer_notification=$this->input->post('transshipment_buyer_notification');
				
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_transshipment',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				//if(!$transshipment->no)$this->respond('400',array('error'=>'empty_no'));
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$transshipment_id=$this->transshipment->create($transshipment);
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$transshipment=$this->transshipment->read($transshipment_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_transshipment',$token);
				/*****************************/
		
				$this->respond(201,$transshipment);
			}
			else{
				if($this->input->post('method')=='update'){
					$transshipment=new stdClass;
					if($this->input->post('transshipment_original_document_arrival'))$transshipment->original_document_arrival=$this->input->post('transshipment_original_document_arrival');
					if($this->input->post('transshipment_payment_notification'))$transshipment->payment_notification=$this->input->post('transshipment_payment_notification');
					if($this->input->post('transshipment_vessel_track_no'))$transshipment->vessel_track_no=$this->input->post('transshipment_vessel_track_no');
					if($this->input->post('transshipment_date'))$transshipment->date=$this->input->post('transshipment_date');
					if($this->input->post('transshipment_port'))$transshipment->port=$this->input->post('transshipment_port');
					if($this->input->post('transshipment_buyer_notification'))$transshipment->buyer_notification=$this->input->post('transshipment_buyer_notification');
				
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('update_transshipment',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$array=(array)$transshipment;
					if(!empty($array))$this->transshipment->update($transshipment_id,$transshipment);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					unset($transshipment);
					$transshipment=$this->transshipment->read($transshipment_id);
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('update_transshipment',$token);
					/*****************************/
		
					$this->respond(200,$transshipment);
				}
				else if($this->input->post('method')=='delete'){
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('delete_transshipment',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$this->transshipment->delete($transshipment_id);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('delete_transshipment',$token);
					/*****************************/
		
					$this->respond(204,array());
				}
			}
		}
		else if($request_type=='GET'){
			if($transshipment_id>0){
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
				$transshipment=$this->transshipment->read($transshipment_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('read_transshipment',$token);
				/*****************************/
		
				$this->respond(200,$transshipment);
			}
			else{
				$this->list_transshipments();
			}
		}
	}
	private function list_transshipments(){
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
		$this->db->select('id,name');
		$this->db->from('transshipments');
		$transshipments=$this->db->get()->result();
		$response=new stdClass;
		
		foreach($transshipments as &$transshipment){
			$value=$transshipment->id;
			$caption=new stdClass;
			$caption->caption=$transshipment->name;
			$response->$value=$caption;
		}
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_transshipment',$token);
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