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

?>
    <form method="post" class="reg_form" id="regfrm">
      <div class="container">
        <p><?php echo esc_html('Please fill in this form to create an account.'); ?></p>
        <hr />

        <label for="username"><b><?php echo esc_html('Username'); ?></b></label>
        <input
          type="text"
          placeholder="Enter username"
          name="username"
          id="username"
          required
        />

        <label for="email"><b><?php echo esc_html('Email'); ?></b></label>
        <input
          type="email"
          placeholder="Enter Email"
          name="email"
          id="email"
          required
        />

        <label for="fname"><b><?php echo esc_html('First name'); ?></b></label>
        <input
          type="text"
          placeholder="Enter first name"
          name="fname"
          id="fname"
          required
        />

        <label for="lname"><b><?php echo esc_html('Last name'); ?></b></label>
        <input
          type="text"
          placeholder="Enter last name"
          name="lname"
          id="lname"
          required
        />

        <label for="password"><b><?php echo esc_html('Password'); ?></b></label>
        <input
          type="password"
          placeholder="Enter Password"
          name="password"
          id="password"
          required
        />
        <hr />

        <input type="hidden" value="<?php echo get_permalink(); ?>" id="url">

        <button type="submit" class="cl_registerbtn" ><?php echo esc_html('Register'); ?></button>
      </div>
    </form>

<?php
?>