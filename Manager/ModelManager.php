<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Model Manager Class : standard CI_Model Class override, which allows to design DB "Manager" classes
 *
 * @class       MY_ModelManager
 * @package     CodeIgniter
 * @category    Core
 * @author      Gregory CARRODANO <g.carrodano@gmail.com>
 * @version     20160407
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
     * Other models dependencies storage. This will allow to control which model can be accessed by another one (thus defining a proper "hierarchy" among all of the application's models)
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
     * Returns new or existing Singleton instance
     * @method get_instance
     * @public
     * @return ModelManager
     */
    public static function get_instance()
    {
        if (static::$_instance !== null) {
            return static::$_instance;
        }

        static::$_instance = new static();

        return static::$_instance;
    }

    /**
     * [get_db_result description]
     * @method get_db_result
     * @public
     * @return array
     */
    public function get_db_result()
    {
        return $this->db_result;
    }

    /**
     * [stack_db_result description]
     * @method stack_db_result
     * @public
     * @param  mixed
     */
    public function stack_db_result($entity)
    {
        if(is_array($entity)) {
            $this->db_result = $entity;
        } else {
            $this->db_result[] = $entity;
        }
    }

    /**
     * [reset_db_result description]
     * @method reset_db_result
     */
    public function reset_db_result()
    {
        $this->db_result = null;
    }

    /**
     * [get_map description]
     * @method get_map
     * @public
     * @param  string
     * @return array
     */
    public function get_map($model = null)
    {
        return ( ! empty($model)) ? $this->map[$model] : $this->map;
    }

    /**
     * [stack_map description]
     * @method stack_map
     * @public
     * @param  string
     * @param  array
     */
    public function stack_map($model, $key, $value)
    {
        $this->_stack_data($this->map, $model, $value, $key);
    }

    /**
     * [get_models description]
     * @method get_models
     * @public
     * @param  string
     * @return array
     */
    public function get_models($model = null)
    {
        return ( ! empty($model)) ? $this->models[$model] : $this->models;
    }

    /**
     * [stack_models description]
     * @method stack_models
     * @public
     * @param  string
     * @param  array
     */
    public function stack_model($model, $value)
    {
        $this->_stack_data($this->models, $model, $value);
    }

    /**
     * [_stack_data description]
     * @method _stack_data
     * @private
     * @param  mixed
     * @param  string
     * @param  mixed
     * @param  string
     */
    private function _stack_data(&$prop, $model, $value, $key = null)
    {
        if ( ! is_null($key)) {
            $prop[$model][$key] = $value;
        } else {
            $prop[$model][] = $value;
        }
    }

}


/* End of file ModelManager.php */
/* Location: ./application/third_party/model/Manager/ModelManager.php */
