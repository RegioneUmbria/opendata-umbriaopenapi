<?php

namespace Umbria\ProLocoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Umbria\OpenApiBundle\Entity\Tourism\Consortium;

/**
 * Consortium controller.
 */
class ConsortiumController extends Controller
{
    /**
     * Lists all Consortium entities.
     */
    public function indexAction(Request $request)
    {
        $itemsOnPage = $this->container->getParameter('items_on_page');

        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('UmbriaOpenApiBundle:Tourism\Consortium')->getAllQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $itemsOnPage/*limit per page*/
        );

        return $this->render('UmbriaProLocoBundle:Consortium:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Finds and displays a Consortium entity.
     *
     * @param Consortium $consortium
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Consortium $consortium)
    {
        return $this->render('UmbriaProLocoBundle:Consortium:show.html.twig', array(
            'consortium' => $consortium,
        ));
    }
}
