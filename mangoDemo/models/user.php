<?php

class User_Model extends Mango {

	protected $_belongs_to = array('account');
	protected $_has_many = array('blogs');
	protected $_has_and_belongs_to_many = array('groups');

	protected $_columns = array(
	 	'role'       => array('type'=>'enum','values'=>array('viewer','contributor','manager','administrator','owner','specific')),
	 	'email'      => array('type'=>'string')
	);

	protected $_db = 'demo'; //don't use default db config

	// Validate
	public function validate(array & $array, $save = FALSE)
	{
		$array = Validation::factory($array)
			->pre_filter('trim')
			->add_rules('email', 'required', 'length[4,127]','valid::email',array($this,'is_unique'))
			->add_rules('role','required') //,'matches[viewer,contributor,manager,administrator,owner,specific]')
			->add_rules('account_id','required');

		return parent::validate($array, $save);
	}
	
	public function is_unique($email)
	{
		if ($this->_loaded AND $this->_object['email'] === $email)
		{
			// This value is unchanged
			return TRUE;
		}

		return $this->_db->find_one($this->_collection_name,array('email' => $email ) ) === NULL;
	}

	// Allows users to be loaded by email.
	public function unique_criteria($id)
	{
		if ( ! empty($id) AND is_string($id) AND valid::email($id))
			return array('email'=>$id);

		return parent::unique_criteria($id);
	}
}