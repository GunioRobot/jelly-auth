<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Jelly Auth User Model
 *
 * @package    Jelly Auth
 * @author     Israel Canasa
 * @author     Thomas Menga
 */
class Model_Auth_User extends Jelly_Model
{
	
	public static function initialize(Jelly_Meta $meta)
    {
		$meta->name_key('username');
		$meta->sorting(array('username' => 'ASC'));
		
		// Fields
		$meta->field('id', 'primary');
		$meta->field('username', 'string', array(
			'unique' => TRUE,
			'rules' => array(
				'not_empty' => array(NULL),
				'max_length' => array(32),
				'min_length' => array(3),
				'regex' => array('/^[\pL_.-]+$/ui')
			)
		));
		$meta->field('password', 'password', array(
			'hash_with' => array(Auth::instance(), 'hash_password'),
			'rules' => array(
				'not_empty' => array(NULL),
				'max_length' => array(50),
				'min_length' => array(6)
			)
		));
		$meta->field('password_confirm', 'password', array(
			'in_db' => FALSE,
			'callbacks' => array(
				'matches' => array('Model_Auth_User', '_check_password_matches')
			),
			'rules' => array(
				'not_empty' => NULL,
				'max_length' => array(50),
				'min_length' => array(6)
			)
		));
		$meta->field('email', 'email', array('unique' => TRUE));
		$meta->field('logins', 'integer', array('default' => 0));
		$meta->field('last_logins', 'timestamp');
		
		// Relationships
		$meta->field('tokens', 'hasmany', array('foreign' => 'user_token'));
		$meta->field('roles', 'manytomany');
    }

	/**
	 * Validate callback wrapper for checking password match
	 * @param Validate $array
	 * @param string   $field
	 * @return void
	 */
	public static function _check_password_matches(Validate $array, $field)
	{
		$auth = Auth::instance();
		
		if ($array['password'] !== $array[$field])
		{
			// Re-use the error messge from the 'matches' rule in Validate
			$array->error($field, 'matches', array('param1' => 'password'));
		}
	}
	
	/**
	 * Check if user has a particular role
	 * @param mixed $role 	Role to test for, can be Model_Role object, string role name of integer role id
	 * @return bool			Whether or not the user has the requested role
	 */
	public function has_role($role)
	{
		// Check what sort of argument we have been passed
		if ($role instanceof Model_Role)
		{
			$key = 'id';
			$val = $role->id;
		}
		elseif (is_string($role))
		{
			$key = 'name';
			$val = $role;
		}
		else
		{
			$key = 'id';
			$val = (int) $role;
		}

		foreach ($this->roles as $user_role)
		{	
			if ($user_role->{$key} === $val)
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
} // End Auth User Model