<?php
class Document extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    public function assign($document_id,$project_id){
    	//Assigns a document to a project
    	//Leaves the old document unattached, unremoved
    	$this->db->select('id');
    	$this->db->from('tree');
    	$this->db->where('item_type','project');
    	$this->db->where('item_id',$project_id);
    	
    	$id=$this->db->get()->row()->id;
    	
    	$this->db->where('item_type','document');
    	$this->db->where('parent',$id);
    	$this->db->delete('tree');
    	
    	if($this->db->insert('tree',array('item_type'=>'document','parent'=>$id,'item_id'=>$document_id)))
    		return true;
    	return false;
    }
    public function create($data){
		//Creates a document
		$this->db->insert('documents',$data);
		return $this->db->insert_id();
	}
	public function read($document_id){
		//Reads a document
		$this->db->select('*');
		$this->db->from('documents');
		$this->db->where('id',$document_id);
		
		$document=$this->db->get()->row();
		if(count($document)>0)
			return $document;
		return false;
	}
	public function update($document_id,$data){
		//Updates a document
		unset($data->id);
		$this->db->where('id',$document_id);
		$this->db->update('documents',$data);
	}
	public function delete($document_id){
		//Deletes a document
		$this->db->where('id',$document_id);
		$this->db->delete('documents');
		return true;
	}
}
