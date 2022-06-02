<?php
require('config.php');
?>
<form action="submit.php" method="post">
<script
    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
    data-key = "<?php echo $publishableKey ?>"
    data-amount = "500"
    data-name = "Job Module"
    data-description = "Payment for job"
    data-image = "https://d1nhio0ox7pgb.cloudfront.net/_img/g_collection_png/standard/512x512/wallet.png"
    data-currency = "inr"
>

</script>
</form>