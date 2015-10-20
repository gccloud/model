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
 * @version     20150902
 */
class MY_Model extends CI_Model {
    private $_map = array();
    private $_entities = array();
    private $_models = array();
    private $_db_result = NULL;

    /**
     * Class constructor
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
     * @param  mixed
     * @return mixed
     */
    public function __get($key) {
        if(in_array($key, $this->_map) || isset($this->$key)) {
            return $this->$key;
        }
        elseif(array_key_exists($key, $this->_entities)) {
            return $this->_entities[$key];
        }
        elseif(in_array($key, $this->_models)) {
            return parent::__get($key);
        }
        $debug = debug_backtrace();
        show_error('Cannot access undefined property \''.$key.'\' of class '.get_class($this).'.<br /><br /><b>Filename :</b> '.$debug[0]['file'].'<br /><b>Function :</b> '.$debug[1]['function'].'<br /><b>Line number :</b> '.$debug[0]['line']);
    }

    /**
     * __set magic
     * @param  mixed
     */
    public function __set($key, $value) {
        if(property_exists($this, $key)) {
            $this->$key = $value;
        }
        else {
            $debug = debug_backtrace();
            show_error('Cannot modify undefined property \''.$key.'\' of class '.get_class($this).'.<br /><br /><b>Filename :</b> '.$debug[0]['file'].'<br /><b>Function :</b> '.$debug[1]['function'].'<br /><b>Line number :</b> '.$debug[0]['line']);
        }
    }

    /**
     * ORigaMi Entities dependencies declaration
     * @param  array
     */
    protected function add_entities($data = array()) {
        if( ! is_array($data)) {
            $data = array($data);
        }

        if( ! empty($data)) {
            foreach ($data as $d) {
                $class = explode('\\', $d);
                if(count($class) !== 4) {
                    $debug = debug_backtrace();
                    show_error('Invalid Entity declaration, unexpected \''.$d.'\'<br /><br /><b>Filename :</b> '.$debug[0]['file'].'<br /><b>Function :</b> '.$debug[0]['function'].'<br /><b>Line number :</b> '.$debug[0]['line']);
                }
                $entity_key = $class[2].'_'.$class[3];
                $this->_entities[$entity_key] = $d;
            }
        }
    }

    /**
     * Other CI Models dependencies declaration
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
     * @param mixed
     */
    protected function store_result($data = array()) {
        if( ! is_array($data)) {
            $data = array($data);
        }

        if( ! empty($data)) {
            foreach($data as $d) {
                $this->_db_result[] = $d;
            }
        }
    }

    /**
     * Stores entity query for future object remap
     * @param mixed
     */
    protected function store_result_list($data = array()) {
        $temp = array();

        if( ! is_array($data)) {
            $data = array($data);
        }

        if( ! empty($data)) {
            $this->_db_result = $data;
        }
    }

    /**
     * Returns a new Model instance
     * @return object
     */
    protected function _get() {
        return $this->_remap_row();
    }

    /**
     * Returns an array of new Model instances
     * @return array
     */
    protected function _get_list() {
        return $this->_remap_list();
    }

    /**
     * Alias of _get()
     * @return object
     */
    protected function _insert() {
        return $this->_get();
    }

    /**
     * Alias of _get_list()
     * @return array
     */
    protected function _insert_list() {
        return $this->_get_list();
    }

    /**
     * Updates a Model instance
     */
    protected function _set() {
        $this->_remap_row(FALSE);
    }

    /**
     * Updates an array of Model instances
     */
    protected function _set_list() {
        $this->_remap_list(FALSE);
    }

    /**
     * [_debug description]
     */
    public function debug() {
        $CI =& get_instance();
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

    /**
     * Fetches back all entities datas and sets Model's corresponding attributes
     * @return object
     */
    private function _remap_row($new_instance = TRUE) {
        if( ! empty($this->_db_result)) {
            foreach($this->_db_result as $d) {
                $this->_remap($d);
            }
        }

        if($new_instance) {
            $return = clone $this;
            $return->_db_result = NULL;

            $this->_clear();
            return $return;
        }
        else {
            $this->_db_result = NULL;
        }
    }

    /**
     * Fetches back all entities datas and sets Model's corresponding attributes
     * @return array
     */
    private function _remap_list() {
        $return = array();

        if( ! empty($this->_db_result)) {
            foreach($this->_db_result as $d) {
                $new_instance = new static();
                $new_instance->_remap($d);
                $new_instance->_db_result = NULL;
                $return[] = $new_instance;
            }
        }

        $this->_clear();
        return $return;
    }

    /**
     * Remap processing function
     * @param  object
     */
    private function _remap($line) {
        if( ! empty($line)) {
            $map = $this->_map;

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
     * Clears all entities results (called right before returning a newly created instance)
     */
    private function _clear() {
        $this->_db_result = NULL;

        $map = $this->_map;
        foreach($map as $m) {
            $this->$m = NULL;
        }
    }

}


/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */
