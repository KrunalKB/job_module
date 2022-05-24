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
        add_action('wp_ajax_search_client_hook', array($this,'jbm_search_client'));
        add_action('wp_ajax_search_contractor_hook', array($this,'jbm_search_contractor'));
        add_action('wp_ajax_job_listing_hook', array($this,'jbm_job_listing'));
        // add_action('wp_ajax_client_verify', array($this,'jbm_client_verify'));

        /* Create custom post type */
        add_action('init', array($this,'jbm_create_cpt'));

        /* Set mail content type */
        add_filter('wp_mail_content_type', array($this,'jbm_mail_content_type'));

        // add_filter('wp_authenticate_user', array($this,'jbm_check_user_activation', 10, 2));

        // add_filter('query_vars', array($this,'add_query_vars_filter'));

        // add_filter('authenticate', array($this,'jbm_users'), 10, 3);
    }

    
    // public function jbm_check_user_activation($user)
    // {
    //     // if (get_user_meta($user->ID, 'wp_user_level', true) != 10) {
    //         if (get_user_meta($user->ID, 'permit', true) == 'true') {
    //             return $user;
    //         }
    //     // } 
    //     else {
    //         return new WP_Error('Account Not Active...');
    //     }
    // }
    // public function add_query_vars_filter($vars)
    // {
    //     $vars[] = "key";
    //     $vars[] = "user";
    //     // return $vars;
    //     // update_option('verification',$vars);

    // }
      
    // public function jbm_client_verify()
    // {
    //     $abc = get_query_var('email');
    //     echo $abc;
    // }
      
    // public function jbm_users($user, $username, $password)
    // {
    //     $user_obj = get_user_by('login', $username);

    //     if ($username!='') {
    //         // $value = update_user_meta($user->ID, 'activate', true);
    //         if (get_user_meta( $user->ID, 'activate' ) == 'false' ) {
    //             $user = new WP_Error('denied', __("<strong>ERROR</strong>: You need to activate your account.".$value.""));
    //             remove_action('authenticate', 'wp_authenticate_username_password', 20);
    //         }
    //     }
    //     return $user;
    // }


    /**
     * Set mail content type
     *
     * @since 1.0.0
     */
    public function jbm_mail_content_type()
    {
        return 'text/html';
    }

    /**
     * Ajax callback function for job list
     *
     * @since 1.0.0
     */
    public function jbm_job_listing()
    {
        global $current_user;
        $current_user_id   = $current_user->ID;
        $offset            = $_POST['offset'];
        $post_query = new WP_Query(array(
            'post_type'      => 'job',
            'posts_per_page' => 2,
            'order'          => 'ASC',
            'offset'         => $offset,
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => 'user_id',
                    'value'   => $current_user_id,
                    'compare' => 'LIKE'
                ),
                array(
                    'key'     => 'user_id',
                    'value'   => get_field('user_id'),
                    'compare' => 'LIKE'
                )
            )
        ));

        if ($post_query -> have_posts()):
            while ($post_query -> have_posts()): $post_query -> the_post(); ?>
                <div id="box">
                    <div class="image">
                        <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="image" height=150 width=150>
                    </div>
                    <p><b><?php echo esc_html('Job name:'); ?></b> <?php the_title(); ?> </p>
                    <p><b><?php echo esc_html('User name:'); ?></b> <?php echo get_the_author_meta('display_name'); ?></p>
                    <p><b><?php echo esc_html('Job description:'); ?></b> <?php echo get_field('job_description'); ?></p>
                    <p><b><?php echo esc_html('Price:'); ?></b> <?php echo get_field('price'); ?> Rs.</p>
                </div>
            <?php
            
        endwhile;
        endif;
        wp_reset_query();
        exit();
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
            $url      = filter_var($_POST['url'], FILTER_SANITIZE_STRING);
            $password = trim($_POST['password']);
            $hash_key = md5(rand(0, 100));

            $clientdata = array(
                'user_login' => $username,
                'user_email' => $email,
                'user_pass'  => $password,
                'first_name' => $fname,
                'last_name'  => $lname,
                'role'       => 'client'
            );

            $insert_client = wp_insert_user($clientdata);
            if (!is_wp_error($insert_client)) {
                // $activation_link = esc_url(home_url('/').'?email='.$email.'&hash='.$hash_key);
                $activation_link = add_query_arg(array( 'key' => $hash_key, 'user' => $username ), $url);
                add_user_meta($insert_client, 'activate', $hash_key);
                add_user_meta($insert_client, 'permit', 'false');
                $subject = 'Signup Verification';
                $message = 'Hello &nbsp;'.$username.'<br/>
                            Please click on the following link or copy the link and paste it to your browser to activate your profile and Sign in. <br/>'.$activation_link;
                $headers = 'From: noreply@gmail.com' . "\r\n";
                wp_mail($email, $subject, $message, $headers);
            }
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
     * Getting all registered client
     *
     * @since 1.0.0
     */
    public function jbm_search_client()
    {
        if (isset($_POST['search'])) {
            $membersArray = get_users('role=client');
            $output = "<ol>";
            foreach ($membersArray as $user) {
                $output .= '<li>' . esc_html($user->display_name)  . '</li>';
            }
            $output .="</ol>";
            echo $output;
            exit();
        }
    }

    /**
     * Getting all registered contractor
     *
     * @since 1.0.0
     */
    public function jbm_search_contractor()
    {
        if (isset($_POST['search'])) {
            $membersArray = get_users('role=contractor');
            $output = "<ol>";
            foreach ($membersArray as $user) {
                $output .= '<li>' . esc_html($user->display_name) . '</li>';
            }
            $output .="</ol>";
            echo $output;
            exit();
        }
    }

    /**
    * Ajax callback for jobform data
    *
    * @since 1.0.0
    */
    public function jbm_jobform_data()
    {
        if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'jobform-nonce')) {
            if (!function_exists('wp_generate_attachment_metadata')) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');
            }
            
            $client     = sanitize_text_field($_POST['client']);
            $contractor = sanitize_text_field($_POST['contractor']);
            $jobname    = sanitize_text_field($_POST['jobname']);
            $jobdesc    = sanitize_textarea_field($_POST['jobdesc']);
            $price      = sanitize_text_field($_POST['price']);
            $post_author= sanitize_text_field($_POST['post_author']);

            if (!empty($client)) {
                $get_user = get_users(array('search' =>  $client));
            } else {
                $get_user = get_users(array('search' =>  $contractor));
            }
            foreach ($get_user as $user) {
                $user_id = esc_html($user->ID);
            }
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
            update_field('author_id', $post_author, $insert_data);
            update_field('user_id', $user_id, $insert_data);
            $attachment_id = media_handle_upload('image', $insert_data);
            if ($attachment_id > 0) {
                update_post_meta($insert_data, '_thumbnail_id', $attachment_id);
            }
        }
    }

    /**
    * Callback function of job list shortcode
    *
    * @since 1.0.0
    */
    public function jbm_job_list()
    {
        wp_enqueue_script(
            'joblist-js',
            jbm_url.'assets/js/job_list.js',
            array('jquery'),
            1.0,
            true
        );
        wp_localize_script(
            'joblist-js',
            'myVar',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('joblisting-nonce')
            )
        );
        wp_enqueue_style(
            'joblist-css',
            jbm_url.'assets/css/job_list.css'
        );
        // require_once jbm_path.'inc/job-list.php';
        // require_once jbm_path.'inc/job-listing.php';
        require_once jbm_path.'inc/job-list-ajax.php';
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
