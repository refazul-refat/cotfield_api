<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payments extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$payment_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		
		$this->load->model('payment');
		if($request_type=='POST'){
			if($payment_id==0){
				$payment=new stdClass;
				$payment->supplier_clearance=$this->input->post('payment_supplier_clearance');
				$payment->receiving_date=$this->input->post('payment_receiving_date');
				$payment->late_payment=$this->input->post('payment_late_payment');
				$payment->payment_document=$this->input->post('payment_payment_document');
				
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_payment',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				//if(!$payment->name)$this->respond('400',array('error'=>'empty_name'));
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$payment_id=$this->payment->create($payment);
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$payment=$this->payment->read($payment_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_payment',$token);
				/*****************************/
		
				$this->respond(201,$payment);
			}
			else{
				if($this->input->post('method')=='update'){
					$payment=new stdClass;
					if($this->input->post('payment_supplier_clearance'))$payment->supplier_clearance=$this->input->post('payment_supplier_clearance');
					if($this->input->post('payment_receiving_date'))$payment->receiving_date=$this->input->post('payment_receiving_date');
					if($this->input->post('payment_late_payment'))$payment->late_payment=$this->input->post('payment_late_payment');
					if($this->input->post('payment_payment_document'))$payment->payment_document=$this->input->post('payment_payment_document');
				
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('update_payment',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$array=(array)$payment;
					if(!empty($array))$this->payment->update($payment_id,$payment);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					unset($payment);
					$payment=$this->payment->read($payment_id);
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('update_payment',$token);
					/*****************************/
		
					$this->respond(200,$payment);
				}
				else if($this->input->post('method')=='delete'){
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('delete_payment',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$this->payment->delete($payment_id);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('delete_payment',$token);
					/*****************************/
		
					$this->respond(204,array());
				}
			}
		}
		else if($request_type=='GET'){
			if($payment_id>0){
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
				$payment=$this->payment->read($payment_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('read_payment',$token);
				/*****************************/
		
				$this->respond(200,$payment);
			}
			else{
				$this->list_payments();
			}
		}
	}
	private function list_payments(){
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
		$this->db->select('id,name');
		$this->db->from('payments');
		$payments=$this->db->get()->result();
		$response=new stdClass;
		
		foreach($payments as &$payment){
			$value=$payment->id;
			$caption=new stdClass;
			$caption->caption=$payment->name;
			$response->$value=$caption;
		}
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_payment',$token);
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
