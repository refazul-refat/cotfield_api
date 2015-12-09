<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calculate extends CI_Controller {
	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	public function invoice_amount_calculate($pid){
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
			else if($result->item_type=='controller'){
				$this->db->select('invoice_weight,invoice_weight_unit');
				$this->db->from('controllers');
				$this->db->where('id',$result->item_id);

				$controller=$this->db->get()->row();
			}
		}
		// Product unit price is always in usc/lbs
		// 1 mton=2204.62 lbs

		$factor=2204.62;

		// Default lbs
		$controller_invoice_weight=$controller->invoice_weight;
		if($controller->invoice_weight_unit=='mton'){
			$controller_invoice_weight=$controller->invoice_weight * $factor;
		}
		else if($controller->invoice_weight_unit=='kgs'){
			$controller_invoice_weight=$controller->invoice_weight * $factor / 1000;
		}

		$invoice_amount=$product->unit_price * $controller_invoice_weight;
		$invoice_amount_usd=$invoice_amount / 100;

		return array('code'=>200,'object'=>array('invoice_amount'=>$invoice_amount_usd,'currency'=>'USD'));
	}
	public function invoice_amount($pid){

		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");

		$response=$this->invoice_amount_calculate($pid);
		$this->respond($response['code'],$response['object']);
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
			else if($result->item_type=='controller'){
				$this->db->select('*');
				$this->db->from('controllers');
				$this->db->where('id',$result->item_id);

				$controller=$this->db->get()->row();
			}
		}
		$claim_weight=$controller->invoice_weight-$controller->final_weight;
		$claim_amount=$claim_weight * $product->unit_price;

		$this->respond(200,array('claim_weight'=>$claim_weight,
							'claim_weight_unit'=>$controller->invoice_weight_unit,
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
			else if($result->item_type=='controller'){
				$this->db->select('*');
				$this->db->from('controllers');
				$this->db->where('id',$result->item_id);

				$controller=$this->db->get()->row();
			}
		}
		$response=$this->invoice_amount_calculate($pid);

		$invoice_amount_usd=$response['object']['invoice_amount'];
		$commission_amount_usd=$invoice_amount_usd * $contract->commission_rate / 100;
		$this->respond(200,array('commission_amount'=>$commission_amount_usd,
							'commission_amount_currecny'=>'USD'));
	}
}
