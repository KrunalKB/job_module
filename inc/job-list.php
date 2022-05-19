<?php
global $current_user;
$role            = $current_user->roles;
$current_author  = $current_user->display_name;
$current_role    = implode($role);
$client_role     = "client";
$contractor_role = "contractor";
    if (is_user_logged_in()) {
        if ($current_role == $client_role) {
            ?>
                <table>
                <tr>
                    <th><?php echo esc_html('Job name'); ?></th>
                    <th><?php echo esc_html('Contractor name'); ?></th>
                    <th><?php echo esc_html('Job description'); ?></th>
                    <th><?php echo esc_html('Image'); ?></th>
                    <th><?php echo esc_html('Price'); ?></th>
                </tr>
            <?php

            $post_query = new WP_Query(array(
                'post_type'      => 'job',
                'posts_per_page' => -1
            ));

            if ($post_query->have_posts()):
                    while ($post_query->have_posts()): $post_query->the_post();
                    
                    
            if (get_field('client_name') == $current_author) {
                ?>
                    <tr class="jobData">
                        <td><?php the_title(); ?></td>
                        <td><?php echo get_the_author_meta('display_name'); ?></td>
                        <td><?php echo get_field('job_description'); ?></td>
                        <td><img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="image" height=150 width=150></td>
                        <td><?php echo get_field('price'); ?></td>
                    </tr>
                <?php
            }
            endwhile;
            endif; 
            ?> 
                </table>
                <button id="loadMore"><?php echo esc_html('Load More'); ?></button>
                <div id="msg"></div> 
            <?php
            wp_reset_query();
        } elseif ($current_role == $contractor_role) {
            ?>
                <table>
                <tr>
                    <th><?php echo esc_html('Job name'); ?></th>
                    <th><?php echo esc_html('Client name'); ?></th>
                    <th><?php echo esc_html('Job description'); ?></th>
                    <th><?php echo esc_html('Image'); ?></th>
                    <th><?php echo esc_html('Price'); ?></th>
                </tr>
            <?php

            $post_query = new WP_Query(array(
                'post_type'      => 'job',
                'posts_per_page' => -1
            ));

            if ($post_query->have_posts()):
                    while ($post_query->have_posts()): $post_query->the_post();
                    
                    
            if (get_field('contractor_name') == $current_author) {
                ?>
                    <tr class="jobData">
                        <td><?php the_title(); ?></td>
                        <td><?php echo get_the_author_meta('display_name'); ?></td>
                        <td><?php echo get_field('job_description'); ?></td>
                        <td><img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="image" height=150 width=150></td>
                        <td><?php echo get_field('price'); ?></td>
                    </tr>
                   
                <?php
            }
            endwhile;
            endif; 
            ?>  
                </table> 
                <button id="loadMore"><?php echo esc_html('Load More'); ?></button>
                <div id="msg"></div> 
            <?php
            wp_reset_query();
        } else {
            echo esc_html("No data available!");
        }
    }
       
?>    
        
    
