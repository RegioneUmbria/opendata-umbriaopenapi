<?php
/**
 * Created by PhpStorm.
 * User: DeveloperOspite
 * Date: 11/07/2017
 * Time: 10:30
 */

namespace Umbria\OpenApiBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Umbria\OpenApiBundle\Controller\Tourism\BaseController;

class FacebookMessengerBotController extends BaseController
{
    public function indexAction()
    {

        $response = new Response();
        $challenge = $_REQUEST['hub_challenge'];
        $verify_token = $_REQUEST['hub_verify_token'];
        if ($verify_token === 'testtoken') {
// Set this Verify Token Value on your Facebook App
            $response->headers->set('Content-Type', 'text/plain');
            $response->sendHeaders();
            $response->setContent($challenge);
            return $response;
        }
        $input = json_decode(file_get_contents('php://input'), true);
// Get the Senders Graph ID
        $sender = $input['entry'][0]['messaging'][0]['sender']['id'];
// Get the returned message
        $message = $input['entry'][0]['messaging'][0]['message']['text'];
        /**
         * Some Basic rules to validate incoming messages
         */
        if(preg_match('[time|current time|now]', strtolower($message))) {

            // Make request to Time API
            ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
            $result = file_get_contents("http://www.timeapi.org/utc/now?format=%25a%20%25b%20%25d%20%25I:%25M:%25S%20%25Y");
            if($result != '') {
                $message_to_reply = $result;
            }
        } else {
            $message_to_reply = 'Huh! what do you mean?';
        }
        print $message_to_reply;

//API Url and Access Token, generate this token value on your Facebook App Page
        $url = 'https://graph.facebook.com/v2.6/me/messages?access_token=EAAEWX2RC5XoBABm2kTFBeACAB8LfaWC7UHbTU273UobfN1vNzoj8qj1idjIjHC0LJytwfzZCC4ZCJ8OqQcKOmN3K3xr4o1bwYmWYWZA0BCV7B2ZCUgJmse7M1SwwE7sCVz0MKpv2YA3U052rLuZCbwbbVBD8y90rzntENprWDUQZDZD';

//Initiate cURL.
        $ch = curl_init($url);
        
        //The JSON data.
        $jsonData = '{
    "recipient":{
        "id":"'.$sender.'"
    },
    "message":{
        "text":"'.$message_to_reply.'"
    }
}';

//Encode the array into JSON.
        $jsonDataEncoded = $jsonData;

//Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);

//Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

//Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

//Execute the request
        if(!empty($input['entry'][0]['messaging'][0]['message'])){
            $result = curl_exec($ch);
        }

    }

}