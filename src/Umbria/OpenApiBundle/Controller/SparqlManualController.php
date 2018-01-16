<?php

namespace Umbria\OpenApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;


class SparqlManualController extends BaseController
{
    public function indexAction()
    {

        return $this->render('UmbriaOpenApiBundle:SparqlManual:index.html.twig'
        );

    }


    public function sparqlQueryGraphsAction()
    {
        $url = "http://dati.umbria.it/sparql?default-graph-uri=http%3A%2F%2Fdati.umbria.it%2Fgraph%2Fattrattor&query=SELECT+DISTINCT+%3Fg%0D%0AWHERE%7B%0D%0A++++GRAPH+%3Fg+%7B%3Fs+a+%3Ft%7D%0D%0A%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";
        $response = $this->getWebResource($url);
        return new JsonResponse(array('data' => $response));
    }

    public function sparqlQueryTypesAction($graph)
    {
        $url = "http://dati.umbria.it/sparql?default-graph-uri=" . $graph . "&query=SELECT+DISTINCT+%3Fo%0D%0AWHERE%7B%0D%0A++++%3Fs+%3Chttp%3A%2F%2Fwww.w3.org%2F1999%2F02%2F22-rdf-syntax-ns%23type%3E+%3Fo%0D%0A%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";
        $response = $this->getWebResource($url);
        return new JsonResponse(array('data' => $response));
    }

    public function sparqlQueryDataAction($graph, $type)
    {
        $url = "http://dati.umbria.it/sparql?default-graph-uri=" . $graph . "&query=SELECT+DISTINCT+%3Fs+%3Fp+%3Fo+WHERE%7B+%3Fs+%3Fp+%3Fo+.+%3Fs+%3Chttp%3A%2F%2Fwww.w3.org%2F1999%2F02%2F22-rdf-syntax-ns%23type%3E+%3C" . $type . "%3E+%7DLIMIT+50&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";
        $response = $this->getWebResource($url);
        return new JsonResponse(array('data' => $response));
    }

}