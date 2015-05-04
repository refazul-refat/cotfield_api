<?php
class Controller extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    public function assign($controller_id,$project_id){
    	//Assigns a controller to a project
    	//Leaves the old controller unattached, unremoved
    	$this->db->select('id');
    	$this->db->from('tree');
    	$this->db->where('item_type','project');
    	$this->db->where('item_id',$project_id);
    	
    	$id=$this->db->get()->row()->id;
    	
    	$this->db->where('item_type','controller');
    	$this->db->where('parent',$id);
    	$this->db->delete('tree');
    	
    	if($this->db->insert('tree',array('item_type'=>'controller','parent'=>$id,'item_id'=>$controller_id)))
    		return true;
    	return false;
    }
    public function create($data){
		//Creates a controller
		$this->db->insert('controllers',$data);
		return $this->db->insert_id();
	}
	public function read($controller_id){
		//Reads a controller
		$this->db->select('*');
		$this->db->from('controllers');
		$this->db->where('id',$controller_id);
		
		$controller=$this->db->get()->row();
		if(count($controller)>0)
			return $controller;
		return false;
	}
	public function update($controller_id,$data){
		//Updates a controller
		unset($data->id);
		$this->db->where('id',$controller_id);
		$this->db->update('controllers',$data);
	}
	public function delete($controller_id){
		//Deletes a controller
		$this->db->where('id',$controller_id);
		$this->db->delete('controllers');
		return true;
	}
}
