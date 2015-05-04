<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import_permits extends CI_Controller {
	
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$import_permit_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		
		$this->load->model('import_permit');
		if($request_type=='POST'){
			if($import_permit_id==0){
				$import_permit=new stdClass;
				$import_permit->no=$this->input->post('import_permit_no');
				$import_permit->date=$this->input->post('import_permit_date');
				$import_permit->copy=$this->input->post('import_permit_copy');
				
				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_import_permit',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/
		
				/******************************/
				/* Section 2 - Validate Input */
				if(!$import_permit->no)$this->respond('400',array('error'=>'empty_no'));
				/******************************/
		
				/**********************************/
				/* Section 3 - Database Operation */
				$import_permit_id=$this->import_permit->create($import_permit);
				/**********************************/
		
				/********************************/
				/* Section 4 - Prepare Response */
				$import_permit=$this->import_permit->read($import_permit_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_import_permit',$token);
				/*****************************/
		
				$this->respond(201,$import_permit);
			}
			else{
				if($this->input->post('method')=='update'){
					$import_permit=new stdClass;
					if($this->input->post('import_permit_no'))$import_permit->no=$this->input->post('import_permit_no');
					if($this->input->post('import_permit_date'))$import_permit->date=$this->input->post('import_permit_date');
					if($this->input->post('import_permit_copy'))$import_permit->copy=$this->input->post('import_permit_copy');
				
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('update_import_permit',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$array=(array)$import_permit;
					if(!empty($array))$this->import_permit->update($import_permit_id,$import_permit);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					unset($import_permit);
					$import_permit=$this->import_permit->read($import_permit_id);
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('update_import_permit',$token);
					/*****************************/
		
					$this->respond(200,$import_permit);
				}
				else if($this->input->post('method')=='delete'){
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('delete_import_permit',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/
		
					/******************************/
					/* Section 2 - Validate Input */
					/******************************/
		
					/**********************************/
					/* Section 3 - Database Operation */
					$this->import_permit->delete($import_permit_id);
					/**********************************/
		
					/********************************/
					/* Section 4 - Prepare Response */
					/********************************/
		
					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('delete_import_permit',$token);
					/*****************************/
		
					$this->respond(204,array());
				}
			}
		}
		else if($request_type=='GET'){
			if($import_permit_id>0){
				$token=$this->input->get_post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('read_import_permit',$token);
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
				$import_permit=$this->import_permit->read($import_permit_id);
				/********************************/
		
				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('read_import_permit',$token);
				/*****************************/
		
				$this->respond(200,$import_permit);
			}
			else{
				$this->list_import_permits();
			}
		}
	}
	private function list_import_permits(){
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_import_permit',$token);
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
		$this->db->from('import_permits');
		$import_permits=$this->db->get()->result();
		$response=new stdClass;
		
		foreach($import_permits as &$import_permit){
			$value=$import_permit->id;
			$caption=new stdClass;
			$caption->caption=$import_permit->name;
			$response->$value=$caption;
		}
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_import_permit',$token);
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