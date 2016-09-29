<?php

namespace Umbria\OpenApiBundle\Controller\Tourism;

use DateTime;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\Paginator;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Event;
use Umbria\OpenApiBundle\Entity\Tourism\Setting;
use Umbria\OpenApiBundle\Serializer\View\EntityResponse;
use Umbria\OpenApiBundle\Service\TourismEntityUpdater;
use Umbria\OpenApiBundle\Service\FilterBag;
use Exception;

/**
 * Class EventController
 * @package Umbria\OpenApiBundle\Controller\Tourism
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class EventController extends FOSRestController
{
    const DEFAULT_PAGE_SIZE = 100;
    const DATASET_TOURISM_EVENT = 'tourism-event';

    private $filterBag;
    private $paginator;

    private $em;
    private $eventRepo;
    private $settingsRepo;

    private $graph;
    private $sameAsGraph;

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
        $this->filterBag = $filterBag;
        $this->paginator = $paginator;
        $this->eventRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Event');
        $this->settingsRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\Setting');
        $this->em = $em;
    }

    /**
     * @Rest\Options(pattern="/open-api/tourism-event")
     */
    public function optionsTourismProposalAction()
    {
        $response = new Response();

        $response->setContent(json_encode(array(
            'Allow' => 'GET,OPTIONS',
            'GET' => array(
                'description' => 'A get request for events',
            ),
        )));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'json');

        return $response;
    }

    /**
     * @Rest\Get(pattern="/open-api/tourism-event")
     *
     * @param Request $request
     *
     * @return View
     *
     * @internal param Request $request
     *
     * @ApiDoc\ApiDoc(
     *  section = "Tourism",
     *  description = "Lista eventi regione Umbria",
     *  tags = {
     *      "beta"
     *  },
     *  parameters={
     *      {"name"="start", "dataType"="integer", "required"=false, "description"="Elemento da cui partire"},
     *      {"name"="limit", "dataType"="integer", "required"=false, "description"="Numero di elementi restituiti"},
     *      {"name"="title_like", "dataType"="string", "required"=false, "description"="Condizione 'LIKE' su denominazione"},
     *      {"name"="descriptions_like", "dataType"="string", "required"=false, "description"="Condizione 'LIKE' sulle descrizioni"},
     *      {"name"="category_like", "dataType"="string", "required"=false, "description"="Condizione 'LIKE' sulle categorie"},
     *      {"name"="lat_max", "dataType"="number", "required"=false, "description"="Latitudine massima"},
     *      {"name"="lat_min", "dataType"="number", "required"=false, "description"="Latitudine minima"},
     *      {"name"="lng_max", "dataType"="number", "required"=false, "description"="Longitudine massima"},
     *      {"name"="lng_min", "dataType"="number", "required"=false, "description"="Longitudine minima"}
     *  },
     *  statusCodes={
     *      200="Returned when successful"
     *  }
     * )
     */
    public function getTourismEventListAction(Request $request)
    {
        $daysToOld = $this->container->getParameter('event_days_to_old');

        $filters = $this->filterBag->getFilterBag($request);
        $offset = $filters->has('start') ? $filters->get('start') : 0;
        $limit = $filters->has('limit') ? $filters->get('limit') : self::DEFAULT_PAGE_SIZE;
        $labelLike = $filters->has('title_like') ? $filters->get('title_like') : null;
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
        $setting = $this->settingsRepo->findOneBy(array('datasetName' => self::DATASET_TOURISM_EVENT));
        if ($setting != null) {
            $diff = $setting->getUpdatedAt()->diff(new DateTime('now'));
            // controllo intervallo di tempo da ultima estrazione
            if ($diff->days >= $daysToOld) {
                $setting->setDatasetName(self::DATASET_TOURISM_EVENT);
                $setting->setUpdatedAtValue();
                $this->em->persist($setting);
                $this->em->flush();

                $this->updateEntities();
            }
        } else {
            $setting = new Setting();
            $setting->setDatasetName(self::DATASET_TOURISM_EVENT);
            $setting->setUpdatedAtValue();
            $this->em->persist($setting);
            $this->em->flush();

            $this->updateEntities();
        }

        $qb = $this->em->createQueryBuilder();
        $builder = $qb
            ->select('a')
            ->from('UmbriaOpenApiBundle:Tourism\GraphsEntities\Event', 'a');

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
                        $qb->expr()->like('d.text', '?1'),
                        $qb->expr()->like('d.title', '?1'),
                        $qb->expr()->like('a.comment', '?1')
                    )
                )
                ->setParameter(1, $descriptionLike);

        }

        if ($categoryLike != null) {
            $builder = $qb
                ->andWhere(
                    $qb->expr()->like('a.categories', ':categoryLike')
                )
                ->setParameter("categoryLike", $categoryLike);

        }

        if ($latMax != null ||
            $latMin != null ||
            $lngMax != null ||
            $lngMin != null
        ) {
            /* $builder = $qb
                 ->innerJoin('a.coordinate', 'c');*/
            if ($latMax != null) {
                $builder =
                    $qb->andWhere(
                        $qb->expr()->lte("a.lat", ':latMax'),
                        $qb->expr()->isNotNull("a.lat"),
                        $qb->expr()->gt("a.lat", ':empty')
                    )
                        ->setParameter('latMax', $latMax)
                        ->setParameter('empty', '0');
            }
            if ($latMin != null) {
                $builder =
                    $qb->andWhere(
                        $qb->expr()->gte("a.lat", ':latMin'),
                        $qb->expr()->isNotNull("a.lat"),
                        $qb->expr()->gt("a.lat", ":empty")
                    )
                        ->setParameter('latMin', $latMin)
                        ->setParameter('empty', '0');
            }
            if ($lngMax != null) {
                $builder =
                    $qb->andWhere(
                        $qb->expr()->lte("a.lng", ':lngMax'),
                        $qb->expr()->isNotNull("a.lng"),
                        $qb->expr()->gt("a.longitude", ":empty")
                    )
                        ->setParameter('lngMax', $lngMax)
                        ->setParameter('empty', '0');
            }
            if ($lngMin != null) {
                $builder =
                    $qb->andWhere(
                        $qb->expr()->gte("a.lng", ':lngMin'),
                        $qb->expr()->isNotNull("a.lng"),
                        $qb->expr()->gt("a.lng", ":empty")
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
        $cnt = 0;

        $this->graph = EasyRdf_Graph::newAndLoad($this->container->getParameter('event_graph_url'));
        $resources = $this->graph->resources();
        foreach ($resources as $resource) {
            if ($cnt > 10) break;
            $resourceTypeArray = $resource->all("rdf:type");
            if ($resourceTypeArray != null) {
                foreach ($resourceTypeArray as $resourceType) {
                    if (trim($resourceType) == "http://dati.umbria.it/tourism/ontology/turismo_consorzi") {
                        $this->createOrUpdateEntity($resource);
                        $cnt++;
                        break;
                    }
                }
            }

        }
        $now = new \DateTime();
        $this->deleteOldEntities($now);
    }

    /**
     * @param \EasyRdf_Resource $eventResource
     */
    private function createOrUpdateEntity($eventResource)
    {
        /** @var Event $newEvent */
        $newEvent = null;
        $uri = $eventResource->getUri();
        if ($uri != null) {
            $oldEvent = $this->eventRepo->find($uri);
            $isAlreadyPersisted = $oldEvent != null;
            if ($isAlreadyPersisted) {
                $newEvent = $oldEvent;
            } else {
                $newEvent = new Event();
            }
            $newEvent->setUri($uri);
            $newEvent->setLastUpdateAt(new \DateTime('now'));
            $newEvent->setName(($p = $eventResource->get("rdfs:label")) != null ? $p->getValue() : null);
            $newEvent->setComment(($p = $eventResource->get("<http://www.w3.org/2000/01/rdf-schema#comment>")) != null ? $p->getValue() : null);
            $newEvent->setLat(($p = $eventResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#lat>")) != null ? (float)$p->getValue() : null);
            $newEvent->setLng(($p = $eventResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#long>")) != null ? (float)$p->getValue() : null);
            $startDate = $eventResource->get("<http://schema.org/start_date>");
            if ($startDate != null) {
                $startDateObj = new DateTime(date_format(date_create("$startDate"), "Y-m-d"));
                $newEvent->setStartDate($startDateObj);
            }
            $endDate = $eventResource->get("<http://schema.org/end_date>");
            if ($endDate != null) {
                $endDateObj = new DateTime(date_format(date_create("$endDate"), "Y-m-d"));
                $newEvent->setEndDate($endDateObj);
            }

            $categoriesarray = $eventResource->all("<http://dati.umbria.it/turismo/ontology/categoria>");
            if ($categoriesarray != null) {
                $tempCategories = array();

                $cnt = 0;
                foreach ($categoriesarray as $category) {
                    $tempCategories[$cnt] = $category->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempCategories) > 0 ? $newEvent->setCategories($tempCategories) : $newEvent->setCategories(null);
            }

            $typesarray = $eventResource->all("rdf:type");
            if ($typesarray != null) {
                $tempTypes = array();

                $cnt = 0;
                foreach ($typesarray as $type) {
                    $tempTypes[$cnt] = $type->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempTypes) > 0 ? $newEvent->setTypes($tempTypes) : $newEvent->setTypes(null);
            }

            $newEvent->setProvenance(($p = $eventResource->get("<http://purl.org/dc/elements/1.1/provenance>")) != null ? $p->getValue() : null);
            $newEvent->setLanguage(($p = $eventResource->get("<http://purl.org/dc/elements/1.1/language>")) != null ? $p->getUri() : null);

            $imagearray1 = $eventResource->all("<http://dati.umbria.it/turismo/ontology/immagine_copertina>");
            $imagearray2 = $eventResource->all("<http://dati.umbria.it/turismo/ontology/immagine_spalla_destra>");
            $imagearray = array_merge($imagearray1, $imagearray2);
            if ($imagearray != null) {
                $tempImage = array();
                $newEvent->setImages(array());
                $cnt = 0;
                foreach ($imagearray as $image) {
                    $tempImage[$cnt] = $image->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempImage) > 0 ? $newEvent->setImages($tempImage) : $newEvent->setImages(null);
            }


            $newEvent->setResourceOriginUrl(($p = $eventResource->get("<http://xmlns.com/foaf/0.1/homepage>")) != null ? $p->getValue() : null);

            if ($isAlreadyPersisted && ($oldAddress = $newEvent->getAddress()) != null) {
                $this->em->remove($oldAddress);
                $newEvent->setAddress(null);
            }

            if (!$isAlreadyPersisted) {
                $this->em->persist($newEvent);
            }

            $this->em->flush();
        }
    }

    private function deleteOldEntities($olderThan)
    {
        $oldEvents = $this->eventRepo->removeLastUpdatedBefore($olderThan);


    }

}
