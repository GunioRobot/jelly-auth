<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Jelly Auth Role Model
 *
 * @package    Jelly Auth
 * @author     Israel Canasa
 * @author     Thomas Menga
 */
class Model_Auth_Role extends Jelly_Model
{

	public static function initialize(Jelly_Meta $meta)
	{
		$meta->name_key('name');

		// Fields
		$meta->field('id', 'primary');
		$meta->field('name', 'string', array(
			'unique' => TRUE,
			'rules'  => array(
				'max_length' => array(32),
				'not_empty'  => array(NULL),
			),
		));

		// Relationships
		$meta->field('description', 'text');
		$meta->field('users', 'manytomany');
	}

} // End Auth User Model