<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suppliers extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$supplier_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		
		$this->load->model('supplier');
		if($request_type=='POST'){
			if($supplier_id==0){
				$supplier=new stdClass;
				$supplier->name=$this->input->post('supplier_name');
				$supplier->description=$this->input->post('supplier_description');
				
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_supplier',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				if(!$supplier->name)$this->respond('400',array('error'=>'empty_name'));
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$supplier_id=$this->supplier->create($supplier);
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$supplier=$this->supplier->read($supplier_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_supplier',$token);
				/*****************************/
		
				$this->respond(201,$supplier);
			}
			else{
				if($this->input->post('method')=='update'){
					$supplier=new stdClass;
					if($this->input->post('supplier_name'))$supplier->name=$this->input->post('supplier_name');
					if($this->input->post('supplier_description'))$supplier->description=$this->input->post('supplier_description');
				
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('update_supplier',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$array=(array)$supplier;
					if(!empty($array))$this->supplier->update($supplier_id,$supplier);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					unset($supplier);
					$supplier=$this->supplier->read($supplier_id);
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('update_supplier',$token);
					/*****************************/
		
					$this->respond(200,$supplier);
				}
				else if($this->input->post('method')=='delete'){
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('delete_supplier',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$this->supplier->delete($supplier_id);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('delete_supplier',$token);
					/*****************************/
		
					$this->respond(204,array());
				}
			}
		}
		else if($request_type=='GET'){
			if($supplier_id>0){
				$token=$this->input->get_post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('read_supplier',$token);
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
				$supplier=$this->supplier->read($supplier_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('read_supplier',$token);
				/*****************************/
		
				$this->respond(200,$supplier);
			}
			else{
				$this->list_suppliers();
			}
		}
	}
	private function list_suppliers(){
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_supplier',$token);
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
		$this->db->select('name');
		$this->db->from('suppliers');
		$suppliers=$this->db->get()->result();
		$response=array();
		
		foreach($suppliers as &$supplier){
			$response[]=$supplier->name;
		}
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_supplier',$token);
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
