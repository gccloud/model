<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usergroup_model extends MY_Model
{
    /* CLASS ATTRIBUTES */

    public $id = 'mymodelexample.enumusergroup.id';
    public $label = 'mymodelexample.enumusergroup.label';

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
    * [Manager method] : Returns a new Usergroup instance
    * @method get
    * @public
    * @return \Usergroup_model
    */
    public function get($usergroup_id)
    {
        // Gets back informations from the entity, stores result, remaps it, and returns a new instance
        return $this->store_result(new \Entity\mymodelexample\enumusergroup($usergroup_id))
            ->_get();
        /*
            ************************************
            *   Details (unfactorized calls)   *
            ************************************
            * First, gets back informations from the entity
                $entity = new \Entity\mymodelexample\enumusergroup($usergroup_id);
            * Then stores the entity result
                $this->store_result($entity);
            * Remaps it, creating a new instance
                $new_instance = $this->_get();
            * Finally returns that instance
                return $new_instance;
            ************************************
        */
    }

    /**
    * [Manager method] : Returns a new list of Usergroup instance
    * @method get_list
    * @public
    * @return array
    */
    public function get_list()
    {
        // Gets back informations from the entity, stores result, and returns a new instance list
        return $this->store_result_list(\Entity\mymodelexample\enumusergroup::order_by('id', 'ASC')
                ->find())
            ->_get_list();
        /*
            ************************************
            *   Details (unfactorized calls)   *
            ************************************
            * First, gets back informations from the entity
                $entity_list = \Entity\mymodelexample\enumusergroup::order_by('id', 'ASC')->find();
            * Then stores the entity result
                $this->store_result_list($entity_list);
            * Remaps it, creating a list of new instance
                $result_list = $this->_get_list();
            * Finally returns that list
                return $result_list;
            ************************************
        */
    }

}


/* End of file Usergroup_model.php */
/* Location: ./application/model/Usergroup_model.php */
