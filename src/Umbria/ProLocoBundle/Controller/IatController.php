<?php

namespace Umbria\ProLocoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Umbria\OpenApiBundle\Entity\Tourism\Iat;

/**
 * Iat controller.
 */
class IatController extends Controller
{
    /**
     * Lists all Iat entities.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $itemsOnPage = $this->container->getParameter('items_on_page');

        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('UmbriaOpenApiBundle:Tourism\Iat')->getAllQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $itemsOnPage/*limit per page*/
        );

        return $this->render('UmbriaProLocoBundle:Iat:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Finds and displays a Iat entity.
     *
     * @param Iat $iat
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Iat $iat)
    {
        $baseUrlRisorsa = $this->container->getParameter('base_url_res_iat');
        return $this->render('UmbriaProLocoBundle:Iat:show.html.twig', array(
            'iat' => $iat, 'baseUrlRisorsa' => $baseUrlRisorsa
        ));
    }
}
