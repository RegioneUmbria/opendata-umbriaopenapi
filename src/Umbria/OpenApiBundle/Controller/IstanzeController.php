<?php

namespace Umbria\OpenApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Umbria\OpenApiBundle\Controller\Tourism\BaseController;

/**
 * Class IstanzeController
 * @package Umbria\OpenApiBundle\Controller
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class IstanzeController extends BaseController
{

    public function indexAction()
    {
        return $this->render('UmbriaOpenApiBundle:Istanze:index.html.twig');
    }

    public function executeSparqlQueryAction()
    {
        $options = array('query' => $_POST['query'], 'format' => "application/sparql-results+json");
        $response = $this->postWebResource("http://dati.umbria.it/sparql", $options);
        return new JsonResponse(array('data' => $response));
    }


}
