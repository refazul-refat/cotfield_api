<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products extends CI_Controller {

	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}

	public function index(){

		$request_type=$_SERVER['REQUEST_METHOD'];
		$product_id=$this->uri->segment(2,0);

		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		$this->load->model('product');
		if($request_type=='POST'){
			if($product_id==0){
				$product=new stdClass;
				$product->name=$this->input->post('product_name');
				$product->origin=$this->input->post('product_origin');
				$product->quality=$this->input->post('product_quality');
				$product->quantity=$this->input->post('product_quantity');
				$product->unit_quantity=$this->input->post('product_unit_quantity');
				$product->unit_price=$this->input->post('product_unit_price');
				$product->unit_price_currency=$this->input->post('product_unit_price_currency');
				$product->pi_document=$this->input->post('product_pi_document');
				$product->pi_date=$this->input->post('product_pi_date');
				$product->pi_no=$this->input->post('product_pi_no');

				$token=$this->input->post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('create_product',$token);
				if($status!='authorized')$this->respond('400',array('error'=>$status));
				/*************************/

				/******************************/
				/* Section 2 - Validate Input */
				if(!$product->name)$this->respond('400',array('error'=>'empty_name'));
				/******************************/

				/**********************************/
				/* Section 3 - Database Operation */
				$product_id=$this->product->create($product);
				/**********************************/

				/********************************/
				/* Section 4 - Prepare Response */
				$product=$this->product->read($product_id);
				/********************************/

				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('create_product',$token);
				/*****************************/

				$this->respond(201,$product);
			}
			else{
				if($this->input->post('method')=='update'){
					$product=new stdClass;
					if($this->input->post('product_name'))$product->name=$this->input->post('product_name');
					if($this->input->post('product_origin'))$product->origin=$this->input->post('product_origin');
					if($this->input->post('product_quality'))$product->quality=$this->input->post('product_quality');
					if($this->input->post('product_quantity'))$product->quantity=$this->input->post('product_quantity');
					if($this->input->post('product_unit_quantity'))$product->unit_quantity=$this->input->post('product_unit_quantity');
					if($this->input->post('product_unit_price'))$product->unit_price=$this->input->post('product_unit_price');
					if($this->input->post('product_unit_price_currency'))$product->unit_price_currency=$this->input->post('product_unit_price_currency');
					if($this->input->post('product_pi_document'))$product->pi_document=$this->input->post('product_pi_document');
					if($this->input->post('product_pi_date'))$product->pi_date=$this->input->post('product_pi_date');
					if($this->input->post('product_pi_no'))$product->pi_no=$this->input->post('product_pi_no');

					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('update_product',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/

					/******************************/
					/* Section 2 - Validate Input */
					/******************************/

					/**********************************/
					/* Section 3 - Database Operation */
					$array=(array)$product;
					if(!empty($array))$this->product->update($product_id,$product);
					/**********************************/

					/********************************/
					/* Section 4 - Prepare Response */
					unset($product);
					$product=$this->product->read($product_id);
					/********************************/

					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('update_product',$token);
					/*****************************/

					$this->respond(200,$product);
				}
				else if($this->input->post('method')=='delete'){
					$token=$this->input->post('token');
					/*************************/
					/* Section 1 - Authorize */
					if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
					$status=$this->authorize->client_can('delete_product',$token);
					if($status!='authorized')$this->respond('400',array('error'=>$status));
					/*************************/

					/******************************/
					/* Section 2 - Validate Input */
					/******************************/

					/**********************************/
					/* Section 3 - Database Operation */
					$this->product->delete($product_id);
					/**********************************/

					/********************************/
					/* Section 4 - Prepare Response */
					/********************************/

					/*****************************/
					/* Section 5 - Consume Token */
					$this->request->dispatch('delete_product',$token);
					/*****************************/

					$this->respond(204,array());
				}
			}
		}
		else if($request_type=='GET'){
			if($product_id>0){
				$token=$this->input->get_post('token');
				/*************************/
				/* Section 1 - Authorize */
				if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
				$status=$this->authorize->client_can('read_product',$token);
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
				$product=$this->product->read($product_id);
				/********************************/

				/*****************************/
				/* Section 5 - Consume Token */
				$this->request->dispatch('read_product',$token);
				/*****************************/

				$this->respond(200,$product);
			}
			else{
				$this->list_products();
			}
		}
	}
	private function list_products(){
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_product',$token);
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
		$this->db->from('products');
		$products=$this->db->get()->result();
		$response=new stdClass;

		foreach($products as &$product){
			$value=$product->id;
			$caption=new stdClass;
			$caption->caption=$product->name;
			$response->$value=$caption;
		}
		/********************************/

		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_product',$token);
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
