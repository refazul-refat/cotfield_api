<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customers extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$customer_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		
		$this->load->model('customer');
		if($request_type=='POST'){
			if($customer_id==0){
				$customer=new stdClass;
				$customer->name=$this->input->post('customer_name');
				$customer->location=$this->input->post('customer_location');
				$customer->contact=$this->input->post('customer_contact');
				$customer->production_details=$this->input->post('customer_production_details');
				$customer->purchase_details=$this->input->post('customer_purchase_details');
				$customer->payment_details=$this->input->post('customer_payment_details');
				$customer->description=$this->input->post('customer_description');
				
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_customer',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				if(!$customer->name)$this->respond('400',array('error'=>'empty_name'));
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$customer_id=$this->customer->create($customer);
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$customer=$this->customer->read($customer_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_customer',$token);
				/*****************************/
		
				$this->respond(201,$customer);
			}
			else{
				if($this->input->post('method')=='update'){
					$customer=new stdClass;
					if($this->input->post('customer_name'))$customer->name=$this->input->post('customer_name');
					if($this->input->post('customer_location'))$customer->location=$this->input->post('customer_location');
					if($this->input->post('customer_contact'))$customer->contact=$this->input->post('customer_contact');
					if($this->input->post('customer_production_details'))$customer->production_details=$this->input->post('customer_production_details');
					if($this->input->post('customer_purchase_details'))$customer->purchase_details=$this->input->post('customer_purchase_details');
					if($this->input->post('customer_payment_details'))$customer->payment_details=$this->input->post('customer_payment_details');
					if($this->input->post('customer_description'))$customer->description=$this->input->post('customer_description');
				
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('update_customer',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$array=(array)$customer;
					if(!empty($array))$this->customer->update($customer_id,$customer);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					unset($customer);
					$customer=$this->customer->read($customer_id);
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('update_customer',$token);
					/*****************************/
		
					$this->respond(200,$customer);
				}
				else if($this->input->post('method')=='delete'){
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('delete_customer',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$this->customer->delete($customer_id);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('delete_customer',$token);
					/*****************************/
		
					$this->respond(204,array());
				}
			}
		}
		else if($request_type=='GET'){
			if($customer_id>0){
				$token=$this->input->get_post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('read_customer',$token);
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
				$customer=$this->customer->read($customer_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('read_customer',$token);
				/*****************************/
		
				$this->respond(200,$customer);
			}
			else{
				$this->list_customers();
			}
		}
	}
	private function list_customers(){
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_customer',$token);
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
		$this->db->from('customers');
		$customers=$this->db->get()->result();
		$response=new stdClass;
		
		foreach($customers as &$customer){
			$value=$customer->id;
			$caption=new stdClass;
			$caption->caption=$customer->name;
			$response->$value=$caption;
		}
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_customer',$token);
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
