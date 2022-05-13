<?php

global $current_user;
$role         = $current_user->roles;
$current_role = implode($role);
$client_role = "client";
$contractor_role = "contractor";

?>
    <form method="post" class="reg_form">
    <div class="container">
    <?php
        if ($current_role == $contractor_role) {
            ?>
            <label for="client"><b><?php echo esc_html('Select client'); ?></b></label>
            <input
            type="text"
            placeholder="Select client"
            name="client"
            id="client"
            required
            />
            <?php
        }
        if ($current_role == $client_role) {
            ?>  
            <label for="contractor"><b><?php echo esc_html('Select contractor'); ?></b></label>
            <input
            type="text"
            placeholder="Select contractor"
            name="contractor"
            id="contractor"
            required
            />
            <?php
        }
    ?>
        
        <label for="jobname"><b><?php echo esc_html('Job name'); ?></b></label>
        <input
          type="text"
          placeholder="Enter job name"
          name="jobname"
          id="jobname"
          required
        />

        <label for="jobdesc"><b><?php echo esc_html('Job description'); ?></b></label>
        <textarea 
            name="jobdesc" 
            id="jobdesc" 
            cols="30" 
            rows="3">
        </textarea>

        <label for="image"><b><?php echo esc_html('Select image'); ?></b></label>
        <input
          type="file"
          name="image"
          id="image"
          required
        />

        <label for="price"><b><?php echo esc_html('Price'); ?></b></label>
        <input
          type="number"
          placeholder="Enter price"
          name="price"
          id="price"
          required  
        />

        <button type="submit" class="cl_registerbtn" ><?php echo esc_html('Submit'); ?></button>
      </div>
    </form>
<?php
?>