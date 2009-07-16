<?php

interface Mango_Interface
{
	public function as_array();
	
	public function get_changed($update,$prefix = NULL);
	
	public function set_saved();
}