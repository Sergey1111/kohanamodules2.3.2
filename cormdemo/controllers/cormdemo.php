<?php 

class Cormdemo_Controller extends Controller {
	
	public function __construct() {
		parent::__construct();

		new Profiler;

		echo '<b>See source for usage, use links & profiler to verify output</b><br>';
		echo 'You probably want to refresh each demo to see when the DB is used and when it isn\'t<br>';
		
		for($i = 1; $i < 12; $i++)
		{
			echo html::anchor('cormdemo/demo'.$i,'demo ' . $i),'<br>';
		}
		echo '<hr>';
		echo html::anchor('cormdemo/db','db schema'),'<br>';
		echo html::anchor('cormdemo/flush','empty cache'),'<hr>';
	}
	
	public function index()
	{
	}

	public function db()
	{
		echo View::factory('db.html');
	}

	public function demo1()
	{
		// load person on email
		$person1 = CORM::factory('person','bla@bla.nl');
		
		// load person on id
		$person2 = CORM::factory('person',2);
		
		echo Kohana::debug($person1->as_array(),$person2->as_array());
	}
	
	public function demo2()
	{
		$person = CORM::factory('person',1);

		// has one relationship
		echo Kohana::debug($person->car->as_array());

		$car = CORM::factory('car',1);

		// belongs_to relationship - this uses the same cached relationship
		echo Kohana::debug($car->person->as_array());
	}


	public function demo3()
	{
		$person = CORM::factory('person',1);
		
		// notice that only the IDs are fetched, not the actual blogposts themselves
		// this is lazy loading
		echo count($person->blogposts);
	}

	public function demo4()
	{
		$person = CORM::factory('person',1);
		
		// this is lazy loading (see demo5 for prefetching)
		foreach($person->blogposts as $blogpost)
		{
			echo Kohana::debug($blogpost->as_array());
		}
		echo 'now empty cache and run demo 5';
	}

	public function demo5()
	{
		$person = CORM::factory('person',1);
		
		// this is prefetching (notice the as_array()) (see demo4 for prefetching)
		foreach($person->blogposts->as_array() as $blogpost)
		{
			echo Kohana::debug($blogpost->as_array());
		}
		echo 'empty cache and compare to demo 4';
	}

	public function demo6()
	{
		$person = CORM::factory('person',1);
		
		// fetch the blogpost relation
		$person->blogposts;
		
		$blogpost = CORM::factory('blogpost',1);
		$blogpost->title = 'my name is ' . rand();
		$blogpost->save();

		// the relation hasn't been changed, so this call
		// won't require any db activity
		echo count($person->blogposts);
	}

	public function demo7()
	{
		$person = CORM::factory('person',1);

		// HABTM
		echo count($person->groups),'<br>';

		$group = CORM::factory('group',1);

		// HABTM relationships work both ways
		echo count($group->persons),'<br>';
	}

	public function demo8()
	{
		$person = CORM::factory('person',1);
		
		// custom set
		foreach($person->latest->as_array() as $blogpost)
		{
			echo Kohana::debug($blogpost->as_array());
		}
		echo 'now add a blogpost (demo9) and rerun this demo';
	}

	public function demo9()
	{
		$blogpost = CORM::factory('blogpost');
		$blogpost->person_id = 1;
		$blogpost->title = 'title' . rand();
		$blogpost->text = 'text' . rand();
		$blogpost->save();
		
		echo 'this will reset the person->blogposts and person->latest relation';
	}

	public function demo10()
	{
		$blogpost = CORM::factory('blogpost',1);
		if($blogpost->loaded)
		{
			$blogpost->delete();
		}
		else
		{
			$blogpost->id = 1;
			$blogpost->title = 'test';
			$blogpost->text = 'text';
			$blogpost->person_id = 1;
			$blogpost->save();
		}

			echo 'this will reset the person->blogposts and person->latest relation';
			echo 'rerun this demo to add/remove this blogpost again';
	}

	public function demo11()
	{
		$group = CORM::factory('group',1);
		$person = CORM::factory('person',1);
		
		if($group->loaded)
		{
			echo $group->has($person),'<br>';
			echo $person->has($group),'<br>';
	
			$group->delete();
		}
		else
		{
			// add group
			$group->id = 1;
			$group->name = 'groupy';
			$group->save();
			
			// add group to user
			$person->add($group);
		}
		
		echo 'this will reset both person::groups and group::persons relation';
	}

	public function flush()
	{
		// empty cache
		MCache::instance()->delete_all();
		
		// back to start
		url::redirect('cormdemo/index');
	}

}