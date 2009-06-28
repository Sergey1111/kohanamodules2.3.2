<?php

$config['default'] = array(
	// default lifetime
	'lifetime'	=> 	0,
	
	// Compression - default compression & increment causes error
	// [http://nl3.php.net/manual/en/function.memcache-increment.php]
	'compression'	=>	FALSE, 
	
	// Running Memcache servers
	'servers'		=>	array(
		array
		(
			'host' => '127.0.0.1',
			'port' => 11211,
			'persistent' => FALSE
		)
	),
);