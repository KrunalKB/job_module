<?php

global $current_user;
$role            = $current_user->roles;
$current_role    = implode($role);
$current_user_id = $current_user->ID;
$client_role     = "client";
$contractor_role = "contractor";
if (is_user_logged_in() && (($current_role == $client_role)||($current_role == $contractor_role))) {
    ?>
    <form method="post" class="reg_form" id="regfrm" name="reg_form">
    <div class="container">
    <?php
        if ($current_role == $contractor_role) {
            ?>
            <label><b><?php echo esc_html('Select client'); ?></b></label>
            <input
            type="text"
            placeholder="Select client"
            name="client"
            id="client"
            />
            <label for="client" class="error"></label><br>
            <?php
        }
        
    if ($current_role == $client_role) {
        ?>  
              <label><b><?php echo esc_html('Select contractor'); ?></b></label>
              <input
              type="text"
              placeholder="Select contractor"
              name="contractor"
              id="contractor"
              />
              <label for="contractor" class="error"></label><br>
        <?php
    } ?>
        <table id="searchtable"><tr><td></td></tr></table>
        
        <label><b><?php echo esc_html('Job name'); ?></b></label>
        <input
          type="text"
          placeholder="Enter job name"
          name="jobname"
          id="jobname"
        />
        <label for="jobname" class="error"></label><br>

        <label><b><?php echo esc_html('Job description'); ?></b></label>
        <textarea 
            name="jobdesc" 
            id="jobdesc" 
            cols="30" 
            rows="3">
        </textarea>
        <label for="jobdesc" class="error"></label><br>

        <label><b><?php echo esc_html('Select image'); ?></b></label>
        <input
          type="file"
          name="image"
          id="image"
        />
        <label for="image" class="error"></label><br>

        <label><b><?php echo esc_html('Price'); ?></b></label>
        <input
          type="number"
          placeholder="Enter price"
          name="price"
          id="price"
        />
        <label for="price" class="error"></label><br><br>

        <input 
          type="hidden"
          name="post_author"
          id="post_author"
          value=<?php echo $current_user_id; ?>
        >

        <button type="submit" class="job_registerbtn" ><?php echo esc_html('Submit'); ?></button>
        <img 
            src="<?php echo plugin_dir_url(__FILE__).'images/load.gif' ?>" 
            class="loader" 
            alt="Loader"
            height=25 
            width=25 
            style="margin-left:10px;"
        >
        <div class="msg"></div>
      </div>
    </form>
  <?php
} else {
        echo esc_html("No data available! You need to signin as a client or contractor to create job.");
    }
?>