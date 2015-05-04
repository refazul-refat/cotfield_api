<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Controllers extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$controller_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		
		$this->load->model('controller');
		if($request_type=='POST'){
			if($controller_id==0){
				$controller=new stdClass;
				$controller->company=$this->input->post('controller_company');
				$controller->weight_finalization_area=$this->input->post('controller_weight_finalization_area');
				$controller->final_weight=$this->input->post('controller_final_weight');
				$controller->final_weight_unit=$this->input->post('controller_final_weight_unit');
				$controller->landing_report=$this->input->post('controller_landing_report');
				
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_controller',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				//if(!$controller->name)$this->respond('400',array('error'=>'empty_name'));
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$controller_id=$this->controller->create($controller);
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$controller=$this->controller->read($controller_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_controller',$token);
				/*****************************/
		
				$this->respond(201,$controller);
			}
			else{
				if($this->input->post('method')=='update'){
					$controller=new stdClass;
					if($this->input->post('controller_company'))$controller->company=$this->input->post('controller_company');
					if($this->input->post('controller_weight_finalization_area'))$controller->weight_finalization_area=$this->input->post('controller_weight_finalization_area');
					if($this->input->post('controller_final_weight'))$controller->final_weight=$this->input->post('controller_final_weight');
					if($this->input->post('controller_final_weight_unit'))$controller->final_weight_unit=$this->input->post('controller_final_weight_unit');
					if($this->input->post('controller_landing_report'))$controller->landing_report=$this->input->post('controller_landing_report');
				
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('update_controller',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$array=(array)$controller;
					if(!empty($array))$this->controller->update($controller_id,$controller);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					unset($controller);
					$controller=$this->controller->read($controller_id);
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('update_controller',$token);
					/*****************************/
		
					$this->respond(200,$controller);
				}
				else if($this->input->post('method')=='delete'){
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('delete_controller',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$this->controller->delete($controller_id);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('delete_controller',$token);
					/*****************************/
		
					$this->respond(204,array());
				}
			}
		}
		else if($request_type=='GET'){
			if($controller_id>0){
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
				$controller=$this->controller->read($controller_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('read_controller',$token);
				/*****************************/
		
				$this->respond(200,$controller);
			}
			else{
				$this->list_controllers();
			}
		}
	}
	private function list_controllers(){
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
		$this->db->select('id,name');
		$this->db->from('controllers');
		$controllers=$this->db->get()->result();
		$response=new stdClass;
		
		foreach($controllers as &$controller){
			$value=$controller->id;
			$caption=new stdClass;
			$caption->caption=$controller->name;
			$response->$value=$caption;
		}
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_controller',$token);
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
