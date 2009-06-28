<?php

class Group_Model extends CORM {

	// Relations
	protected $has_and_belongs_to_many = array('persons');

	protected $table_columns = array(
		'id'         => array('type'=>'int'),
		'name'       => array('type'=>'string')
	);
}
?>