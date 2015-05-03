<?php
class Product extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    public function assign($product_id,$project_id){
    	//Assigns a product to a project
    	//Leaves the old product unattached, unremoved
    	$this->db->select('id');
    	$this->db->from('tree');
    	$this->db->where('item_type','project');
    	$this->db->where('item_id',$project_id);
    	
    	$id=$this->db->get()->row()->id;
    	
    	$this->db->where('item_type','product');
    	$this->db->where('parent',$id);
    	$this->db->delete('tree');
    	
    	if($this->db->insert('tree',array('item_type'=>'product','parent'=>$id,'item_id'=>$product_id)))
    		return true;
    	return false;
    }
    public function create($data){
		//Creates a product
		$this->db->insert('products',$data);
		return $this->db->insert_id();
	}
	public function read($product_id){
		//Reads a product
		$this->db->select('*');
		$this->db->from('products');
		$this->db->where('id',$product_id);
		
		$product=$this->db->get()->row();
		if(count($product)>0)
			return $product;
		return false;
	}
	public function update($product_id,$data){
		//Updates a product
		unset($data->id);
		$this->db->where('id',$product_id);
		$this->db->update('products',$data);
	}
	public function delete($product_id){
		//Deletes a product
		$this->db->where('id',$product_id);
		$this->db->delete('products');
		return true;
	}
}
