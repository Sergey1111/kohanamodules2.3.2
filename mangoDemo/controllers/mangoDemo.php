<?php

class MangoDemo_Controller extends Template_Controller {

	public $template = 'mangoDemo.html';

	public function index()
	{
		$this->template->content = View::factory('mango/intro.html');
	}

	public function demo1()
	{
		$this->template->bind('content',$content);
		$content = '';

		// creating empty account object
		$account = Mango::factory('account');

		// set data
		$account->name = 'testaccount';

		// save account to database
		$account->save();

		$content .= Kohana::debug($account->as_array());

		// now we can use the ID to retrieve it from DB
		$account2 = Mango::factory('account', $account->_id);

		// this should be the same account
		$content .= Kohana::debug($account2->as_array());

		// Clean up
		$account->delete();
	}

	public function demo2()
	{
		$this->template->bind('content',$content);
		$content = '';

		// creating account
		$account = Mango::factory('account');
		$account->name = 'testaccount';
		$account->save();

		// simulate $_POST object
		$post = array(
			'email' => 'user@domain.com',
			'role' => 'manager'
		);

		// add related account (user belongs_to account)
		$post['account_id'] = $account->_id;

		// create empty user object
		$user = Mango::factory('user');

		// validate post data and try to save user
		if($user->validate($post,TRUE))
		{
			// user saved
			$content .= Kohana::debug($user->as_array());

			// users can not only be loaded by ID, but also by email
			$user2 = Mango::factory('user','user@domain.com');

			// this should be the same
			$content.= Kohana::debug($user2->as_array());

			// you can access the account from the user object
			$content .= Kohana::debug($user->account->as_array());

			// and you can access the users from the account object
			$users = $account->users;

			$content .= Kohana::debug('account',$account->name,'has',$users->count(),'users');
		}
		else
		{
			$content = 'invalid user data' . Kohana::debug($post->errors());
		}

		// clean up (because account has_many users, the users will be removed too)
		$account->delete();
	}

	public function demo3()
	{
		$this->template->bind('content',$content);
		$content = '';

		// creating account
		$account = Mango::factory('account');
		$account->name = 'testaccount';

		$account->save();

		$content .= Kohana::debug($account->as_array());

		// atomic update
		$account->name = 'name2';
		$account->some_counter->increment(5);
		$account->save(); // this will invoke an update query with the $set and $inc modifiers

		$content .= Kohana::debug($account->as_array());

		// another update
		$account->some_counter->increment();
		$account->save();

		$content .= Kohana::debug($account->as_array());

		$account->delete();
	}

	public function demo4()
	{
		$this->template->bind('content',$content);
		$content = '';

		// creating account
		$account = Mango::factory('account');
		$account->name = 'testaccount';
		$account->save();
		// create user
		$user = Mango::factory('user');
		$user->role = 'manager';
		$user->email = 'user@domain.com';
		$user->account = $account; // (or $user->account_id = $account->_id, same effect)
		$user->save();

		//$content .= Kohana::debug($account->as_array(),$user->as_array());

		// create blog
		$blog = Mango::factory('blog');
		$blog->title = 'my first blog';
		$blog->text = 'hello world';
		$blog->time_written = time();
		$blog->user = $user;  // (or $blog->user_id = $user->_id, same effect)
		$blog->save();

		// add an embedded has many object
		$comment = Mango::factory('comment');
		$comment->name = 'John Doe';
		$comment->comment = 'Hello to you to';
		$comment->time = time();

		// to add a comment to blog (atomic) you can choose:
		$blog->add($comment);
		// OR
		//$blog->comments[] = $comment;

		// save blog (! not comment !)
		$blog->save(); // note that you can only do ONE push before each save - if you remove this save, the next comment WILL NOT be saved

		$blog->comments[] = $comment;

		// add another comment
		$comment = Mango::factory('comment');
		$comment->name = 'Jane Doe';
		$comment->comment = 'I like your style';
		$comment->time = time();

		$blog->comments[] = $comment; // atomic add using $push
		// $blog->add($comment);

		$blog->save();

		// This will show the comments stored IN the blog object
		$content .= Kohana::debug($blog->as_array());

		// You can access the comments
		// $blog->comments->as_array() is also possible
		foreach($blog->comments as $comment)
		{
			$content .= Kohana::debug($comment->as_array());
		}

		$blog2 = Mango::factory('blog',$blog->_id);
		
		$content .= '<hr>' . Kohana::debug($blog2->comments->as_array(),$blog2->get_changed(TRUE)) . '<hr>';

		// To remove a comment
		//$blog->remove($comment); - this uses the $pull modifier, but that is not implemented in MongoDB yet
		// unset($blog->comments[1]);

		// Clean up
		$account->delete();
	}

