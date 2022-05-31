<?php
global $current_user;
$role            = $current_user->roles;
$current_role    = implode($role);
$client_role     = "client";
$contractor_role = "contractor";
if (is_user_logged_in()) {
    if (($current_role == $client_role) || ($current_role == $contractor_role)) {
        ?>
				
				<div class="main-container" id="response">	

				</div>

				<button id="loadMore"><?php echo esc_html('Load More'); ?></button>
				<div id="msg"></div>
        <?php
    }
} else {
    echo esc_html("No data available! You need to signin as a client or contractor.");
}
?>