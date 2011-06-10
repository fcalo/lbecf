<?php

/*
*******************************************************************
THIS IS STRICTLY EXAMPLE SOURCE CODE. IT IS ONLY MEANT TO
QUICKLY DEMONSTRATE THE CONCEPT AND THE USAGE OF THE ADAPTIVE
PAYMENTS API. PLEASE NOTE THAT THIS IS *NOT* PRODUCTION-QUALITY
CODE AND SHOULD NOT BE USED AS SUCH.

THIS EXAMPLE CODE IS PROVIDED TO YOU ONLY ON AN "AS IS"
BASIS WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, EITHER
EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION ANY WARRANTIES
OR CONDITIONS OF TITLE, NON-INFRINGEMENT, MERCHANTABILITY OR
FITNESS FOR A PARTICULAR PURPOSE. PAYPAL MAKES NO WARRANTY THAT
THE SOFTWARE OR DOCUMENTATION WILL BE ERROR-FREE. IN NO EVENT
SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL,  EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT
OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS;
OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF
THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
OF SUCH DAMAGE.


INSTRUCTIONS
1) Ensure that SSL and fopen() are enabled in the php.ini file
2) Written and Tested with PHP 5.3.0


IMPORTANT:
When you integrate this code look for TODO as an indication that 
you may need to provide a value or take action before executing this code.
*******************************************************************

*/

//turn php errors on
ini_set("track_errors", true);

//set PayPal Endpoint to sandbox
$url = trim("https://svcs.sandbox.paypal.com/AdaptivePayments/CancelPreapproval");

/*
*******************************************************************
PayPal API Credentials
Replace <API_USERNAME> with your API Username
Replace <API_PASSWORD> with your API Password
Replace <API_SIGNATURE> with your Signature
*******************************************************************
*/

//PayPal API Credentials
$API_UserName = "sbapi_1287090601_biz_api1.paypal.com"; //TODO
$API_Password = "1287090610"; //TODO
$API_Signature = "ANFgtzcGWolmjcm5vfrf07xVQ6B9AsoDvVryVxEQqezY85hChCfdBMvY"; //TODO
	
//Default App ID for Sandbox	
$API_AppID = "APP-80W284485P519543T";

$API_RequestFormat = "NV";
$API_ResponseFormat = "NV";


//Create request payload with minimum required parameters
$bodyparams = array (	"requestEnvelope.errorLanguage" => "en_US",
											"preapprovalKey" => "PA-85Y11963AH271435W"
											);
											
// convert payload array into url encoded query string
$body_data = http_build_query($bodyparams, "", chr(38));


try
{

    //create request and add headers
    $params = array("http" => array( 
    																 "method" => "POST",
                  									 "content" => $body_data,
                  									 "header" =>  "X-PAYPAL-SECURITY-USERID: " . $API_UserName . "\r\n" .
                               										"X-PAYPAL-SECURITY-SIGNATURE: " . $API_Signature . "\r\n" .
                 							 										"X-PAYPAL-SECURITY-PASSWORD: " . $API_Password . "\r\n" .
                   						 										"X-PAYPAL-APPLICATION-ID: " . $API_AppID . "\r\n" .
                   						 										"X-PAYPAL-REQUEST-DATA-FORMAT: " . $API_RequestFormat . "\r\n" .
                  						 										"X-PAYPAL-RESPONSE-DATA-FORMAT: " . $API_ResponseFormat . "\r\n" 
                  																));


    //create stream context
     $ctx = stream_context_create($params);
    

    //open the stream and send request
     $fp = @fopen($url, "r", false, $ctx);

    //get response
  	 $response = stream_get_contents($fp);

  	//check to see if stream is open
     if ($response === false) {
        throw new Exception("php error message = " . "$php_errormsg");
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
    If ( $kArray["responseEnvelope.ack"] == "Success") {
    	
    	 foreach ($kArray as $key =>$value){
    	echo $key . ": " .$value . "<br/>";
    }
     }
    else {
    	echo 'ERROR Code: ' .  $kArray["error(0).errorId"] . " <br/>";
      echo 'ERROR Message: ' .  urldecode($kArray["error(0).message"]) . " <br/>";
    }
   
   }


catch(Exception $e) {
  	echo "Message: ||" .$e->getMessage()."||";
  }

?>
