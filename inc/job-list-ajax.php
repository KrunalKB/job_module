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
<!-- modal  -->
<div id="delete_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form>
				<div class="modal-header">						
					<h4 class="modal-title">Delete Country</h4>
				
				</div>
				<div class="modal-body">
					<input type="hidden" id="id_d" name="co_id" class="form-control">					
					<p>Are you sure you want to delete these Records?</p>
				
				</div>
				<div class="modal-footer">
					<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
					<button type="button" class="btn btn-danger" id="delete">Delete</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- modal  -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#delete_modal">modal</button>
            <button id="loadMore"><?php echo esc_html('Load More'); ?></button>
            <div id="msg"></div>
        <?php
    }
} else {
    echo esc_html("No data available! You need to signin as a client or contractor.");
}
?>