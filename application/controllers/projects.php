<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Projects extends CI_Controller {

	public function respond($http_response_code,$message){
		http_response_code($http_response_code);
		echo json_encode($message);
		die();
	}

	public function index(){

		$request_type=$_SERVER['REQUEST_METHOD'];
		$project_id=$this->uri->segment(2,0);

		header('Content-Type: application/json');
		header("Access-Control-Allow-Origin: *");
		if($request_type=='POST'){
			if($project_id==0)
				$this->new_project();
			else if($project_id>0){
				if($this->input->post('method')=='delete'){
					$token=$this->input->post('token');

					$this->db->where('id',$project_id);
					$this->db->delete('projects');

					$this->db->select('id');
					$this->db->from('tree');
					$this->db->where('item_id',$project_id);
					$this->db->where('item_type','project');

					$temp=$this->db->get()->row();

					$this->db->where('item_id',$project_id);
					$this->db->where('item_type','project');
					$this->db->delete('tree');

					if(count($temp)>0){
						$this->db->where('parent',$temp->id);
						$this->db->delete('tree');

						$this->respond(204,array());
					}
				}
				else if($this->input->post('method')=='update'){
					$project=new stdClass;
					if($this->input->post('project_name'))$project->name=$this->input->post('project_name');
					if($this->input->post('project_description'))$project->description=$this->input->post('project_description');

					$this->db->select('*');
					$this->db->from('projects');
					$this->db->where('id',$project_id);

					$db_project=$this->db->get()->row();
					if(count($db_project)>0){
						$db_project->name=$project->name;
						$db_project->description=$project->description;

						unset($db_project->id);
						$this->db->where('id',$project_id);
						$this->db->update('projects',$db_project);
					}

					$this->respond(200,array('project'=>array('name'=>$project->name,'description'=>$project->description)));
				}
				else{
					$class=$this->uri->segment(3,FALSE);
					if(!$class)die();
					if(!in_array($class,array('bootstrap','customer','supplier','product','contract','import_permit','lc','shipment','document','transshipment','port','controller','payment')))die();
					if(in_array($class,array('customer','supplier'))){
						$id=$this->input->post('object_id');
						if(!$id)$this->respond(400,array('error'=>'no_object_id_provided'));

						$this->db->select('id');
						$this->db->from($class.'s');
						$this->db->where('id',$id);
						if($this->db->get()->num_rows()==0)$this->respond(400,array('error'=>'object_not_found'));

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

						if($this->db->get()->num_rows()>0){
							$this->db->where('item_type',$class);
							$this->db->where('parent',$parent);
							$this->db->update('tree',array('item_id'=>$id));
							$this->respond(201,array('relationship_updated'));
						}

						$this->db->insert('tree',array('item_type'=>$class,'item_id'=>$id,'parent'=>$parent));
						/*
						$current_step+=1;
						$this->db->where('id',$project_id);
						$this->db->update('projects',array('current_step'=>$current_step));
						*/
						$this->respond(201,array('status'=>'relationship_created'));
					}
					$this->db->select('step');
					$this->db->from('steps');
					$this->db->where('entity',$class);
					$target_step=$this->db->get()->row()->step;

					$this->db->select('current_step');
					$this->db->from('projects');
					$this->db->where('id',$project_id);
					$current_step=$this->db->get()->row()->current_step;

					if(($target_step-1)<=$current_step){
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
		}
		else if($request_type=='GET'){
			if($project_id>0){
				$class=$this->uri->segment(3,FALSE);
				if(in_array($class,array('bootstrap','customer','supplier','product','contract','import_permit','lc','shipment','document','transshipment','port','controller','payment'))){

					if($class=='bootstrap'){
						$this->db->select('id');
						$this->db->from('tree');
						$this->db->where('item_type','project');
						$this->db->where('item_id',$project_id);
						$parent=$this->db->get()->row()->id;

						$this->db->select('*');
						$this->db->from('tree');
						$this->db->where('item_type','customer');
						//$this->db->where('item_id',$id);
						$this->db->where('parent',$parent);

						$result=new stdClass;
						$result->customer_id=$this->db->get()->row()->item_id;

						$this->db->select('*');
						$this->db->from('customers');
						$this->db->where('id',$result->customer_id);

						$result->customer=$this->db->get()->row();

						$this->db->select('*');
						$this->db->from('tree');
						$this->db->where('item_type','supplier');
						//$this->db->where('item_id',$id);
						$this->db->where('parent',$parent);

						$result->supplier_id=$this->db->get()->row()->item_id;

						$this->db->select('*');
						$this->db->from('suppliers');
						$this->db->where('id',$result->supplier_id);

						$result->supplier=$this->db->get()->row();

						//
						$this->db->select('*');
						$this->db->from('tree');
						$this->db->where('item_type','shipment');
						$this->db->where('parent',$parent);

						$s=$this->db->get()->row();
						if($s){
							$result->shipment_id=$s->item_id;

							$this->db->select('*');
							$this->db->from('shipments');
							$this->db->where('id',$result->shipment_id);

							$result->shipment=$this->db->get()->row();
						}
						//

						$this->db->select('*');
						$this->db->from('projects');
						$this->db->where('id',$project_id);
						$result->project=$this->db->get()->row();

						$this->respond(200,$result);
						die();
					}
					$this->db->select('id');
					$this->db->from('tree');
					$this->db->where('item_type','project');
					$this->db->where('item_id',$project_id);
					$parent=$this->db->get()->row()->id;

					$this->db->select('*');
					$this->db->from('tree');
					$this->db->where('item_type',$class);
					//$this->db->where('item_id',$id);
					$this->db->where('parent',$parent);

					$result=$this->db->get()->row();

					if(count($result)>0)$this->respond(200,$result);
					else $this->respond(400,array('not_found'));
				}
				else $this->get_project($project_id);
			}
		}
		else if($request_type=='DELETE'){


		}
	}
	private function new_project(){

		$name=$this->input->post('project_name');
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
											'description'=>$this->input->post('project_description')?$this->input->post('project_description'):'',
											'created_on'=>date('Y-m-d H:i:s'),
											'last_modified'=>date('Y-m-d H:i:s'),
											'current_step'=>1));
		$project_id=$this->db->insert_id();
		$this->db->select('id');
		$this->db->from('folders');
		$this->db->where('name',$this->input->post('folder'));
		$parent=$this->db->get()->row();
		if(count($parent)>0){
			$this->db->select('*');
			$this->db->from('tree');
			$this->db->where('item_id',$parent->id);
			$this->db->where('item_type','folder');
			$parent=$this->db->get()->row();
			if(count($parent)>0)$parent=$parent->id;
			else $parent=2;
		}
		else $parent=2;
		$this->db->insert('tree',array('item_id'=>$project_id,
										'item_type'=>'project',
										'parent'=>$parent
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

		if($this->db->get()->num_rows()>0){
			$this->db->where('item_type',$class);
			$this->db->where('parent',$parent);
			$this->db->update('tree',array('item_id'=>$id));
			$this->respond(201,array('relationship_updated'));
		}

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
