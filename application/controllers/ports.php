<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ports extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$port_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		
		$this->load->model('port');
		if($request_type=='POST'){
			if($port_id==0){
				$port=new stdClass;
				$port->buyer_cnf=$this->input->post('port_buyer_cnf');
				$port->clearance_document=$this->input->post('port_clearance_document');
				$port->invoice_weight=$this->input->post('port_invoice_weight');
				$port->invoice_weight_unit=$this->input->post('port_invoice_weight_unit');
				
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_port',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				//if(!$port->name)$this->respond('400',array('error'=>'empty_name'));
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$port_id=$this->port->create($port);
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$port=$this->port->read($port_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_port',$token);
				/*****************************/
		
				$this->respond(201,$port);
			}
			else{
				if($this->input->post('method')=='update'){
					$port=new stdClass;
					if($this->input->post('port_buyer_cnf'))$port->buyer_cnf=$this->input->post('port_buyer_cnf');
					if($this->input->post('port_clearance_document'))$port->clearance_document=$this->input->post('port_clearance_document');
					if($this->input->post('port_invoice_weight'))$port->invoice_weight=$this->input->post('port_invoice_weight');
					if($this->input->post('port_invoice_weight_unit'))$port->invoice_weight_unit=$this->input->post('port_invoice_weight_unit');
				
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('update_port',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$array=(array)$port;
					if(!empty($array))$this->port->update($port_id,$port);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					unset($port);
					$port=$this->port->read($port_id);
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('update_port',$token);
					/*****************************/
		
					$this->respond(200,$port);
				}
				else if($this->input->post('method')=='delete'){
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('delete_port',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$this->port->delete($port_id);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('delete_port',$token);
					/*****************************/
		
					$this->respond(204,array());
				}
			}
		}
		else if($request_type=='GET'){
			if($port_id>0){
				$token=$this->input->get_post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('read_port',$token);
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
				$port=$this->port->read($port_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('read_port',$token);
				/*****************************/
		
				$this->respond(200,$port);
			}
			else{
				$this->list_ports();
			}
		}
	}
	private function list_ports(){
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_port',$token);
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
		$this->db->from('ports');
		$ports=$this->db->get()->result();
		$response=new stdClass;
		
		foreach($ports as &$port){
			$value=$port->id;
			$caption=new stdClass;
			$caption->caption=$port->name;
			$response->$value=$caption;
		}
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_port',$token);
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
