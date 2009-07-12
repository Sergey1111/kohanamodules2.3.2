<?php

class Group_Model extends Mango {

	protected $_has_and_belongs_to_many = array('users');

	protected $_columns = array(
	 	'name'       => array('type'=>'string')
	);

	protected $_db = 'demo'; //don't use default db config

	// Validate
	public function validate(array & $array, $save = FALSE)
	{
		$array = Validation::factory($array)
			->pre_filter('trim')
			->add_rules('name', 'required', 'length[4,127]','alpha_numeric');

		return parent::validate($array, $save);
	}
}