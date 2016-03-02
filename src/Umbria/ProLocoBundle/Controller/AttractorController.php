<?php

namespace Umbria\ProLocoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Umbria\OpenApiBundle\Entity\Tourism\Attractor;

/**
 * Attractor controller.
 */
class AttractorController extends Controller
{
    /**
     * Lists all Attractor entities.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $itemsOnPage = $this->container->getParameter('items_on_page');

        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('UmbriaOpenApiBundle:Tourism\Attractor')->getAllQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $itemsOnPage/*limit per page*/
        );

        return $this->render('UmbriaProLocoBundle:Attractor:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Finds and displays a Attractor entity.
     *
     * @param Attractor $attractor
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Attractor $attractor)
    {
        return $this->render('UmbriaProLocoBundle:Attractor:show.html.twig', array(
            'attractor' => $attractor,
        ));
    }
}
