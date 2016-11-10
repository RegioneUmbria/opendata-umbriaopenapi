<?php

namespace Umbria\OpenApiBundle\Controller\Tourism;

use DateTime;
use Doctrine\ORM\EntityManager;
use EasyRdf_Graph;
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
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Profession;
use Umbria\OpenApiBundle\Entity\Tourism\Setting;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\ProfessionRepository;
use Umbria\OpenApiBundle\Serializer\View\EntityResponse;
use Umbria\OpenApiBundle\Service\TourismEntityUpdater;
use Umbria\OpenApiBundle\Service\FilterBag;

/**
 * Class ProfessionController
 * @package Umbria\OpenApiBundle\Controller\Tourism
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class ProfessionController extends BaseController
{
    const DEFAULT_PAGE_SIZE = 100;
    const DATASET_TOURISM_PROFESSION = 'tourism-profession';

    private $filterBag;
    private $paginator;

    private $em;
    /**@var ProfessionRepository $professionRepo */
    private $professionRepo;
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
        $this->professionRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Profession');
        $this->settingsRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\Setting');
        $this->em = $em;
    }

    /**
     * @Rest\Options(pattern="/open-api/tourism-profession")
     */
    public function optionsTourismProfessionAction()
    {
        $response = new Response();

        $response->setContent(json_encode(array(
            'Allow' => 'GET,OPTIONS',
            'GET' => array(
                'description' => 'A get request for profession',
            ),
        )));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'json');

        return $response;
    }

    /**
     * @Rest\Get(pattern="/open-api/tourism-profession")
     *
     * @param Request $request
     *
     * @return View
     *
     * @internal param Request $request
     *
     * @ApiDoc\ApiDoc(
     *  section = "Tourism",
     *  description = "Lista professioni turistiche regione Umbria",
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
    public function getTourismProfessionListAction(Request $request)
    {
        $daysToOld = $this->container->getParameter('profession_days_to_old');

        $filters = $this->filterBag->getFilterBag($request);
        $offset = $filters->has('start') ? $filters->get('start') : 0;
        $limit = $filters->has('limit') ? $filters->get('limit') : self::DEFAULT_PAGE_SIZE;
        if ($limit == 0) {
            $limit = self::DEFAULT_PAGE_SIZE;
        }
        $page = floor($offset / $limit) + 1;

        /** @var Setting $setting */
        $setting = $this->settingsRepo->findOneBy(array('datasetName' => self::DATASET_TOURISM_PROFESSION));
        if ($setting != null) {
            $diff = $setting->getUpdatedAt()->diff(new DateTime('now'));
            if ($diff->days >= $daysToOld) {
                $setting->setDatasetName(self::DATASET_TOURISM_PROFESSION);
                $setting->setUpdatedAtValue();
                $this->em->persist($setting);
                $this->em->flush();

                $this->updateEntities();
            }
        } else {
            $setting = new Setting();
            $setting->setDatasetName(self::DATASET_TOURISM_PROFESSION);
            $setting->setUpdatedAtValue();
            $this->em->persist($setting);
            $this->em->flush();

            $this->updateEntities();
        }

        $builder = $this->em->createQueryBuilder()
            ->select('a')
            ->from('UmbriaOpenApiBundle:Tourism\GraphsEntities\Profession', 'a');

        /** @var AbstractPagination $resultsPagination */
        $resultsPagination = $this->paginator->paginate($builder, $page, $limit);
        /** @var AbstractPagination $countPagination */
        $countPagination = $this->paginator->paginate($builder, 1, 1);

        $view = new View(new EntityResponse($resultsPagination->getItems(), count($resultsPagination), $countPagination->getTotalItemCount()));

        return $view;
    }

    private function updateEntities()
    {

        $this->graph = EasyRdf_Graph::newAndLoad($this->container->getParameter('profession_graph_url'));
        /**@var EasyRdf_Resource[] $resources */
        $resources = $this->graph->resources();
        foreach ($resources as $resource) {
            $resourceTypeArray = $resource->all("rdf:type");
            if ($resourceTypeArray != null) {
                foreach ($resourceTypeArray as $resourceType) {
                    if (trim($resourceType) == "http://dati.umbria.it/turismo/ontology/professioni_turistiche") {
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
     * @param \EasyRdf_Resource $professionResource
     */
    private function createOrUpdateEntity($professionResource)
    {
        /** @var Profession $newProfession */
        $newProfession = null;
        $uri = $professionResource->getUri();
        if ($uri != null) {
            $oldProfession = $this->professionRepo->find($uri);
            $isAlreadyPersisted = $oldProfession != null;
            if ($isAlreadyPersisted) {
                $newProfession = $oldProfession;
            } else {
                $newProfession = new Profession();
            }
            $newProfession->setUri($uri);
            $newProfession->setLastUpdateAt(new \DateTime('now'));
            $newProfession->setFirstName(($p = $professionResource->get("<http://xmlns.com/foaf/0.1/firstName>")) != null ? $p->getValue() : null);
            $newProfession->setLastName(($p = $professionResource->get("<http://xmlns.com/foaf/0.1/lastName>")) != null ? $p->getValue() : null);


            $typesarray = $professionResource->all("rdf:type");
            if ($typesarray != null) {
                $tempTypes = array();

                $cnt = 0;
                foreach ($typesarray as $type) {
                    $tempTypes[$cnt] = $type->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempTypes) > 0 ? $newProfession->setTypes($tempTypes) : $newProfession->setTypes(null);
            }

            $newProfession->setProvenance(($p = $professionResource->get("<http://purl.org/dc/elements/1.1/provenance>")) != null ? $p->getValue() : null);
            $newProfession->setResourceOriginUrl(($p = $professionResource->get("<http://dati.umbria.it/tourism/ontology/url_risorsa>")) != null ? $p->getValue() : null);


            $telephonearray = $professionResource->all("<http://schema.org/telephone>");
            if ($telephonearray != null) {
                $tempTelephone = array();
                $cnt = 0;
                foreach ($telephonearray as $telephone) {
                    $tempTelephone[$cnt] = $telephone->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempTelephone) > 0 ? $newProfession->setTelephone($tempTelephone) : $newProfession->setTelephone(null);
            }

            $faxarray = $professionResource->all("<http://schema.org/faxNumber>");
            if ($faxarray != null) {
                $tempFax = array();
                $newProfession->setFax(array());
                $cnt = 0;
                foreach ($faxarray as $fax) {
                    $tempFax[$cnt] = $fax->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempFax) > 0 ? $newProfession->setFax($tempFax) : $newProfession->setFax(null);
            }

            $emailarray = $professionResource->all("<http://schema.org/email>");
            if ($emailarray != null) {
                $tempEmail = array();
                $newProfession->setEmail(array());
                $cnt = 0;
                foreach ($emailarray as $email) {
                    $tempEmail[$cnt] = $email->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempEmail) > 0 ? $newProfession->setEmail($tempEmail) : $newProfession->setEmail(null);
            }

            $spokenLanguagearray = $professionResource->all("<http://dati.umbria.it/tourism/ontology/lingua_parlata>");
            if ($spokenLanguagearray != null) {
                $tempSpokenLanguage = array();
                $newProfession->setSpokenLanguage(array());
                $cnt = 0;
                foreach ($spokenLanguagearray as $spokenLanguage) {
                    $tempSpokenLanguage[$cnt] = $spokenLanguage->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempSpokenLanguage) > 0 ? $newProfession->setSpokenLanguage($tempSpokenLanguage) : $newProfession->setSpokenLanguage(null);
            }

            $specializationarray = $professionResource->all("<http://dati.umbria.it/tourism/ontology/specializzazione>");
            if ($specializationarray != null) {
                $tempSpecialization = array();
                $newProfession->setSpecialization(array());
                $cnt = 0;
                foreach ($specializationarray as $specialization) {
                    $tempSpecialization[$cnt] = $specialization->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempSpecialization) > 0 ? $newProfession->setSpecialization($tempSpecialization) : $newProfession->setSpecialization(null);
            }


            if ($isAlreadyPersisted && ($oldAddress = $newProfession->getAddress()) != null) {
                $this->em->remove($oldAddress);
                $newProfession->setAddress(null);
            }

            /**@var EasyRdf_Resource $addressResource */
            $addressResource = $professionResource->get("<http://dati.umbria.it/tourism/ontology/indirizzo>");
            if ($addressResource != null) {
                $addressObject = new Address();
                $addressObject->setPostalCode(($p = $addressResource->get("<http://schema.org/postalCode>")) != null ? $p->getValue() : null);
                $addressObject->setIstat(($p = $addressResource->get("<http://dbpedia.org/ontology/istat>")) != null ? $p->getValue() : null);
                $addressObject->setAddressLocality(($p = $addressResource->get("<http://schema.org/addressLocality>")) != null ? $p->getValue() : null);
                $addressObject->setAddressRegion(($p = $addressResource->get("<http://schema.org/addressRegion>")) != null ? $p->getValue() : null);
                $addressObject->setStreetAddress(($p = $addressResource->get("<http://schema.org/streetAddress>")) != null ? $p->getValue() : null);
                $newProfession->setAddress($addressObject);

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
                        $newProfession->setLat($lat);
                        $newProfession->setLng($lng);
                    }
                }

            }

            if (!$isAlreadyPersisted) {
                $this->em->persist($newProfession);
            }

            $this->em->flush();
        }
    }

    private function deleteOldEntities($olderThan)
    {
        $this->professionRepo->removeLastUpdatedBefore($olderThan);

    }

}
