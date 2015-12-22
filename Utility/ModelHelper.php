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
 * Utility function (internal use only) : über fast CI instance getter
 * @method CI
 * @return CI_Controller
 */
function CI()
{
    static $CI;
    isset($CI) || $CI = CI_Controller::get_instance();

    return $CI;
}

/**
 * Utility function (internal use only) : Manager instance getter
 * @method Manager
 * @return ModelManager
 */
function Manager()
{
    static $manager;
    isset($manager) || $manager = ModelManager::get_instance();

    return $manager;
}


/* End of file ModelHelper.php */
/* Location: ./application/core/ModelHelper.php */
