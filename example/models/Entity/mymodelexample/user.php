<?php
namespace Entity\mymodelexample;

defined('BASEPATH') OR exit('No direct script access allowed');

class user extends \Origami\Entity
{

	public static $table = 'user';

	/**
	 * @property integer $id
	 * @property integer $enumusergroup_id
	 * @property integer $address_id
	 * @property string $lastname
	 * @property string $firstname
	 * @property date $dateinsert
	 * @property date $dateupdate
	 */
	public static $fields = array(
		array('name' => 'id', 'type' => 'int'),
		array('name' => 'enumusergroup_id', 'type' => 'int'),
		array('name' => 'address_id', 'type' => 'int', 'allow_null' => true),
		array('name' => 'lastname', 'type' => 'string'),
		array('name' => 'firstname', 'type' => 'string'),
		array('name' => 'dateinsert', 'type' => 'date', 'date_format' => 'Y-m-d H:i:s'),
		array('name' => 'dateupdate', 'type' => 'date', 'date_format' => 'Y-m-d H:i:s')
	);

	public static $primary_key = 'id';

	/**
	 * @method \Entity\mymodelexample\address address() has_one
	 * @method \Entity\mymodelexample\enumusergroup enumusergroup() has_one
	 */
	public static $associations = array(
		array('association_key' => 'address', 'entity' => '\Entity\mymodelexample\address', 'type' => 'has_one', 'primary_key' => 'id', 'foreign_key' => 'address_id'),
		array('association_key' => 'enumusergroup', 'entity' => '\Entity\mymodelexample\enumusergroup', 'type' => 'has_one', 'primary_key' => 'id', 'foreign_key' => 'enumusergroup_id')
	);
}

