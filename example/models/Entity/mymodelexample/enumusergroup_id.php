<?php
namespace Entity\mymodelexample;

defined('BASEPATH') OR exit('No direct script access allowed');

class enumusergroup_id extends \Origami\Entity
{
	const SUPERADMIN = 1;
	const ADMIN = 2;

	public static $table = 'enumusergroup_id';

	/**
	 * @property integer $id
	 * @property string $label
	 * @property string $constant
	 */
	public static $fields = array(
		array('name' => 'id', 'type' => 'int'),
		array('name' => 'label', 'type' => 'string'),
		array('name' => 'constant', 'type' => 'string')
	);

	public static $primary_key = 'id';

}

