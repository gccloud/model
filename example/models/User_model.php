<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends MY_Model
{
    /* CLASS ATTRIBUTES */

    public $id = 'mymodelexample.user.id';
    public $lastname = 'mymodelexample.user.lastname';
    public $firstname = 'mymodelexample.user.firstname';
    public $address_id = 'mymodelexample.user.address_id';
    public $group_id = 'mymodelexample.user.enumusergroup_id';
    // Other model dependencies
    public $address;
    public $group;

    /* CORE FUNCTIONS */

    /**
     * Class Constructor
     * @method __construct
     * @public
     */
    public function __construct()
    {
        parent::__construct();

        // Model dependencies declaration
        $this->add_models(array('address_model', 'usergroup_model'));
    }

    /* PUBLIC FUNCTIONS */

    /**
    * [Manager method] : Creates a new User instance
    * @method insert
    * @public
    * @return \User_model
    */
    public function insert($data = array())
    {
        // Loads the entity
        $user_entity = new \Entity\mymodelexample\user();

        // Adds new informations that must be saved
        $user_entity->lastname = $data['lastname'];
        $user_entity->firstname = $data['firstname'];
        $user_entity->enumusergroup_id = $data['group_id'];
        $user_entity->dateinsert = date('Y-m-d H:i:s');
        $user_entity->dateupdate = date('Y-m-d H:i:s');

        // Saves it
        $user_entity->save();

        // Stores entity result, remaps it, and creates a new instance
        return $this
            ->storeResult($user_entity)
            ->getInstance();
    }

    /**
    * [Manager method] : Returns a User instance
    * @method get
    * @public
    * @return \User_model
    */
    public function get($user_id)
    {
        // Gets back informations from the entity, stores result, remaps it, creates a new instance, calls other models attached to this one, and finally returns the fully prepared instance
        return $this
            ->storeResult(new \Entity\mymodelexample\user($user_id))
            ->getInstance()
            ->getAddress()
            ->getGroup();
    }

    /**
    * [Manager method] : Returns a list of User instance (short version)
    * @method getListV1
    * @public
    * @return array
    */
    public function getListV1()
    {
        /* VERSION 1 : returns User datas only */

        // Gets back entity informations, stores result, remaps it, and returns the instance list
        return $this
            ->storeResultList(\Entity\mymodelexample\user::order_by('id', 'ASC')
            ->find())
            ->getInstanceList();
    }

    /**
    * [Manager method] : Returns a list of User instance (complex version)
    * @method getListV2
    * @public
    * @return array
    */
    public function getListV2()
    {
        /* VERSION 2 : returns User datas, completed with their Address and Usergroup datas */

        // Gets back entity informations
        $entity_result = \Entity\mymodelexample\user::order_by('id', 'ASC')
            ->find();

        // Declares an array to store the instance list
        $user_list = array();

        // Loops on every entity result
        foreach($entity_result as $entity) {
            // Stores result, remaps it, creates a new instance, calls other models attached to this one, and adds the fully prepared instance it to the result list
            $user_list[] = $this->storeResult($entity)
                ->getInstance()
                ->getAddress()
                ->getGroup();
        }

        // Finally returns the instance list
        return $user_list;
    }

    /**
    * [Instance method] : Updates current User informations
    */
    public function set($data = array())
    {
        // Loads the entity
        $user_entity = new \Entity\mymodelexample\user($this->id);

        // Sets entity informations
        foreach($data as $key => $value) {
            $user_entity->$key = $value;
        }
        $user_entity->dateupdate = date('Y-m-d H:i:s');

        // Saves them
        $user_entity->save();

        // Stores entity result, and updates the instance
        $this->store_result($user_entity)
            ->_set();
    }

    /**
    * [Instance method] : Adds an Address instance for current User
    * @method addAddress
    * @public
    * @return \Address_model
    */
    public function addAddress($data = array())
    {
        $this->address = $this->address_model->insert($data);

        $this->address_id = $this->address->id;
        $this->setInstance();
    }

    /**
    * [Instance method] : Returns an Address instance for current User
    * @method getAddress
    * @public
    * @return \User_model
    */
    public function getAddress()
    {
        $this->address = $this->address_model->get($this->address_id);

        return $this;
    }

    /**
    * [Instance method] : Updates the Address instance for current User
    * @method set_address
    * @public
    */
    public function setAddress($data)
    {
        $this->address->setInstance($data);
    }

    /**
    * [Instance method] : Returns a Usergroup instance for the current User
    * @method getGroup
    * @public
    * @return \User_model
    */
    public function getGroup()
    {
        $this->group = $this->usergroup_model->get($this->group_id);

        return $this;
    }

}


/* End of file User_model.php */
/* Location: ./application/model/User_model.php */
