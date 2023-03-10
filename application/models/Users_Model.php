<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model{

	protected $table = "ci_users";

	public function login($data){

		$this->db->from($this->table);
		$this->db->where('email', $data['email']);

		$query = $this->db->get();
		if ($query->num_rows() == 0){
			return false;
		}
		else{
			//Compare the password attempt with the password we have stored.
			$result = $query->row_array();
		    $validPassword = password_verify($data['password'], $result['password']);
		    if($validPassword){
		        return $result = $query->row_array();
		    }
		}
	}

	//--------------------------------------------------------------------
	public function register($data){
		$this->db->insert($this->table, $data);
		return true;
	}

	//--------------------------------------------------------------------
	public function email_verification($code){
		$this->db->select('email, token, is_active');
		$this->db->from('ci_users');
		$this->db->where('token', $code);
		$query = $this->db->get();
		$result= $query->result_array();
		$match = count($result);
		if($match > 0){
			$this->db->where('token', $code);
			$this->db->update($this->table, array('is_verify' => 1, 'token'=> ''));
			return true;
		}
		else{
			return false;
			}
	}

	//============ Check User Email ============
    function check_user_mail($email)
    {
    	$result = $this->db->get_where($this->table, array('email' => $email));

    	if($result->num_rows() > 0){
    		$result = $result->row_array();
    		return $result;
    	}
    	else {
    		return false;
    	}
    }

    //============ Update Reset Code Function ===================
    public function update_reset_code($reset_code, $user_id){
    	$data = array('password_reset_code' => $reset_code);
    	$this->db->where('user_id', $user_id);
    	$this->db->update($this->table, $data);
    }

    //============ Activation code for Password Reset Function ===================
    public function check_password_reset_code($code){

    	$result = $this->db->get_where($this->table,  array('password_reset_code' => $code ));
    	if($result->num_rows() > 0){
    		return true;
    	}
    	else{
    		return false;
    	}
    }
    
    //============ Reset Password ===================
    public function reset_password($id, $new_password){
	    $data = array(
			'password_reset_code' => '',
			'password' => $new_password
	    );
		$this->db->where('password_reset_code', $id);
		$this->db->update($this->table, $data);
		return true;
    }

    //--------------------------------------------------------------------
	public function get_admin_detail(){
		$id = $this->session->userdata('user_id');
		$query = $this->db->get_where($this->table, array('user_id' => $id));
		return $result = $query->row_array();
	}

	//--------------------------------------------------------------------
	public function update_admin($data){
		$id = $this->session->userdata('user_id');
		$this->db->where('user_id', $id);
		$this->db->update($this->table, $data);
		return true;
	}

	//--------------------------------------------------------------------
	public function change_pwd($data, $id){
		$this->db->where('user_id', $id);
		$this->db->update($this->table, $data);
		return true;
	}

}

?>
