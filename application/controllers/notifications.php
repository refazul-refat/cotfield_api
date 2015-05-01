<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notifications extends CI_Controller {
	
	private function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index()
	{
		$this->db->select('*');
		$this->db->from('tree');
		$this->db->where('item_type','project');
		$this->db->where('parent',2);
		
		$projects=$this->db->get()->result();
		
		$maturity_notifications=array();
		$payment_notifications=array();
		$buyer_notifications=array();
		
		$notifications=array();
		foreach($projects as $p){
			$lcs=array();
			$transshipments=array();
			
			$this->db->select('*');
			$this->db->from('tree');
			$this->db->where('parent',$p->id);
			
			$t=$this->db->get()->result();
			foreach($t as $z){
				if($z->item_type=='lc')
					$lcs[]=$z->item_id;
				else if($z->item_type=='transshipment')
					$transshipments[]=$z->item_id;
			}
			$this->db->select('name');
			$this->db->from('projects');
			$this->db->where('id',$p->item_id);
			$project_name=$this->db->get()->row()->name;
			
			foreach($lcs as $lc){
				$this->db->select('maturity_notification');
				$this->db->from('lcs');
				$this->db->where('id',$lc);
				
				$t=$this->db->get()->row();
				
				$n=new stdClass;
				$n->type='maturity';
				$n->step='lc';
				$n->pid=$p->item_id;
				$n->pname=$project_name;
				$n->deadline=$t->maturity_notification;
				
				$notifications[]=$n;
			}
			
			foreach($transshipments as $transshipment){
				$this->db->select('payment_notification,buyer_notification');
				$this->db->from('transshipments');
				$this->db->where('id',$transshipment);
				
				$t=$this->db->get()->row();
				
				$n=new stdClass;
				$n->type='payment';
				$n->step='transshipment';
				$n->pid=$p->item_id;
				$n->pname=$project_name;
				$n->deadline=$t->payment_notification;
				
				$notifications[]=$n;
				
				$n=new stdClass;
				$n->type='buyer';
				$n->step='transshipment';
				$n->pid=$p->item_id;
				$n->pname=$project_name;
				$n->deadline=$t->buyer_notification;
				
				$notifications[]=$n;
			}
		}
		
		header("Access-Control-Allow-Origin: *");
		header('Content-Type: application/json');
		
		$this->respond(200,$notifications);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */