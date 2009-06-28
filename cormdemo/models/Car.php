<?php

class Car_Model extends CORM {

	// Relations
	protected $belongs_to = array('person');

	protected $table_columns = array(
		'id'         => array('type'=>'int'),
		'person_id'  => array('type'=>'int'),
		'brand'      => array('type'=>'string')
	);
}
?>