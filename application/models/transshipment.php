<?php
class Transshipment extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    public function assign($transshipment_id,$project_id){
    	//Assigns a transshipment to a project
    	//Leaves the old transshipment unattached, unremoved
    	$this->db->select('id');
    	$this->db->from('tree');
    	$this->db->where('item_type','project');
    	$this->db->where('item_id',$project_id);
    	
    	$id=$this->db->get()->row()->id;
    	
    	$this->db->where('item_type','transshipment');
    	$this->db->where('parent',$id);
    	$this->db->delete('tree');
    	
    	if($this->db->insert('tree',array('item_type'=>'transshipment','parent'=>$id,'item_id'=>$transshipment_id)))
    		return true;
    	return false;
    }
    public function create($data){
		//Creates a transshipment
		$this->db->insert('transshipments',$data);
		return $this->db->insert_id();
	}
	public function read($transshipment_id){
		//Reads a transshipment
		$this->db->select('*');
		$this->db->from('transshipments');
		$this->db->where('id',$transshipment_id);
		
		$transshipment=$this->db->get()->row();
		if(count($transshipment)>0)
			return $transshipment;
		return false;
	}
	public function update($transshipment_id,$data){
		//Updates a transshipment
		unset($data->id);
		$this->db->where('id',$transshipment_id);
		$this->db->update('transshipments',$data);
	}
	public function delete($transshipment_id){
		//Deletes a transshipment
		$this->db->where('id',$transshipment_id);
		$this->db->delete('transshipments');
		return true;
	}
}
