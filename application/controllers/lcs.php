<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lcs extends CI_Controller {

	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}

	public function index(){

		$request_type=$_SERVER['REQUEST_METHOD'];
		$lc_id=$this->uri->segment(2,0);

		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		$this->load->model('lc');
		if($request_type=='POST'){
			if($lc_id==0){
				$lc=new stdClass;
				$lc->no=$this->input->post('lc_no');
				$lc->issue_date=$this->input->post('lc_issue_date');
				$lc->type=$this->input->post('lc_type');
				$lc->opening_bank=$this->input->post('lc_opening_bank');
				$lc->receiving_bank=$this->input->post('lc_receiving_bank');
				$lc->copy=$this->input->post('lc_copy');
				$lc->amendment_documents=$this->input->post('lc_amendment_documents');
				$lc->amendment_date=$this->input->post('lc_amendment_date');
				//$lc->maturity_notification=$this->input->post('lc_maturity_notification');

				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_lc',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/

				/******************************/
				/* Section 2 - Validate Input */
				if(!$lc->no)$this->respond('400',array('error'=>'empty_no'));
				/******************************/

				/**********************************/
				/* Section 3 - Database Operation */
				$lc_id=$this->lc->create($lc);
				/**********************************/

				/********************************/
				/* Section 4 - Prepare Response */
				$lc=$this->lc->read($lc_id);
				/********************************/

				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_lc',$token);
				/*****************************/

				$this->respond(201,$lc);
			}
			else{
				if($this->input->post('method')=='update'){
					$lc=new stdClass;
					if($this->input->post('lc_no'))$lc->no=$this->input->post('lc_no');
					if($this->input->post('lc_issue_date'))$lc->issue_date=$this->input->post('lc_issue_date');
					if($this->input->post('lc_type'))$lc->type=$this->input->post('lc_type');
					if($this->input->post('lc_opening_bank'))$lc->opening_bank=$this->input->post('lc_opening_bank');
					if($this->input->post('lc_receiving_bank'))$lc->receiving_bank=$this->input->post('lc_receiving_bank');
					if($this->input->post('lc_copy'))$lc->copy=$this->input->post('lc_copy');
					if($this->input->post('lc_amendment_documents'))$lc->amendment_documents=$this->input->post('lc_amendment_documents');
					if($this->input->post('lc_amendment_date'))$lc->amendment_date=$this->input->post('lc_amendment_date');
					//if($this->input->post('lc_maturity_notification'))$lc->maturity_notification=$this->input->post('lc_maturity_notification');

					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('update_lc',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/

					/******************************/
					/* Section 2 - Validate Input */
					/******************************/

					/**********************************/
					/* Section 3 - Database Operation */
					$array=(array)$lc;
					if(!empty($array))$this->lc->update($lc_id,$lc);
					/**********************************/

					/********************************/
					/* Section 4 - Prepare Response */
					unset($lc);
					$lc=$this->lc->read($lc_id);
					/********************************/

					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('update_lc',$token);
					/*****************************/

					$this->respond(200,$lc);
				}
				else if($this->input->post('method')=='delete'){
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('delete_lc',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/

					/******************************/
					/* Section 2 - Validate Input */
					/******************************/

					/**********************************/
					/* Section 3 - Database Operation */
					$this->lc->delete($lc_id);
					/**********************************/

					/********************************/
					/* Section 4 - Prepare Response */
					/********************************/

					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('delete_lc',$token);
					/*****************************/

					$this->respond(204,array());
				}
			}
		}
		else if($request_type=='GET'){
			if($lc_id>0){
				$token=$this->input->get_post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('read_lc',$token);
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
				$lc=$this->lc->read($lc_id);
				/********************************/

				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('read_lc',$token);
				/*****************************/

				$this->respond(200,$lc);
			}
			else{
				$this->list_lcs();
			}
		}
	}
	private function list_lcs(){
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_lc',$token);
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
		$this->db->from('lcs');
		$lcs=$this->db->get()->result();
		$response=new stdClass;

		foreach($lcs as &$lc){
			$value=$lc->id;
			$caption=new stdClass;
			$caption->caption=$lc->name;
			$response->$value=$caption;
		}
		/********************************/

		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_lc',$token);
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
