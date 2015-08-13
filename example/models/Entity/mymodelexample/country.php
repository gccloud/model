<?php
namespace Entity\mymodelexample;

defined('BASEPATH') OR exit('No direct script access allowed');

class country extends \Origami\Entity
{

	public static $table = 'country';

	/**
	 * @property integer $id
	 * @property string $label
	 * @property string $iso
	 * @property date $dateinsert
	 * @property date $dateupdate
	 */
	public static $fields = array(
		array('name' => 'id', 'type' => 'int'),
		array('name' => 'label', 'type' => 'string'),
		array('name' => 'iso', 'type' => 'string'),
		array('name' => 'dateinsert', 'type' => 'date', 'date_format' => 'Y-m-d H:i:s'),
		array('name' => 'dateupdate', 'type' => 'date', 'date_format' => 'Y-m-d H:i:s')
	);

	public static $primary_key = 'id';

}

