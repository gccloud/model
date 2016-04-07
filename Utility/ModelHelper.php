<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Model utility functions
 *
 * @package     CodeIgniter
 * @category    Helper
 * @author      Gregory CARRODANO <g.carrodano@gmail.com>
 * @version     20160407
 * @used-by     ./model/MY_Model.php
 */

if ( ! function_exists('CI')) {
    /**
     * Utility function (internal use only) : über fast CI instance getter
     * @method CI
     * @return CI_Controller
     */
    function CI()
    {
        static $CI;
        isset($CI) or $CI = CI_Controller::get_instance();

        return $CI;
    }
}

if ( ! function_exists('Manager')) {
    /**
     * Utility function (internal use only) : Manager instance getter
     * @method Manager
     * @return ModelManager
     */
    function Manager()
    {
        static $manager;
        isset($manager) or $manager = ModelManager::get_instance();

        return $manager;
    }
}

if ( ! function_exists('model_debug')) {
    /**
    * Utility function : Model debugger. Will ensure and validate the mapping of a specified Model, or every one (in case no argument is passed)
    * @method model_debug
    * @return boolean
    */
    function model_debug($model = null)
    {
        $CI = CI();

        $CI->load->database();
        $CI->load->dbutil();

        $map_list = Manager()->get_map($model);
        is_array($map_list) or $map_list = array($map_list);

        foreach ($map_list as $map) {
            foreach ($map as $key => $value) {
                $parts = explode('.', $key);
                if ( ! $CI->dbutil->database_exists($parts[1])) {
                    $debug = debug_backtrace();
                    show_error('Remap declaration error for attribute "'.$value.'" : '.$parts[1].' is not a valid database name.<br /><br /><b>Filename :</b> '.get_class($debug[0]['object']));
                }
                if ( ! $CI->db->table_exists($parts[2])) {
                    $debug = debug_backtrace();
                    show_error('Remap declaration error for attribute "'.$value.'" : '.$parts[2].' is not a valid table name.<br /><br /><b>Filename :</b> '.get_class($debug[0]['object']));
                }
                if ( ! $CI->db->field_exists($parts[3], $parts[2])) {
                    $debug = debug_backtrace();
                    show_error('Remap declaration error for attribute "'.$value.'" : '.$parts[3].' is not a valid field name.<br /><br /><b>Filename :</b> '.get_class($debug[0]['object']));
                }
            }
        }

        return true;
    }
}


/* End of file ModelHelper.php */
/* Location: ./application/third_party/model/Utility/ModelHelper.php */
