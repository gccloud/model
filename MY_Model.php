<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(__DIR__.'/Manager/ModelManager.php');
require_once(__DIR__.'/Utility/ModelHelper.php');

/**
 * CodeIgniter Model Class : standard CI_Model Class override, which allows to design DB "Manager" classes
 * SIDENOTE : Though not mandatory, these Models are designed to work paired with Maltyxx's Origami package (see https://github.com/maltyxx/origami for further explainations)
 *
 * @class       MY_Model
 * @package     CodeIgniter
 * @category    Core
 * @author      Gregory CARRODANO <g.carrodano@gmail.com>
 * @version     20160407
 * @uses        ./model/Manager/ModelManager.php
 *              ./model/Utility/ModelHelper.php
 */
class MY_Model extends CI_Model
{
    /* CORE FUNCTIONS */

    /**
     * Class constructor
     * @method __construct
     * @public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        if (get_class($this) !== 'MY_Model') {
            $datas = get_object_vars($this);

            if (! empty($datas)) {
                foreach ($datas as $key => $value) {
                    if (! empty($value)) {
                        Manager()->stack_map(get_class($this), 'Entity.'.$value, $key);
                        $this->$key = null;
                    }
                }
            }

            // MICRO-OPTIMIZATION : MUST BE TESTED BEFORE BEING INTEGRATED
            // unset($datas);
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
        // This method should ONLY be called when trying to access another model, so that's the only verification we'll do here.
        if (in_array($key, Manager()->get_models(get_class($this)))) {
            return get_instance()->$key;
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
     * @return void
     */
    public function __set($key, $value)
    {
        // Since Models should always have explicit attributes declarations, dynamic attributes (i.e declaring and seting non-class attributes to Instances) should NEVER be declared. In case such declaration is processed, this function will be called, thus throwing an error
        $debug = debug_backtrace();
        show_error('Cannot modify undefined property "'.$key.'" of class '.get_class($this).'.<br /><br /><b>Filename :</b> '.$debug[0]['file'].'<br /><b>Function :</b> '.$debug[1]['function'].'<br /><b>Line number :</b> '.$debug[0]['line']);
    }

    /* MAIN FUNCTIONS */

