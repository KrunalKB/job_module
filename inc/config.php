<?php
require('stripe-php-master/init.php');

$publishableKey = "pk_test_51L6AYWSDXkTZVsFZx1VRWyNr4QoG1JqHFqKNLUVoJaeIA1mH3X7PFd9UxPjX72GBXsdTqm9uiWekQok9rM6YIvSc00hLC682HK";
$secretKey = "sk_test_51L6AYWSDXkTZVsFZhBMCwShni5dkStiC61V3XZdLLqZDJjMzKnnBpzE0mDDnT9UWRvpT4VWqH1LGHQStysl7oiCH00Kw6W7Dwv";

\Stripe\Stripe::setApiKey($secretKey);
?>