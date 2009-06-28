<?php

class Blogpost_Model extends CORM {

	// Relations
	protected $belongs_to = array('person');

	protected $table_columns = array(
		'id'         => array('type'=>'int'),
		'person_id'  => array('type'=>'int'),
		'text'       => array('type'=>'string'),
		'title'      => array('type'=>'string'),
		'time'       => array('type'=>'int')
	);
	
	public function save()
	{
		if(!$this->loaded)
		{
			// automatically store time on saving new objects
			$this->time = time();
			// manually clear cache for custom set 'latest'
			$this->person->clear_relations('latest');
		}
			
		return parent::save();
	}

	public function delete()
	{
		// manually clear custom set cache
		$this->person->clear_relations('latest');
		return parent::delete();
	}
}
?>