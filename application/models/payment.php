<?php
class Payment extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    public function assign($payment_id,$project_id){
    	//Assigns a payment to a project
    	//Leaves the old payment unattached, unremoved
    	$this->db->select('id');
    	$this->db->from('tree');
    	$this->db->where('item_type','project');
    	$this->db->where('item_id',$project_id);
    	
    	$id=$this->db->get()->row()->id;
    	
    	$this->db->where('item_type','payment');
    	$this->db->where('parent',$id);
    	$this->db->delete('tree');
    	
    	if($this->db->insert('tree',array('item_type'=>'payment','parent'=>$id,'item_id'=>$payment_id)))
    		return true;
    	return false;
    }
    public function create($data){
		//Creates a payment
		$this->db->insert('payments',$data);
		return $this->db->insert_id();
	}
	public function read($payment_id){
		//Reads a payment
		$this->db->select('*');
		$this->db->from('payments');
		$this->db->where('id',$payment_id);
		
		$payment=$this->db->get()->row();
		if(count($payment)>0)
			return $payment;
		return false;
	}
	public function update($payment_id,$data){
		//Updates a payment
		unset($data->id);
		$this->db->where('id',$payment_id);
		$this->db->update('payments',$data);
	}
	public function delete($payment_id){
		//Deletes a payment
		$this->db->where('id',$payment_id);
		$this->db->delete('payments');
		return true;
	}
}
