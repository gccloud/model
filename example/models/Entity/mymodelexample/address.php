<?php
namespace Entity\mymodelexample;

defined('BASEPATH') OR exit('No direct script access allowed');

class address extends \Origami\Entity
{

	public static $table = 'address';

	/**
	 * @property integer $id
	 * @property integer $city_id
	 * @property string $label
	 * @property date $dateinsert
	 */
	public static $fields = array(
		array('name' => 'id', 'type' => 'int'),
		array('name' => 'city_id', 'type' => 'int'),
		array('name' => 'label', 'type' => 'string'),
		array('name' => 'dateinsert', 'type' => 'date', 'date_format' => 'Y-m-d H:i:s')
	);

	public static $primary_key = 'id';

	/**
	 * @method \Entity\mymodelexample\city city() has_one
	 */
	public static $associations = array(
		array('association_key' => 'city', 'entity' => '\Entity\mymodelexample\city', 'type' => 'has_one', 'primary_key' => 'id', 'foreign_key' => 'city_id')
	);
}

