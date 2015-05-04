<?php
class Import_permit extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    public function assign($import_permit_id,$project_id){
    	//Assigns a import_permit to a project
    	//Leaves the old import_permit unattached, unremoved
    	$this->db->select('id');
    	$this->db->from('tree');
    	$this->db->where('item_type','project');
    	$this->db->where('item_id',$project_id);
    	
    	$id=$this->db->get()->row()->id;
    	
    	$this->db->where('item_type','import_permit');
    	$this->db->where('parent',$id);
    	$this->db->delete('tree');
    	
    	if($this->db->insert('tree',array('item_type'=>'import_permit','parent'=>$id,'item_id'=>$import_permit_id)))
    		return true;
    	return false;
    }
    public function create($data){
		//Creates a import_permit
		$this->db->insert('import_permits',$data);
		return $this->db->insert_id();
	}
	public function read($import_permit_id){
		//Reads a import_permit
		$this->db->select('*');
		$this->db->from('import_permits');
		$this->db->where('id',$import_permit_id);
		
		$import_permit=$this->db->get()->row();
		if(count($import_permit)>0)
			return $import_permit;
		return false;
	}
	public function update($import_permit_id,$data){
		//Updates a import_permit
		unset($data->id);
		$this->db->where('id',$import_permit_id);
		$this->db->update('import_permits',$data);
	}
	public function delete($import_permit_id){
		//Deletes a import_permit
		$this->db->where('id',$import_permit_id);
		$this->db->delete('import_permits');
		return true;
	}
}
