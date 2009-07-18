<?php

class arr extends arr_Core {
	
	public static function build($keys,$value)
	{
		$arr = array();
		$copy =& $arr;

		while(count($keys))
		{
			$key = array_shift($keys);
			$copy[$key] = array();
			$copy =& $copy[$key];
		}
		$copy = $value;
		return $arr;
	}
}

?>