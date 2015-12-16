<?php

/**
 * CodeIgniter
 *
 * @package   CodeIgniter
 * @author    EllisLab Dev Team
 * @copyright Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license   http://opensource.org/licenses/MIT  MIT License
 * @link      http://codeigniter.com
 * @since     Version 1.0.0
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Model Class : standard CI_Model Class override, which allows to design DB "Manager" classes
 * SIDENOTE : Though not mandatory, theses "Managers" are designed to work paired with Maltyxx's Origami package (see https://github.com/maltyxx/origami for further explainations)
 *
 * @class       MY_Model
 * @package     CodeIgniter
 * @subpackage  Core
 * @category    Core
 * @author      Gregory CARRODANO
 * @version     20151208
 */
class MY_Model extends CI_Model
{
    /* CLASS ATTRIBUTES */

    /**
     * DB result storage. Actually, this array will serve to temporarlly store Origami Entities before mapping it to a new model Instance.
     * @var array
     * @public
     */
    public $mngr_db_result = NULL;
    /**
     * Model <-> Entities attributes (datas) auto-remap. That's the core of our Manager task : each time a new Instance is created, we loop through the map to assign each stored Entity data to the corresponding model Attribute
     * @var array
     * @public
     */
    public $mngr_map = array();
    /**
     * Other models dependencies storage. This will allow to control which model can be accessed by another one (thus defining a proper "hierarchy" among all of the application's models)
     * @var array
     * @public
     */
    public $mngr_models = array();

    /* CORE FUNCTIONS */

    /**
     * Class constructor
     * @method __construct
     * @public
     */
    public function __construct()
    {
        parent::__construct();

        $datas = get_object_vars($this);

        if ( ! empty($datas)) {
            foreach ($datas as $key => $value) {
                if ( ! empty($value)) {
                    $this->mngr_map['Entity.'.$value] = $key;
                    $this->$key = NULL;
                }
            }
        }
    }

    /**
     * __get magic override
     * @method __get
     * @public
     * @param  string
     * @return object
     */
    public function __get($key)
    {
        // This method should be called when trying to access another model, so that's the only verification we'll do here.
        if (in_array($key, get_instance()->{strtolower(get_class($this))}->mngr_models)) {
            return parent::__get($key);
        }

        // Nothing returned, an error must be thrown
        $debug = debug_backtrace();
        show_error('Cannot access undefined property "'.$key.'" of class "'.get_class($this).'".<br /><br /><b>Filename :</b> '.$debug[0]['file'].'<br /><b>Function :</b> '.$debug[1]['function'].'<br /><b>Line number :</b> '.$debug[0]['line']);
    }

    /**
     * __set magic override
     * @method __set
     * @public
     * @param  mixed
     * @param  mixed
     */
    public function __set($key, $value)
    {
        if (in_array($key, array('mngr_db_result', 'mngr_map'))) {
            $this->$key = $value;
        } else {
        // Since Models should always have explicit attributes declarations, dynamic attributes (i.e declaring and seting non-class attributes to Instances) should NEVER be declared. In case such declaration is processed, this function will be called, thus throwing an error
        $debug = debug_backtrace();
        show_error('Cannot modify undefined property "'.$key.'" of class '.get_class($this).'.<br /><br /><b>Filename :</b> '.$debug[0]['file'].'<br /><b>Function :</b> '.$debug[1]['function'].'<br /><b>Line number :</b> '.$debug[0]['line']);
        }
    }

    /* MAIN FUNCTIONS */

    /**
     * Other CI Models dependencies declaration
     * @method add_models
     * @protected
     * @param array
     */
    protected function add_models($data = array())
    {
        // First, we check if anything was actually passed to the function call
        if ( ! empty($data)) {
            // If so, we auto-convert the argument passed
            if ( ! is_array($data)) {
                $data = array($data);
            }

            // And loop through it
            foreach ($data as $d) {
                // Here, we've just got to check if we're actually trying to load a correct model
                if (strpos($d, '_model') === FALSE) {
                    // If not, we throw an error
                    $debug = debug_backtrace();
                    show_error('Invalid Model declaration, unexpected "'.$d.'"<br /><br /><b>Filename :</b> '.$debug[0]['file'].'<br /><b>Function :</b> '.$debug[0]['function'].'<br /><b>Line number :</b> '.$debug[0]['line']);
                }
                // Otherwise, the declared model is correct, so we'll pack it on the models list
                $this->mngr_models[] = $d;
            }

            // Finally, we can load all previously packed models
            get_instance()->load->model($this->mngr_models);
        }
    }

