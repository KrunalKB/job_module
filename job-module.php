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

        /* Create shortcode for user registration */
        add_shortcode('user-registration', array($this,'jbm_user_registration'));

        /* Create shortcode for job creation form */
        add_shortcode('job_form', array($this,'jbm_job_form'));

        /* Create shortcode for job list */
        add_shortcode('job_list', array($this,'jbm_job_list'));

        /* Execute ajax hook */
        add_action('wp_ajax_user_hook', array($this,'jbm_register_user'));
        add_action('wp_ajax_nopriv_user_hook', array($this,'jbm_register_user'));
        add_action('wp_ajax_jobform_hook', array($this,'jbm_jobform_data'));
        add_action('wp_ajax_search_client_hook', array($this,'jbm_search_client'));
        add_action('wp_ajax_search_contractor_hook', array($this,'jbm_search_contractor'));
        add_action('wp_ajax_job_listing_hook', array($this,'jbm_job_listing'));
        add_action('wp_ajax_job_status', array($this,'jbm_job_status'));

        /* Create custom post type */
        add_action('init', array($this,'jbm_create_cpt'));

        /* Filter hook to set mail content type */
        add_filter('wp_mail_content_type', array($this,'jbm_mail_content_type'));

        /* Action hook to verify activation link */
        add_action('wp', array($this,'jbm_verify_link'));

        /* Filter hook to authenticate user */
        add_filter('wp_authenticate_user', array($this,'jbm_authenticate_user'));
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
            add_role('client', __('Client'), $subscriber_role->capabilities);
        }
        if (!in_array('Contractor', $total_role)) {
            add_role('contractor', __('Contractor'), $subscriber_role->capabilities);
        }

        global $wpdb;
        $db_table_name = $wpdb->prefix . 'job_notification';  // table name
        if ($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
            $sql = "CREATE TABLE " . $db_table_name . " (
                    id int(11) NOT NULL auto_increment,
                    client_id varchar(15) NOT NULL,
                    contractor_id varchar(15) NOT NULL,
                    job_id varchar(15) NOT NULL,
                    job_status varchar(15) NOT NULL,
                    notification_text varchar(200) NOT NULL,
                    notification_status varchar(60) NOT NULL,
                    PRIMARY KEY (id) 
                )";
        }
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql); // create query
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
     * Set mail content type
     *
     * @since 1.0.0
     */
    public function jbm_mail_content_type()
    {
        return 'text/html';
    }

    /**
     * Shortcode for user registration
     *
     * @since 1.0.0
     */
    public function jbm_user_registration($params, $content)
    {
        wp_enqueue_script('jquery-cdn', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', 1.0, true);
        wp_enqueue_script('jquery-validation', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js', 1.0, true);
        wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', 1.0, true);

        wp_enqueue_script(
            'userReg-js',
            jbm_url . 'assets/js/user_registration.js',
            array('jquery'),
            1.0,
            true
        );

        wp_localize_script(
            'userReg-js',
            'myLink',
            array(
                'ajax_link' => admin_url('admin-ajax.php'),
                'nonce'     => wp_create_nonce('user-registration-nonce')
            )
        );

        wp_enqueue_style(
            'user-css',
            jbm_url . 'assets/css/user_registration.css'
        );

        $values = shortcode_atts(
            array(
                "user" => ""
            ),
            $params,
            'user-registration'
        );
        if ($values['user'] == 'client') {
            require_once jbm_path.'inc/client-registration.php';
        } elseif ($values['user'] == 'contractor') {
            require_once jbm_path.'inc/contractor-regitration.php';
        } else {
            esc_html_e("Please enter user role!");
        }
    }

    /**
     * Ajax callback for user registration
     *
     * @since 1.0.0
     */
    public function jbm_register_user()
    {
        if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'user-registration-nonce')) {
            $secret_key   = '6Lf1lCIgAAAAADLiOoq_oVg1WXi9OnK-_7xjB62h';
            $response_key = $_POST['g-recaptcha-response'];
            $url          = "https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$response_key";
            $responseData = file_get_contents($url);
            $response     = json_decode($responseData);
            if ($response->success) {
                $username = sanitize_text_field($_POST['username']);
                $email    = sanitize_email($_POST['email']);
                $fname    = sanitize_text_field($_POST['fname']);
                $lname    = sanitize_text_field($_POST['lname']);
                $password = trim($_POST['password']);
                $hash_key = rand(0, 1000);

                if (isset($_POST['buss_name']) && isset($_POST['buss_phone'])) {
                    $buss_name  = sanitize_text_field($_POST['buss_name']);
                    $buss_phone = sanitize_text_field($_POST['buss_phone']);
                    $role       = "contractor";
                } else {
                    $role = "client";
                }

                $userdata = array(
                    'user_login' => $username,
                    'user_email' => $email,
                    'user_pass'  => $password,
                    'first_name' => $fname,
                    'last_name'  => $lname,
                    'role'       => $role
                );

                $insert_user = wp_insert_user($userdata);
                if ($role == "contractor") {
                    $user_data = get_user_by('login', $username);
                    add_user_meta($user_data->ID, 'bussiness-name', $buss_name);
                    add_user_meta($user_data->ID, 'bussiness-number', $buss_phone);
                }
                if (!is_wp_error($insert_user)) {
                    $activation_link = esc_url(home_url('/').'?user='.$username.'&key='.$hash_key);
                    add_user_meta($insert_user, 'activate', $hash_key);
                    add_user_meta($insert_user, 'permit', 'false');
                    $subject = 'Signup Verification';
                    $message = 'Hello &nbsp;'.$username.'<br/>
                            Please click on the following link or copy the link and paste it to your browser to activate your profile and Sign in. <br/>'.$activation_link;
                    $headers = 'From: noreply@gmail.com' . "\r\n";
                    wp_mail($email, $subject, $message, $headers);
                }
                // echo "Registration completed successfully!";
                echo "Account activation link has been sent to your email. Please verify and signin!";
                exit();
            } else {
                echo "Invalid captcha, Please try again!";
                exit();
            }
        }
    }

    /**
     * For email verification
     */
    public function jbm_verify_link()
    {
        if (isset($_GET['user']) && isset($_GET['key'])) {
            $key        = $_GET['key'];
            $user       = $_GET['user'];
            $usr_detail = get_user_by('login', $user);
            $user_id    = $usr_detail->ID;
            $hash_key   = get_user_meta($user_id, 'activate', true);
            $permit     = get_user_meta($user_id, 'permit', true);
            if ($hash_key == $key) {
                if ($permit == 'false') {
                    update_user_meta($user_id, 'permit', 'true');
                    echo "<h4 style='color:green'>Hurray! Your account has been activated.</h4>";
                } else {
                    echo "<h4 style='color:red'>The url is either invalid or you already have activated your account.</h4>";
                }
            } else {
                echo "<h4 style='color:red'>Invalid approach, please use the link that has been send to your email.</h4>";
            }
        }
    }

    /**
     * Authenticate user
     *
     * @param WP_User $user
     * @return void
     *
     * @since 1.0.0
     */
    public function jbm_authenticate_user(WP_User $user)
    {
        $activation_status = get_user_meta($user->ID, 'permit', true);
        if ($activation_status) {
            if ($activation_status == 'false') {
                $message = esc_html__('User not verified.Please click the link in the activation email that has been sent to you.', 'jbm');
                return new WP_Error('user_not_verified', $message);
            }
        }
        return $user;
    }

    /**
     * Callback function of job form shortcode
     *
     * @since 1.0.0
     */
    public function jbm_job_form()
    {
        wp_enqueue_script('jqueryCDN', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', 1.0, true);
        wp_enqueue_script('jqueryValidation', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js', 1.0, true);

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
            update_field('job_status', 'requested', $insert_data);
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

        require_once jbm_path.'inc/job-list-ajax.php';
    }

    /**
     * Ajax callback function for job list
     *
     * @since 1.0.0
     */
    public function jbm_job_listing()
    {
        global $current_user;
        global $post;
        $role            = $current_user->roles;
        $current_role    = implode($role);
        $current_user_id = $current_user->ID;
        $offset          = $_POST['offset'];
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
            while ($post_query -> have_posts()): $post_query -> the_post();

        $jobStatus = get_field('job_status');
        if ($jobStatus == 'approved') {
            $btnStatus = 'disabled';
            $btnColor  = '#008000';
        } elseif ($jobStatus == 'rejected') {
            $btnStatus = 'disabled';
            $btnColor  = '#FF0000';
        } else {
            $btnStatus = '';
            $btnColor  = '#008cff';
        } ?>
                <div id="box">
                    <div class="image">
                        <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="image" height=150 width=150>
                    </div>
                    <p><b><?php echo esc_html('Job name:'); ?></b> <?php the_title(); ?> </p>
                    <p><b><?php echo esc_html('User name:'); ?></b> <?php echo get_the_author_meta('display_name'); ?></p>
                    <p><b><?php echo esc_html('Job description:'); ?></b> <?php echo get_field('job_description'); ?></p>
                    <p><b><?php echo esc_html('Price:'); ?></b> <?php echo get_field('price'); ?> Rs.</p>
                    <?php
                    if ($current_role == 'contractor') {
                        ?>
                        <p><b><?php echo esc_html('Status:'); ?></b> <input type="submit" value="<?php echo get_field('job_status'); ?>" id="<?php echo $post->ID; ?>" style="background-color:<?php echo $btnColor; ?>" <?php echo $btnStatus; ?>></p>
                        <?php
                    } ?>
                </div>
            <?php
            
        endwhile;
        endif;
        wp_reset_query();
        exit();
    }

    /**
     * To update job status and insert data into custom table
     *
     * @since 1.0.0
     */
    public function jbm_job_status()
    {
        if (isset($_POST['updateVal'])) {
            $updateVal = $_POST['updateVal'];
            $jobID     = $_POST['elementId'];
            update_field('job_status', $updateVal, $jobID);
        }
        global $wpdb;
        $client_id     = get_field('author_id', $jobID);
        $contractor_id = get_field('user_id', $jobID);
        $user          = get_user_by('ID', $contractor_id);
        $user_name     = $user->display_name;
        $job_status    = get_field('job_status', $jobID);
        $job_title     = get_the_title($jobID);

        $notification_text   = "Job name $job_title is $job_status by $user_name";
        $notification_status = 0;
        
        $table = $wpdb->prefix.'job_notification';
        $data = array(
            'client_id'           => $client_id,
            'contractor_id'       => $contractor_id,
            'job_id'              => $jobID,
            'job_status'          => $job_status,
            'notification_text'   => $notification_text,
            'notification_status' => $notification_status
        );
        $wpdb->insert($table, $data);
    }

    /**
     * Create custom post type "job"
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
