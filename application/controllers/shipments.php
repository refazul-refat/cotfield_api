<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shipments extends CI_Controller {

	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}

	public function index(){

		$request_type=$_SERVER['REQUEST_METHOD'];
		$shipment_id=$this->uri->segment(2,0);

		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		$this->load->model('shipment');
		if($request_type=='POST'){
			if($shipment_id==0){
				$shipment=new stdClass;
				$shipment->date=$this->input->post('shipment_date');
				$shipment->type=$this->input->post('shipment_type');
				$shipment->partial=$this->input->post('shipment_partial');
				$shipment->transshipment=$this->input->post('shipment_transshipment');
				$shipment->loading_port=$this->input->post('shipment_loading_port');
				$shipment->discharge_port=$this->input->post('shipment_discharge_port');
				$shipment->document_arrival=$this->input->post('shipment_document_arrival');
				$shipment->document=$this->input->post('shipment_document');
				$shipment->courier_details=$this->input->post('shipment_courier_details');

				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_shipment',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/

				/******************************/
				/* Section 2 - Validate Input */
				//if(!$shipment->no)$this->respond('400',array('error'=>'empty_no'));
				/******************************/

				/**********************************/
				/* Section 3 - Database Operation */
				$shipment_id=$this->shipment->create($shipment);
				/**********************************/

				/********************************/
				/* Section 4 - Prepare Response */
				$shipment=$this->shipment->read($shipment_id);
				/********************************/

				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_shipment',$token);
				/*****************************/

				$this->respond(201,$shipment);
			}
			else{
				if($this->input->post('method')=='update'){
					$shipment=new stdClass;
					if($this->input->post('shipment_date'))$shipment->date=$this->input->post('shipment_date');
					if($this->input->post('shipment_type'))$shipment->type=$this->input->post('shipment_type');
					if($this->input->post('shipment_partial'))$shipment->partial=$this->input->post('shipment_partial');
					if($this->input->post('shipment_transshipment'))$shipment->transshipment=$this->input->post('shipment_transshipment');
					if($this->input->post('shipment_loading_port'))$shipment->loading_port=$this->input->post('shipment_loading_port');
					if($this->input->post('shipment_discharge_port'))$shipment->discharge_port=$this->input->post('shipment_discharge_port');
					if($this->input->post('shipment_document_arrival'))$shipment->document_arrival=$this->input->post('shipment_document_arrival');
					if($this->input->post('shipment_document'))$shipment->document=$this->input->post('shipment_document');
					if($this->input->post('shipment_courier_details'))$shipment->courier_details=$this->input->post('shipment_courier_details');

					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('update_shipment',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/

					/******************************/
					/* Section 2 - Validate Input */
					/******************************/

					/**********************************/
					/* Section 3 - Database Operation */
					$array=(array)$shipment;
					if(!empty($array))$this->shipment->update($shipment_id,$shipment);
					/**********************************/

					/********************************/
					/* Section 4 - Prepare Response */
					unset($shipment);
					$shipment=$this->shipment->read($shipment_id);
					/********************************/

					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('update_shipment',$token);
					/*****************************/

					$this->respond(200,$shipment);
				}
				else if($this->input->post('method')=='delete'){
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('delete_shipment',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/

					/******************************/
					/* Section 2 - Validate Input */
					/******************************/

					/**********************************/
					/* Section 3 - Database Operation */
					$this->shipment->delete($shipment_id);
					/**********************************/

					/********************************/
					/* Section 4 - Prepare Response */
					/********************************/

					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('delete_shipment',$token);
					/*****************************/

					$this->respond(204,array());
				}
			}
		}
		else if($request_type=='GET'){
			if($shipment_id>0){
				$token=$this->input->get_post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('read_shipment',$token);
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
				$shipment=$this->shipment->read($shipment_id);
				/********************************/

				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('read_shipment',$token);
				/*****************************/

				$this->respond(200,$shipment);
			}
			else{
				$this->list_shipments();
			}
		}
	}
	private function list_shipments(){
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_shipment',$token);
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
		$this->db->from('shipments');
		$shipments=$this->db->get()->result();
		$response=new stdClass;

		foreach($shipments as &$shipment){
			$value=$shipment->id;
			$caption=new stdClass;
			$caption->caption=$shipment->name;
			$response->$value=$caption;
		}
		/********************************/

		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_shipment',$token);
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
