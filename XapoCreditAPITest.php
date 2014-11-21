<?php
  require 'XapoCreditAPI.php';

  // Test the example. Replace with your own AppId and Secret
  $serviceUrl = "https://api.xapo.com/v1";
  $creditAPI = new XapoCreditAPI($serviceUrl, "your app id", "your app secret");

  $to = "sample@xapo.com";
  $currency = "SAT"; // SAT | BTC
  $unique_request_id = uniqid();
  $amount = 1;
  $comments = "This is a sample deposit";

  $result = $creditAPI->credit($to, $currency, $unique_request_id, $amount, $comments);
  printf("XAPO API Credit call result -> \n%s\n", json_encode($result));
?>