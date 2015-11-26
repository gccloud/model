<?php

/**
 * CodeIgniter
 *
 * @package CodeIgniter
 * @author  EllisLab Dev Team
 * @copyright   Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright   Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license http://opensource.org/licenses/MIT  MIT License
 * @link    http://codeigniter.com
 * @since   Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model Class
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Libraries
 * @author      Gregory Carrodano
 * @version     20151124
 */
class MY_Model extends CI_Model {
    private $_map = array();
    private $_models = array();
    private $_db_result = NULL;

    /* CORE FUNCTIONS */

    /**
     * Class constructor
     * @method __construct
     */
    public function __construct() {
        parent::__construct();

        $datas = get_object_vars($this);

        if( ! empty($datas)) {
            foreach($datas as $key => $value) {
                if( ! empty($value)) {
                    $this->_map['Entity.'.$value] = $key;
                    $this->$key = NULL;
                }
            }
        }
    }

    /**
     * __get magic
     * @method __get
     * @param  mixed
     * @return mixed
     */
    public function __get($key) {
        // This method should be called when trying to access another Model, or to store results on an existing instance; so these are the only verifications we'll do here.
        // First, we have to check if we're trying to retrieve some attributes attached to the Manager only
        if(in_array($key, array('_models', '_map'))) {
            return $key;
        }
        // Then, we have to check if we're trying to acces another model from an instance (and therefore, take it back from the corresponding Manager)
        else {
            $manager = get_instance()->{strtolower(get_class($this))};
            if(in_array($key, $manager->_models)) {
                return parent::__get($key);
            }
        }

        $debug = debug_backtrace();
        show_error('Cannot access undefined property \''.$key.'\' of class '.get_class($this).'.<br /><br /><b>Filename :</b> '.$debug[0]['file'].'<br /><b>Function :</b> '.$debug[1]['function'].'<br /><b>Line number :</b> '.$debug[0]['line']);
    }

    /**
     * __set magic
     * @method __set
     * @param  mixed
     */
    public function __set($key, $value) {
        if($key === '_db_result') {
            $this->_db_result = $value;
        }
        else if(property_exists($this, $key)) {
            $this->$key = $value;
        }
        else {
            $debug = debug_backtrace();
            show_error('Cannot modify undefined property \''.$key.'\' of class '.get_class($this).'.<br /><br /><b>Filename :</b> '.$debug[0]['file'].'<br /><b>Function :</b> '.$debug[1]['function'].'<br /><b>Line number :</b> '.$debug[0]['line']);
        }
    }

    /* MAIN METHODS */

    /**
     * Other CI Models dependencies declaration
     * @method add_models
     * @param array
     */
    protected function add_models($data = array()) {
        if( ! is_array($data)) {
            $data = array($data);
        }

        if( ! empty($data)) {
            foreach ($data as $d) {
                if(strpos($d, '_model') === FALSE) {
                    $debug = debug_backtrace();
                    show_error('Invalid Model declaration, unexpected \''.$d.'\'<br /><br /><b>Filename :</b> '.$debug[0]['file'].'<br /><b>Function :</b> '.$debug[0]['function'].'<br /><b>Line number :</b> '.$debug[0]['line']);
                }
                $this->_models[] = $d;
            }
            get_instance()->load->model($this->_models);
        }
    }

    /**
     * Stores entity query for future object remap
     * @method store_result
     * @param mixed
     */
    protected function store_result($data = array()) {
        if( ! is_array($data)) {
            $data = array($data);
        }

        if( ! isset($this->_db_result)) {
            $this->_db_result = NULL;
        }

        if( ! empty($data)) {
            foreach($data as $d) {
                $this->_db_result[] = $d;
            }
        }
    }

    /**
     * Stores entity query for future object remap
     * @method store_result_list
     * @param mixed
     */
    protected function store_result_list($data = array()) {
        $temp = array();

        if( ! is_array($data)) {
            $data = array($data);
        }

        if( ! isset($this->_db_result)) {
            $this->_db_result = NULL;
        }

        if( ! empty($data)) {
            $this->_db_result = $data;
        }
    }

