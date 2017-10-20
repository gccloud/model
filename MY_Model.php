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
 * @version     20171020
 * @uses        ./model/Manager/ModelManager.php
 *              ./model/Utility/ModelHelper.php
 */
class MY_Model extends CI_Model
{
    /* CORE FUNCTIONS */

    /**
     * Class constructor
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
                        Manager()->stackMap(get_class($this), 'Entity.'.$value, $key);
                        $this->$key = null;
                    }
                }
            }
        }
    }

    /**
     * __get magic override
     * @param  string
     * @return object
     */
    public function __get($key)
    {
        // This method should ONLY be called when trying to access another CI's DB Object or model, so these are the only verifications we'll do here.
        if (strpos($key, 'db_') !== false) {
            if (! isset(CI()->$key)) {
                CI()->$key = CI()->load->database(str_replace('db_', '', $key), true);
                CI()->$key->initialize();
            }

            return parent::__get($key);
        } elseif (in_array($key, Manager()->getModels(get_class($this)))) {
            return CI()->$key;
        }

        // Nothing returned, an error must be thrown
        $debug = debug_backtrace();
        show_error('Cannot access undefined property "'.$key.'" of class "'.get_class($this).'".<br /><br /><b>Filename :</b> '.$debug[0]['file'].'<br /><b>Function :</b> '.$debug[1]['function'].'<br /><b>Line number :</b> '.$debug[0]['line']);
    }

