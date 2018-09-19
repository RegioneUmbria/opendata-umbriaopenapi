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
 * Class AccomodationController
 * @package Umbria\OpenApiBundle\Controller\Tourism
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class AccomodationController extends Controller
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
     * Lists all Accomodation entities.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
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
            ->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Accomodation');
        $qb = $repository->createQueryBuilder('a');
        $qb
            ->andWhere($qb->expr()->like('a.name', '?1'))
            ->setParameter(1, '%' . $text . '%');

        $qb->andWhere($qb->expr()->eq('a.isDeleted', '0'));

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $itemsOnPage/*limit per page*/
        );

        return $this->render('UmbriaOpenApiBundle:Accomodation:index.html.twig', array(
            'pagination' => $pagination,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Accomodation entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $repository = $this->getDoctrine()
            ->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Accomodation');
        $accomodation = $repository->findById($id);
        if (!isset($accomodation) || $accomodation[0]->isDeleted()) {
            throw $this->createNotFoundException('La risorsa non esiste');
        }
        return $this->render('UmbriaOpenApiBundle:Accomodation:show.html.twig', array(
            'accomodation' => $accomodation[0]
        ));
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
     *      {"name"="lng_min", "dataType"="number", "required"=false, "description"="Longitudine minima"},
     *      {"name"="address_locality", "dataType"="string", "required"=false, "description"="Condizione 'LIKE' su Comune"}
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
        $addressLocality = $filters->has('address_locality') ? $filters->get('address_locality') : null;
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
        $qb
            ->select('a')
            ->from('UmbriaOpenApiBundle:Tourism\GraphsEntities\Accomodation', 'a');


        if ($labelLike != null) {
            $qb
                ->andWhere($qb->expr()->like('a.name', '?2'))
                ->setParameter(2, $labelLike);
        }


        if ($categoryLike != null) {
            $qb->leftJoin('a.categories', 'cat')
                ->andWhere(
                    $qb->expr()->like('cat.name', ':categoryLike')
                )
                ->setParameter("categoryLike", $categoryLike);

        }

        if ($latMax != null ||
            $latMin != null ||
            $lngMax != null ||
            $lngMin != null ||
            $addressLocality != null
        ) {
            $qb->join('a.address', 'address');
            if ($latMax != null) {
                $qb->andWhere(
                        $qb->expr()->lte("address.lat", ':latMax'),
                        $qb->expr()->isNotNull("address.lat"),
                        $qb->expr()->gt("address.lat", ':empty')
                    )
                        ->setParameter('latMax', $latMax)
                        ->setParameter('empty', '0');
            }
            if ($latMin != null) {
                $qb->andWhere(
                        $qb->expr()->gte("address.lat", ':latMin'),
                        $qb->expr()->isNotNull("address.lat"),
                        $qb->expr()->gt("address.lat", ":empty")
                    )
                        ->setParameter('latMin', $latMin)
                        ->setParameter('empty', '0');
            }
            if ($lngMax != null) {
                $qb->andWhere(
                        $qb->expr()->lte("address.lng", ':lngMax'),
                        $qb->expr()->isNotNull("address.lng"),
                        $qb->expr()->gt("address.lng", ":empty")
                    )
                        ->setParameter('lngMax', $lngMax)
                        ->setParameter('empty', '0');
            }
            if ($lngMin != null) {
                $qb->andWhere(
                        $qb->expr()->gte("address.lng", ':lngMin'),
                        $qb->expr()->isNotNull("address.lng"),
                        $qb->expr()->gt("address.lng", ":empty")
                    )
                        ->setParameter('lngMin', $lngMin)
                        ->setParameter('empty', '0');
            }
            if ($addressLocality != null) {
                $qb->andWhere(
                    $qb->expr()->like("address.addressLocality", ":addressLocality")
                )->setParameter('addressLocality', '%' . $addressLocality . '%');
            }
        }
        $qb->andWhere($qb->expr()->eq('a.isDeleted', '0'));
        /** @var AbstractPagination $resultsPagination */
        $resultsPagination = $this->paginator->paginate($qb, $page, $limit);
        /** @var AbstractPagination $countPagination */
        $countPagination = $this->paginator->paginate($qb, 1, 1);


        $view = new View(new EntityResponse($resultsPagination->getItems(), count($resultsPagination), $countPagination->getTotalItemCount()));

        return $view;
    }




}
