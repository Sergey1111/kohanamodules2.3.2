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
		$account->increment('some_counter',5);
		$account->save(); // this will invoke an update query with the $set and $inc modifiers

		$content .= Kohana::debug($account->as_array());

		// another update
		$account->increment('some_counter',1);
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

		$content .= Kohana::debug($account->as_array(),$user->as_array());

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

		// add comment to blog
		$blog->add($comment); // atomic add using $push

		// save blog (! not comment !)
		$blog->save(); // note that you can only do ONE push before each save - if you remove this save, the next comment WILL NOT be saved

		// add another comment
		$comment = Mango::factory('comment');
		$comment->name = 'Jane Doe';
		$comment->comment = 'I like your style';
		$comment->time = time();

		$blog->add($comment); // atomic add using $push

		$blog->save();

		// This will show the comments stored IN the blog object
		$content .= Kohana::debug($blog->as_array());

		// You can access the comments
		foreach($blog->comments as $comment)
		{
			$content .= Kohana::debug($comment->as_array());
		}

		// To remove a comment
		//$blog->remove($comment); - this uses the $pull modifier, but that is not implemented in MongoDB yet

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
		$account->push('categories','cat1');
		$account->save();

		// this isn't (but is possible)
		// $account->categories = array('cat1');
		// $account->save();

		echo Kohana::debug($account->as_array());

		// try to push the same value
		$account->push('categories','cat1');

		echo Kohana::debug($account->as_array());

		$account->push('categories','cat2');
		$account->save();

		echo Kohana::debug($account->as_array());

		// atomic pull (not yet implemented in Mongo)
		//$account->pull('categories','cat1');
		//$account->save();

		// Clean up
		$account->delete();
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