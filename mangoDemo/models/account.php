<?php
class Account_Model extends Mango {

	protected $_has_many = array('users');

	protected $_columns = array(
	 	'name'         => array('type'=>'name'),
	 	// the attributes below might seem strange - they are :-)
	 	// but in the demos I needed a counter, a set, an array and an
	 	// multidimensional array of counters
	 	'some_counter' => array('type'=>'counter'),
	 	'categories'   => array('type'=>'set'),
	 	'some_array'   => array('type'=>'array'),
	 	'report'       => array('type'=>'array','type_hint'=>'counter')
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