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
        $answer = null;
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
//        $location = $input['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates'];
        //set Message
        if ($message == "hi") {
//            $url0 = 'https://graph.facebook.com/v2.6/me/messages?access_token=EAAEWX2RC5XoBABm2kTFBeACAB8LfaWC7UHbTU273UobfN1vNzoj8qj1idjIjHC0LJytwfzZCC4ZCJ8OqQcKOmN3K3xr4o1bwYmWYWZA0BCV7B2ZCUgJmse7M1SwwE7sCVz0MKpv2YA3U052rLuZCbwbbVBD8y90rzntENprWDUQZDZD';
            $ch0 = curl_init();
            curl_setopt($ch0, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch0, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch0, CURLOPT_URL, 'https://graph.facebook.com/v2.6/1664521423558935?fields=first_name,last_name&access_token=EAAEWX2RC5XoBABm2kTFBeACAB8LfaWC7UHbTU273UobfN1vNzoj8qj1idjIjHC0LJytwfzZCC4ZCJ8OqQcKOmN3K3xr4o1bwYmWYWZA0BCV7B2ZCUgJmse7M1SwwE7sCVz0MKpv2YA3U052rLuZCbwbbVBD8y90rzntENprWDUQZDZD');
            $uname = curl_exec($ch0);
            curl_close($ch0);
            $obj = json_decode($uname,true);
//            $s2 =  implode(',',$obj);
//            $fname = $obj['first_name'];
            $answer = "Hello";
            $payload = array("recipient" => array("id" => $sender), "message" => array("text" =>$answer.''. $obj['first_name']));
        }
        else if($message == "website") {
            $answer = ["attachment" => [
                "type" => "template",
                "payload" => [
                    "template_type" => "generic",
                    "elements" => [
                        [
                            "title" => "Welcome to Umbria Digitale",
                            "item_url" => "https://umbriaopenapi-nigel.tk/",
                            "image_url" => "http://www.innovazionelogistica.it/wp-content/uploads/2015/08/logo_Umbria.jpg",
//                            "subtitle" => "We\'ve got the right hat for everyone.",
                            "buttons" => [
                                [
                                    "type" => "web_url",
                                    "url" => "https://umbriaopenapi-nigel.tk/",
                                    "title" => "View Website"
                                ],
                                [
                                    "type" => "postback",
                                    "title" => "Start Chatting",
                                    "payload" => "DEVELOPER_DEFINED_PAYLOAD"
                                ]
                            ]
                        ]
                    ]
                ]
            ]];
            $payload = array("recipient" => array("id" => $sender), "message" => $answer);
        }
        //If input contains coordinates data, do something with it
        else if ($message == "location") {
           //The Latitude of the location sent
            $userLat = $input['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates']['lat'];
           //The Longitude of the location sent
            $userLong = $input['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates']['long'];
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?&latlng=' . $userLat . ',' . $userLong;
            $content = file_get_contents($url);
            $json = json_decode($content, true);
            $userCity = $json['results'][0]['address_components'][2]['long_name'];
            $payload = array("recipient" => array("id" => $sender), "message" => array("text" =>" Please share your location:", "quick_replies"=> ["content_type"=>"location"]));
        }

        //API Url and Access Token, generate this token value on your Facebook App Page
        $url = 'https://graph.facebook.com/v2.6/me/messages?access_token=EAAEWX2RC5XoBABm2kTFBeACAB8LfaWC7UHbTU273UobfN1vNzoj8qj1idjIjHC0LJytwfzZCC4ZCJ8OqQcKOmN3K3xr4o1bwYmWYWZA0BCV7B2ZCUgJmse7M1SwwE7sCVz0MKpv2YA3U052rLuZCbwbbVBD8y90rzntENprWDUQZDZD';
        //Initiate cURL.
        $ch = curl_init($url);
//        $payload = array("recipient" => array("id" => $sender), "message" => array("text" => $answer));
        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);
        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        //Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        //Execute the request but first check if the message is not empty.
        if (!empty($input['entry'][0]['messaging'][0]['message'])) {
            $result = curl_exec($ch);
        }
        $logger = $this->get('logger');
        $logger->info(json_encode($payload));
        $response->setContent(json_encode($payload));
        return $response;
    }
}