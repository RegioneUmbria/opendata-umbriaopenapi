<?php

namespace Umbria\OpenApiBundle\Controller\Tourism;

use Doctrine\ORM\EntityManager;
use EasyRdf_Resource;
use EasyRdf_Sparql_Client;
use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use Umbria\OpenApiBundle\Entity\ExternalResource;

/**
 * Class BaseController
 * @package Umbria\OpenApiBundle\Controller\Tourism
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class BaseController extends FOSRestController
{

    /**@var ExternalResource */
    private $externalResourceRepo;

    /**
     * BaseController constructor.
     * @param $em EntityManager
     */
    public function __construct($em)
    {
        $this->externalResourceRepo = $em->getRepository('UmbriaOpenApiBundle:ExternalResource');
    }


    /**
     * @param EasyRdf_Resource[] $externalResourcesUriArray
     * @param String $sparqlEndpointUri
     * @param String $labelPropertyUri
     * @param String $descriptionPropertyUri
     * @param String $resourceOriginPropertyUri
     * @return ExternalResource[]|null
     */
    protected function getExternalResources($externalResourcesUriArray, $sparqlEndpointUri,
                                            $labelPropertyUri, $descriptionPropertyUri, $resourceOriginPropertyUri)
    {
        if ($externalResourcesUriArray != null) {
            $tempExternalResources = array();
            $cnt = 0;
            foreach ($externalResourcesUriArray as $externalResourcesUri) {
                $externalResource = null;
                $externalResourceUri = $externalResourcesUri->toRdfPhp()['value'];
                $oldExternalResource = $this->externalResourceRepo->find($externalResourceUri);
                if ($oldExternalResource != null) {
                    $externalResource = $oldExternalResource;
                } else {
                    $externalResource = new ExternalResource();
                }


                $externalResource->setUri($externalResourceUri);
                $sparqlClient = new EasyRdf_Sparql_Client($sparqlEndpointUri);

                $queryLabel = "SELECT ?o WHERE {<" . $externalResourceUri . "> <" . $labelPropertyUri . "> ?o. FILTER ( lang(?o) = \"it\" )}";
                $sparqlResultLabel = $sparqlClient->query($queryLabel);
                $sparqlResultLabel->rewind();
                while ($sparqlResultLabel->valid()) {
                    $current = $sparqlResultLabel->current();
                    $externalResource->setName($current->o);
                    $sparqlResultLabel->next();
                }

                $queryDescription = "SELECT ?o WHERE {<" . $externalResourceUri . "> <" . $descriptionPropertyUri . "> ?o. FILTER ( lang(?o) = \"it\" )}";
                $sparqlResultDescription = $sparqlClient->query($queryDescription);
                $sparqlResultDescription->rewind();
                while ($sparqlResultDescription->valid()) {
                    $current = $sparqlResultDescription->current();
                    $externalResource->setDescription($current->o);
                    $sparqlResultDescription->next();
                }

                $queryResourceOrigin = "SELECT ?o WHERE {<" . $externalResourceUri . "> <" . $resourceOriginPropertyUri . "> ?o}";
                $sparqlResultResourceOrigin = $sparqlClient->query($queryResourceOrigin);
                $sparqlResultResourceOrigin->rewind();
                while ($sparqlResultResourceOrigin->valid()) {
                    $current = $sparqlResultResourceOrigin->current();
                    $externalResource->setResourceOriginUrl($current->o);
                    $sparqlResultResourceOrigin->next();
                }
                $tempExternalResources[$cnt] = $externalResource;
                $cnt++;
            }
            if (count($tempExternalResources) > 0) {
                return $tempExternalResources;
            } else {
                return null;
            }
        }
        return null;
    }

    public function getWebResource($url = 'null', $writeError = true)
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

        return null;
    }

}