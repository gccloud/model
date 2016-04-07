<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 *
 * @class       Example_controller
 * @package     CodeIgniter
 * @category    Controller
 * @author      Gregory CARRODANO <g.carrodano@gmail.com>
 * @version     20160119
 */
class Example_controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->driver('cache');
        $this->load->library('session');

        // Origami
        $this->load->add_package_path(APPPATH.'third_party/origami');
        $this->load->library('origami');
        $this->load->remove_package_path(APPPATH.'third_party/origami');
    }

    public function index()
    {
        // Loads a model
        $this->load->model('user_model');



        // ----------------- EXAMPLE 1 -----------------
        // Gets a User instance
        $user = $this->user_model->get(1);
        // var_dump($user);
        // exit;
        // ---------------------------------------------

        echo '<br /><br /><br />';

        // ----------------- EXAMPLE 2 -----------------
        // Gets an instance list
        $user_list = $this->user_model->get_list_v1();
        // var_dump($user_list);
        // And another one, with more informations (see model declaration for more details)
        $other_user_list = $this->user_model->get_list_v2();
        // var_dump($other_user_list);
        // exit;
        // ---------------------------------------------

        echo '<br /><br /><br />';

        // ----------------- EXAMPLE 3 -----------------
        // // Creates a new User (and automatically gets back its instance)
        $new_user = $this->user_model->insert(array(
            'lastname' => 'Test 3',
            'firstname' => 'User 3',
            'group_id' => 2
        ));
        $new_user->add_address(array(
            'label' => '33 Traverse Joint',
            'city_id' => 1
        ));
        // var_dump($new_user);
        // exit;
        // ---------------------------------------------

        echo '<br /><br /><br />';

        // ----------------- EXAMPLE 4 -----------------
        // Updates a User
        $user->set(array(
            'lastname' => 'Test 4',
            'firstname' => 'User 4'
        ));
        // And its address
        $user->set_address(array(
            'label' => '44 Traverse Joint',
        ));
        // var_dump($user);
        // exit;
        // ---------------------------------------------

        echo '<br /><br /><br />';

        // ----------------- EXAMPLE 5 -----------------
        // If needed, any loaded model can still be called as usual
        $user_group_list = $this->usergroup_model->get_list();
        var_dump($user_group_list);
        // Even stores new datas on the DB
        $this->address_model->insert_city(array(
            'label' => 'Test City 2',
            'zipcode' => '012345',
            'country_id' => 1
        ));
        $this->address_model->insert_country(array(
            'label' => 'Test Country 2',
            'iso' => 'TC2'
        ));
        // exit;
        // ---------------------------------------------
    }

}


/* End of file Example_controller.php */
/* Location: ./application/controller/Example_controller.php */
