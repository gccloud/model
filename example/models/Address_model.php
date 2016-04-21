<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Address_model extends MY_Model
{
    /* CLASS ATTRIBUTES */

    public $id = 'mymodelexample.address.id';
    public $label = 'mymodelexample.address.label';
    public $zipcode = 'mymodelexample.city.zipcode';
    public $city = 'mymodelexample.city.label';
    public $country = 'mymodelexample.country.label';
    public $country_iso = 'mymodelexample.country.iso';

    /* CORE FUNCTIONS */

    /**
     * Class Constructor
     * @method __construct
     * @public
     */
    public function __construct()
    {
        parent::__construct();
    }

    /* PUBLIC FUNCTIONS */

    /**
    * [Manager method] : Returns a new Address instance
    * @method insert
    * @public
    */
    public function insert($data = array())
    {
        // Loads the entity
        $address_entity = new \Entity\mymodelexample\address();

        // Adds new informations that must be saved
        $address_entity->label = $data['label'];
        $address_entity->city_id = $data['city_id'];
        $address_entity->dateinsert = date('Y-m-d H:i:s');
        $address_entity->dateupdate = date('Y-m-d H:i:s');
        // Saves it
        $address_entity->save();

        // Stores entity result
        $this->store_result($address_entity);

        // Remaps entities results and returns a new instance
        return $this->getInstance();
    }

    /**
    * [Manager method] : Adds a new entry on the city DB table
    */
    public function insertCity($data = array())
    {
        // Loads the entity
        $city_entity = new \Entity\mymodelexample\city();

        // Adds new informations that must be saved
        $city_entity->label = $data['label'];
        $city_entity->zipcode = $data['zipcode'];
        $city_entity->country_id = $data['country_id'];
        $city_entity->dateinsert = date('Y-m-d H:i:s');
        $city_entity->dateupdate = date('Y-m-d H:i:s');
        // Saves it
        $city_entity->save();
    }

    /**
    * [Manager method] : Adds a new entry on the country DB table
    */
    public function insertCountry($data = array())
    {
        // Loads the entity
        $country_entity = new \Entity\mymodelexample\country();

        // Adds new informations that must be saved
        $country_entity->label = $data['label'];
        $country_entity->iso = $data['iso'];
        $country_entity->dateinsert = date('Y-m-d H:i:s');
        $country_entity->dateupdate = date('Y-m-d H:i:s');
        // Saves it
        $country_entity->save();
    }

    /**
     * [Manger method]
     * @param  int
     * @return \Address_model
     */
    public function get($address_id)
    {
        // Get back informations from the entities
        $address_entity = new \Entity\mymodelexample\address($address_id);
        $city_entity = $address_entity->city()->find_one();
        $country_entity = $city_entity->country()->find_one();

        // Stores entity result, remaps it, and returns a new instance
        return $this
            ->storeResult(array($address_entity, $city_entity, $country_entity))
            ->getInstance();
    }

    /**
     * [Instance method] : Updates current Address
     * @param array
     */
    public function set($data = array())
    {
        // Loads the entity
        $address_entity = new \Entity\mymodelexample\address($this->id);

        // Sets entity informations
        foreach ($data as $key => $value) {
            $address_entity->$key = $value;
        }
        // Saves them
        $address_entity->save();

        // Stores entity result, and updates the instance
        $this
            ->storeResult($address_entity)
            ->setInstance();
    }

}


/* End of file Address_model.php */
/* Location: ./application/model/Address_model.php */
