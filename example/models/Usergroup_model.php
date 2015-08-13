<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usergroup_model extends MY_Model {

    // Model attributes
    protected $id = 'mymodelexample.enumusergroup.id';
    protected $label = 'mymodelexample.enumusergroup.label';

    // Class Constructor
    public function __construct() {
        parent::__construct();
    }

    /**
    * [Manager method] : Returns a Usergroup instance
    * @return \Usergroup_model
    */
    public function get($usergroup_id) {
        // Get back informations from the entity
        $usergroup_entity = new \Entity\mymodelexample\enumusergroup($usergroup_id);

        // Stores entity result
        $this->save_result($usergroup_entity);

        // Remaps entities results and returns a new instance
        return parent::_get();
    }

    /**
    * [Manager method] : Returns a list of Usergroup instance
    * @return array
    */
    public function get_list() {
        // Loads the entity
        $usergroup_entity = new \Entity\mymodelexample\enumusergroup();

        // Gets back informations
        $usergroup_list = $usergroup_entity::order_by('id', 'ASC')
            ->find();

        // Stores entity result
        $this->save_result_list($usergroup_list);

        // Remaps entities results and returns a new instance
        return parent::_get_list();
    }

}


/* End of file Usergroup_model.php */
/* Location: ./application/model/Usergroup_model.php */