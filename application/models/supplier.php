<?php
class Supplier extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    public function assign($supplier_id,$project_id){
    	//Assigns a supplier to a project
    	//Leaves the old supplier unattached, unremoved
    	$this->db->select('id');
    	$this->db->from('tree');
    	$this->db->where('item_type','project');
    	$this->db->where('item_id',$project_id);

    	$id=$this->db->get()->row()->id;

    	$this->db->where('item_type','supplier');
    	$this->db->where('parent',$id);
    	$this->db->delete('tree');

    	if($this->db->insert('tree',array('item_type'=>'supplier','parent'=>$id,'item_id'=>$supplier_id)))
    		return true;
    	return false;
    }
    public function create($data){
		//Creates a supplier
      $this->db->select('id');
		$this->db->from('suppliers');
		$this->db->where('name',$data->name);
		$result=$this->db->get()->row();
		if($result)return $result->id;

		$this->db->insert('suppliers',$data);
		return $this->db->insert_id();
	}
	public function read($supplier_id){
		//Reads a supplier
		$this->db->select('*');
		$this->db->from('suppliers');
		$this->db->where('id',$supplier_id);

		$supplier=$this->db->get()->row();
		if(count($supplier)>0)
			return $supplier;
		return false;
	}
	public function update($supplier_id,$data){
		//Updates a supplier
		unset($data->id);
		$this->db->where('id',$supplier_id);
		$this->db->update('suppliers',$data);
	}
	public function delete($supplier_id){
		//Deletes a supplier
		$this->db->where('id',$supplier_id);
		$this->db->delete('suppliers');
		return true;
	}
}
