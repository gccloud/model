<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CodeIgniter Model utility functions
 *
 * @package     CodeIgniter
 * @category    Helper
 * @author      Gregory CARRODANO <g.carrodano@gmail.com>
 * @version     20171020
 * @used-by     ./model/MY_Model.php
 */

if (! function_exists('CI')) {
    /**
     * Utility function (internal use only) : über fast CI instance getter
     * @return CI_Controller
     */
    function CI()
    {
        static $CI;
        isset($CI) or $CI = CI_Controller::get_instance();

        return $CI;
    }
}

if (! function_exists('Manager')) {
    /**
     * Utility function (internal use only) : Manager instance getter
     * @return ModelManager
     */
    function Manager()
    {
        static $manager;
        isset($manager) or $manager = ModelManager::getInstance();

        return $manager;
    }
}

if (! function_exists('modelDebug')) {
    /**
    * Utility function : Model debugger. Will ensure and validate the mapping of a specified Model, or every one (in case no argument is passed)
    * @param  string
    * @return boolean
    */
    function modelDebug($model = null)
    {
        CI()->load->database();
        CI()->load->dbutil();

        $map_list = Manager()->getMap($model);
        is_array($map_list) or $map_list = array($map_list);

        foreach ($map_list as $model => $map) {
            $primary_key = false;
            foreach ($map as $key => $value) {
                $parts = explode('.', $key);
                // Database name verification
                if (! CI()->dbutil->database_exists($parts[1])) {
                    show_error('Remap declaration error for attribute "'.$value.'" : '.$parts[1].' is not a valid database name.<br /><br /><b>Filename :</b> '.$model.'.php');
                    die;
                }
                // Table name verification
                if (! CI()->db->table_exists($parts[2])) {
                    show_error('Remap declaration error for attribute "'.$value.'" : '.$parts[2].' is not a valid table name.<br /><br /><b>Filename :</b> '.$model.'.php');
                    die;
                }
                // Field name verification
                if (! CI()->db->field_exists($parts[3], $parts[2])) {
                    show_error('Remap declaration error for attribute "'.$value.'" : '.$parts[3].' is not a valid field name.<br /><br /><b>Filename :</b> '.$model.'.php');
                    die;
                }
                // Primary keay verification
                $primary_key = ($primary_key || $parts[3] === 'id');
            }
            if (! $primary_key) {
                show_error('Remap declaration error : missing "id" attribute !<br /><br /><b>Filename :</b> '.$model.'.php');
                die;
            }
        }

        echo('<h1>Model Debbuger ended without any problem. Every Model seems correctly remaped, good work ! ;)</h1>');
        die;
    }

}


/* End of file ModelHelper.php */
/* Location: ./application/third_party/model/Utility/ModelHelper.php */
