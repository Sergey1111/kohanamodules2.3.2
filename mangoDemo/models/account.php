<?php
class Account_Model extends Mango {

	protected $_has_many = array('users');

	protected $_columns = array(
	 	'name'         => array('type'=>'name'),
	 	'some_counter' => array('type'=>'int','null'=>true),
	 	'categories'   => array('type'=>'array','null'=>true)
	);

	protected $_db = 'demo'; //don't use default db config

	// Validate
	public function validate(array & $array, $save = FALSE)
	{
		$array = Validation::factory($array)
			->pre_filter('name','trim')
			->add_rules('name', 'required', 'length[3,127]', 'alpha_numeric');

		return parent::validate($array, $save);
	}

}