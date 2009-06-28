<?php

class Person_Model extends CORM {

	// inflector makes people of tablename
	protected $table_name = 'persons';

	// Relations
	protected $has_many = array('blogposts');
	protected $has_and_belongs_to_many = array('groups');
	protected $has_one = array('car');

	// The object is on ID and email (both are unique)
	// ID is used with relations to this object, eg $blogpost->user;
	// Email is used when logging in (the auth lib runs CORM::factory('user',EMAIL)
	// The first key in this array will be used to store the object in cache
	// Any other keys will contain a reference to the object
	// If no keys are given, the CORM lib assumes ID
	protected $cache_keys = array('id','email');

	// Custom set
	protected $sets = array(
		'latest' => array(
			'object_name' => 'blogpost',
			'methods'     => array(
				'orderby'     => array('time','DESC'),
				'limit'       => array(2)
			)
		)
	);

	protected $table_columns = array(
		'id'         => array('type'=>'int'),
		'email'      => array('type'=>'string'),
		'name'       => array('type'=>'string')
	);

	// user models can also be loaded by email
	public function unique_key($id)
	{
		if ( ! empty($id) AND is_string($id) AND ! ctype_digit($id))
			return 'email';

		return parent::unique_key($id);
	}

}
?>