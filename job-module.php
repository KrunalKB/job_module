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

        /* Create shortcode for job creation form */
        add_shortcode('job_form', array($this,'jbm_job_form'));

        /* Create shortcode for job list */
        add_shortcode('job_list', array($this,'jbm_job_list'));

        /* Execute ajax hook */
        add_action('wp_ajax_client_hook', array($this,'jbm_register_client'));
        add_action('wp_ajax_contractor_hook', array($this,'jbm_register_contractor'));
        add_action('wp_ajax_jobform_hook', array($this,'jbm_jobform_data'));
        add_action('wp_ajax_search_user_hook', array($this,'jbm_search_user'));

        /* Create custom post type */
        add_action('init', array($this,'jbm_create_cpt'));
    }
    
    public function jbm_search_user()
    {
        global $wpdb;
        if (isset($_POST['search'])) {
            $search = sanitize_text_field($_POST['search']);

            $result = $wpdb->get_var("SELECT distinct(display_name) FROM wp_users WHERE display_name LIKE '%{$search}%' ");
            if (isset($result)) {
                echo $result;
                exit();
            } else {
                echo "No data found!";
                exit();
            }
        }
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
        $total_role      = $wp_roles->get_names();
        $subscriber_role = $wp_roles->get_role('subscriber');

   
        if (!in_array('Client', $total_role)) {
            add_role('client', 'Client', $subscriber_role->capabilities);
        }
        if (!in_array('Contractor', $total_role)) {
            add_role('contractor', 'Contractor', $subscriber_role->capabilities);
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
        wp_enqueue_script(
            'client-js',
            jbm_url . 'assets/js/client_reg.js',
            array('jquery'),
            1.0,
            true
        );
        wp_localize_script(
            'client-js',
            'myVar',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('client-nonce')
            )
        );
        wp_enqueue_style(
            'client-css',
            jbm_url . 'assets/css/client_reg.css'
        );

        require_once jbm_path.'inc/client-registration.php';
    }

    /**
    * Ajax callback for client registration
    *
    * @since 1.0.0
    */
    public function jbm_register_client()
    {
        if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'client-nonce')) {
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $fname    = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
            $lname    = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
            $password = trim($_POST['password']);

            $clientdata = array(
                'user_login' => $username,
                'user_email' => $email,
                'user_pass'  => $password,
                'first_name' => $fname,
                'last_name'  => $lname,
                'role'       => 'client'
            );

            $insert_client = wp_insert_user($clientdata);
        }
    }

    /**
     * Callback function of contractor registration shortcode
     *
     * @since 1.0.0
     */
    public function jbm_contractor_reg()
    {
        wp_enqueue_script(
            'contractor-js',
            jbm_url . 'assets/js/contractor_reg.js',
            array('jquery'),
            1.0,
            true
        );
        wp_localize_script(
            'contractor-js',
            'myVar',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('contractor-nonce')
            )
        );
        wp_enqueue_style(
            'contractor-css',
            jbm_url .'assets/css/contractor_reg.css'
        );

        require_once jbm_path.'inc/contractor-regitration.php';
    }

    /**
    * Ajax callback for contractor registration
    *
    * @since 1.0.0
    */
    public function jbm_register_contractor()
    {
        if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'contractor-nonce')) {
            $username   = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $email      = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $fname      = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
            $lname      = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
            $password   = trim($_POST['password']);
            $buss_name  = filter_var($_POST['buss_name'], FILTER_SANITIZE_STRING);
            $buss_phone = filter_var($_POST['buss_phone'], FILTER_SANITIZE_NUMBER_INT);

            $contractordata = array(
                'user_login' => $username,
                'user_email' => $email,
                'user_pass'  => $password,
                'first_name' => $fname,
                'last_name'  => $lname,
                'role'       => 'contractor'
            );

            $insert_contractor = wp_insert_user($contractordata);

            if ($insert_contractor) {
                $user_data = get_user_by('login', $username);
                add_user_meta($user_data->ID, 'bussiness-name', $buss_name);
                add_user_meta($user_data->ID, 'bussiness-number', $buss_phone);
            }
        }
    }

    /**
     * Callback function of job form shortcode
     *
     * @since 1.0.0
     */
    public function jbm_job_form()
    {
        wp_enqueue_script(
            'jobform-js',
            jbm_url . 'assets/js/job_form.js',
            array('jquery'),
            1.0,
            true
        );
        wp_localize_script(
            'jobform-js',
            'myVar',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('jobform-nonce')
            )
        );
        wp_enqueue_style(
            'job-form',
            jbm_url .'assets/css/job-form.css'
        );
        require_once jbm_path.'inc/job-form.php';
    }

    /**
    * Ajax callback for jobform data
    *
    * @since 1.0.0
    */
    public function jbm_jobform_data()
    {
        if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'jobform-nonce')) {
            $client     = sanitize_text_field($_POST['client']);
            $contractor = sanitize_text_field($_POST['contractor']);
            $jobname    = sanitize_text_field($_POST['jobname']);
            $jobdesc    = sanitize_textarea_field($_POST['jobdesc']);
            $price      = sanitize_text_field($_POST['price']);
            $cpt_args = array(
                'post_title'  => $jobname,
                'post_status' => 'publish',
                'post_type'   => 'job'
            );
            $insert_data = wp_insert_post($cpt_args);
            update_field('client_name', $client, $insert_data);
            update_field('contractor_name', $contractor, $insert_data);
            update_field('job_description', $jobdesc, $insert_data);
            update_field('price', $price, $insert_data);
        }
    }

    /**
    * Callback function of job list shortcode
    *
    * @since 1.0.0
    */
    public function jbm_job_list()
    {
        require_once jbm_path.'inc/job-list.php';
    }

    /**
     * Create custom post type of job
     *
     * @since 1.0.0
     */
    public function jbm_create_cpt()
    {
        $labels = array(
            'name'                  => _x('job', 'Post Type General Name', 'jbm'),
            'singular_name'         => _x('job', 'Post Type Singular Name', 'jbm'),
            'menu_name'             => _x('job', 'Admin Menu text', 'jbm'),
            'name_admin_bar'        => _x('job', 'Add New on Toolbar', 'jbm'),
            'archives'              => __('job Archives', 'jbm'),
            'attributes'            => __('job Attributes', 'jbm'),
            'parent_item_colon'     => __('Parent job:', 'jbm'),
            'all_items'             => __('All job', 'jbm'),
            'add_new_item'          => __('Add New job', 'jbm'),
            'add_new'               => __('Add New', 'jbm'),
            'new_item'              => __('New job', 'jbm'),
            'edit_item'             => __('Edit job', 'jbm'),
            'update_item'           => __('Update job', 'jbm'),
            'view_item'             => __('View job', 'jbm'),
            'view_items'            => __('View job', 'jbm'),
            'search_items'          => __('Search job', 'jbm'),
            'not_found'             => __('Not found', 'jbm'),
            'not_found_in_trash'    => __('Not found in Trash', 'jbm'),
            'featured_image'        => __('Featured Image', 'jbm'),
            'set_featured_image'    => __('Set featured image', 'jbm'),
            'remove_featured_image' => __('Remove featured image', 'jbm'),
            'use_featured_image'    => __('Use as featured image', 'jbm'),
            'insert_into_item'      => __('Insert into job', 'jbm'),
            'uploaded_to_this_item' => __('Uploaded to this job', 'jbm'),
            'items_list'            => __('job list', 'jbm'),
            'items_list_navigation' => __('job list navigation', 'jbm'),
            'filter_items_list'     => __('Filter job list', 'jbm'),
        );
        $args = array(
            'label'               => __('job', 'jbm'),
            'description'         => __('Display job creation form', 'jbm'),
            'labels'              => $labels,
            'menu_icon'           => 'dashicons-editor-table',
            'supports'            => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'comments', 'page-attributes', 'post-formats', 'custom-fields'),
            'taxonomies'          => array(),
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'hierarchical'        => false,
            'exclude_from_search' => false,
            'show_in_rest'        => true,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
        );
        register_post_type('job', $args);
    }
}

$jbm_controller = new jbm_controller();
