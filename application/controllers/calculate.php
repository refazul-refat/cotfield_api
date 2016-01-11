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
		$factor=2204.62;

		// Default lbs
		$controller_invoice_weight=$controller->invoice_weight;
		if($controller->invoice_weight_unit=='mton'){
			$controller_invoice_weight=$controller->invoice_weight * $factor;
		}
		else if($controller->invoice_weight_unit=='kgs'){
			$controller_invoice_weight=$controller->invoice_weight * $factor / 1000;
		}

		// Default lbs
		$controller_final_weight=$controller->final_weight;
		if($controller->final_weight_unit=='mton'){
			$controller_final_weight=$controller->final_weight * $factor;
		}
		else if($controller->final_weight_unit=='kgs'){
			$controller_final_weight=$controller->final_weight * $factor / 1000;
		}

		$claim_weight=$controller_invoice_weight-$controller_final_weight;
		$claim_amount=$claim_weight * $product->unit_price;
		$claim_amount_usd=$claim_amount / 100;

		$this->respond(200,array('claim_weight'=>$claim_weight,
							'claim_weight_unit'=>'lbs',
							'claim_amount'=>$claim_amount_usd,
							'claim_amount_currency'=>'USD'));
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

		$point_value=$this->point_value($pid);
		$commission_amount_usd+=$point_value;

		$this->respond(200,array('commission_amount'=>$commission_amount_usd,
							'commission_amount_currecny'=>'USD'));
	}
	function point_value($pid){
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
			if($result->item_type=='controller'){
				$this->db->select('invoice_weight,invoice_weight_unit');
				$this->db->from('controllers');
				$this->db->where('id',$result->item_id);

				$controller=$this->db->get()->row();
			}
			else if($result->item_type=='contract'){
				$this->db->select('point_per10k');
				$this->db->from('contracts');
				$this->db->where('id',$result->item_id);

				$contract=$this->db->get()->row();
			}
		}
		$factor=2204.62;

		// Default lbs
		$controller_invoice_weight=$controller->invoice_weight;
		if($controller->invoice_weight_unit=='mton'){
			$controller_invoice_weight=$controller->invoice_weight * $factor;
		}
		else if($controller->invoice_weight_unit=='kgs'){
			$controller_invoice_weight=$controller->invoice_weight * $factor / 1000;
		}

		$contract_point_per10k=$contract->point_per10k;

		return $controller_invoice_weight * $contract_point_per10k / 10000;
	}
}
