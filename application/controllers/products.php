<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products extends CI_Controller {
	
	public function respond($http_response_code,$message){
		header("Content-Type: application/json");
		header("Access-Control-Allow-Origin: *");
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$product_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		if($request_type=='POST'){
			if($product_id==0)
				$this->new_product();
			else{
				
			}
		}
		else if($request_type=='GET'){
			if($product_id>0){
				$this->get_product($product_id);
			}
		}
	}
	private function new_product(){
		
		$name=$this->input->post('product_name');
		$type=$this->input->post('product_type');
		$origin=$this->input->post('product_origin');
		$quantity=$this->input->post('product_quantity');
		$unit_quantity=$this->input->post('product_unit_quantity');
		$unit_price=$this->input->post('product_unit_price');
		$unit_price_currency=$this->input->post('product_unit_price_currency');
		$token=$this->input->post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('create_product',$token);
		if($status!='authorized')$this->respond('400',array('error'=>$status));
		/*************************/
		
		/******************************/
		/* Section 2 - Validate Input */
		if(!$name)$this->respond('400',array('error'=>'empty_name'));
		if(!$type)$this->respond('400',array('error'=>'empty_type'));
		if(!$origin)$this->respond('400',array('error'=>'empty_origin'));
		
		if(!is_numeric($quantity))$this->respond('400',array('error'=>'invalid_quantity'));
		if(!is_numeric($unit_price))$this->respond('400',array('error'=>'invalid_unit_price'));
		
		if(!in_array(strtolower($unit_quantity),array('lbs','kg')))$this->respond('400',array('error'=>'invalid_unit_weight'));
		if(!in_array(strtolower($unit_price_currency),array('usd','gbp','inr','bdt')))$this->respond('400',array('error'=>'invalid_unit_price_currency'));
		/******************************/
		
		/**********************************/
		/* Section 3 - Database Operation */
		$this->db->insert('products',array('name'=>$name,
											'type'=>$type,
											'origin'=>$origin,
											'quantity'=>$quantity,
											'unit_quantity'=>$unit_quantity,
											'unit_price'=>$unit_price,
											'unit_price_currency'=>$unit_price_currency
											));
		$product_id=$this->db->insert_id();
		/**********************************/
		
		/********************************/
		/* Section 4 - Prepare Response */
		$this->db->select('*');
		$this->db->from('products');
		$this->db->where('id',$product_id);
		$product=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('create_product',$token);
		/*****************************/
		
		$this->respond(201,$product);
	}
	private function get_product($product_id){
		
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
		$this->db->select('*');
		$this->db->from('products');
		$this->db->where('id',$product_id);
		$product=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_product',$token);
		/*****************************/
		
		$this->respond(200,$product);
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
