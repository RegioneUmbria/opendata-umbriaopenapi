<?php

namespace Umbria\OpenApiBundle\Controller\Tourism;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\Paginator;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Umbria\OpenApiBundle\Entity\SearchFilter;
use Umbria\OpenApiBundle\Serializer\View\EntityResponse;
use Umbria\OpenApiBundle\Service\FilterBag;

/**
 * Class ConsortiumController
 * @package Umbria\OpenApiBundle\Controller\Tourism
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class ConsortiumController extends Controller
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
     * Lists all Consortium entities.
     */
    public function indexAction(Request $request)
    {
        $itemsOnPage = $this->container->getParameter('items_on_page');

        $searchFilter = new SearchFilter();

        $form = $this->createFormBuilder($searchFilter)
            ->add("text", TextType::class, array('required' => false))
            ->add('search', SubmitType::class, array('label' => 'Cerca'))
            ->getForm();

        $form->handleRequest($request);
        $text = "";
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $searchFilter = $form->getData();
            $text = $searchFilter->getText();
            $form = $this->createFormBuilder($searchFilter)
                ->add("text", TextType::class, array('required' => false))
                ->add('search', SubmitType::class, array('label' => 'Ricerca'))
                ->getForm();
        }

        $repository = $this->getDoctrine()
            ->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Consortium');
        $qb = $repository->createQueryBuilder('a');
        $query = $qb
            ->where($qb->expr()->like('a.name', '?1'))
            ->setParameter(1, '%' . $text . '%')
            ->andWhere($qb->expr()->eq('a.isDeleted', '0'));

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $itemsOnPage/*limit per page*/
        );

        return $this->render('UmbriaOpenApiBundle:Consortium:index.html.twig', array(
            'pagination' => $pagination,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Consortium entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $repository = $this->getDoctrine()
            ->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Consortium');
        $consortium = $repository->findById($id);
        if (!isset($consortium) || $consortium[0]->isDeleted()) {
            throw $this->createNotFoundException('La risorsa non esiste');
        }
        return $this->render('UmbriaOpenApiBundle:Consortium:show.html.twig', array(
            'consortium' => $consortium[0]
        ));
    }

    /**
     * @Rest\Options(pattern="/open-api/tourism-consortium")
     *
     * @return Response
     */
    public function optionsTourismConsortiumAction()
    {
        $response = new Response();

        $response->setContent(json_encode(array(
            'Allow' => 'GET,OPTIONS',
            'GET' => array(
                'description' => 'A get request for consortiums',
            ),
        )));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'json');

        return $response;
    }

    /**
     * @Rest\Get(pattern="/open-api/tourism-consortium")
     *
     * @param Request $request
     *
     * @return View
     *
     * @internal param Request $request
     *
     * @ApiDoc\ApiDoc(
     *  section = "Tourism",
     *  description = "Lista consorzi regione Umbria",
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
    public function getTourismConsortiumListAction(Request $request)
    {
        $filters = $this->filterBag->getFilterBag($request);
        $offset = $filters->has('start') ? $filters->get('start') : 0;
        $limit = $filters->has('limit') ? $filters->get('limit') : self::DEFAULT_PAGE_SIZE;
        if ($limit == 0) {
            $limit = self::DEFAULT_PAGE_SIZE;
        }
        $page = floor($offset / $limit) + 1;


        $builder = $this->em->createQueryBuilder()
            ->select('a')
            ->from('UmbriaOpenApiBundle:Tourism\GraphsEntities\Consortium', 'a');
        $builder = $builder->andWhere($builder->expr()->eq('a.isDeleted', '0'));
        /** @var AbstractPagination $resultsPagination */
        $resultsPagination = $this->paginator->paginate($builder, $page, $limit);
        /** @var AbstractPagination $countPagination */
        $countPagination = $this->paginator->paginate($builder, 1, 1);


        $view = new View(new EntityResponse($resultsPagination->getItems(), count($resultsPagination), $countPagination->getTotalItemCount()));

        return $view;
    }







}
