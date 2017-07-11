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
        $response= array("casa1"=>"blu", "casa2"=>"rosso");
        return new JsonResponse( $response);

    }

}