<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Address_model extends MY_Model {

    // Model attributes
    protected $id = 'mymodelexample.address.id';
    protected $street = 'mymodelexample.address.street';
    protected $zipcode = 'mymodelexample.address.zipcode';
    protected $city = 'mymodelexample.city.label';
    protected $country = 'mymodelexample.country.label';
    protected $country_iso = 'mymodelexample.country.iso';

    public function get($address_id) {
        // Get back informations from the entities
        $address_entity = new \Entity\mymodelexample\address($address_id);
        $city_entity = $address_entity->city()->find_one();
        $country_entity = $address_entity->country()->find_one();

        // Stores entity result
        $this->save_result(array($address_entity, $city_entity, $country_entity));

        // Remaps entities results and returns a new instance
        return parent::_get();
    }

}


/* End of file Address_model.php */
/* Location: ./application/model/Address_model.php */