	public function demo5()
	{
		$this->template->bind('content',$content);
		$content = '<b>Please ignore the $pull messages above</b><br>';

		// creating account
		$account = Mango::factory('account');
		$account->name = 'testaccount';
		$account->save();
		// create user
		$user = Mango::factory('user');
		$user->role = 'manager';
		$user->email = 'user@domain.com';
		$user->account = $account; // (or $user->account_id = $account->_id, same effect)
		$user->save();

		//$content .= Kohana::debug($account->as_array(),$user->as_array());

		// create a group
		$group1 = Mango::factory('group');
		$group1->name = 'Group1';
		$group1->save();

		// add HABTM relationship between $user and $group1
		$user->add($group1);

		//SAVE BOTH OBJECTS
		$user->save();
		$group1->save();

		$content .= Kohana::debug($user->as_array(),$group1->as_array());

		// Clean up
		$account->delete();

		// Note: this will also delete the user, and because the user has a HABTM relation
		// the related group(s) have to be updated too. This uses $pull and this is not yet
		// implemented in Mongo (should be though in Mongo 0.9.7)

		// The $group1 object will still exist, although it's related $user is removed
		// lets check if the relationship is gone too:
		$content .= Kohana::debug($group1->as_array());

		$group1->delete();
	}

	public function demo6()
	{
		$this->template->bind('content',$content);
		$content = '';

		// creating account
		$account = Mango::factory('account');
		$account->name = 'testaccount';
		$account->save();

		// this is atomic
		$account->categories[] = 'cat1';
		
		// as is $account->categories->push('cat1');
		$account->save();

		// this isn't (but is possible)
		// $account->categories = array('cat1');
		// $account->save();

		$content .= Kohana::debug($account->as_array());

		// try to push the same value
		$account->categories[] = 'cat1'; 

		echo Kohana::debug($account->as_array());

		$account->categories[] = 'cat2';
		$account->save();

		$content .= Kohana::debug($account->as_array());

		// atomic pull (not yet implemented in Mongo)
		//$account->categories->pull('cat1');
		// OR
		// unset($account->categories[ $account->categories->find('cat1') ]);
		//$account->save();

		// Clean up
		$account->delete();
	}

	public function demo7()
	{
		$this->template->bind('content',$content);
		$content = '';
		
		// An unsaved object
		// All actions should result in a save query without updates/modifiers (it is inserted into DB)
		$content .= '<h1>Unsaved objects</h1>';

		/* Counters */
		$content.='<h2>counters</h2>';
		
		$account = Mango::factory('account');
		$account->some_counter->increment();
		$content.=Kohana::debug($account->as_array(),$account->get_changed(FALSE)) . '<hr>';

		$account = Mango::factory('account');
		$account->some_counter->decrement();
		$content.=Kohana::debug($account->as_array(),$account->get_changed(FALSE)) . '<hr>';

		$account = Mango::factory('account');
		$account->some_counter = 5;
		$content.=Kohana::debug($account->as_array(),$account->get_changed(FALSE)) . '<hr>';

		/* Sets */
		$content.='<h2>sets</h2>';

		$account = Mango::factory('account');
		$account->categories[] = 'cat1';
		$content.=Kohana::debug($account->as_array(),$account->get_changed(FALSE)) . '<hr>';

		$account = Mango::factory('account');
		$account->categories = array('cat1','cat2');
		$content.=Kohana::debug($account->as_array(),$account->get_changed(FALSE)) . '<hr>';

		/* Arrays */
		$content.='<h2>arrays</h2>';

		$account = Mango::factory('account');
		$account->some_array[] = 'cat1';
		$content.=Kohana::debug($account->as_array(),$account->get_changed(FALSE)) . '<hr>';

		$account = Mango::factory('account');
		$account->some_array['key'] = 'cat1';
		$content.=Kohana::debug($account->as_array(),$account->get_changed(FALSE)) . '<hr>';

		$account = Mango::factory('account');
		$account->some_array = array('cat1','key'=>'bla');
		$content.=Kohana::debug($account->as_array(),$account->get_changed(FALSE)) . '<hr>';

		// Saved objects
		// Here we want $modifiers
		$content .= '<h1>Unsaved objects</h1>';

		/* Counters */
		$content.='<h2>counters</h2>';
		
		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->save();
		$account->some_counter->increment(); // this is atomic (uses $inc)
		$content.=Kohana::debug($account->as_array(),$account->get_changed(TRUE)) . '<hr>';
		$account->delete();

		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->save();
		$account->some_counter = 5; // this is NOT atomic (uses $set, not $inc)
		$content.=Kohana::debug($account->as_array(),$account->get_changed(TRUE)) . '<hr>';
		$account->delete();

		/* Sets */
		$content.='<h2>sets</h2>';

		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->save();
		$account->categories[] = 'cat1'; // this is atomic (uses $push)
		$content.=Kohana::debug($account->as_array(),$account->get_changed(TRUE)) . '<hr>';
		$account->delete();

		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->save();
		$account->categories = array('cat1','cat2'); // this is not atomic - a full reset of the categories array
		$content.=Kohana::debug($account->as_array(),$account->get_changed(TRUE)) . '<hr>';
		$account->delete();

		/* Arrays */
		$content.='<h2>arrays</h2>';

		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->save();
		$account->some_array[] = 'bla';
		$content.=Kohana::debug($account->as_array(),$account->get_changed(TRUE)) . '<hr>';
		$account->delete();

		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->save();
		$account->some_array['key'] = 'bla';
		$content.=Kohana::debug($account->as_array(),$account->get_changed(TRUE)) . '<hr>';
		$account->delete();

		$account = Mango::factory('account');
		$account->name = 'hello';
		$account->save();
		$account->some_array = array('key' => 'bla', 'blo');
		$content.=Kohana::debug($account->as_array(),$account->get_changed(TRUE)) . '<hr>';
		$account->delete();
	}