    /**
     * Stores entity query for future object remap
     * @method store_result
     * @protected
     * @param  mixed
     * @return object
     */
    protected function store_result($data = array())
    {
        // First, we check if anything was actually passed to the function call
        if ( ! empty($data)) {
            // If so, we auto-convert the argument passed
            if ( ! is_array($data)) {
                $data = array($data);
            }

            // Since DB results are removed each time a new Instance is returned by the corresponding Manager, we've got to dynamically recreate it (for Instances only)
            // (SIDENOTE : i know i've said earlier that models' attributes should never be dynamically declared; but that's a very special case with one internal attribute that should never be seen outside of here - and hey, afterall, nobody's perfect ^^)
            if ( ! isset($this->mngr_db_result)) {
                $this->mngr_db_result = NULL;
            }

            // We can finally loop through our DB datas, and store it
            foreach ($data as $d) {
                $this->mngr_db_result[] = $d;
            }
        }

        return $this;
    }

    /**
     * Stores entity query for future object remap
     * @method store_result_list
     * @protected
     * @param  mixed
     * @return object
     */
    protected function store_result_list($data = array())
    {
        // First, we check if anything was actually passed to the function call
        if ( ! empty($data)) {
            // If so, we auto-convert the argument passed
            if ( ! is_array($data)) {
                $data = array($data);
            }

            // This is the same deal as in the previous function, so refer to it for further explainations
            if ( ! isset($this->mngr_db_result)) {
                $this->mngr_db_result = NULL;
            }

            // We can finally store our DB datas (no loop this time, that's a direct storing call)
            $this->mngr_db_result = $data;
        }

        return $this;
    }

    /**
     * Returns a new model Instance
     * @method _get
     * @protected
     * @return object
     */
    protected function _get()
    {
        return $this->_remap_row();
    }

    /**
     * Returns an array of new model Instances
     * @method _get_list
     * @protected
     * @return array
     */
    protected function _get_list()
    {
        return $this->_remap_list();
    }

    /**
     * Alias of _get()
     * @method _insert
     * @protected
     * @return object
     */
    protected function _insert()
    {
        return $this->_get();
    }

    /**
     * Alias of _get_list()
     * @method _insert_list
     * @protected
     * @return array
     */
    protected function _insert_list()
    {
        return $this->_get_list();
    }

    /**
     * Updates a model Instance
     * @method _set
     * @protected
     */
    protected function _set()
    {
        $this->_remap_row(TRUE);
    }

    /**
     * Updates an array of model Instances
     * @method _set_list
     * @protected
     */
    protected function _set_list()
    {
        $this->_remap_list();
    }

    /* UTILITY FUNCTIONS */
    /**
     * Flattens a Model instance, returning it as an array of key/value pairs
     * @method to_array
     * @public
     * @param  string
     * @return array
     */
    public function to_array($suffix = '')
    {
        foreach ($this as $key => $value) {
            if ( ! is_object($value)) {
                if ($value !== NULL) {
                    $return[$suffix.$key] = $value;
                }
            }
        }
    }

    /**
     * Utility method : checks the Model map, in order to to track incorrect database, tables or field names
     * @method _debug
     * @public
     */
    public function debug()
    {
        $CI =& get_instance();

        $CI->load->database();
        $CI->load->dbutil();

        $map = $this->mngr_map;
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

        return TRUE;
    }

    /* INTERNAL FUNCTIONS */

    /**
     * Fetches back all entities datas and sets Model's corresponding attributes
     * @method _remap_row
     * @private
     * @param  boolean
     * @return object
     */
    private function _remap_row($new_instance = FALSE)
    {
        if ( ! empty($this->mngr_db_result)) {
            foreach ($this->mngr_db_result as $d) {
                $this->_do_remap($d);
            }
        }

        if($new_instance) {
            $return = clone $this;
            $return->_clear();

            $this->_reset();
            return $return;
        }
    }

    /**
     * Fetches back all entities datas and sets Model's corresponding attributes
     * @method _remap_list
     * @private
     * @return array
     */
    private function _remap_list()
    {
        $return = array();

        if ( ! empty($this->mngr_db_result)) {
            foreach ($this->mngr_db_result as $d) {
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
     * @private
     * @param  object
     */
    private function _do_remap($line)
    {
        if ( ! empty($line)) {
            $map = get_instance()->{strtolower(get_class($this))}->mngr_map;

            $db_table = str_replace('\\', '.', get_class($line));
            $db_keys = $line->get();

            if ( ! empty($db_keys)) {
                foreach ($db_keys as $key => $value) {
                    $attr = $db_table.'.'.$key;
                    if (array_key_exists($attr, $map)) {
                        $this->$map[$attr] = $value;
                    }
                }
            }
        }
    }

    /**
     * Utility function (internal use only) : clears all entities results (called right before returning a newly created instance). Actually, this is only called for the base class loaded inside CI, the one acting as a "Manager".
     * @method _clear
     * @private
     */
    private function _clear() {
        unset($this->mngr_db_result, $this->mngr_map, $this->mngr_models);
    }

    /**
     * Utility function (internal use only) : clears all private variables (called right before returning a newly created instance).
     * @method _reset
     * @private
     */
    private function _reset()
    {
        $this->mngr_db_result = NULL;

        $map = $this->mngr_map;

        if (is_array($map)) {
            foreach ($map as $m) {
                $this->$m = NULL;
            }
        }
    }

}


/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */
