<?php
class Customer extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    public function assign($customer_id,$project_id){
    	//Assigns a customer to a project
    	//Leaves the old customer unattached, unremoved
    	$this->db->select('id');
    	$this->db->from('tree');
    	$this->db->where('item_type','project');
    	$this->db->where('item_id',$project_id);
    	
    	$id=$this->db->get()->row()->id;
    	
    	$this->db->where('item_type','customer');
    	$this->db->where('parent',$id);
    	$this->db->delete('tree');
    	
    	if($this->db->insert('tree',array('item_type'=>'customer','parent'=>$id,'item_id'=>$customer_id)))
    		return true;
    	return false;
    }
    public function create($data){
		//Creates a customer
		$this->db->insert('customers',$data);
		return $this->db->insert_id();
	}
	public function read($customer_id){
		//Reads a customer
		$this->db->select('*');
		$this->db->from('customers');
		$this->db->where('id',$customer_id);
		
		$customer=$this->db->get()->row();
		if(count($customer)>0)
			return $customer;
		return false;
	}
	public function update($customer_id,$data){
		//Updates a customer
		unset($data->id);
		$this->db->where('id',$customer_id);
		$this->db->update('customers',$data);
	}
	public function delete($customer_id){
		//Deletes a customer
		$this->db->where('id',$customer_id);
		$this->db->delete('customers');
		return true;
	}
}
