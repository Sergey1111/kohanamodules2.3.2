<?php defined('SYSPATH') OR die('No direct access allowed.');

class CORM_Iterator implements Iterator, ArrayAccess, Countable {

	// Model
	protected $object_name;

	// Set of IDs
	protected $id_set;

	public function __construct($object_name, array $id_set)
	{
		$this->object_name = $object_name;
		$this->id_set      = $id_set;
	}

	public function as_array()
	{
		if( !count($this->id_set) )
			return array();

		$model = CORM::factory($this->object_name);

		// multi get from cache
		$array = $model->cache->get( array_map(array($model,'cache_key'),$this->id_set) );

		if(count($array) !== count($this->id_set))
		{
			// some objects weren't found in cache

			// determine missing IDs
			$missing_ids = array();
			
			if(count($array) === 0)
			{
				// all IDs missing
				$missing_ids = $this->id_set;
			}
			else
			{
				// selected IDs missing
				foreach($array as $object)
				{
					$missing_ids[] = $object->primary_key_value;
				}
				$missing_ids = array_diff($this->id_set,$missing_ids);
			}

			// lookup missing objects in DB
			$missing_objects = $model->db
				->select('*')
				->in($model->primary_key,$missing_ids)
				->get($model->table_name)
				->result(TRUE)
				->as_array();

			$retrieved_objects = array();

			foreach($missing_objects as $object_data)
			{
				$object = CORM::factory($this->object_name,$object_data);
				$retrieved_objects[$model->cache_key($object->primary_key_value)] = $object;
			}
			
			if(count($retrieved_objects))
			{
				$array = array_merge($array,$retrieved_objects);
				ksort($array);
			}
		}

		echo 'Retrieved related set of <b>' . $this->object_name. '</b><br>';

		if(count($array) && ! is_object(current($array)))
		{
			echo 'Retrieved related set of <b>' . $this->object_name. '</b contained links - retrieving actual objects<br>';

			// objects aren't cached on there primary_key but on some other key
			// load actual objects
			$array = $model->cache->get( array_map(array($model,'cache_key'),$array));
		}
		
		return array_values($array);
	}

	/**
	 * Countable: count
	 */
	public function count()
	{
		return count($this->id_set);
	}

	/**
	 * Iterator: Return the current element
	 */
	public function current()
	{
		return CORM::factory($this->object_name,current($this->id_set));
	}

	/**
	 * Iterator: Return the key of the current element.
	 */
	public function key()
	{
		return key($this->id_set);
	}

	/**
	 * Iterator: Move forward to next element.
	 */
	public function next()
	{
		next($this->id_set);
	}

	/**
	 * Iterator: Rewind the Iterator to the first element.
	 */
	public function rewind()
	{
		reset($this->id_set);
	}

	/**
	 * Iterator: Check if there is a current element after calls to rewind() or next().
	 */
	public function valid()
	{
		return current($this->id_set);
	}

	/**
	 * ArrayAccess: whether the offset exists.
	 */
	public function offsetExists($offset)
	{
		return isset($this->id_set[$offset]);
	}

	/**
	 * ArrayAccess: value at given offset
	 */
	public function offsetGet($offset)
	{
		return isset($this->id_set[$offset]) ? CORM::factory($this->object_name,$this->id_set[$offset]) : NULL;
	}

	/**
	 * ArrayAccess: offsetSet
	 *
	 * @throws  Kohana_Database_Exception
	 */
	public function offsetSet($offset, $value)
	{
		throw new Kohana_Exception('database.result_read_only');
	}

	/**
	 * ArrayAccess: offsetUnset
	 *
	 * @throws  Kohana_Database_Exception
	 */
	public function offsetUnset($offset)
	{
		throw new Kohana_Exception('database.result_read_only');
	}

} // End ORM Iterator