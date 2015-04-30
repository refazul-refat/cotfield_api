<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	function base64url_encode($s) {
		return str_replace(array('+', '/'), array('-', '_'), base64_encode($s));
	}

	function base64url_decode($s) {
   		return base64_decode(str_replace(array('-', '_'), array('+', '/'), $s));
	}
	public function index()
	{
		if($this->input->post('user') && $this->input->post('pass')){
			$this->load->library('encrypt');
			$key = 'super-secret-key';
			$this->db->select('*');
			$this->db->from('users');
			$this->db->where('user',$this->input->post('user'));
			
			$user=$this->db->get()->row();
			if(count($user)>0){

				if(md5($this->input->post('pass'))==$user->pass){
					$cipher = $this->encrypt->encode('ok,'.$user->id.','.$user->user, $key);
					header('Location:'.$this->input->post('origin').'index.php/authenticate?token='.$this->base64url_encode($cipher));
					die();
				}			
			}
			$cipher = $this->encrypt->encode('no', $key);
			header('Location:'.$this->input->post('origin').'index.php/authenticate?token='.$this->base64url_encode($cipher));
			die();
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */