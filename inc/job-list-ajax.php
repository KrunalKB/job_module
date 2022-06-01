<?php
global $current_user;
$role            = $current_user->roles;
$current_user_id = $current_user->ID;
$current_role    = implode($role);
$client_role     = "client";
$contractor_role = "contractor";

// Notificaton popup
global $wpdb;
$table = $wpdb->prefix.'job_notification';
$result = $wpdb->get_results("SELECT * FROM $table WHERE client_id = $current_user_id");
$html  = '';
foreach ($result as $row) {
    $notification_text = $row->notification_text;
    $html .= sprintf('<div class="alert">');
    $html .= sprintf('<span class="closebtn">&times;</span>');
    $html .= sprintf('<strong>Hello!</strong> %s', $notification_text);
    $html .= sprintf('</div><br>');
}
esc_html_e($html);
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