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
//API Url and Access Token, generate this token value on your Facebook App Page
        $url = 'https://graph.facebook.com/v2.6/me/messages?access_token=EAADeS6lnyqoBALR7gyuSYGk5dYdkzj7r8wLFVS1AxLoAPZCg4NJG2KWOzjs8CIMp2VLApWDbPZC44cnnl0gz1e93oNeEKmZAD2qEk7khJlzVZCMGzVeVZAUOpCN5BXFInZBjJceXebMtRxqzbMjBFJddgEPNczS44qZBSH1urRurQZDZD';
//Initiate cURL.
        $ch = curl_init($url);
//The JSON data.
        $jsonData = '{
"recipient":{
"id":"' . $sender . '"
},
"message":{
"text":"The message you want to return"
}
}';
//Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);
//Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
//Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//Execute the request but first check if the message is not empty.
        if(!empty($input['entry'][0]['messaging'][0]['message'])){
            $result = curl_exec($ch);
        }


        $response->setContent($jsonData);
        return $response;


    }

}