	public function demo8()
	{
		$this->template->bind('content',$content);
		$content = '';
		
		// let's simulate a load from DB
		// notice the array structure - it has to be predefined
		$account = Mango::factory('account',array(
			'_id'=>1,
			'name'=>'test',
			'report' => array(
				'total' => 5,
				'blog1' => array(
					'views' => 0,
					'comments'=> 0
				)
			)
		));
		
		// but now, atomic counters are easy:
		
		$account->report['total']->increment();
		$account->report['blog1']['views']->increment();

		$content .= Kohana::debug($account->get_changed(TRUE));
		
		// simulate changes were saved
		$account->set_saved();
		
		// we can even add counters
		$account->report['blog2'] = array('views'=>0,'comments'=>0);

		// and they are ready to use as counter
		$account->report['blog2']['views']->increment();

		// also update an existing counter
		$account->report['blog1']['views']->increment();

		$content .= Kohana::debug($account->get_changed(TRUE));

		// simulate save
		$account->set_saved();

		// do some more counting
		$account->report['blog2']['views']->increment();

		// and now it will inc
		$content .= Kohana::debug($account->get_changed(TRUE));
	}

	public function demo9()
	{
		// Note: extension support is useful if you have different classes, that inherit
		// from the same (base) class, but each have different additional columns.

		$this->template->bind('content',$content);
		$content = '';

		// Create a Spyker car object
		$car = Mango_Ext::factory('spyker');

		// We should have access to the Car_Model columns as well as the Spyker_Model columns
		$car->price = 1000;
		$car->spyker_data = 'hello';

		// this should save in the cars collection
		$car->save();

		$content .= Kohana::debug($car->as_array());

		// Now create another car
		$car = Mango_Ext::factory('ferrari');

		$car->price = 750;
		$car->ferrari_data = 'world';

		$car->save();

		$content .= Kohana::debug($car->as_array());

		// Now we have 2 cars saved in the cars collection, one ferrari, one spyker
		// Let's check - note we use 'car' in the factory method, but we get a fully
		// extended ferrari/spyker_model in return
		$cars = Mango_Ext::factory('car')->find();
		foreach($cars as $car)
		{
			$content .= Kohana::debug($car->as_array());

			// clean up
			$car->delete();
		}
	}

	public function demo12()
	{
		$this->template->bind('content',$content);

		// add to queue
		for($i = 0; $i < 10; $i++)
		{
			MangoQueue::set('message: ' . $i . ' ' . rand());
		}

		$content = '';

		// remove first from queue
		while($msg = MangoQueue::get())
		{
			$content .= Kohana::debug($msg);
		}

		// add to queue
		for($i = 0; $i < 10; $i++)
		{
			MangoQueue::set('message: ' . $i . ' ' . rand());
		}

		// fetch from queue (don't delete)
		$msgs = array();
		while($msg = MangoQueue::get(0,FALSE))
		{
			$msgs[] = $msg;
			$content .= Kohana::debug($msg);
		}

		// remove from queue
		foreach($msgs as $msg)
		{
			MangoQueue::delete($msg);
		}
	}

	public function demo13()
	{
		$this->template->content = 'done';
		
		// add to queue
		for($i = 0; $i < 20; $i++)
		{
			MangoQueue::set('message: ' . $i . ' ' . rand());
		}
	}

	public function demo14()
	{
		$this->template->bind('content',$content);

		$content = '';

		// get from queue (of course you should build some sort of CLI daemon, this is just for demo purposes)
		while($key = MangoQueue::get())
		{
			sleep(1);
			$content .= Kohana::debug($key);
		}
	}

}