<?php
if (isset($_GET['key']) && isset($_GET['user'])) {
    $key        = $_GET['key'];
    $user       = $_GET['user'];
    $usr_detail = get_user_by('login', $user);
    $user_id    = $usr_detail->ID;
    $hash_key   = get_user_meta($user_id, 'activate', true);
    $permit     = get_user_meta($user_id, 'permit', true);
    if ($hash_key == $key) {
        if ($permit == 'false') {
            echo "<h4 style='color:red'>Your account has been activated.</h4>";
            update_user_meta($user_id, 'permit', 'true');
        } else {
            echo "<h4 style='color:red'>The url is either invalid or you already have activated your account.</h4>";
        }
    } else {
        echo "<h4 style='color:red'>Invalid approach, please use the link that has been send to your email.</h4>";
    }
}
if (!is_user_logged_in()) {
    ?>
    <form method="post" class="reg_form" id="regfrm">
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
        <hr />

        <input type="hidden" value="<?php echo get_permalink(); ?>" id="url">

        <button type="submit" class="registerbtn" ><?php echo esc_html('Register'); ?></button>
      </div>
    </form>

<?php
} else {
        esc_html_e("You are already logged in!");
    }
?>