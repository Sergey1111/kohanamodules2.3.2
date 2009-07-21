<?php defined('SYSPATH') OR die('No direct access allowed.');

class Mango_Ext_Iterator extends Mango_Iterator {

	public function current()
	{
		return Mango_Ext::factory($this->_object_name,$this->_cursor->current());
	}

} // End Iterator