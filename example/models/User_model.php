<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model {

    // Model attributes
    protected $id = 'mymodelexample.user.id';
    protected $lastname = 'mymodelexample.user.lastname';
    protected $firstname = 'mymodelexample.user.firstname';
    protected $address_id = 'mymodelexample.user.address_id';
    protected $group_id = 'mymodelexample.user.usergroup_id';
    // Other model dependencies
    public $address;
    public $group;

    // Class Constructor
    public function __construct() {
        parent::__construct();

        // Model dependencies declaration
        $this->add_models(array('address_model', 'usergroup_model'));
    }

    /**
    * [Manager method] : Returns a new list of User instance
    */
    public function insert($data = array()) {
        // Loads the entities
        $user_entity = new \Entity\mymodelexample\user();

        // Adds new informations that must be saved
        $user_entity->lastname = $data['lastname'];
        $user_entity->firstname = $data['firstname'];
        // Saves it
        $user_entity->save();

        // Stores entity result
        $this->save_result($user_entity);

        // Remaps entities results and creates a new instance
        $user = parent::_get(); 

        // Calls other models attached to this one
        $user->add_address(array(
            'street' => $data['street'],
            'city' => $data['city'],
            'country' => $data['country']
        ));
        $user->add_group(array(
            'group' => $data['group']
        ));

        return $user;
    }

    /**
    * [Manager method] : Returns a new User instance
    */
    public function get($user_id) {
        // Gets back informations from the entity
        $user_entity = new \Entity\mymodelexample\user($user_id);

        // Stores entity result
        $this->save_result($user_entity);

        // Remaps entities results and creates a new instance
        $user = parent::_get(); 

        // Calls other models attached to this one
        $user->get_address();
        $user->get_group();

        // Finally returns the fully prepared instance
        return $user;
    }

    /**
    * [Manager method] : Returns a new list of User instance
    */
    public function get_list_v1() {
        /* VERSION 1 : returns User datas only */

        // Loads the entity
        $user_entity = new \Entity\mymodelexample\user();

        // Gets back informations
        $user_list = $user_entity::order_by('id', 'ASC')
            ->find();

        // Stores entity result
        $this->save_result_list($user_list);

        // Remaps entities results and returns the instance list
        return parent::_get_list();
    }

    /**
    * [Manager method] : Returns a new list of User instance
    */
    public function get_list_v2() {
        /* VERSION 2 : returns User datas, completed with their Address and Usergroup datas */

        // Loads the entity
        $user_entity = new \Entity\mymodelexample\user();

        // Gets back informations
        $entity_result = $user_entity::order_by('id', 'ASC')
            ->find();

        // Declares an array to store the instance list
        $user_list = array();
        // Loops on every entity result
        foreach($entity_result as $entity) {        
            // Stores entity result
            $this->save_result($entity);

            // Remaps entities results and creates a new instance
            $user = parent::_get(); 

            // Call other models attached to this one
            $user->get_address();
            $user->get_group();

            // Adds it to the result list
            $user_list[] = $user;
        }

        // Finally returns the instance list
        return $user_list;
    }

    /**
    * [Instance method] : Adds an Address instance for current User
    */
    public function add_address($data = array()) {
        $this->address = $this->address_model->insert($data);
    }

    /**
    * [Instance method] : Returns an Address instance for current User
    */
    public function get_address() {
        $this->address = $this->address_model->get($this->address_id);
    }

    /**
    * [Instance method] : Returns a Usergroup instance for current User
    */
    public function get_group() {
        $this->group = $this->usergroup_model->get($this->group_id);
    }
}


/* End of file User_model.php */
/* Location: ./application/model/User_model.php */