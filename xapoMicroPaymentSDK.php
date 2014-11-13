<?php
    
  class XapoMicroPaymentSDK
  {
    
    static $serviceUrl;
    static $appID;
    static $appSecret;
   
    static public function setEnvironmentUrl($url)
    {
      XapoMicroPaymentSDK::$serviceUrl = $url;
    }
  
    static public function setApplication($appID, $secret)
    {
      XapoMicroPaymentSDK::$appID = $appID;
      XapoMicroPaymentSDK::$appSecret = $secret;
    }
    
    static private function encrypt($data)
    {
      //pkcs7 padding
      $block = 16;
      $pad = $block - (strlen($data) % $block);
      $data .= str_repeat(chr($pad), $pad);
      
      $key = XapoMicroPaymentSDK::$appSecret;
      
      $enc = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, 'ecb'));
      
      return $enc;
    }
    
    static private function buildWidgetUrl($request)
    {
      $buttonRequestObj = new stdClass;
      $buttonRequestObj->sender_user_id = $request->sender_user_id;
      $buttonRequestObj->sender_user_email = $request->sender_user_email;
      $buttonRequestObj->sender_user_cellphone = $request->sender_user_cellphone;
      $buttonRequestObj->receiver_user_id = $request->receiver_user_id;
      $buttonRequestObj->receiver_user_email = $request->receiver_user_email;
      $buttonRequestObj->pay_object_id = $request->pay_object_id;
      $buttonRequestObj->amount_BIT = $request->amount_BIT;
      $buttonRequestObj->timestamp = time() * 1000;
      $buttonRequestObj->pay_type = $request->pay_type;
      $buttonRequestObj->reference_code = $request->reference_code;
      $buttonRequestJson = json_encode($buttonRequestObj);

      $customization = new stdClass;
      $customization->button_text = $request->pay_type;
      $customization->predefined_pay_values = $request->predefined_pay_values;
      $customization->end_mpayment_uri = $request->end_mpayment_uri;
      $customization->redirect_uri = $request->redirect_uri;
      $customization->button_css = $request->button_css;
      
      $queryStrObj = new stdClass;
      $queryStrObj->customization = json_encode($customization);
      
      if(isset(XapoMicroPaymentSDK::$appID) && isset(XapoMicroPaymentSDK::$appSecret)){
        $buttonRequestEnc = XapoMicroPaymentSDK::encrypt($buttonRequestJson);
        $queryStrObj->app_id = XapoMicroPaymentSDK::$appID;
        $queryStrObj->button_request = $buttonRequestEnc;
      }else{
        $queryStrObj->payload = $buttonRequestJson;
      }
      
      $queryStr = http_build_query($queryStrObj);
      
      $widgetUrl = XapoMicroPaymentSDK::$serviceUrl.'?'.$queryStr;
      return $widgetUrl;      
    }
    
    static public function buildIframeWidget($request)
    {
      $widgetUrl = XapoMicroPaymentSDK::buildWidgetUrl($request);
      $res = '<iframe id="tipButtonFrame" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:22px;" allowTransparency="true" src="'.$widgetUrl.'"></iframe>';
      return $res;
    }
    
    //pay_type possible values: pay, tip, deposit, donation
    //button_css possible values: red, grey
    //redirect_uri callback to be invoked by http get with the following query parameters: resultCode, resultDescription, sender_user_id, receiver_user_id, pay_object_id, amount_BIT, reference_code
    //mandatory fields: sender_user_id, receiver_user_id, receiver_user_email, pay_object_id
    static public function iframeWidget($sender_user_id, $sender_user_email, $sender_user_cellphone, $receiver_user_id, $receiver_user_email, $pay_object_id, 
          $amount_BIT, $pay_type, $reference_code, $predefined_pay_values, $end_mpayment_uri, $redirect_uri, $button_css)
    {
      $buttonRequestObj = new stdClass();
      $buttonRequestObj->sender_user_id = $sender_user_id;
      $buttonRequestObj->sender_user_email = $sender_user_email;
      $buttonRequestObj->sender_user_cellphone = $sender_user_cellphone;
      $buttonRequestObj->receiver_user_id = $receiver_user_id;
      $buttonRequestObj->receiver_user_email = $receiver_user_email;
      $buttonRequestObj->pay_object_id = $pay_object_id;
      $buttonRequestObj->amount_BIT = $amount_BIT;
      $buttonRequestObj->pay_type = $pay_type; 
      
      $buttonRequestObj->reference_code = $reference_code; 
      $buttonRequestObj->predefined_pay_values = $predefined_pay_values; 
      $buttonRequestObj->end_mpayment_uri = $end_mpayment_uri; 
      $buttonRequestObj->redirect_uri = $redirect_uri; 
      $buttonRequestObj->button_css = $button_css;        
      
      $res = XapoMicroPaymentSDK::buildIframeWidget($buttonRequestObj);
      return $res;
    }    
    
  }

?>