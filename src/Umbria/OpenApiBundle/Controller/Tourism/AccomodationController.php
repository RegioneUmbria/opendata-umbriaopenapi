<?php


namespace Umbria\OpenApiBundle\Controller\Tourism;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\Paginator;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Umbria\OpenApiBundle\Serializer\View\EntityResponse;
use Umbria\OpenApiBundle\Service\FilterBag;


/**
 * Class AccomodationController
 * @package Umbria\OpenApiBundle\Controller\Tourism
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class AccomodationController
{
    const DEFAULT_PAGE_SIZE = 100;
    private $filterBag;
    private $paginator;

    private $em;

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
        $this->em = $em;
    }

    /**
     * @Rest\Options(pattern="/open-api/tourism-accomodation")
     */
    public function optionsTourismAccomodationAction()
    {
        $response = new Response();

        $response->setContent(json_encode(array(
            'Allow' => 'GET,OPTIONS',
            'GET' => array(
                'description' => 'A get request for accomodations',
            ),
        )));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'json');

        return $response;
    }

    /**
     * @Rest\Get(pattern="/open-api/tourism-accomodation")
     *
     * @param Request $request
     *
     * @return View
     *
     * @internal param Request $request
     *
     * @ApiDoc\ApiDoc(
     *  section = "Tourism",
     *  description = "Lista strutture ricettive regione Umbria",
     *  tags = {
     *      "beta"
     *  },
     *  parameters={
     *      {"name"="start", "dataType"="integer", "required"=false, "description"="Indice elemento iniziale"},
     *      {"name"="limit", "dataType"="integer", "required"=false, "description"="Numero di elementi"},
     *      {"name"="label_like", "dataType"="string", "required"=false, "description"="Condizione 'LIKE' su denominazione"},
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
    public function getTourismAccomodationListAction(Request $request)
    {
        $filters = $this->filterBag->getFilterBag($request);
        $offset = $filters->has('start') ? $filters->get('start') : 0;
        $limit = $filters->has('limit') ? $filters->get('limit') : self::DEFAULT_PAGE_SIZE;
        $labelLike = $filters->has('label_like') ? $filters->get('label_like') : null;
        $categoryLike = $filters->has('category_like') ? $filters->get('category_like') : null;
        $latMax = $filters->has('lat_max') && $filters->get('lat_max') ? floatval($filters->get('lat_max')) : null;
        $latMin = $filters->has('lat_min') && $filters->get('lat_min') ? floatval($filters->get('lat_min')) : null;
        $lngMax = $filters->has('lng_max') && $filters->get('lng_max') ? floatval($filters->get('lng_max')) : null;
        $lngMin = $filters->has('lng_min') && $filters->get('lng_min') ? floatval($filters->get('lng_min')) : null;
        if ($limit == 0) {
            $limit = self::DEFAULT_PAGE_SIZE;
        }
        $page = floor($offset / $limit) + 1;

        $qb = $this->em->createQueryBuilder();
        $builder = $qb
            ->select('a')
            ->from('UmbriaOpenApiBundle:Tourism\GraphsEntities\Accomodation', 'a');


        if ($labelLike != null) {
            $builder = $qb
                ->andWhere($qb->expr()->like('a.name', '?2'))
                ->setParameter(2, $labelLike);
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




}
