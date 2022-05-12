<?php
/**
 * Plugin Name:       Job Module
 * Description:       This is job module for client and contractor.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Author:            Krunal Bhimajiyani
 * Author URI:        https://github.com/KrunalKB
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    // Exit if accessed directly.

    exit;
}

/**
 *  Class jbm_controller
 */

class jbm_controller
{
    /**
     * Construct function
     */

    public function __construct()
    {
        if (!defined('jbm_url')) {
            // Deprecated constants
            define('jbm_url', plugin_dir_url(__FILE__));
            define('jbm_path', plugin_dir_path(__FILE__));
            define('jbm_file', __FILE__);
        }
        /* Activation hook fires when plugin activate */
        register_activation_hook(jbm_file, array($this,'jbm_activate'));

        /* Deactivate hook fires when plugin deactivate */
        register_deactivation_hook(jbm_file, array($this,'jbm_deactivate'));

        /* Create shortcode for client registration form */
        add_shortcode('client_reg', array($this,'jbm_client_reg'));

        /* Create shortcode for contractor registration form */
        add_shortcode('contractor_reg', array($this,'jbm_contractor_reg'));
    }

    /**
     * Callback function of activation hook
     *
     * Register two roles of 'Client' and 'Contractor' when plugin activate.
     *
     * @since 1.0.0
     */

    public function jbm_activate()
    {
        global $wp_roles;
        $total_role = $wp_roles->get_names();
        $admin_role = $wp_roles->get_role('subscriber');
        
        if (!in_array('Client', $total_role)) {
            add_role('client', 'Client', $admin_role->capabilities);
        }
        if (!in_array('Contractor', $total_role)) {
            add_role('contractor', 'Contractor', $admin_role->capabilities);
        }
    }

    /**
     * Callback function of deactivation hook
     *
     * Remove roles of 'Client' and  'Contractor' when plugin deactivate.
     *
     * @since 1.0.0
     */
    public function jbm_deactivate()
    {
        global $wp_roles;
        $total_role = $wp_roles->get_names();

        if (in_array('Client', $total_role)) {
            remove_role('client');
        }
        if (in_array('Contractor', $total_role)) {
            remove_role('contractor');
        }
    }

    /**
     * Callback function of client registration shortcode
     *
     * @since 1.0.0
     */
    public function jbm_client_reg()
    {
        require_once jbm_path.'inc/client-registration.php';
    }

    /**
     * Callback function of contractor registration shortcode
     *
     * @since 1.0.0
     */
    public function jbm_contractor_reg()
    {
        require_once jbm_path.'inc/contractor-regitration.php';
    }
}

$jbm_controller = new jbm_controller();
