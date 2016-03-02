<?php

namespace Umbria\ProLocoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Umbria\OpenApiBundle\Entity\Tourism\TravelAgency;

/**
 * TravelAgency controller.
 */
class TravelAgencyController extends Controller
{
    /**
     * Lists all TravelAgency entities.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $itemsOnPage = $this->container->getParameter('items_on_page');

        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('UmbriaOpenApiBundle:Tourism\TravelAgency')->getAllQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $itemsOnPage/*limit per page*/
        );

        return $this->render('UmbriaProLocoBundle:TravelAgency:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Finds and displays a TravelAgency entity.
     *
     * @param TravelAgency $travelAgency
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(TravelAgency $travelAgency)
    {
        return $this->render('UmbriaProLocoBundle:TravelAgency:show.html.twig', array(
            'travelAgency' => $travelAgency,
        ));
    }
}
