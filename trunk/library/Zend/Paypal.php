<?php

class Zend_Paypal {
    
   private $API_UserName = "labutacaescarlata_api1.gmail.com";
   private $API_Password = "A24GTV9YPS5KYSJ7";
   private $API_Signature = "AFcWxV21C7fd0v3bYYYRCpSSRl31AhkRWH4zVQFPnA22EZ8a-w55BxCY";
    

   /*private $API_UserName = "sbapi_1287090601_biz_api1.paypal.com"; //TODO
   private $API_Password = "1287090610"; //TODO
   private $API_Signature = "ANFgtzcGWolmjcm5vfrf07xVQ6B9AsoDvVryVxEQqezY85hChCfdBMvY"; //TODO
    * 
    */

   private $urlPreapproval = "https://svcs.sandbox.paypal.com/AdaptivePayments/Preapproval";

    //Default App ID for Sandbox
   private $API_AppID = "APP-80W284485P519543T";

   private $API_RequestFormat = "NV";
   private $API_ResponseFormat = "NV";

   private $lang="es_ES";
   private $currency="EUR";

   private $maxNumberOfPayments="1";

   private $paypalUrl=null;
   private $cancelUrl=null;
   private $returnUrl=null;
   private $notifyUrl=null;
   private $bussines=null;


   
   function Zend_Paypal(array $config) {
       $this->cancelUrl=$config['cancelUrl'];
       $this->returnUrl=$config['returnUrl'];
       $this->paypalUrl=$config['paypalUrl'];
       $this->notifyUrl=$config['notifyUrl'];
       $this->bussines=$config['bussines'];
   }

   function getPreapprovalKey(array $params){
       //Create request payload with minimum required parameters
        $bodyParams = array ("requestEnvelope.errorLanguage" => $this->lang,
                            "maxTotalAmountOfAllPayments" => $params['amount'],
                            "currencyCode" => $this->currency,
                            "cancelUrl" => $this->cancelUrl,
                            "returnUrl" => $this->returnUrl,
                            "ipnNotificationUrl" => $this->notifyUrl,
                            "startingDate" => $params['date'],
                            "endingDate" => $params['date'],
                            "maxNumberOfPayments"=>$this->maxNumberOfPayments);

        // convert payload array into url encoded query string
        $bodyData = http_build_query($bodyParams, "", chr(38));
        try{
            //create request and add headers
            $context = array("http" => array(
                    "method" => "POST",
                    "content" => $bodyData,
                    "header" =>  "X-PAYPAL-SECURITY-USERID: " . $this->API_UserName . "\r\n" .
                    "X-PAYPAL-SECURITY-SIGNATURE: " . $this->API_Signature . "\r\n" .
                    "X-PAYPAL-SECURITY-PASSWORD: " . $this->API_Password . "\r\n" .
                    "X-PAYPAL-APPLICATION-ID: " . $this->API_AppID . "\r\n" .
                    "X-PAYPAL-REQUEST-DATA-FORMAT: " . $this->API_RequestFormat . "\r\n" .
                    "X-PAYPAL-RESPONSE-DATA-FORMAT: " . $this->API_ResponseFormat . "\r\n"
                    ));

            var_dump($context);


            //create stream context
             $ctx = stream_context_create($context);

            //open the stream and send request
             $fp = @fopen($this->urlPreapproval, "r", false, $ctx);
            //get response
            $response = stream_get_contents($fp);

            //check to see if stream is open
            if ($response === false) {
                throw new Exception($php_errormsg);
            }
            //close the stream
            fclose($fp);

            //parse the ap key from the response
            $keyArray = explode("&", $response);

            foreach ($keyArray as $rVal){
                list($qKey, $qVal) = explode ("=", $rVal);
                $kArray[$qKey] = $qVal;
            }
 
            //print the url to screen for testing purposes
            If ( $kArray["responseEnvelope.ack"] == "Success")
                return $kArray["preapprovalKey"];
            else {
                throw new Exception($kArray["error(0).errorId"]." - ".urldecode($kArray["error(0).message"]));
            }

        }
        catch(Exception $e) {
                die ("Message: ||" .$e->getMessage()."||");
        }
   }

   public function getUrlPreapproval($preapprovalKey){
       return $this->paypalUrl."?cmd=_ap-preapproval&preapprovalkey=".$preapprovalKey;
   }

   public function pay($amount, $preapprovalKey){

        $bodyparams = array (	"requestEnvelope.errorLanguage" => $this->lang,
	"actionType" => "PAY",
        "currencyCode" => $this->currency,
        "cancelUrl" => $this->cancelUrl,
        "returnUrl" => $this->returnUrl,
        "receiverList.receiver(0).email" => $this->bussines, //TODO
        "receiverList.receiver(0).amount" => $amount, 
        "preapprovalKey" => $preapprovalKey);

    // convert payload array into url encoded query string
    $body_data = http_build_query($bodyparams, "", chr(38));


    try{

         $context = array("http" => array(
                    "method" => "POST",
                    "content" => $bodyData,
                    "header" =>  "X-PAYPAL-SECURITY-USERID: " . $this->API_UserName . "\r\n" .
                    "X-PAYPAL-SECURITY-SIGNATURE: " . $this->API_Signature . "\r\n" .
                    "X-PAYPAL-SECURITY-PASSWORD: " . $this->API_Password . "\r\n" .
                    "X-PAYPAL-APPLICATION-ID: " . $this->API_AppID . "\r\n" .
                    "X-PAYPAL-REQUEST-DATA-FORMAT: " . $this->API_RequestFormat . "\r\n" .
                    "X-PAYPAL-RESPONSE-DATA-FORMAT: " . $this->API_ResponseFormat . "\r\n"
                    ));

            //create stream context
             $ctx = stream_context_create($context);

            //open the stream and send request
             $fp = @fopen($url, "r", false, $ctx);

            //get response
             $response = stream_get_contents($fp);

            //check to see if stream is open
             if ($response === false) {
                throw new Exception($php_errormsg);
             }

            //close the stream
             fclose($fp);

            //parse the ap key from the response
            $keyArray = explode("&", $response);

            foreach ($keyArray as $rVal){
                list($qKey, $qVal) = explode ("=", $rVal);
                                $kArray[$qKey] = $qVal;
            }

            //set url to approve the transaction
            $payPalURL = "https://www.sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=" . $kArray["payKey"];

            //print the url to screen for testing purposes
            If ( $kArray["responseEnvelope.ack"] == "Success") {
                die('<p><a href="' . $payPalURL . '" target="_blank">' . $payPalURL . '</a></p>');
             }
            else {
                throw new Exception($kArray["error(0).errorId"]." - ".urldecode($kArray["error(0).message"]));
            }


       }catch(Exception $e) {
           echo "Message: ||" .$e->getMessage()."||";
       }

   }

}         


 
