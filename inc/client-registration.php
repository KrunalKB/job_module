<?php
if (is_user_logged_in()) {
    esc_html_e("You are already logged in!");
} else {
    ?>
    <form method="post" action="?" class="reg_form" id="regfrm" name="reg_form">
      <div class="container">
        <p><?php echo esc_html('Please fill in this form to create an account.'); ?></p>
        <hr />

        <label><b><?php echo esc_html('Username'); ?></b></label>
        <input
          type="text"
          placeholder="Enter username"
          name="username"
          id="username"
        />
        <label for="username" class="error"></label><br><br>

        <label><b><?php echo esc_html('Email'); ?></b></label>
        <input
          type="email"
          placeholder="Enter Email"
          name="email"
          id="email"
        />
        <label for="email" class="error"></label><br><br>

        <label><b><?php echo esc_html('First name'); ?></b></label>
        <input
          type="text"
          placeholder="Enter first name"
          name="fname"
          id="fname"
        />
        <label for="fname" class="error"></label><br><br>

        <label><b><?php echo esc_html('Last name'); ?></b></label>
        <input
          type="text"
          placeholder="Enter last name"
          name="lname"
          id="lname"
        />
        <label for="lname" class="error"></label><br><br>

        <label><b><?php echo esc_html('Password'); ?></b></label>
        <input
          type="password"
          placeholder="Enter Password"
          name="password"
          id="password"
        />
        <label for="password" class="error"></label><br><br>

        <div class="g-recaptcha" data-sitekey="6Lf1lCIgAAAAAE9vEUKb8sMfF2t4Oagwpz35jaf1"></div><br>
        <hr/>

        <input type="hidden" value="<?php echo get_permalink(); ?>" id="url">

        <button type="submit" class="registerbtn" ><?php echo esc_html('Register'); ?></button>

        <img 
            src="<?php echo plugin_dir_url(__FILE__).'images/load.gif' ?>" 
            class="loader" 
            alt="Loader"
            height=25 
            width=25 
            style="margin-left:10px;"
        >
        <br>
        <div class="msg"></div>
      </div>
    </form>
  <?php
}
?>
