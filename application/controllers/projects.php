<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Projects extends CI_Controller {
	
	public function respond($http_response_code,$message){
		header("Content-Type: application/json");
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}
	
	public function index(){
		
		$request_type=$_SERVER['REQUEST_METHOD'];
		$project_id=$this->uri->segment(2,0);
		
		header('Content-Type: application/json');
		if($request_type=='POST'){
			if($project_id==0)
				$this->new_project();
			else if($project_id>0){
				$class=$this->uri->segment(3,FALSE);
				if(!$class)die();
				if(!in_array($class,array('customer','supplier','product','contract','import_permit','lc')))die();
				
				$this->db->select('step');
				$this->db->from('steps');
				$this->db->where('entity',$class);
				$target_step=$this->db->get()->row()->step;
				
				$this->db->select('current_step');
				$this->db->from('projects');
				$this->db->where('id',$project_id);
				$current_step=$this->db->get()->row()->current_step;
				
				if($target_step<=$current_step){
					$id=$this->input->post('object_id');
					if(!$id)$this->respond(400,array('error'=>'no_object_id_provided'));
					
					$this->db->select('id');
					$this->db->from($class.'s');
					$this->db->where('id',$id);
					if($this->db->get()->num_rows()==0)$this->respond(400,array('error'=>'object_not_found'));
					
					$this->assign($class,$id,$project_id,$current_step);
				}
				else $this->respond(400,array('error'=>'slow_down_buddy'));
			}
		}
		else if($request_type=='GET'){
			if($project_id>0){
				$this->get_project($project_id);
			}
		}
	}
	private function new_project(){
		
		$name=$this->input->post('name');
		$token=$this->input->post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('create_project',$token);
		if($status!='authorized')$this->respond('400',array('error'=>$status));
		/*************************/
		
		/******************************/
		/* Section 2 - Validate Input */
		if(!$name)$this->respond('400',array('error'=>'empty_name'));
		/******************************/
		
		/**********************************/
		/* Section 3 - Database Operation */
		$this->db->insert('projects',array( 'name'=>$name,
											'description'=>$this->input->post('description')?$this->input->post('description'):'',
											'created_on'=>date('Y-m-d H:i:s'),
											'last_modified'=>date('Y-m-d H:i:s'),
											'current_step'=>0));
		$project_id=$this->db->insert_id();
		$this->db->insert('tree',array('item_id'=>$project_id,
										'item_type'=>'project',
										'parent'=>$this->input->post('parent')?$this->input->post('parent'):1
										));
		/**********************************/
		
		/********************************/
		/* Section 4 - Prepare Response */
		$this->db->select('*');
		$this->db->from('projects');
		$this->db->where('id',$project_id);
		$project=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('create_project',$token);
		/*****************************/
		
		$this->respond(201,$project);
	}
	private function assign($class,$id,$project_id,$current_step){
		
		$this->db->select('id');
		$this->db->from('tree');
		$this->db->where('item_type','project');
		$this->db->where('item_id',$project_id);
		$parent=$this->db->get()->row()->id;
		
		$this->db->select('id');
		$this->db->from('tree');
		$this->db->where('item_type',$class);
		//$this->db->where('item_id',$id);
		$this->db->where('parent',$parent);
		
		if($this->db->get()->num_rows()>0)$this->respond(400,array('relationship_already_exists'));
		
		$this->db->insert('tree',array('item_type'=>$class,'item_id'=>$id,'parent'=>$parent));
		
		$current_step+=1;
		$this->db->where('id',$project_id);
		$this->db->update('projects',array('current_step'=>$current_step));
		$this->respond(201,array('status'=>'relationship_created'));
		
	}
	private function get_project($project_id){
		
		$token=$this->input->get_post('token');
		/*************************/
		/* Section 1 - Authorize */
		if(!$token)$this->respond('400',array('error'=>'unauthorized_access'));
		$status=$this->authorize->client_can('read_project',$token);
		if($status!='authorized')$this->respond('400',array('error'=>$status));
		/*************************/
		
		/******************************/
		/* Section 2 - Validate Input */
		/******************************/
		
		/**********************************/
		/* Section 3 - Database Operation */
		/**********************************/
		
		/********************************/
		/* Section 4 - Prepare Response */
		$this->db->select('*');
		$this->db->from('projects');
		$this->db->where('id',$project_id);
		$project=$this->db->get()->row();
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		$this->request->dispatch('read_project',$token);
		/*****************************/
		
		$this->respond(200,$project);
	}
	private function skeleton(){
		
		/*************************/
		/* Section 1 - Authorize */
		/*************************/
		
		/******************************/
		/* Section 2 - Validate Input */
		/******************************/
		
		/**********************************/
		/* Section 3 - Database Operation */
		/**********************************/
		
		/********************************/
		/* Section 4 - Prepare Response */
		/********************************/
		
		/*****************************/
		/* Section 5 - Consume Token */
		/*****************************/
	}
	
	public function _remap(){
		
		$this->index();
	}
}
