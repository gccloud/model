<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Model Manager Class : standard CI_Model Class override, which allows to design DB "Manager" classes
 *
 * @class       MY_ModelManager
 * @package     CodeIgniter
 * @category    Core
 * @author      Gregory CARRODANO <g.carrodano@gmail.com>
 * @version     20160415
 * @used-by     ./model/MY_Model.php
 */
final class ModelManager
{
    /* CLASS ATTRIBUTES */
    /**
     * DB result storage. Actually, this array will serve to temporarlly store Origami Entities before mapping it to a new model Instance.
     * @var array
     * @private
     */
    private $db_result = null;
    /**
     * Model <-> Entities attributes (datas) auto-remap. That's the core of our Manager task : each time a new Model Instance is created, we loop through that Model map to assign each stored Entity data to the corresponding Model attribute
     * @var array
     * @private
     */
    private $map = array();
    /**
     * Other Models dependencies storage. This will allow to control which Model can be accessed by another one (thus defining a proper "hierarchy" among all of the application's Models)
     * @var array
     * @private
     */
    private $models = array();
    /**
     * Singleton instance
     * @var ModelManager
     * @private
     */
    private static $_instance = null;

    /**
     * Class constructor override : prevent direct object creation
     * @method __construct
     * @private
     */
    private function  __construct() { }

    /**
     * Class cloning override : prevent object cloning
     * @method __clone
     * @private
     */
    private function  __clone() { }

    /* MAIN FUNCTIONS */

    /**
     * Returns a new or existing Singleton instance
     * @method getInstance
     * @public
     * @return ModelManager
     */
    public static function getInstance()
    {
        if (static::$_instance !== null) {
            return static::$_instance;
        }

        static::$_instance = new static();

        return static::$_instance;
    }

    /**
     * Gets back any Entity stored in the Manager
     * @method getDbResult
     * @public
     * @return array
     */
    public function getDbResult()
    {
        return $this->db_result;
    }

    /**
     * Registers - if any - some new Entities before running the Model remap process
     * @method stackDbResult
     * @public
     * @param  mixed
     * @param  string
     */
    public function stackDbResult($entity, $extra_key = null)
    {
        if (is_array($entity)) {
            $this->db_result = $entity;
        } else {
            if (! empty($extra_key)) {
                $this->db_result[$extra_key] = $entity;
            } else {
                $this->db_result[] = $entity;
            }
        }
    }

    /**
     * Resets any Entity result previously stored here (though this function is public, it should usually only be called internally, and after ending a Model auto-remap process)
     * @method resetDbResult
     */
    public function resetDbResult()
    {
        $this->db_result = null;
    }

    /**
     * Fetches back some Model mapping previously stored on the Manager (wether to return the whole map, or just for one Model, is determined by the optional parameter passed when calling this function)
     * @method getMap
     * @public
     * @param  string
     * @return array
     */
    public function getMap($model = null)
    {
        return (! empty($model)) ? $this->map[$model] : $this->map;
    }

    /**
     * Registers a new Model mapping (i.e a new list of Model <-> Entities attributes matching)
     * @method stackMap
     * @public
     * @param  string
     * @param  array
     */
    public function stackMap($model, $key, $value)
    {
        $this->_stackData($this->map, $model, $value, $key);
    }

    /**
     * Fetches back some Model hierarchy previously stored on the Manager (wether to return the whole map, or just for one Model, is determined by the optional parameter passed when calling this function)
     * @method getModels
     * @public
     * @param  string
     * @return array
     */
    public function getModels($model = null)
    {
        return (! empty($model)) ? $this->models[$model] : $this->models;
    }

    /**
     * Registers a new Model dependency (i.e a new "sub-Model" called by another "Master" one)
     * @method stackModel
     * @public
     * @param  string
     * @param  array
     */
    public function stackModel($model, $value)
    {
        $explode = explode('/', $value);

        $this->_stackData($this->models, $model, end($explode));
    }

    /**
     * Generic Manager data storage function : will be called internally to store some utility datas for further use
     * @method _stackData
     * @private
     * @param  mixed
     * @param  string
     * @param  mixed
     * @param  string
     */
    private function _stackData(&$prop, $model, $value, $key = null)
    {
        if (! is_null($key)) {
            $prop[$model][$key] = $value;
        } else {
            $prop[$model][] = $value;
        }
    }

}


/* End of file ModelManager.php */
/* Location: ./application/third_party/model/Manager/ModelManager.php */