    /**
     * Returns a new Model instance
     * @method _get
     * @return object
     */
    protected function _get() {
        return $this->_remap_row();
    }

    /**
     * Returns an array of new Model instances
     * @method _get_list
     * @return array
     */
    protected function _get_list() {
        return $this->_remap_list();
    }

    /**
     * Alias of _get()
     * @method _insert
     * @return object
     */
    protected function _insert() {
        return $this->_get();
    }

    /**
     * Alias of _get_list()
     * @method _insert_list
     * @return array
     */
    protected function _insert_list() {
        return $this->_get_list();
    }

    /**
     * Updates a Model instance
     * @method _set
     */
    protected function _set() {
        $this->_remap_row();
    }

    /**
     * Updates an array of Model instances
     * @method _set_list
     */
    protected function _set_list() {
        $this->_remap_list(FALSE);
    }

    /**
     * Utility method : checks the Model map, in order to to track incorrect database, tables or field names
     * @method _debug
     */
    public function debug() {
        $CI =& get_instance();

        $CI->load->database();
        $CI->load->dbutil();

        $map = $this->_map;
        foreach($map as $key => $value) {
            $parts = explode('.', $key);
            if( ! $CI->dbutil->database_exists($parts[1])) {
                $debug = debug_backtrace();
                show_error('Remap declaration error for attribute "'.$value.'" : '.$parts[1].' is not a valid database name.<br /><br /><b>Filename :</b> '.get_class($debug[0]['object']));
            }
            if( ! $CI->db->table_exists($parts[2])) {
                $debug = debug_backtrace();
                show_error('Remap declaration error for attribute "'.$value.'" : '.$parts[2].' is not a valid table name.<br /><br /><b>Filename :</b> '.get_class($debug[0]['object']));
            }
            if( ! $CI->db->field_exists($parts[3], $parts[2])) {
                $debug = debug_backtrace();
                show_error('Remap declaration error for attribute "'.$value.'" : '.$parts[3].' is not a valid field name.<br /><br /><b>Filename :</b> '.get_class($debug[0]['object']));
            }
        }

        return TRUE;
    }

    /* INTERNAL METHODS */

    /**
     * Fetches back all entities datas and sets Model's corresponding attributes
     * @method _remap_row
     * @return object
     */
    private function _remap_row() {
        if( ! empty($this->_db_result)) {
            foreach($this->_db_result as $d) {
                $this->_do_remap($d);
            }
        }

        $return = clone $this;
        $return->_clear();

        $this->_reset();
        return $return;
    }

    /**
     * Fetches back all entities datas and sets Model's corresponding attributes
     * @method _remap_list
     * @return array
     */
    private function _remap_list() {
        $return = array();

        if( ! empty($this->_db_result)) {
            foreach($this->_db_result as $d) {
                $new_instance = new static();

                $new_instance->_do_remap($d);
                $new_instance->_clear();

                $return[] = $new_instance;
            }
        }

        $this->_reset();
        return $return;
    }

    /**
     * Remap processing function
     * @method _do_remap
     * @param  object
     */
    private function _do_remap($line) {
        if( ! empty($line)) {
            // $map = $this->_map;
            $map = get_instance()->{strtolower(get_class($this))}->_map;

            $db_table = str_replace('\\', '.', get_class($line));
            $db_keys = $line->get();

            if( ! empty($db_keys)) {
                foreach($db_keys as $key => $value) {
                    $attr = $db_table.'.'.$key;
                    if(array_key_exists($attr, $map)) {
                        $this->$map[$attr] = $value;
                    }
                }
            }
        }
    }

    /**
     * Utility function (internal use only) : clears all entities results (called right before returning a newly created instance). Actually, this is only called for the base class loaded inside CI, the one acting as a "Manager".
     * @method _clear
     */
    private function _clear() {
        unset($this->_db_result, $this->_map, $this->_models);
    }

    /**
     * Utility function (internal use only) : clears all private variables (called right before returning a newly created instance).
     * @method _reset
     */
    private function _reset() {
        $this->_db_result = NULL;

        $map = $this->_map;

        if(is_array($map)) {
            foreach($map as $m) {
                $this->$m = NULL;
            }
        }
    }

}


/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */
