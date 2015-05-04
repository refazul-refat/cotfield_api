<?php
class Shipment extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    public function assign($shipment_id,$project_id){
    	//Assigns a shipment to a project
    	//Leaves the old shipment unattached, unremoved
    	$this->db->select('id');
    	$this->db->from('tree');
    	$this->db->where('item_type','project');
    	$this->db->where('item_id',$project_id);
    	
    	$id=$this->db->get()->row()->id;
    	
    	$this->db->where('item_type','shipment');
    	$this->db->where('parent',$id);
    	$this->db->delete('tree');
    	
    	if($this->db->insert('tree',array('item_type'=>'shipment','parent'=>$id,'item_id'=>$shipment_id)))
    		return true;
    	return false;
    }
    public function create($data){
		//Creates a shipment
		$this->db->insert('shipments',$data);
		return $this->db->insert_id();
	}
	public function read($shipment_id){
		//Reads a shipment
		$this->db->select('*');
		$this->db->from('shipments');
		$this->db->where('id',$shipment_id);
		
		$shipment=$this->db->get()->row();
		if(count($shipment)>0)
			return $shipment;
		return false;
	}
	public function update($shipment_id,$data){
		//Updates a shipment
		unset($data->id);
		$this->db->where('id',$shipment_id);
		$this->db->update('shipments',$data);
	}
	public function delete($shipment_id){
		//Deletes a shipment
		$this->db->where('id',$shipment_id);
		$this->db->delete('shipments');
		return true;
	}
}
