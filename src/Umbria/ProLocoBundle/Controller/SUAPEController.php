<?php

namespace Umbria\ProLocoBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Umbria\OpenApiBundle\Controller\Tourism\BaseController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SUAPEController
 * @package Umbria\ProLocoBundle\Controller
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class SUAPEController extends BaseController
{

    public function indexAction()
    {
        return $this->render('UmbriaProLocoBundle:SUAPE:index.html.twig');
    }

    public function executeSparqlQueryAction()
    {
        $options = array('query' => $_POST['query'], 'format' => "application/sparql-results+json");
        $response = $this->postWebResource("http://dati.umbria.it/sparql", $options);
        return new JsonResponse(array('data' => $response));
    }


}
