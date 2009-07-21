<?php

class Mango_Ext_Core extends Mango {

	protected static $config;

	// Factory
	public static function factory($object_name,$id = NULL)
	{
		if (is_array($id))
		{
			// load config
			if(self::$config === NULL)
			{
				self::$config = Kohana::config('mango_ext');
			}

			// read array for extension data
			while(isset(self::$config[$object_name]))
			{
				$type_key = key(self::$config[$object_name]);

				if (isset($id[$type_key]) && isset(self::$config[$object_name][$type_key][$id[$type_key]]))
				{
					// extension found - update model_name
					$object_name = self::$config[$object_name][$type_key][$id[$type_key]];
				}
				else
				{
					break;
				}
			}
		}

		return parent::factory($object_name,$id);
	}

	protected function set_model_definition() {}

	protected function _set_model_definition(array $definition = NULL)
	{
		if(isset($definition['_columns']))
		{
			$this->_columns = array_merge($this->_columns,$definition['_columns']);
		}

		if(isset($definition['_has_one']))
		{
			$this->_has_one = array_merge($this->_has_one,$definition['_has_one']);
		}

		if(isset($definition['_has_many']))
		{
			$this->_has_many = array_merge($this->_has_many,$definition['_has_many']);
		}

		if(isset($definition['_has_and_belongs_to_many']))
		{
			$this->_has_and_belongs_to_many = array_merge($this->_has_and_belongs_to_many,$definition['_has_and_belongs_to_many']);
		}

		//if(isset($definition['breaking_rules']))
		//	$this->_breaking_rules = array_merge($this->_breaking_rules,$definition['breaking_rules']);
	}

	// Constructor
	public function __construct($id)
	{
		$this->set_model_definition();

		parent::__construct($id);
	}

	public function __wakeup()
	{
		$this->set_model_definition();

		parent::__wakeup();
	}

	public function find(array $criteria = array() ,$limit = NULL,array $sort = NULL,$fields = array())
	{
		$result = parent::find($criteria,$limit,$sort,$fields);

		return $result instanceof Mango_Iterator ? new Mango_Ext_Iterator($this->_object_name,$result->cursor()) : $result;
	}

}
?>