<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calculate extends CI_Controller {
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	public function invoice_amount($pid){
		
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		
		$this->db->select('*');
		$this->db->from('tree');
		$this->db->where('item_type','project');
		$this->db->where('item_id',$pid);
		
		$parent=$this->db->get()->row()->id;
		
		$this->db->select('*');
		$this->db->from('tree');
		$this->db->where('parent',$parent);
		
		$results=$this->db->get()->result();
		
		foreach($results as $result){
			if($result->item_type=='product'){
				$this->db->select('unit_quantity,unit_price,unit_price_currency');
				$this->db->from('products');
				$this->db->where('id',$result->item_id);
				
				$product=$this->db->get()->row();
			}
			else if($result->item_type=='port'){
				$this->db->select('invoice_weight,invoice_weight_unit');
				$this->db->from('ports');
				$this->db->where('id',$result->item_id);
				
				$port=$this->db->get()->row();
			}
		}
		if(strtolower($product->unit_quantity)==strtolower($port->invoice_weight_unit)){
			$this->respond(200,array('invoice_amount'=>$product->unit_price*$port->invoice_weight,
										'currency'=>$product->unit_price_currency));
		}
	}
	public function claim_weight($pid){
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		
		$this->db->select('*');
		$this->db->from('tree');
		$this->db->where('item_type','project');
		$this->db->where('item_id',$pid);
		
		$parent=$this->db->get()->row()->id;
		
		$this->db->select('*');
		$this->db->from('tree');
		$this->db->where('parent',$parent);
		
		$results=$this->db->get()->result();
		
		foreach($results as $result){
			if($result->item_type=='product'){
				$this->db->select('*');
				$this->db->from('products');
				$this->db->where('id',$result->item_id);
				
				$product=$this->db->get()->row();
			}
			else if($result->item_type=='port'){
				$this->db->select('*');
				$this->db->from('ports');
				$this->db->where('id',$result->item_id);
				
				$port=$this->db->get()->row();
			}
			else if($result->item_type=='controller'){
				$this->db->select('*');
				$this->db->from('controllers');
				$this->db->where('id',$result->item_id);
				
				$controller=$this->db->get()->row();
			}
		}
		$claim_weight=$port->invoice_weight-$controller->final_weight;
		$claim_amount=$claim_weight*$product->unit_price;
		$this->respond(200,array('claim_weight'=>$claim_weight,
							'claim_weight_unit'=>$port->invoice_weight_unit,
							'claim_amount'=>$claim_amount,
							'claim_amount_currency'=>$product->unit_price_currency));
	}
	public function commission_amount($pid){
		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		
		$this->db->select('*');
		$this->db->from('tree');
		$this->db->where('item_type','project');
		$this->db->where('item_id',$pid);
		
		$parent=$this->db->get()->row()->id;
		
		$this->db->select('*');
		$this->db->from('tree');
		$this->db->where('parent',$parent);
		
		$results=$this->db->get()->result();
		
		foreach($results as $result){
			if($result->item_type=='product'){
				$this->db->select('*');
				$this->db->from('products');
				$this->db->where('id',$result->item_id);
				
				$product=$this->db->get()->row();
			}
			else if($result->item_type=='contract'){
				$this->db->select('*');
				$this->db->from('contracts');
				$this->db->where('id',$result->item_id);
				
				$contract=$this->db->get()->row();
			}
			else if($result->item_type=='port'){
				$this->db->select('*');
				$this->db->from('ports');
				$this->db->where('id',$result->item_id);
				
				$port=$this->db->get()->row();
			}
			else if($result->item_type=='controller'){
				$this->db->select('*');
				$this->db->from('controllers');
				$this->db->where('id',$result->item_id);
				
				$controller=$this->db->get()->row();
			}
		}
		$commission_amount=$port->invoice_weight*$contract->commission_rate/100;
		$this->respond(200,array('commission_amount'=>$commission_amount,
							'commission_amount_currecny'=>$product->unit_price_currency));
	}
}
