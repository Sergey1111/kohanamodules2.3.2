<?php

class Comment_Model extends Mango {

	protected $_embedded = TRUE;

	protected $_columns = array(
	 	'name'         => array('type'=>'string'),
	 	'comment'      => array('type'=>'string'),
		'time'         => array('type'=>'int')
	);

	protected $_db = 'demo'; //don't use default db config

	// Validate
	public function validate(array & $array)
	{
		$array = Validation::factory($array)
			->pre_filter('trim')
			->add_rules('name', 'required', 'length[4,127]')
			->add_rules('comment', 'required')
			->add_rules('time', 'required');

		// Just validate - don't save
		return parent::validate($array, FALSE);
	}
}