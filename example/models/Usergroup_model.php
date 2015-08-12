<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usergroup_model extends MY_Model {

    // Model attributes
    protected $id = 'mymodelexample.enumusergroup.id';
    protected $label = 'mymodelexample.enumusergroup.label';

    /**
    * [Manager method] : Returns a new Usergroup instance
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
    * [Manager method] : Returns a new list of Usergroup instance
    */
    public function get_list() {
        // Gets back informations
        $usergroup_list = $user_entity::order_by('id', 'ASC')
            ->find();

        // Stores entity result
        $this->save_result_list($usergroup_list);

        // Remaps entities results and returns a new instance
        return parent::_get_list();
    }

}


/* End of file Usergroup_model.php */
/* Location: ./application/model/Usergroup_model.php */