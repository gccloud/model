<?php
namespace Entity\mymodelexample;

defined('BASEPATH') OR exit('No direct script access allowed');

class city extends \Origami\Entity
{

	public static $table = 'city';

	/**
	 * @property integer $id
	 * @property integer $country_id
	 * @property string $label
	 * @property string $zipcode
	 * @property date $dateinsert
	 * @property date $dateupdate
	 */
	public static $fields = array(
		array('name' => 'id', 'type' => 'int'),
		array('name' => 'country_id', 'type' => 'int'),
		array('name' => 'label', 'type' => 'string'),
		array('name' => 'zipcode', 'type' => 'string'),
		array('name' => 'dateinsert', 'type' => 'date', 'date_format' => 'Y-m-d H:i:s'),
		array('name' => 'dateupdate', 'type' => 'date', 'date_format' => 'Y-m-d H:i:s')
	);

	public static $primary_key = 'id';

	/**
	 * @method \Entity\mymodelexample\country country() has_one
	 */
	public static $associations = array(
		array('association_key' => 'country', 'entity' => '\Entity\mymodelexample\country', 'type' => 'has_one', 'primary_key' => 'id', 'foreign_key' => 'country_id')
	);
}

