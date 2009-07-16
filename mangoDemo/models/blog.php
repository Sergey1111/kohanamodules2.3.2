<?php

class Blog_Model extends Mango {
	
	protected $_belongs_to = array('user');
	
	protected $_columns = array(
	 	'title'        => array('type'=>'string'),
	 	'text'         => array('type'=>'string'),
		'time_written' => array('type'=>'int'),
		'time_post'    => array('type'=>'int'),
		'comments'     => array('type'=>'has_many')
	);

	protected $_db = 'demo'; //don't use default db config

	// Validate
	public function validate(array & $array, $save = FALSE)
	{
		$array = Validation::factory($array)
			->pre_filter('trim')
			->add_rules('title', 'required', 'length[4,127]')
			->add_rules('text', 'required')
			->add_rules('time_written', 'required');

		return parent::validate($array, $save);
	}
}