    /**
     * Other CI Models dependencies declaration
     * @method add_models
     * @protected
     * @param array
     * @return void
     */
    protected function add_models($data = array())
    {
        // First, we check if anything was actually passed to the function call
        if (! empty($data)) {
            $new_models = array();

            // If so, we auto-convert the argument passed (in case it's not an array)
            is_array($data) or $data = array($data);

            // And loop through it
            foreach ($data as $d) {
                // Here, we've just got to check if we're actually trying to load a correct model
                if (strpos($d, '_model') === false) {
                    // If not, we throw an error
                    $debug = debug_backtrace();
                    show_error('Invalid Model declaration, unexpected "'.$d.'"<br /><br /><b>Filename :</b> '.$debug[0]['file'].'<br /><b>Function :</b> '.$debug[0]['function'].'<br /><b>Line number :</b> '.$debug[0]['line']);
                }
                // Otherwise, the declared model is correct, so we'll pack it on the models list
                $new_models[] = $d;
                Manager()->stack_model(get_class($this), $d);
            }

            // Finally, we can load all previously packed models and store it in our Manager
            CI()->load->model($new_models);

            // MICRO-OPTIMIZATION : MUST BE TESTED BEFORE BEING INTEGRATED
            // unset($new_models)
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
        // MICRO-OPTIMIZATION : MUST BE TESTED BEFORE BEING INTEGRATED
        // $Manager = Manager();

        // First, we've got to insure that something was actually passed to the function call
        if (! empty($data)) {
            // If so, we auto-convert the argument passed (in case it's not an array)
            is_array($data) or $data = array($data);

            // Now we can loop through our DB datas, and store it on the current Manager instance
            foreach ($data as $d) {
                // MICRO-OPTIMIZATION : MUST BE TESTED BEFORE BEING INTEGRATED
                // $Manager->stack_db_result($d);
                Manager()->stack_db_result($d);
            }
        }

        // MICRO-OPTIMIZATION : MUST BE TESTED BEFORE BEING INTEGRATED
        // unset($Manager)

        // And finally return the current instance itslef (allowing "CI's like" chaining method calls)
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
        // MICRO-OPTIMIZATION : MUST BE TESTED BEFORE BEING INTEGRATED
        // $Manager = Manager();

        // First, we've got to insure that something was actually passed to the function call
        if (! empty($data)) {
            // If so, we auto-convert the argument passed (in case it's not an array)
            is_array($data) or $data = array($data);

            // Now we can store our DB datas (no loop this time, that's a direct storing call)
            // MICRO-OPTIMIZATION : MUST BE TESTED BEFORE BEING INTEGRATED
            // $Manager->stack_db_result($data);
            Manager()->stack_db_result($data);
        }

        // And finally return the current instance itself (allowing "CI's like" chained method calls)
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
        return $this->_remap_row(true);
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
        return $this->_get(true);
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
     * @return void
     */
    protected function _set()
    {
        $this->_remap_row();
    }

    /**
     * Updates an array of model Instances
     * @method _set_list
     * @protected
     * @return void
     */
    protected function _set_list()
    {
        $this->_remap_list();
    }

    /* UTILITY FUNCTIONS */

    /**
     * Flattens a Model instance, returning it as an array of key / value pairs
     * @method to_array
     * @public
     * @param  string
     * @return array
     */
    public function to_array($prefix = '')
    {
        $return = array();

        foreach ($this as $key => $value) {
            if (! is_object($value)) {
                if ($value !== null) {
                    $return[$prefix.$key] = $value;
                }
            }
        }

        return $return;
    }

    /* INTERNAL FUNCTIONS */

    /**
     * Fetches back all entities datas and sets Model's corresponding attributes
     * @method _remap_row
     * @private
     * @param  boolean
     * @return object
     */
    private function _remap_row($new_instance = false)
    {
        $return = null;

        $db_result = Manager()->get_db_result();

        if (! empty($db_result)) {
            foreach ($db_result as $d) {
                $this->_do_remap($d);
            }
        }

        // MICRO-OPTIMIZATION : MUST BE TESTED BEFORE BEING INTEGRATED
        // unset($db_result);

        (! $new_instance) or $return = clone $this;

        $this->_reset($new_instance);
        return $return;
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

        $db_result = Manager()->get_db_result();

        if (! empty($db_result)) {
            foreach ($db_result as $d) {
                $instance = new static();
                $instance->_do_remap($d);

                $return[] = $instance;
            }
        }

        // MICRO-OPTIMIZATION : MUST BE TESTED BEFORE BEING INTEGRATED
        // unset($db_result);

        $this->_reset();
        return $return;
    }

    /**
     * Remap processing function
     * @method _do_remap
     * @private
     * @param  object
     * @return void
     */
    private function _do_remap($line = null)
    {
        if (! empty($line)) {
            $map = Manager()->get_map(get_class($this));
            $db_table = str_replace('\\', '.', get_class($line));
            $db_keys = $line->get();

            if (! empty($db_keys)) {
                foreach ($db_keys as $key => $value) {
                    $attr = $db_table.'.'.$key;
                    if (array_key_exists($attr, $map)) {
                        $this->$map[$attr] = $value;
                    }
                }
            }

            // MICRO-OPTIMIZATION : MUST BE TESTED BEFORE BEING INTEGRATED
            // unset($map, $db_table, $db_keys);
        }
    }

    /**
     * Utility function (internal use only) : clears Model / Manager variables (called right before returning a newly created instance).
     * @method _reset
     * @private
     * @return void
     */
    private function _reset($new_instance = false)
    {
        // MICRO-OPTIMIZATION : MUST BE TESTED BEFORE BEING INTEGRATED
        // $Manager = Manager();

        // $Manager->reset_db_result();
        Manager()->reset_db_result();

        if ($new_instance) {
            // $map = $Manager->get_map(get_class($this));
            $map = Manager()->get_map(get_class($this));

            if (is_array($map)) {
                foreach ($map as $m) {
                    $this->$m = null;
                }
            }

            // MICRO-OPTIMIZATION : MUST BE TESTED BEFORE BEING INTEGRATED
            // unset($map);
        }
    }

}


/* End of file MY_Model.php */
/* Location: ./application/third_party/model/MY_Model.php */
