<?php


namespace Umbria\OpenApiBundle\Controller\Tourism;

use DateTime;
use Doctrine\ORM\EntityManager;
use EasyRdf_Graph;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\Paginator;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Attractor;
use Umbria\OpenApiBundle\Entity\Tourism\Setting;
use Umbria\OpenApiBundle\Serializer\View\EntityResponse;
use Umbria\OpenApiBundle\Service\TourismEntityUpdater;
use Umbria\OpenApiBundle\Service\FilterBag;

/*
 *  @author Lorenzo Franco Ranucci
 * */
class AttractorController extends FOSRestController
{
    const DEFAULT_PAGE_SIZE = 100;
    const DATASET_TOURISM_ATTRACTOR = 'tourism-attractor';
    private $graph;
    private $sameAsGraph;

    public function _construct()
    {
        $this->graph = EasyRdf_Graph::newAndLoad("http://odnt-srv01/dataset/54480509-bf69-47e1-b735-de5ddac001a2/resource/e27179f1-4020-4d8b-90cb-6ec4f47471f3/download/attrattoriitIT.zipattrattoriitIT.rdf");
        $this->sameAsGraph = EasyRdf_Graph::newAndLoad("http://odnt-srv01/dataset/54480509-bf69-47e1-b735-de5ddac001a2/resource/75826811-f908-4c19-854d-3dbcb12c5242/download/sameAsdbpediaresource.rdf");
    }


    /**
     * @var EntityManager
     * @DI\Inject("doctrine.orm.entity_manager")
     */
    public $em;


    /**
     * @var TourismEntityUpdater
     * @DI\Inject("umbria_open_api.tourism_entity_updater")
     */
    public $tourismEntityUpdater;

    /**
     * @var FilterBag
     * @DI\Inject("umbria_open_api.filter_bag")
     */
    private $filterBag;

    /**
     * @var Paginator
     * @DI\Inject("knp_paginator")
     */
    private $paginator;

    /**
     * @Rest\Options(pattern="/open-api/tourism-attractor")
     */
    public function optionsTourismAttractorAction()
    {
        $response = new Response();

        $response->setContent(json_encode(array(
            'Allow' => 'GET,OPTIONS',
            'GET' => array(
                'description' => 'A get request for attractors',
            ),
        )));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'json');

        return $response;
    }

    /**
     * @Rest\Get(pattern="/open-api/tourism-attractor")
     *
     * @param Request $request
     *
     * @return View
     *
     * @internal param Request $request
     *
     * @ApiDoc\ApiDoc(
     *  section = "Tourism",
     *  description = "Lista attrattori turistici regione Umbria",
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
     *
     *
     *  },
     *  statusCodes={
     *      200="Restituito in caso di successo"
     *  }
     * )
     */
    public function getTourismAttractorListAction(Request $request)
    {
        $daysToOld = $this->container->getParameter('attractor_days_to_old');
        $url = $this->container->getParameter('url_attractor');
        $urlSilkSameAs = $this->container->getParameter('url_attractor_silk');
        $urlSilkLocatedIn = $this->container->getParameter('url_attractor_silk_located_in');

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
        $setting = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\Setting')->findOneBy(array('datasetName' => self::DATASET_TOURISM_ATTRACTOR));
        if ($setting != null) {
            $diff = $setting->getUpdatedAt()->diff(new DateTime('now'));
            // controllo intervallo di tempo da ultima estrazione
            if ($diff->days >= $daysToOld) {
                $setting->setDatasetName(self::DATASET_TOURISM_ATTRACTOR);
                $setting->setUpdatedAtValue();
                $this->em->persist($setting);
                $this->em->flush();
                $this->updateEntities();
            }
        } else {
            $setting = new Setting();
            $setting->setDatasetName(self::DATASET_TOURISM_ATTRACTOR);
            $setting->setUpdatedAtValue();
            $this->em->persist($setting);
            $this->em->flush();
            $this->updateEntities();
        }
        $qb = $this->em->createQueryBuilder();
        $builder = $qb
            ->select('a')
            ->from('UmbriaOpenApiBundle:Tourism\GraphsEntities\Attractor', 'a');


        if ($labelLike != null) {
            $builder = $qb
                ->andWhere($qb->expr()->like('a.name', '?2'))
                ->setParameter(2, $labelLike);
        }

        if ($descriptionLike != null) {
            $builder = $qb
                ->innerJoin('a.descrizioni', 'd')
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like('d.shortDescription', '?1'),
                        $qb->expr()->like('a.comment', '?1')/*,
                        $qb->expr()->like('a.abstract', '?1'),
                        $qb->expr()->like('a.dbpediaAbstract', '?1')*/
                    )
                )
                ->setParameter(1, $descriptionLike);

        }

        /*TODO categories*/
        /*if ($categoryLike != null) {
            $builder = $qb
                ->leftJoin('a.categorie', 'cat')
                ->andWhere(
                    $qb->expr()->like('cat.cat', ':categoryLike')
                )
                ->setParameter("categoryLike", $categoryLike);

        }*/

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
        //$view->getSerializationContext()->setGroups($viewGroups);

        return $view;
    }

    private function updateEntities()
    {
        $cnt = 0;

        $this->graph = EasyRdf_Graph::newAndLoad("http://odnt-srv01/dataset/54480509-bf69-47e1-b735-de5ddac001a2/resource/e27179f1-4020-4d8b-90cb-6ec4f47471f3/download/attrattoriitIT.zipattrattoriitIT.rdf");
        $this->sameAsGraph = EasyRdf_Graph::newAndLoad("http://odnt-srv01/dataset/54480509-bf69-47e1-b735-de5ddac001a2/resource/75826811-f908-4c19-854d-3dbcb12c5242/download/sameAsdbpediaresource.rdf");
        $resources = $this->graph->resources();
        foreach ($resources as $resource) {
            if ($cnt > 10) break;
            //$propertyUris=$this->graph->propertyUris($resource);
            $resourceTypeArray = $resource->all("rdf:type");
            if ($resourceTypeArray != null) {
                foreach ($resourceTypeArray as $resourceType) {
                    if (trim($resourceType) == "http://linkedgeodata.org/ontology/Attraction") {//is attractor
                        $sameAsResource = null;
                        if ($this->sameAsGraph != null) {
                            $sameAsResource = $this->sameAsGraph->resource($resource->getUri());
                        }

                        $this->createOrUpdateEntity($resource, $sameAsResource);
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
     * @param \EasyRdf_Resource $attrattoriResource
     * @param \EasyRdf_Resource null $sameAsResource
     */
    private function createOrUpdateEntity($attrattoriResource, $sameAsResource = null)
    {
        $newAttractor = Attractor::load($attrattoriResource, $sameAsResource);
        if ($newAttractor != null) {
            $oldAttractor = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Attractor')->find($newAttractor->getUri());
            if ($oldAttractor != null) {
                $oldAttractor = $newAttractor;
            } else {
                $this->em->persist($newAttractor);
            }
            $this->em->flush();
        }
    }

    public function deleteOldEntities($olderThan)
    {
        $oldAttractors = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Attractor')
            ->removeLastUpdatedBefore($olderThan);
    }
}
