<?php

class Mango_ArrayObject extends ArrayObject implements Mango_Interface {

	protected $_changed = array();
	protected $_type_hint;

	public function __construct(array $array = array(),$type_hint = NULL)
	{
		parent::__construct($array,ArrayObject::STD_PROP_LIST);

		$this->_type_hint = $type_hint;

		$this->load();
	}

	// Implemented by child classes
	public function get_changed($update,$prefix = NULL) {}

	public function load()
	{
		foreach($this as &$value)
		{
			$value = $this->load_type($value,$this->_type_hint);
		}
	}

	public function type_hint(array $value)
	{
		return array_keys($value) === range(0, count($value) - 1) ? 'set' : 'array';
	}

	public function load_type($value)
	{
		$type_hint = $this->_type_hint === NULL && is_array($value) ? $this->type_hint($value) : $this->_type_hint;

		if($type_hint !== NULL)
		{
			switch(strtolower($type_hint))
			{
				case 'counter':
					if(is_array($value))
					{
						$value = $this->type_hint($value) === 'set' ? new Mango_Set($value,$type_hint) : new Mango_Array($value,$type_hint);
					}
					else
					{
						$value = new Mango_Counter($value);
					}
				break;
				case 'set':
					$value = new Mango_Set($value);
				break;
				case 'array':
					$value = new Mango_Array($value);
				break;
				default:
					$value = Mango::factory($type_hint,$value);
				break;
			}
		}

		return $value;
	}

	public function getArrayCopy()
	{
		return $this->as_array();
	}

	public function as_array()
	{
		$array = parent::getArrayCopy();
	
		foreach($array as &$value)
		{
			if ($value instanceof Mango_Interface)
			{
				$value = $value->as_array();
			}
		}
		
		return $array;
	}

	public function set_saved()
	{
		$this->_changed = array();

		foreach($this as $value)
		{
			if ($value instanceof Mango_Interface)
			{
				$value->set_saved();
			}
		}
	}

	public function offsetSet($index,$newval)
	{
		$newval = $this->load_type($newval);

		parent::offsetSet($index,$newval);

		// on $array[], the $index value === NULL
		if($index === NULL)
		{
			foreach($this as $index => $value)
			{
				if($value === $newval)
					break;
			}
		}
		return $index;
	}

	public function find($needle)
	{
		if($needle instanceof Mango_Interface)
		{
			$needle = $needle->as_array();
		}

		foreach($this as $key => $val)
		{
			if( ($val instanceof Mango_Interface && $val->as_array() === $needle) || ($val === $needle))
			{
				return $key;
			}
		}
		return FALSE;
	}
}