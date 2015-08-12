<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Example_controller extends CI_Controller {

    public function index() {
        // Loads a model
        $this->load->model('user_model');



        // ----------------- EXAMPLE 1 -----------------
        // Gets a User instance
        $user = $this->user_model->get(1);
        // var_dump($user);
        // exit;
        // ---------------------------------------------



        // ----------------- EXAMPLE 2 -----------------
        // Gets an instance list
        $user_list = $this->user_model->get_list_v1();
        // var_dump($user_list);
        // And another one, with more informations (see model declaration for more details)
        $other_user_list = $this->user_model->get_list_v2();
        // var_dump($other_user_list);
        // exit;
        // ---------------------------------------------



        // ----------------- EXAMPLE 3 -----------------
        // Creates a new User (and automatically gets back its instance)
        $new_user = $this->user_model->insert(array(
            'lastname' => 'Test',
            'firstname' => 'New User',
            'street' => '11 Traverse Joint',
            'city' => 'Nice',
            'country' => 'France',
            'group' => \Entity\mymodelexample\enumusergroup::ADMIN
        ));
        // var_dump($new_user);
        // exit;
        // ---------------------------------------------



        // ----------------- EXAMPLE 5 -----------------
        // If needed, any loaded model can still be called as usual
        $user_group_list = $this->usergroup_model->get_list();
        // var_dump($user_group_list);
        $new_address = $this->address_model->insert(
        );
        // var_dump($new_address);
        // exit;
        // ---------------------------------------------
    }

}


/* End of file Example_controller.php */
/* Location: ./application/controller/Example_controller.php */