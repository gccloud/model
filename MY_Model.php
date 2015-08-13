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
 * @version     20150710
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
        show_error('Undefined property : '.get_class($this).'::'.$key);
    }

    /**
     * __set magic
     * @param  mixed
     */
    public function __set($key, $value) {
        if(isset($this->$key)) {
            $this->$key = $value;
        }
        else {
            show_error('Unable to access property : '.get_class($this).'::'.$key);
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
                (count($class) === 4) OR show_error('Invalid Entity declaration : '.$d);
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
                (strpos($d, '_model') !== FALSE) OR show_error('Invalid Model declaration : '.$d);
                $this->_models[] = $d;
            }
            get_instance()->load->model($this->_models);
        }
    }

    /**
     * Stores entity query for futur object remap
     * @param mixed
     */
    protected function save_result($data = array()) {
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
     * Stores entity query for futur object remap
     * @param mixed
     */
    protected function save_result_list($data = array()) {
        $temp = array();

        if( ! is_array($data)) {
            $data = array($data);
        }

        if( ! empty($data)) {
            $this->_db_result = $data;
        }
    }

    /**
     * [get description]
     * @return object
     */
    protected function _get() {
        return $this->_remap_row();
    }

    /**
     * [get_list description]
     * @return array
     */
    protected function _get_list() {
        return $this->_remap_list();
    }

    /**
     * [insert description]
     * @return object
     */
    protected function _insert() {
        return $this->_remap_row();
    }

    /**
     * [insert_list description]
     * @return array
     */
    protected function _insert_list() {
        return $this->_remap_list();
    }

    /**
     * [set description]
     */
    protected function _set() {
        $this->_remap_row(FALSE);
    }

    /**
     * [set_list description]
     */
    protected function _set_list() {
        $this->_remap_list(FALSE);
    }

    /**
     * Fetches back all entities datas and sets model's corresponding attributes
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
     * Fetches back all entities datas and sets model's corresponding attributes
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
