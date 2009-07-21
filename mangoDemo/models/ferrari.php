<?php

class Ferrari_Model extends Car_Model {

	public function __construct($id = NULL)
	{
		parent::__construct($id);
		$this->car_type = 2;
	}

	// !! With extending classes - specify columns and relations in this method !!
	public function set_model_definition()
	{
		// Specify the columns/relations specific for this class
		$this->_set_model_definition(array(
			'_columns' => array(
				'ferrari_data' => array('type'=>'string')
			)
		));

		parent::set_model_definition();
	}
}