<?php
global $current_user;
$role            = $current_user->roles;
$current_author  = $current_user->display_name;
$current_role    = implode($role);
$client_role     = "client";
$contractor_role = "contractor";
if (is_user_logged_in()) {
    if ($current_role == $client_role) {
        ?><div class="main-container"> <?php
        
        $post_query = new WP_Query(array(
            'post_type'      => 'job',
            'posts_per_page' => -1
        ));
        
        if ($post_query->have_posts()):
                    while ($post_query->have_posts()):$post_query->the_post();
                    
        if (get_field('client_name') == $current_author) {
            ?>
                <div id="box">
                    <div class="image">
                        <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="image" height=150 width=150>
                    </div>
                    <p><b><?php echo esc_html('Job name:'); ?></b> <?php the_title(); ?> </p>
                    <p><b><?php echo esc_html('Contractor name:'); ?></b> <?php echo get_the_author_meta('display_name'); ?></p>
                    <p><b><?php echo esc_html('Job description:'); ?></b> <?php echo get_field('job_description'); ?></p>
                    <p><b><?php echo esc_html('Price:'); ?></b> <?php echo get_field('price'); ?> Rs.</p>
                </div>
            <?php
        }
        endwhile;
        endif;
        wp_reset_query(); ?> 
            </div>
            <button id="loadMore"><?php echo esc_html('Load More'); ?></button>
            <div id="msg"></div> 
        <?php
    } elseif ($current_role == $contractor_role) {
        ?><div class="main-container"> <?php
        
        $post_query = new WP_Query(array(
            'post_type'      => 'job',
            'posts_per_page' => -1
        ));
        
        if ($post_query->have_posts()):
                    while ($post_query->have_posts()):$post_query->the_post();
                    
        if (get_field('contractor_name') == $current_author) {
            ?>
                <div id="box" class="jobData">
                    <div class="image">
                        <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="image" height=150 width=150>
                    </div>
                    <p><b><?php echo esc_html('Job name:'); ?></b> <?php the_title(); ?> </p>
                    <p><b><?php echo esc_html('Client name:'); ?></b> <?php echo get_the_author_meta('display_name'); ?></p>
                    <p><b><?php echo esc_html('Job description:'); ?></b> <?php echo get_field('job_description'); ?></p>
                    <p><b><?php echo esc_html('Price:'); ?></b> <?php echo get_field('price'); ?> Rs.</p>
                </div>
            <?php
        }
        endwhile;
        endif;
        wp_reset_query(); ?> 
            </div>
            <button id="loadMore"><?php echo esc_html('Load More'); ?></button>
            <div id="msg"></div> 
        <?php
    } else {
        echo esc_html("No data available!");
    }
}

?>