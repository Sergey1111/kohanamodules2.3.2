<?php defined('SYSPATH') OR die('No direct access allowed.');

/* Some ideas from Kohana's Cache Library:
 * @package    Cache
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */

class MCache {

	protected static $instances = array();

	protected $config;
	protected $backend;
	protected $flags;

	// Instance
	public static function & instance($config = 'default')
	{
		if ( ! isset(self::$instances[$config]))
			// Create a new instance
			self::$instances[$config] = new MCache($config);
		
		return self::$instances[$config];
	}

	// Constructor
	public function __construct($config = 'default')
	{
		if ( ! extension_loaded('memcache'))
			throw new Kohana_Exception('cache.extension_not_loaded', 'memcache');

		$this->config = Kohana::config('mcache.' . $config);
		
		if($config !== 'default')
			// Append the default configuration options
			$this->config += Kohana::config('mcache.default');
		
		$this->backend = new Memcache;
		$this->flags = $this->config['compression'] ? MEMCACHE_COMPRESSED : 0;

		foreach ($this->config['servers'] as $server)
		{
			// Make sure all required keys are set
			$server += array('host' => '127.0.0.1', 'port' => 11211, 'persistent' => FALSE);

			// Add the server to the pool
			$this->backend->addServer($server['host'], $server['port'], (bool) $server['persistent'])
				or Kohana::log('error', 'Cache: Connection failed: '.$server['host']);
		}

	}

	// Increment
	public function increment($key, $value = 1)
	{
		return $this->backend->increment($key, $value);
	}
	
	// Decrement
	public function decrement($key, $value = 1)
	{
		return $this->backend->decrement($key, $value);
	}

	// Get / Multi-Get
	// returns NULL / array() if nothing found
	public function get($id)
	{
		$id = is_array($id) ? array_map(array($this,'sanitize_id'),$id) : $this->sanitize_id($id);
		
		return (($return = $this->backend->get($id)) === FALSE) ? (is_array($id) ? array() : NULL) : $return;
	}

	// Set
	function set($id, $data, array $tags = NULL, $lifetime = NULL)
	{
		if (is_resource($data))
			throw new Kohana_Exception('cache.resources');
	
		if ($lifetime === NULL)
			// Get the default lifetime
			$lifetime = $this->config['lifetime'];

		// Memcache prefers unix timestamp
		if (!empty($lifetime))
			$lifetime += time();
		
		return $this->backend->set( $this->sanitize_id($id), $data, $this->flags, $lifetime);
	}

	// Delete
	public function delete($id)
	{
		return $this->backend->delete( $this->sanitize_id($id) );
	}

	// Flush
	public function delete_all()
	{
		return $this->backend->flush();
		
		// The flush has a one second granularity. The flush will expire all items up to the ones set within the same second
		// http://nl3.php.net/manual/en/function.memcache-flush.php
		// sleep(1);
	}

	// Clean id
	private function sanitize_id($id)
	{
		// Change slashes and spaces to underscores
		return str_replace(array('/', '\\', ' '), '_', $id);
	}

}
