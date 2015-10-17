<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nodes extends CI_Controller {

	public function index(){
		header("Access-Control-Allow-Origin: *");

		$id=$this->uri->segment(2,FALSE);

		if(!$id){
			$id=$this->input->get_post('id');
		}

		$this->db->select('*');
		$this->db->from('tree');
		$this->db->where('parent',$id);

		$roots=$this->db->get()->result();
		foreach($roots as &$item){
			if($item->item_type=='folder' || $item->item_type=='project'){
				$this->db->select('name');
				$this->db->from($item->item_type.'s');
				$this->db->where('id',$item->item_id);

				$item->name=$this->db->get()->row()->name;
				$item->isParent=true;

				$this->db->select('*');
				$this->db->from('tree');
				$this->db->where('parent',$item->id);
				$item->child=count($this->db->get()->result());
			}
			else $item->name=$item->item_type;
		}
		echo json_encode($roots);die();
	}
	public function _remap(){
		$this->index();
	}
}