    /**
     * __set magic override
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
     * @param  array
     * @return void
     */
    protected function addModels($data = array())
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
                Manager()->stackModel(get_class($this), $d);
            }

            // Finally, we can load all previously packed models and store it in our Manager
            CI()->load->model($new_models);
        }
    }

    /**
     * Stores entity query for future object remap
     * @param  mixed
     * @return object
     */
    protected function storeResult($data = array())
    {
        // First, we've got to insure that something was actually passed to the function call
        if (! empty($data)) {
            // If so, we auto-convert the argument passed (in case it's not an array)
            is_array($data) or $data = array($data);

            // Now we can loop through our DB datas, and store it on the current Manager instance
            foreach ($data as $d) {
                Manager()->stackDbResult($d);
            }
        }

        // And finally return the current instance itslef (allowing "CI's like" chaining method calls)
        return $this;
    }

    /**
     * Stores entity query for future object remap
     * @param  mixed
     * @return object
     */
    protected function storeResultList($data = array())
    {
        // First, we've got to insure that something was actually passed to the function call
        if (! empty($data)) {
            // If so, we auto-convert the argument passed (in case it's not an array)
            is_array($data) or $data = array($data);

            // Now we can store our DB datas (no loop this time, that's a direct storing call)
            Manager()->stackDbResult($data);
        }

        // And finally return the current instance itself (allowing "CI's like" chained method calls)
        return $this;
    }

    /**
     * Returns a new model Instance
     * @return object
     */
    public function getInstance()
    {
        return $this->_forwardRemapRow();
    }

    /**
     * Returns an array of new model Instances
     * @return array
     */
    public function getInstanceList()
    {
        return $this->_forwardRemapList();
    }

    /**
     * Updates a model Instance
     * @param  boolean
     * @return void
     */
    public function setInstance($final = true)
    {
        // First of all we fetch back every Model attribute
        $attribute_list = get_object_vars($this);

        // And then loop through it
        foreach ($attribute_list as $key => $attribute) {
            // Each attribute must be processed specifically according to it's type
            if (! is_null($this->$key)) {
                // Is it an array (probably an array of Model Instances) ?
                if (is_array($this->$key)) {
                    // If so, we've got to loop through it, and set every Model Instance found
                    foreach ($this->$key as $sub_key) {
                        if (is_object($sub_key) && strpos(get_class($sub_key), '_model') !== false) {
                            $sub_key->setInstance(false);
                        }
                    }
                // Is it a single Model Instance waiting to be set ?
                } elseif (is_object($this->$key) && strpos(get_class($this->$key), '_model') !== false) {
                    $this->$key->setInstance(false);
                // This is the last case. We're facing a simple attribute waiting for our revers-engine remap to do the job !
                } else {
                    // Just a last control : we're going to remap everything except id, so...
                    ($key === 'id') or $this->_backwardMap($key);
                }
            }
        }

        // Once the whole master Model has been remaped, we can save our stored entities (and - obviously - reset our good'ol Manager too)
        if ($final) {
            $db_result = Manager()->getDbResult();
            if (! empty($db_result)) {
                foreach ($db_result as $entity) {
                    $entity->save();
                }
            }

            $this->_reset();
        }
    }

    /* UTILITY FUNCTIONS */

    /**
     * Flattens a Model instance, returning it as an array of key / value pairs
     * @param  string
     * @return array
     */
    public function toArray($prefix = '')
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
     * @return object
     */
    private function _forwardRemapRow()
    {
        $return = null;

        $db_result = Manager()->getDbResult();

        if (! empty($db_result)) {
            foreach ($db_result as $d) {
                $this->_forwardMap($d);
            }

            $return = clone $this;
        }

        $this->_reset(true);
        return $return;
    }

    /**
     * Fetches back all entities datas and sets Model's corresponding attributes
     * @return array
     */
    private function _forwardRemapList()
    {
        $return = null;

        $db_result = Manager()->getDbResult();

        if (! empty($db_result)) {
            $return = array();

            foreach ($db_result as $d) {
                $instance = new static();
                $instance->_forwardMap($d);

                $return[] = $instance;
            }
        }

        $this->_reset();
        return $return;
    }

    /**
     * Remap processing function (Entity to Model)
     * @param  object
     * @return void
     */
    private function _forwardMap($line = null)
    {
        if (! empty($line)) {
            $map = Manager()->getMap(get_class($this));
            $db_table = str_replace('\\', '.', get_class($line));
            $db_keys = $line->get();

            if (! empty($db_keys)) {
                foreach ($db_keys as $key => $value) {
                    $attr = $db_table.'.'.$key;
                    if (array_key_exists($attr, $map)) {
                        $this->{$map[$attr]} = $value;
                    }
                }
            }
        }
    }

    /**
     * Reverse remap processing function (Model to Entity)
     * @author Gregory CARRODANO <g.carrodano@santiane.fr>
     * @return void
     */
    private function _backwardMap($key = null)
    {
        // First of all, we need that Model map informations (how would you expect to remap something without any information, eh ?)
        // Actually, this is trickier than it looks. We're undergoing a reverse maping process, so let's trully flip it for good !
        $flipped_map = array_flip(Manager()->getMap(get_class($this)));
        // As we don't want to re-instanciate an Entity for each Model attribute, we also need to check for every Entity previously instanciated and stored
        $db_result = Manager()->getDbResult();
        // As usual, we auto-convert that variable (in case it's not an array)
        is_array($db_result) or $db_result = array($db_result);

        // Then we can begin to work
        // First of all, we've got to determine the Entity we're about to work on
        if (isset($flipped_map[$key])) {
            $target = explode('.', $flipped_map[$key]);
            $entity_chain = '\\'.$target[0].'\\'.$target[1].'\\'.$target[2];

            // Then, we'll check if it was already declared before
            if (isset($db_result[$target[2].'_'.$this->id])) {
                // If so, we fetch it back from the Manager, set the new value, and stores it again
                $entity = $db_result[$target[2].'_'.$this->id];
                (! isset($entity->$target[3])) or $entity->$target[3] = $this->$key;
                Manager()->stackDbResult($entity, $target[2].'_'.$this->id);
            } else {
                // If not, we simply instanciate it, set the new value, and stores it for future use
                $entity = new $entity_chain($this->id);
                (! isset($entity->$target[3])) or $entity->$target[3] = $this->$key;
                Manager()->stackDbResult($entity, $target[2].'_'.$this->id);
            }
        }
    }

    /**
     * Utility function (internal use only) : clears Model / Manager variables (called right before returning a newly created instance).
     * @return void
     */
    private function _reset($new_instance = false)
    {
        Manager()->resetDbResult();

        if ($new_instance) {
            $map = Manager()->getMap(get_class($this));

            if (is_array($map)) {
                foreach ($map as $m) {
                    $this->$m = null;
                }
            }
        }
    }
}


/* End of file MY_Model.php */
/* Location: ./application/third_party/model/MY_Model.php */
