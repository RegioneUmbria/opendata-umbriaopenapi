<?php

namespace Umbria\OpenApiBundle\Controller\Tourism;

use DateTime;
use Doctrine\ORM\EntityManager;
use EasyRdf_Graph;
use EasyRdf_Literal;
use EasyRdf_Resource;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\Paginator;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Umbria\OpenApiBundle\Entity\Address;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\TravelAgency;
use Umbria\OpenApiBundle\Entity\Tourism\Setting;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\TravelAgencyRepository;
use Umbria\OpenApiBundle\Serializer\View\EntityResponse;
use Umbria\OpenApiBundle\Service\TourismEntityUpdater;
use Umbria\OpenApiBundle\Service\FilterBag;

/**
 * Class TravelAgencyController
 * @package Umbria\OpenApiBundle\Controller\Tourism
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class TravelAgencyController extends BaseController
{
    const DEFAULT_PAGE_SIZE = 100;
    const DATASET_TOURISM_TRAVEL_AGENCY = 'tourism-travel-agency';

    private $filterBag;
    private $paginator;

    private $em;
    /**@var TravelAgencyRepository $travelAgencyRepo */
    private $travelAgencyRepo;
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
        $this->travelAgencyRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\TravelAgency');
        $this->settingsRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\Setting');
        $this->em = $em;
    }

    /**
     * @Rest\Options(pattern="/open-api/tourism-travel-agency")
     */
    public function optionsTourismProposalAction()
    {
        $response = new Response();

        $response->setContent(json_encode(array(
            'Allow' => 'GET,OPTIONS',
            'GET' => array(
                'description' => 'A get request for travel agencies',
            ),
        )));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'json');

        return $response;
    }

    /**
     * @Rest\Get(pattern="/open-api/tourism-travel-agency")
     *
     * @param Request $request
     *
     * @return View
     *
     * @internal param Request $request
     *
     * @ApiDoc\ApiDoc(
     *  section = "Tourism",
     *  description = "Lista agenzie turistiche regione Umbria",
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
    public function getTourismTravelAgencyListAction(Request $request)
    {
        $daysToOld = $this->container->getParameter('travel_agency_days_to_old');

        $filters = $this->filterBag->getFilterBag($request);
        $offset = $filters->has('start') ? $filters->get('start') : 0;
        $limit = $filters->has('limit') ? $filters->get('limit') : self::DEFAULT_PAGE_SIZE;
        if ($limit == 0) {
            $limit = self::DEFAULT_PAGE_SIZE;
        }
        $page = floor($offset / $limit) + 1;

        /** @var Setting $setting */
        $setting = $this->settingsRepo->findOneBy(array('datasetName' => self::DATASET_TOURISM_TRAVEL_AGENCY));
        if ($setting != null) {
            $diff = $setting->getUpdatedAt()->diff(new DateTime('now'));
            if ($diff->days >= $daysToOld) {
                $setting->setDatasetName(self::DATASET_TOURISM_TRAVEL_AGENCY);
                $setting->setUpdatedAtValue();
                $this->em->persist($setting);
                $this->em->flush();

                $this->updateEntities();
            }
        } else {
            $setting = new Setting();
            $setting->setDatasetName(self::DATASET_TOURISM_TRAVEL_AGENCY);
            $setting->setUpdatedAtValue();
            $this->em->persist($setting);
            $this->em->flush();

            $this->updateEntities();
        }

        $builder = $this->em->createQueryBuilder()
            ->select('a')
            ->from('UmbriaOpenApiBundle:Tourism\GraphsEntities\TravelAgency', 'a');

        /** @var AbstractPagination $resultsPagination */
        $resultsPagination = $this->paginator->paginate($builder, $page, $limit);
        /** @var AbstractPagination $countPagination */
        $countPagination = $this->paginator->paginate($builder, 1, 1);


        $view = new View(new EntityResponse($resultsPagination->getItems(), count($resultsPagination), $countPagination->getTotalItemCount()));

        return $view;
    }

    private function updateEntities()
    {
        $this->graph = EasyRdf_Graph::newAndLoad($this->container->getParameter('travel_agency_graph_url'));
        /**@var EasyRdf_Resource[] $resources */
        $resources = $this->graph->resources();
        foreach ($resources as $resource) {
            $resourceTypeArray = $resource->all("rdf:type");
            if ($resourceTypeArray != null) {
                foreach ($resourceTypeArray as $resourceType) {
                    if (trim($resourceType) == "http://dati.umbria.it/tourism/ontology/agenzia_viaggio") {
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
     * @param \EasyRdf_Resource $travelAgencyResource
     */
    private function createOrUpdateEntity($travelAgencyResource)
    {
        /** @var TravelAgency $newTravelAgency */
        $newTravelAgency = null;
        $uri = $travelAgencyResource->getUri();
        if ($uri != null) {
            $oldTravelAgency = $this->travelAgencyRepo->find($uri);
            $isAlreadyPersisted = $oldTravelAgency != null;
            if ($isAlreadyPersisted) {
                $newTravelAgency = $oldTravelAgency;
            } else {
                $newTravelAgency = new TravelAgency();
            }
            $newTravelAgency->setUri($uri);
            $newTravelAgency->setLastUpdateAt(new \DateTime('now'));

            /**@var EasyRdf_Literal[] $labelArray */
            $labelArray = $travelAgencyResource->all("rdfs:label");
            foreach ($labelArray as $label) {
                if ($label->getLang() == "it") {
                    $newTravelAgency->setName($label->getValue());
                    break;
                }
            }
            $typesarray = $travelAgencyResource->all("rdf:type");
            if ($typesarray != null) {
                $tempTypes = array();

                $cnt = 0;
                foreach ($typesarray as $type) {
                    $tempTypes[$cnt] = $type->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempTypes) > 0 ? $newTravelAgency->setTypes($tempTypes) : $newTravelAgency->setTypes(null);
            }

            $newTravelAgency->setProvenance(($p = $travelAgencyResource->get("<http://purl.org/dc/elements/1.1/provenance>")) != null ? $p->getValue() : null);


            $emailarray = $travelAgencyResource->all("<http://schema.org/email>");
            if ($emailarray != null) {
                $tempEmail = array();
                $newTravelAgency->setEmail(array());
                $cnt = 0;
                foreach ($emailarray as $email) {
                    $tempEmail[$cnt] = $email->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempEmail) > 0 ? $newTravelAgency->setEmail($tempEmail) : $newTravelAgency->setEmail(null);
            }

            $telephonearray = $travelAgencyResource->all("<http://schema.org/telephone>");
            if ($telephonearray != null) {
                $tempTelephone = array();
                $cnt = 0;
                foreach ($telephonearray as $telephone) {
                    $tempTelephone[$cnt] = $telephone->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempTelephone) > 0 ? $newTravelAgency->setTelephone($tempTelephone) : $newTravelAgency->setTelephone(null);
            }

            $faxarray = $travelAgencyResource->all("<http://schema.org/faxNumber>");
            if ($faxarray != null) {
                $tempFax = array();
                $newTravelAgency->setFax(array());
                $cnt = 0;
                foreach ($faxarray as $fax) {
                    $tempFax[$cnt] = $fax->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempFax) > 0 ? $newTravelAgency->setFax($tempFax) : $newTravelAgency->setFax(null);
            }
            $newTravelAgency->setResourceOriginUrl(($p = $travelAgencyResource->get("<http://dati.umbria.it/tourism/ontology/url_risorsa>")) != null ? $p->getValue() : null);


            if ($isAlreadyPersisted && ($oldAddress = $newTravelAgency->getAddress()) != null) {
                $this->em->remove($oldAddress);
                $newTravelAgency->setAddress(null);
            }



            /**@var EasyRdf_Resource $addressResource */
            $addressResource = $travelAgencyResource->get("<http://dati.umbria.it/tourism/ontology/indirizzo>");
            if ($addressResource != null) {
                $addressObject = new Address();
                $addressObject->setPostalCode(($p = $addressResource->get("<http://schema.org/postalCode>")) != null ? $p->getValue() : null);
                $addressObject->setIstat(($p = $addressResource->get("<http://dbpedia.org/ontology/istat>")) != null ? $p->getValue() : null);
                $addressObject->setAddressLocality(($p = $addressResource->get("<http://schema.org/addressLocality>")) != null ? $p->getValue() : null);
                $addressObject->setAddressRegion(($p = $addressResource->get("<http://schema.org/addressRegion>")) != null ? $p->getValue() : null);
                $addressObject->setStreetAddress(($p = $addressResource->get("<http://schema.org/streetAddress>")) != null ? $p->getValue() : null);
                $newTravelAgency->setAddress($addressObject);

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
                        $newTravelAgency->setLat($lat);
                        $newTravelAgency->setLng($lng);
                    }
                }

            }

            if (!$isAlreadyPersisted) {
                $this->em->persist($newTravelAgency);
            }

            $this->em->flush();
        }
    }

    private function deleteOldEntities($olderThan)
    {
        $this->travelAgencyRepo->removeLastUpdatedBefore($olderThan);

    }

}
