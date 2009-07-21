<?php

class Car_Model extends Mango_Ext {

	protected $_db = 'demo'; //don't use default db config

	// hardcode the collection name, so that all instances, including all extending
	// instances (like Spyker_Model) are stored in the collection 'cars'
	protected $_collection_name = 'cars';

	// !! With extending classes - specify columns and relations in this method !!
	public function set_model_definition()
	{
		$this->_set_model_definition(array(
			'_columns' => array(
				'price'    => array('type'=>'int'),
				'car_type' => array('type'=>'int')
			)
		));

		parent::set_model_definition();
	}
}