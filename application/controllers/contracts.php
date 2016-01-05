<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contracts extends CI_Controller {

	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}

	public function index(){

		$request_type=$_SERVER['REQUEST_METHOD'];
		$contract_id=$this->uri->segment(2,0);

		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		$this->load->model('contract');
		if($request_type=='POST'){
			if($contract_id==0){
				$contract=new stdClass;
				$contract->no=$this->input->post('contract_no');
				$contract->initiate_date=$this->input->post('contract_initiate_date');
				$contract->agreement_date=$this->input->post('contract_agreement_date');
				$contract->commission_rate=$this->input->post('contract_commission_rate');
				$contract->commission_rate_unit=$this->input->post('contract_commission_rate_unit');
				$contract->point_per10k=$this->input->post('contract_point_per10k');
				$contract->copy=$this->input->post('contract_copy');

				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_contract',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/

				/******************************/
				/* Section 2 - Validate Input */
				if(!$contract->no)$this->respond('400',array('error'=>'empty_no'));
				/******************************/

				/**********************************/
				/* Section 3 - Database Operation */
				$contract_id=$this->contract->create($contract);
				/**********************************/

				/********************************/
				/* Section 4 - Prepare Response */
				$contract=$this->contract->read($contract_id);
				/********************************/

				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_contract',$token);
				/*****************************/

				$this->respond(201,$contract);
			}
			else{
				if($this->input->post('method')=='update'){
					$contract=new stdClass;
					if($this->input->post('contract_no'))$contract->no=$this->input->post('contract_no');
					if($this->input->post('contract_initiate_date'))$contract->initiate_date=$this->input->post('contract_initiate_date');
					if($this->input->post('contract_agreement_date'))$contract->agreement_date=$this->input->post('contract_agreement_date');
					if($this->input->post('contract_commission_rate'))$contract->commission_rate=$this->input->post('contract_commission_rate');
					if($this->input->post('contract_point_per10k'))$contract->point_per10k=$this->input->post('contract_point_per10k');
					if($this->input->post('contract_commission_rate_unit'))$contract->commission_rate_unit=$this->input->post('contract_commission_rate_unit');
					$contract->copy=$this->input->post('contract_copy');

					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('update_contract',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/

					/******************************/
					/* Section 2 - Validate Input */
					/******************************/

					/**********************************/
					/* Section 3 - Database Operation */
					$array=(array)$contract;
					if(!empty($array))$this->contract->update($contract_id,$contract);
					/**********************************/

					/********************************/
					/* Section 4 - Prepare Response */
					unset($contract);
					$contract=$this->contract->read($contract_id);
					/********************************/

					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('update_contract',$token);
					/*****************************/

					$this->respond(200,$contract);
				}
				else if($this->input->post('method')=='delete'){
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('delete_contract',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/

					/******************************/
					/* Section 2 - Validate Input */
					/******************************/

					/**********************************/
					/* Section 3 - Database Operation */
					$this->contract->delete($contract_id);
					/**********************************/

					/********************************/
					/* Section 4 - Prepare Response */
					/********************************/

					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('delete_contract',$token);
					/*****************************/

					$this->respond(204,array());
				}
			}
		}
		else if($request_type=='GET'){
			if($contract_id>0){
				$token=$this->input->get_post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('read_contract',$token);
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
				$contract=$this->contract->read($contract_id);
				/********************************/

				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('read_contract',$token);
				/*****************************/

				$this->respond(200,$contract);
			}
			else{
				$this->list_contracts();
			}
		}
	}
	private function list_contracts(){
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_contract',$token);
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
		$this->db->from('contracts');
		$contracts=$this->db->get()->result();
		$response=new stdClass;

		foreach($contracts as &$contract){
			$value=$contract->id;
			$caption=new stdClass;
			$caption->caption=$contract->name;
			$response->$value=$caption;
		}
		/********************************/

		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_contract',$token);
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
