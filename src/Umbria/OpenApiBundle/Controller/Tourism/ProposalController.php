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
use Umbria\OpenApiBundle\Entity\Tourism\Setting;
use Umbria\OpenApiBundle\Serializer\View\EntityResponse;
use Umbria\OpenApiBundle\Service\CurlBuilder;
use Umbria\OpenApiBundle\Service\FilterBag;

class ProposalController extends FOSRestController
{
    const DEFAULT_PAGE_SIZE = 100;
    const DATASET_TOURISM_PROPOSAL = 'tourism-proposal';

    /**
     * @var EntityManager
     * @DI\Inject("doctrine.orm.entity_manager")
     */
    public $em;

    /**
     * @var CurlBuilder
     * @DI\Inject("umbria_open_api.curl_builder")
     */
    public $curlBuilder;

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
     * @Rest\Options(pattern="/open-api/tourism-proposal")
     */
    public function optionsTourismProposalAction()
    {
        $response = new Response();

        $response->setContent(json_encode(array(
            'Allow' => 'GET,OPTIONS',
            'GET' => array(
                'description' => 'A get request for proposals',
            ),
        )));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'json');

        return $response;
    }

    /**
     * @Rest\Get(pattern="/open-api/tourism-proposal")
     *
     * @param Request $request
     *
     * @return View
     *
     * @internal param Request $request
     *
     * @ApiDoc\ApiDoc(
     *  section = "Tourism",
     *  description = "Lista proposte turistiche regione Umbria",
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
    public function getTourismProposalListAction(Request $request)
    {
        $daysToOld = $this->container->getParameter('proposal_days_to_old');
        $url = $this->container->getParameter('url_proposal');
        $urlSilk = $this->container->getParameter('url_proposal_silk');

        $filters = $this->filterBag->getFilterBag($request);
        $offset = $filters->has('start') ? $filters->get('start') : 0;
        $limit = $filters->has('limit') ? $filters->get('limit') : self::DEFAULT_PAGE_SIZE;
        if ($limit == 0) {
            $limit = self::DEFAULT_PAGE_SIZE;
        }
        $page = floor($offset / $limit) + 1;

        /** @var Setting $setting */
        $setting = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\Setting')->findOneBy(array('datasetName' => self::DATASET_TOURISM_PROPOSAL));
        if ($setting != null) {
            $diff = $setting->getUpdatedAt()->diff(new DateTime('now'));
            // controllo intervallo di tempo da ultima estrazione
            if ($diff->days >= $daysToOld) {
                $setting->setDatasetName(self::DATASET_TOURISM_PROPOSAL);
                $setting->setUpdatedAtValue();
                $this->em->persist($setting);
                $this->em->flush();

                $this->curlBuilder->updateEntities($url, $urlSilk, self::DATASET_TOURISM_PROPOSAL);
            }
        } else {
            $setting = new Setting();
            $setting->setDatasetName(self::DATASET_TOURISM_PROPOSAL);
            $setting->setUpdatedAtValue();
            $this->em->persist($setting);
            $this->em->flush();

            $this->curlBuilder->updateEntities($url, $urlSilk, self::DATASET_TOURISM_PROPOSAL);
        }

        $builder = $this->em->createQueryBuilder()
            ->select('a')
            ->from('UmbriaOpenApiBundle:Tourism\Proposal', 'a');

        /** @var AbstractPagination $resultsPagination */
        $resultsPagination = $this->paginator->paginate($builder, $page, $limit);
        /** @var AbstractPagination $countPagination */
        $countPagination = $this->paginator->paginate($builder, 1, 1);

        $viewGroups = array(
            'response',
            'rdf.*', 'proposal.*', 'description.*', 'category.*', 'info.*', 'travel-time.*', 'coordinate.*', 'download.*',
        );

        $view = new View(new EntityResponse($resultsPagination->getItems(), count($resultsPagination), $countPagination->getTotalItemCount()));
        $view->getSerializationContext()->setGroups($viewGroups);

        return $view;
    }
}
