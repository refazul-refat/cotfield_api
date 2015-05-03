<?php
class Contract extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    public function assign($contract_id,$project_id){
    	//Assigns a contract to a project
    	//Leaves the old contract unattached, unremoved
    	$this->db->select('id');
    	$this->db->from('tree');
    	$this->db->where('item_type','project');
    	$this->db->where('item_id',$project_id);
    	
    	$id=$this->db->get()->row()->id;
    	
    	$this->db->where('item_type','contract');
    	$this->db->where('parent',$id);
    	$this->db->delete('tree');
    	
    	if($this->db->insert('tree',array('item_type'=>'contract','parent'=>$id,'item_id'=>$contract_id)))
    		return true;
    	return false;
    }
    public function create($data){
		//Creates a contract
		$this->db->insert('contracts',$data);
		return $this->db->insert_id();
	}
	public function read($contract_id){
		//Reads a contract
		$this->db->select('*');
		$this->db->from('contracts');
		$this->db->where('id',$contract_id);
		
		$contract=$this->db->get()->row();
		if(count($contract)>0)
			return $contract;
		return false;
	}
	public function update($contract_id,$data){
		//Updates a contract
		unset($data->id);
		$this->db->where('id',$contract_id);
		$this->db->update('contracts',$data);
	}
	public function delete($contract_id){
		//Deletes a contract
		$this->db->where('id',$contract_id);
		$this->db->delete('contracts');
		return true;
	}
}
