<?php
?>
    <form method="post" class="reg_form">
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

        <button type="submit" class="cl_registerbtn" ><?php echo esc_html('Register'); ?></button>
      </div>
    </form>

<?php
?>