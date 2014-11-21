<?php
    require 'XapoCreditAPI.php';

    // Xapo Credit API test
    class CreditExample {  
        public $serviceUrl = "https://api.xapo.com/v1/credit/";
        public $appID;
        public $appSecret;

        function __construct($appID, $appSecret) {
            $this->appID = $appID;
            $this->appSecret = $appSecret;
        } 
    
        public function testCredit() {
            // build the payload
            $payload = new stdClass;
            $payload->to = "sample@xapo.com";
            $payload->currency = "SAT";
            $payload->amount = 1;
            $payload->comments = "This is a sample deposit";
            $payload->timestamp = time();
            $payload->unique_request_id = uniqid();

            // convert to json and encrypt
            $json = json_encode($payload);
            $hash = XapoAPIUtil::encrypt($json, $this->appSecret);
            $payload = array("appID" => $this->appID, "hash" => $hash);

            // call de API
            $result = XapoAPIUtil::callAPI("POST", $this->serviceUrl, $payload);
 
            return $result;
        }
    }

    // Test the example. Replace with your own AppId and Secret
    $creditExample = new CreditExample("your app id", "your app secret");
    $result = $creditExample->testCredit();
    printf("XAPO API Credit call result -> \n%s\n", $result);
?>