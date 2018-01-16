<?php

namespace Umbria\OpenApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ServiziInReteController
 * @package Umbria\OpenApiBundle\Controller
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class ServiziInReteController extends BaseController
{

    public function indexAction()
    {
        return $this->render('UmbriaOpenApiBundle:ServiziInRete:index.html.twig');
    }

    public function executeSparqlQueryAction()
    {
        $options = array('query' => $_POST['query'], 'format' => "application/sparql-results+json");
        $response = $this->postWebResource("http://dati.umbria.it/sparql", $options);
        return new JsonResponse(array('data' => $response));
    }


}
