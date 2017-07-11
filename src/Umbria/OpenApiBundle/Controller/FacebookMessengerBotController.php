<?php
/**
 * Created by PhpStorm.
 * User: DeveloperOspite
 * Date: 11/07/2017
 * Time: 10:30
 */

namespace Umbria\OpenApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Umbria\OpenApiBundle\Controller\Tourism\BaseController;




class FacebookMessengerBotController extends BaseController
{
    public function indexAction()
    {
        $challenge = $_REQUEST['hub_challenge'];
        $verify_token = $_REQUEST['hub_verify_token'];
        // Set this Verify Token Value on your Facebook App
        return new JsonResponse( $challenge);


    }

}