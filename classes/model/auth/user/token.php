<?php defined('SYSPATH') or die ('No direct script access.');
/**
* Jelly Auth User Token Model
*
* @package    Jelly Auth
* @author     Israel Canasa
* @author     Thomas Menga
 */
class Model_Auth_User_Token extends Jelly_Model
{
	
 	public static function initialize(Jelly_Meta $meta)
	{
		
		// Fields
		$meta->field('id', 'primary');
		$meta->field('token', 'string', array(
			'unique' => TRUE,
			'rules' => array(
				'max_length' => array(32)
			)
		));
		$meta->field('user', 'belongsto');
		$meta->field('user_agent', 'string');
		$meta->field('created', 'timestamp', array('auto_now_create' => TRUE));
		$meta->field('expires', 'timestamp');
		
		
		if (mt_rand(1, 100) === 1)
		{
			// Do garbage collection
			Jelly::query('user_token')->where('expires', '<', time())->delete();
		}
	}
	
	public function create()
	{		
		// Set hash of the user agent
		$this->user_agent = sha1(Request::$user_agent);

		// Create a new token each time the token is saved
		$this->token = $this->create_token();
		
		return parent::save();
	}
	
	public function update()
	{
		// Create a new token each time the token is saved
		$this->token = $this->create_token();
		
		return parent::save();
	}

	/**
	 * Finds a new unique token, using a loop to make sure that the token does
	 * not already exist in the database. This could potentially become an
	 * infinite loop, but the chances of that happening are very unlikely.
	 *
	 * @return  string
	 */
	public function create_token()
	{
		while (TRUE)
		{
			// Create a random token
			$token = text::random('alnum', 32);

			// Make sure the token does not already exist
			if( ! Jelly::query('user_token')->where('token', '=', $token)->count())
			{
				// A unique token has been found
				return $token;
			}
		}
	}
	
} // End Auth User Token Model