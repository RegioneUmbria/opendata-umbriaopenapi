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

        $answer = null;
        $hubVerifyToken = 'testtoken';
        $accessToken = "EAAEWX2RC5XoBABm2kTFBeACAB8LfaWC7UHbTU273UobfN1vNzoj8qj1idjIjHC0LJytwfzZCC4ZCJ8OqQcKOmN3K3xr4o1bwYmWYWZA0BCV7B2ZCUgJmse7M1SwwE7sCVz0MKpv2YA3U052rLuZCbwbbVBD8y90rzntENprWDUQZDZD";
// check token at setup
        if ($_REQUEST['hub_verify_token'] === $hubVerifyToken) {
            echo $_REQUEST['hub_challenge'];
            exit;
        }
// handle bot's anwser
        $input = json_decode(file_get_contents('php://input'), true);
        $sender = $input['entry'][0]['messaging'][0]['sender']['id'];
        $message = $input['entry'][0]['messaging'][0]['message']['text'];
        $response = null;
//set Message
        if ($message == "hi") {
            $answer = "Hello";
        }
//send message to facebook bot
        $response = [
            'recipient' => ['id' => $sender],
            'message' => ['text' => $answer]
        ];
        $url = 'https://graph.facebook.com/v2.6/me/messages?access_token=EAAEWX2RC5XoBABm2kTFBeACAB8LfaWC7UHbTU273UobfN1vNzoj8qj1idjIjHC0LJytwfzZCC4ZCJ8OqQcKOmN3K3xr4o1bwYmWYWZA0BCV7B2ZCUgJmse7M1SwwE7sCVz0MKpv2YA3U052rLuZCbwbbVBD8y90rzntENprWDUQZDZD';

//Initiate cURL.
        $ch = curl_init($url);
//        $ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . $accessToken);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        if (!empty($message)) {
            $result = curl_exec($ch);
        }
        curl_close($ch);
    }

}