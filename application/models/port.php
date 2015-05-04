<?php
class Port extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    public function assign($port_id,$project_id){
    	//Assigns a port to a project
    	//Leaves the old port unattached, unremoved
    	$this->db->select('id');
    	$this->db->from('tree');
    	$this->db->where('item_type','project');
    	$this->db->where('item_id',$project_id);
    	
    	$id=$this->db->get()->row()->id;
    	
    	$this->db->where('item_type','port');
    	$this->db->where('parent',$id);
    	$this->db->delete('tree');
    	
    	if($this->db->insert('tree',array('item_type'=>'port','parent'=>$id,'item_id'=>$port_id)))
    		return true;
    	return false;
    }
    public function create($data){
		//Creates a port
		$this->db->insert('ports',$data);
		return $this->db->insert_id();
	}
	public function read($port_id){
		//Reads a port
		$this->db->select('*');
		$this->db->from('ports');
		$this->db->where('id',$port_id);
		
		$port=$this->db->get()->row();
		if(count($port)>0)
			return $port;
		return false;
	}
	public function update($port_id,$data){
		//Updates a port
		unset($data->id);
		$this->db->where('id',$port_id);
		$this->db->update('ports',$data);
	}
	public function delete($port_id){
		//Deletes a port
		$this->db->where('id',$port_id);
		$this->db->delete('ports');
		return true;
	}
}
