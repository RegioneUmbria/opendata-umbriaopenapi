<?php

namespace Umbria\OpenApiBundle\Controller\Tourism;

use DateTime;
use Doctrine\ORM\EntityManager;
use EasyRdf_Graph;
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
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\ImpiantoSportivo;
use Umbria\OpenApiBundle\Entity\Tourism\Setting;
use Umbria\OpenApiBundle\Entity\Type;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\ImpiantoSportivoRepository;
use Umbria\OpenApiBundle\Serializer\View\EntityResponse;
use Umbria\OpenApiBundle\Service\FilterBag;


/**
 * Class ImpiantoSportivoController
 * @package Umbria\OpenApiBundle\Controller\Tourism
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class ImpiantoSportivoController
    extends BaseController
{
    const DEFAULT_PAGE_SIZE = 100;
    const DATASET_TOURISM_IMPIANTI_SPORTIVI = 'tourism-impianti-sportivi';

    private $filterBag;
    private $paginator;

    private $em;
    /**@var ImpiantoSportivoRepository $impiantoSportivoRepo */
    private $impiantoSportivoRepo;
    private $settingsRepo;
    private $typeRepo;

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
        $this->impiantoSportivoRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\ImpiantoSportivo');
        $this->settingsRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\Setting');
        $this->typeRepo = $em->getRepository('UmbriaOpenApiBundle:Type');
        $this->em = $em;
    }

    /**
     * @Rest\Options(pattern="/open-api/tourism-sports-facility")
     */
    public function optionsTourismImpiantoSportivoAction()
    {
        $response = new Response();

        $response->setContent(json_encode(array(
            'Allow' => 'GET,OPTIONS',
            'GET' => array(
                'description' => 'A get request for impiantoSportivo',
            ),
        )));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'json');

        return $response;
    }

    /**
     * @Rest\Get(pattern="/open-api/tourism-sports-facility")
     *
     * @param Request $request
     *
     * @return View
     *
     * @internal param Request $request
     *
     * @ApiDoc\ApiDoc(
     *  section = "Tourism",
     *  description = "Lista impianti sportivi regione Umbria",
     *  tags = {
     *      "beta"
     *  },
     *  parameters={
     *      {"name"="start", "dataType"="integer", "required"=false, "description"="Indice elemento iniziale"},
     *      {"name"="limit", "dataType"="integer", "required"=false, "description"="Numero di elementi"},
     *      {"name"="label_like", "dataType"="string", "required"=false, "description"="Condizione 'LIKE' su denominazione"},
     *      {"name"="descriptions_like", "dataType"="string", "required"=false, "description"="Condizione 'LIKE' sulle descrizioni"},
     *      {"name"="category_like", "dataType"="string", "required"=false, "description"="Condizione 'LIKE' sulle categorie"},
     *      {"name"="lat_max", "dataType"="number", "required"=false, "description"="Latitudine massima"},
     *      {"name"="lat_min", "dataType"="number", "required"=false, "description"="Latitudine minima"},
     *      {"name"="lng_max", "dataType"="number", "required"=false, "description"="Longitudine massima"},
     *      {"name"="lng_min", "dataType"="number", "required"=false, "description"="Longitudine minima"}
     *  },
     *
     *  statusCodes={
     *      200="Returned when successful"
     *  }
     * )
     */
    public function getTourismImpiantoSportivoListAction(Request $request)
    {
        $daysToOld = $this->container->getParameter('impianto_sportivo_days_to_old');
        $filters = $this->filterBag->getFilterBag($request);
        $offset = $filters->has('start') ? $filters->get('start') : 0;
        $limit = $filters->has('limit') ? $filters->get('limit') : self::DEFAULT_PAGE_SIZE;
        $labelLike = $filters->has('label_like') ? $filters->get('label_like') : null;
        $descriptionLike = $filters->has('descriptions_like') ? $filters->get('descriptions_like') : null;
        $categoryLike = $filters->has('category_like') ? $filters->get('category_like') : null;
        $latMax = $filters->has('lat_max') && $filters->get('lat_max') ? floatval($filters->get('lat_max')) : null;
        $latMin = $filters->has('lat_min') && $filters->get('lat_min') ? floatval($filters->get('lat_min')) : null;
        $lngMax = $filters->has('lng_max') && $filters->get('lng_max') ? floatval($filters->get('lng_max')) : null;
        $lngMin = $filters->has('lng_min') && $filters->get('lng_min') ? floatval($filters->get('lng_min')) : null;
        if ($limit == 0) {
            $limit = self::DEFAULT_PAGE_SIZE;
        }
        $page = floor($offset / $limit) + 1;

        /** @var Setting $setting */
        $setting = $this->settingsRepo->findOneBy(array('datasetName' => self::DATASET_TOURISM_IMPIANTI_SPORTIVI));
        if ($setting != null) {
            $diff = $setting->getUpdatedAt()->diff(new DateTime('now'));
            if ($diff->days >= $daysToOld) {
                $setting->setDatasetName(self::DATASET_TOURISM_IMPIANTI_SPORTIVI);
                $setting->setUpdatedAtValue();
                $this->em->persist($setting);
                $this->em->flush();
                $this->updateEntities();
            }
        } else {
            $setting = new Setting();
            $setting->setDatasetName(self::DATASET_TOURISM_IMPIANTI_SPORTIVI);
            $setting->setUpdatedAtValue();
            $this->em->persist($setting);
            $this->em->flush();
            $this->updateEntities();
        }
        $qb = $this->em->createQueryBuilder();
        $builder = $qb
            ->select('a')
            ->from('UmbriaOpenApiBundle:Tourism\GraphsEntities\ImpiantoSportivo', 'a');


        if ($labelLike != null) {
            $builder = $qb
                ->andWhere($qb->expr()->like('a.name', '?2'))
                ->setParameter(2, $labelLike);
        }

        if ($descriptionLike != null) {
            $builder = $qb
                ->innerJoin('a.descriptions', 'd')
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like('d.title', '?1'),
                        $qb->expr()->like('d.text', '?1'),
                        $qb->expr()->like('a.comment', '?1')
                    )
                )
                ->setParameter(1, $descriptionLike);

        }

        if ($categoryLike != null) {
            $builder = $qb
                ->leftJoin('a.categories', 'cat')
                ->andWhere(
                    $qb->expr()->like('cat.name', ':categoryLike')
                )
                ->setParameter("categoryLike", $categoryLike);

        }

        if ($latMax != null ||
            $latMin != null ||
            $lngMax != null ||
            $lngMin != null
        ) {
            $builder = $qb->join('a.address', 'address');
            if ($latMax != null) {
                $builder =
                    $qb->andWhere(
                        $qb->expr()->lte("address.lat", ':latMax'),
                        $qb->expr()->isNotNull("address.lat"),
                        $qb->expr()->gt("address.lat", ':empty')
                    )
                        ->setParameter('latMax', $latMax)
                        ->setParameter('empty', '0');
            }
            if ($latMin != null) {
                $builder =
                    $qb->andWhere(
                        $qb->expr()->gte("address.lat", ':latMin'),
                        $qb->expr()->isNotNull("address.lat"),
                        $qb->expr()->gt("address.lat", ":empty")
                    )
                        ->setParameter('latMin', $latMin)
                        ->setParameter('empty', '0');
            }
            if ($lngMax != null) {
                $builder =
                    $qb->andWhere(
                        $qb->expr()->lte("address.lng", ':lngMax'),
                        $qb->expr()->isNotNull("address.lng"),
                        $qb->expr()->gt("address.lng", ":empty")
                    )
                        ->setParameter('lngMax', $lngMax)
                        ->setParameter('empty', '0');
            }
            if ($lngMin != null) {
                $builder =
                    $qb->andWhere(
                        $qb->expr()->gte("address.lng", ':lngMin'),
                        $qb->expr()->isNotNull("address.lng"),
                        $qb->expr()->gt("address.lng", ":empty")
                    )
                        ->setParameter('lngMin', $lngMin)
                        ->setParameter('empty', '0');
            }
        }

        /** @var AbstractPagination $resultsPagination */
        $resultsPagination = $this->paginator->paginate($builder, $page, $limit);
        /** @var AbstractPagination $countPagination */
        $countPagination = $this->paginator->paginate($builder, 1, 1);


        $view = new View(new EntityResponse($resultsPagination->getItems(), count($resultsPagination), $countPagination->getTotalItemCount()));

        return $view;
    }

    private function updateEntities()
    {

        $this->graph = EasyRdf_Graph::newAndLoad($this->container->getParameter('impianto_sportivo_graph_url'));
        /**@var EasyRdf_Resource[] $resources */
        $resources = $this->graph->resources();
        foreach ($resources as $resource) {
            $resourceTypeArray = $resource->all("rdf:type");
            if ($resourceTypeArray != null) {
                foreach ($resourceTypeArray as $resourceType) {
                    if (trim($resourceType) == "http://dbpedia.org/ontology/sportFacility") {
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
     * @param \EasyRdf_Resource $impiantoSportivoResource
     */
    private function createOrUpdateEntity($impiantoSportivoResource)
    {
        /** @var ImpiantoSportivo $newImpiantoSportivo */
        $newImpiantoSportivo = null;
        $uri = $impiantoSportivoResource->getUri();
        if ($uri != null) {
            $oldImpiantoSportivo = $this->impiantoSportivoRepo->find($uri);
            $isAlreadyPersisted = $oldImpiantoSportivo != null;
            if ($isAlreadyPersisted) {
                $newImpiantoSportivo = $oldImpiantoSportivo;
            } else {
                $newImpiantoSportivo = new ImpiantoSportivo();
            }
            $newImpiantoSportivo->setUri($uri);
            $newImpiantoSportivo->setLastUpdateAt(new \DateTime('now'));
            $newImpiantoSportivo->setName(($p = $impiantoSportivoResource->get("rdfs:label")) != null ? $p->getValue() : null);


            /**@var EasyRdf_Resource[] $typesarray */
            $typesarray = $impiantoSportivoResource->all("rdf:type");
            if ($typesarray != null) {
                /**@var Type[] $tempTypes */
                $tempTypes = array();
                $cnt = 0;
                foreach ($typesarray as $type) {
                    $oldType = $this->typeRepo->find($type->getUri());
                    if ($oldType != null) {
                        $tempTypes[$cnt] = $oldType;
                    } else {
                        $tempTypes[$cnt] = new Type();
                        $tempTypes[$cnt]->setUri($type->getUri());
                        $tempTypes[$cnt]->setName(($p = $type->get("rdfs:label")) != null ? $p->getValue() : null);
                        $tempTypes[$cnt]->setComment(($p = $type->get("<http://www.w3.org/2000/01/rdf-schema#comment>")) != null ? $p->getValue() : null);
                    }
                    $cnt++;
                }
                count($tempTypes) > 0 ? $newImpiantoSportivo->setTypes($tempTypes) : $newImpiantoSportivo->setTypes(null);
            }

            $newImpiantoSportivo->setProvenance(($p = $impiantoSportivoResource->get("<http://purl.org/dc/elements/1.1/provenance>")) != null ? $p->getValue() : null);

            /**@var EasyRdf_Resource[] $sportarray */
            $sportarray = $impiantoSportivoResource->all("<http://schema.org/sport>");
            if ($sportarray != null) {
                $tempSport = array();
                $cnt = 0;
                foreach ($sportarray as $sport) {
                    $tempSport[$cnt] = $sport->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempSport) > 0 ? $newImpiantoSportivo->setSport($tempSport) : $newImpiantoSportivo->setSport(null);
            }

            $newImpiantoSportivo->setMunicipality(($p = $impiantoSportivoResource->get("<http://dbpedia.org/ontology/municipality>")) != null ? $p->getValue() : null);
            $newImpiantoSportivo->setPublicTransport(($p = $impiantoSportivoResource->get("<http://dati.umbria.it/base/ontology/trasportoPubblico>")) != null ? $p->getValue() : null);
            $newImpiantoSportivo->setParkings(($p = $impiantoSportivoResource->get("<http://dati.umbria.it/base/ontology/numeroPostiMacchina>")) != null ? $p->getValue() : null);
            $newImpiantoSportivo->setDisabledAccess(($p = $impiantoSportivoResource->get("<http://dati.umbria.it/base/ontology/accessoDisabili>")) != null && strtoupper($p->getValue()) == "TRUE" ? 1 : 0);
            $newImpiantoSportivo->setEmployees(($p = $impiantoSportivoResource->get("<http://dati.umbria.it/base/ontology/accessoDisabili>")) != null ? $p->getValue() : null);


            if ($isAlreadyPersisted && ($oldAddress = $newImpiantoSportivo->getAddress()) != null) {
                $this->em->remove($oldAddress);
                $newImpiantoSportivo->setAddress(null);
            }

            /**@var EasyRdf_Resource $addressResource */
            $addressResource = $impiantoSportivoResource->get("<http://schema.org/address>");
            if ($addressResource != null) {
                $addressObject = new Address();
                $addressObject->setPostalCode(($p = $addressResource->get("<http://schema.org/postalCode>")) != null ? $p->getValue() : null);
                $addressObject->setIstat(($p = $addressResource->get("<http://dbpedia.org/ontology/istat>")) != null ? $p->getValue() : null);
                $addressObject->setAddressLocality(($p = $addressResource->get("<http://schema.org/addressLocality>")) != null ? $p->getValue() : null);
                $addressObject->setAddressRegion(($p = $addressResource->get("<http://schema.org/addressRegion>")) != null ? $p->getValue() : null);
                $addressObject->setStreetAddress(($p = $addressResource->get("<http://schema.org/streetAddress>")) != null ? $p->getValue() : null);
                $addressObject->setLat(($p = $addressResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#lat>")) != null ? $p->getValue() : null);
                $addressObject->setLng(($p = $addressResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#long>")) != null ? $p->getValue() : null);
                $newImpiantoSportivo->setAddress($addressObject);
            }

            if (!$isAlreadyPersisted) {
                $this->em->persist($newImpiantoSportivo);
            }

            $this->em->flush();
        }
    }

    private function deleteOldEntities($olderThan)
    {
        $this->impiantoSportivoRepo->removeLastUpdatedBefore($olderThan);

    }

}