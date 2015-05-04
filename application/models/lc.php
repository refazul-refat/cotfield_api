<?php
class Lc extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    public function assign($lc_id,$project_id){
    	//Assigns a lc to a project
    	//Leaves the old lc unattached, unremoved
    	$this->db->select('id');
    	$this->db->from('tree');
    	$this->db->where('item_type','project');
    	$this->db->where('item_id',$project_id);
    	
    	$id=$this->db->get()->row()->id;
    	
    	$this->db->where('item_type','lc');
    	$this->db->where('parent',$id);
    	$this->db->delete('tree');
    	
    	if($this->db->insert('tree',array('item_type'=>'lc','parent'=>$id,'item_id'=>$lc_id)))
    		return true;
    	return false;
    }
    public function create($data){
		//Creates a lc
		$this->db->insert('lcs',$data);
		return $this->db->insert_id();
	}
	public function read($lc_id){
		//Reads a lc
		$this->db->select('*');
		$this->db->from('lcs');
		$this->db->where('id',$lc_id);
		
		$lc=$this->db->get()->row();
		if(count($lc)>0)
			return $lc;
		return false;
	}
	public function update($lc_id,$data){
		//Updates a lc
		unset($data->id);
		$this->db->where('id',$lc_id);
		$this->db->update('lcs',$data);
	}
	public function delete($lc_id){
		//Deletes a lc
		$this->db->where('id',$lc_id);
		$this->db->delete('lcs');
		return true;
	}
}
