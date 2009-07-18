<?php

class Mango_Set extends Mango_ArrayObject {

	public function get_changed($update, array $prefix = array())
	{
		if( ! empty($this->_changed) )
		{
			// something is pushed/pulled
			list($push,$value) = $this->_changed;

			if($push)
			{
				$value = $this->offsetGet($value);
			}

			$value = $value instanceof Mango_Interface ? $value->as_array() : $value; 
			
			return $update ? array( $push ? '$push' : '$pull' => array(implode('.',$prefix) => $value) ) : array(arr::build($prefix,array($value)) );
		}
		else
		{
			$changed = array();
			
			// if nothing is pushed or pulled, we support $set
			
			$level = $prefix;
			foreach($this as $key => $value)
			{
				if($value instanceof Mango_Interface)
				{
					$level[] = $key;
					$changed = arr::merge($changed, $value->get_changed($update, $level));
				}
			}

			return $changed;
		}
	}

	public function offsetSet($index,$newval)
	{
		// sets don't have associative keys
		if(! is_int($index) && ! is_null($index))
		{
			return FALSE;
		}
		
		// Only one $push/$pull action allowed
		if( ! empty($this->_changed))
		{
			return FALSE;
		}

		// Check if value is already added
		// TODO - do we only allow unique items to be added?
		if( $this->find($this->load_type($newval)) !== FALSE )
		{
			return TRUE;
		}

		$index = parent::offsetSet($index,$newval);

		// when pushing, we store index (we only retrieve actual value upon saving - value might change after being pushed)
		$this->_changed = array(TRUE,$index);

		return TRUE;
	}
	
	public function offsetUnset($index)
	{
		// sets don't have associative keys
		if(! is_int($index) && ! is_null($index))
		{
			return FALSE;
		}

		// Only one $push/$pull action allowed
		if( ! empty($this->_changed))
		{
			return FALSE;
		}

		// when pulling, we store value itself, only way to have access to it
		$this->_changed = array(FALSE,$this->offsetGet($index));

		parent::offsetUnset($index);
	}

	public function push($newval)
	{
		return $this->offsetSet(NULL,$newval);
	}

	public function pull($oldval)
	{
		if( ($index = $this->find($this->load_type($oldval))) !== FALSE )
		{
			$this->offsetUnset( $index );
		}

		return TRUE;
	}
}