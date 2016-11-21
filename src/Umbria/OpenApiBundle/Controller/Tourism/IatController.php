<?php

namespace Umbria\OpenApiBundle\Controller\Tourism;

use DateTime;
use Doctrine\ORM\EntityManager;
use EasyRdf_Graph;
use EasyRdf_Literal;
use EasyRdf_Resource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\Paginator;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Umbria\OpenApiBundle\Entity\Address;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Iat;
use Umbria\OpenApiBundle\Entity\Tourism\Setting;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\IatRepository;
use Umbria\OpenApiBundle\Serializer\View\EntityResponse;
use Umbria\OpenApiBundle\Service\FilterBag;

/**
 * Class IatController
 * @package Umbria\OpenApiBundle\Controller\Tourism
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class IatController extends BaseController
{
    const DEFAULT_PAGE_SIZE = 100;
    const DATASET_TOURISM_IAT = 'tourism-iat';

    private $filterBag;
    private $paginator;

    private $em;
    /**@var IatRepository $iatRepo */
    private $iatRepo;
    private $settingsRepo;

    private $graph;

    /**
     * @DI\InjectParams({
     *      "em" = @DI\Inject("doctrine.orm.entity_manager"),
     *      "filterBag" = @DI\Inject("umbria_open_api.filter_bag"),
     *      "paginator" = @DI\Inject("knp_paginator")
     * })
     * @param $em EntityManager
     * @param $filterBag FilterBag
     * @param $paginator Paginator
     */
    public function __construct($em, $filterBag, $paginator)
    {
        parent::__construct($em);
        $this->filterBag = $filterBag;
        $this->paginator = $paginator;
        $this->iatRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Iat');
        $this->settingsRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\Setting');
        $this->em = $em;
    }

    /**
     * @Rest\Options(pattern="/open-api/tourism-iat")
     */
    public function optionsTourismIatAction()
    {
        $response = new Response();

        $response->setContent(json_encode(array(
            'Allow' => 'GET,OPTIONS',
            'GET' => array(
                'description' => 'A get request for iat',
            ),
        )));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'json');

        return $response;
    }

    /**
     * @Rest\Get(pattern="/open-api/tourism-iat")
     *
     * @param Request $request
     *
     * @return View
     *
     * @internal param Request $request
     *
     * @ApiDoc\ApiDoc(
     *  section = "Tourism",
     *  description = "Lista IAT regione Umbria",
     *  tags = {
     *      "beta"
     *  },
     *  parameters={
     *      {"name"="start", "dataType"="integer", "required"=false, "description"="Elemento da cui partire"},
     *      {"name"="limit", "dataType"="integer", "required"=false, "description"="Numero di elementi restituiti"}
     *  },
     *  statusCodes={
     *      200="Returned when successful"
     *  }
     * )
     */
    public function getTourismIatListAction(Request $request)
    {
        $daysToOld = $this->container->getParameter('iat_days_to_old');

        $filters = $this->filterBag->getFilterBag($request);
        $offset = $filters->has('start') ? $filters->get('start') : 0;
        $limit = $filters->has('limit') ? $filters->get('limit') : self::DEFAULT_PAGE_SIZE;
        if ($limit == 0) {
            $limit = self::DEFAULT_PAGE_SIZE;
        }
        $page = floor($offset / $limit) + 1;

        /** @var Setting $setting */
        $setting = $this->settingsRepo->findOneBy(array('datasetName' => self::DATASET_TOURISM_IAT));
        if ($setting != null) {
            $diff = $setting->getUpdatedAt()->diff(new DateTime('now'));
            // controllo intervallo di tempo da ultima estrazione
            if ($diff->days >= $daysToOld) {
                $setting->setDatasetName(self::DATASET_TOURISM_IAT);
                $setting->setUpdatedAtValue();
                $this->em->persist($setting);
                $this->em->flush();

                $this->updateEntities();
            }
        } else {
            $setting = new Setting();
            $setting->setDatasetName(self::DATASET_TOURISM_IAT);
            $setting->setUpdatedAtValue();
            $this->em->persist($setting);
            $this->em->flush();

            $this->updateEntities();
        }

        $builder = $this->em->createQueryBuilder()
            ->select('i')
            ->from('UmbriaOpenApiBundle:Tourism\GraphsEntities\Iat', 'i');

        /** @var AbstractPagination $resultsPagination */
        $resultsPagination = $this->paginator->paginate($builder, $page, $limit);
        /** @var AbstractPagination $countPagination */
        $countPagination = $this->paginator->paginate($builder, 1, 1);

        $view = new View(new EntityResponse($resultsPagination->getItems(), count($resultsPagination), $countPagination->getTotalItemCount()));

        return $view;
    }

    private function updateEntities()
    {

        $this->graph = EasyRdf_Graph::newAndLoad($this->container->getParameter('iat_graph_url'));
        /**@var EasyRdf_Resource[] $resources */
        $resources = $this->graph->resources();
        foreach ($resources as $resource) {
            $resourceTypeArray = $resource->all("rdf:type");
            if ($resourceTypeArray != null) {
                foreach ($resourceTypeArray as $resourceType) {
                    if (trim($resourceType) == "http://dati.umbria.it/tourism/ontology/iat") {
                        $this->createOrUpdateEntity($resource);
                        break;
                    }
                }
            }

        }
        $now = new \DateTime();
        $this->deleteOldEntities($now);
    }

    /**
     * @param \EasyRdf_Resource $iatResource
     */
    private function createOrUpdateEntity($iatResource)
    {
        /** @var Iat $newIat */
        $newIat = null;
        $uri = $iatResource->getUri();
        if ($uri != null) {
            $oldIat = $this->iatRepo->find($uri);
            $isAlreadyPersisted = $oldIat != null;
            if ($isAlreadyPersisted) {
                $newIat = $oldIat;
            } else {
                $newIat = new Iat();
            }
            $newIat->setUri($uri);
            $newIat->setLastUpdateAt(new \DateTime('now'));

            /**@var EasyRdf_Literal[] $labelArray */
            $labelArray = $iatResource->all("rdfs:label");
            foreach ($labelArray as $label) {
                if ($label->getLang() == "it") {
                    $newIat->setName($label->getValue());
                    break;
                }
            }
            $typesarray = $iatResource->all("rdf:type");
            if ($typesarray != null) {
                $tempTypes = array();

                $cnt = 0;
                foreach ($typesarray as $type) {
                    $tempTypes[$cnt] = $type->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempTypes) > 0 ? $newIat->setTypes($tempTypes) : $newIat->setTypes(null);
            }

            $newIat->setProvenance(($p = $iatResource->get("<http://purl.org/dc/elements/1.1/provenance>")) != null ? $p->getValue() : null);
            $newIat->setLanguage(($p = $iatResource->get("<http://purl.org/dc/elements/1.1/language>")) != null ? $p->getUri() : null);
            $newIat->setMunicipalitiesList(($p = $iatResource->get("<http://dati.umbria.it/tourism/ontology/lista_comuni>")) != null ? $p->getValue() : null);


            $emailarray = $iatResource->all("<http://schema.org/email>");
            if ($emailarray != null) {
                $tempEmail = array();
                $newIat->setEmail(array());
                $cnt = 0;
                foreach ($emailarray as $email) {
                    $tempEmail[$cnt] = $email->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempEmail) > 0 ? $newIat->setEmail($tempEmail) : $newIat->setEmail(null);
            }

            $telephonearray = $iatResource->all("<http://schema.org/telephone>");
            if ($telephonearray != null) {
                $tempTelephone = array();
                $cnt = 0;
                foreach ($telephonearray as $telephone) {
                    $tempTelephone[$cnt] = $telephone->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempTelephone) > 0 ? $newIat->setTelephone($tempTelephone) : $newIat->setTelephone(null);
            }

            $faxarray = $iatResource->all("<http://schema.org/faxNumber>");
            if ($faxarray != null) {
                $tempFax = array();
                $newIat->setFax(array());
                $cnt = 0;
                foreach ($faxarray as $fax) {
                    $tempFax[$cnt] = $fax->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempFax) > 0 ? $newIat->setFax($tempFax) : $newIat->setFax(null);
            }


            if ($isAlreadyPersisted && ($oldAddress = $newIat->getAddress()) != null) {
                $this->em->remove($oldAddress);
                $newIat->setAddress(null);
            }

            /**@var EasyRdf_Resource $addressResource */
            $addressResource = $iatResource->get("<http://dati.umbria.it/tourism/ontology/indirizzo>");
            if ($addressResource != null) {
                $addressObject = new Address();
                $addressObject->setPostalCode(($p = $addressResource->get("<http://schema.org/postalCode>")) != null ? $p->getValue() : null);
                $addressObject->setIstat(($p = $addressResource->get("<http://dbpedia.org/ontology/istat>")) != null ? $p->getValue() : null);
                $addressObject->setAddressLocality(($p = $addressResource->get("<http://schema.org/addressLocality>")) != null ? $p->getValue() : null);
                $addressObject->setAddressRegion(($p = $addressResource->get("<http://schema.org/addressRegion>")) != null ? $p->getValue() : null);
                $addressObject->setStreetAddress(($p = $addressResource->get("<http://schema.org/streetAddress>")) != null ? $p->getValue() : null);
                $newIat->setAddress($addressObject);

                // Google Maps Api --------------------------
                $url = 'http://maps.google.com/maps/api/geocode/json?address=' . urlencode($addressObject->getStreetAddress()) .
                    '+' . $addressObject->getPostalCode() .
                    '+' . $addressObject->getAddressLocality() .
                    '+' . $addressObject->getAddressRegion() . '+Umbria+Italia';

                $resp = json_decode($this->getWebResource($url), true);

                // response status will be 'OK', if able to geocode given address
                if ($resp['status'] == 'OK') {

                    // get the important data
                    $lat = $resp['results'][0]['geometry']['location']['lat'];
                    $lng = $resp['results'][0]['geometry']['location']['lng'];

                    // verify if data is complete
                    if ($lat && $lng) {
                        $newIat->setLat($lat);
                        $newIat->setLng($lng);
                    }
                }

            }

            if (!$isAlreadyPersisted) {
                $this->em->persist($newIat);
            }

            $this->em->flush();
        }
    }

    private function deleteOldEntities($olderThan)
    {
        $this->iatRepo->removeLastUpdatedBefore($olderThan);

    }

}
