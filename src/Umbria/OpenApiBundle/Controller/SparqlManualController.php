<?php
/**
 * Created by PhpStorm.
 * User: DeveloperOspite
 * Date: 02/09/2016
 * Time: 12:24
 */

namespace Umbria\OpenApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


class SparqlManualController extends Controller
{
    public function indexAction()
    {

        return $this->render('UmbriaOpenApiBundle:SparqlManual:index.html.twig'
        );

    }


    public function sparqlQueryGraphsAction()
    {
        $url = "https://odnt-srv01/sparql?default-graph-uri=http%3A%2F%2Fdati.umbria.it%2Fgraph%2Fattrattor&query=SELECT+DISTINCT+%3Fg%0D%0AWHERE%7B%0D%0A++++GRAPH+%3Fg+%7B%3Fs+a+%3Ft%7D%0D%0A%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";
        $response = $this->getResource($url);
        return new JsonResponse(array('data' => $response));
    }

    public function sparqlQueryTypesAction($graph)
    {
        $url = "https://odnt-srv01/sparql?default-graph-uri=" . $graph . "&query=SELECT+DISTINCT+%3Fo%0D%0AWHERE%7B%0D%0A++++%3Fs+%3Chttp%3A%2F%2Fwww.w3.org%2F1999%2F02%2F22-rdf-syntax-ns%23type%3E+%3Fo%0D%0A%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";
        $response = $this->getResource($url);
        return new JsonResponse(array('data' => $response));
    }

    public function sparqlQueryDataAction($graph, $type)
    {
        $url = "https://odnt-srv01/sparql?default-graph-uri=" . $graph . "&query=SELECT+DISTINCT+%3Fs+%3Fp+%3Fo+WHERE%7B+%3Fs+%3Fp+%3Fo+.+%3Fs+%3Chttp%3A%2F%2Fwww.w3.org%2F1999%2F02%2F22-rdf-syntax-ns%23type%3E+%3C" . $type . "%3E+%7DLIMIT+50&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on";
        $response = $this->getResource($url);
        return new JsonResponse(array('data' => $response));
    }

    public function getResource($url = 'null', $writeError = true)
    {
        $ch = curl_init();
        try {
            if (false === $ch) {
                throw new Exception('failed to initialize');
            }

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $content = curl_exec($ch);
            curl_close($ch);

            if (false === $content) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            return $content;
        } catch (Exception $e) {
            if ($writeError == true) {
                trigger_error(sprintf(
                    'Curl failed with error #%d: %s, URL: %s',
                    $e->getCode(), $e->getMessage(), $url),
                    E_USER_ERROR);
            } else {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
        }

        return;
    }
}