<?php

namespace Umbria\ProLocoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Umbria\OpenApiBundle\Entity\Tourism\Profession;

/**
 * Profession controller.
 */
class ProfessionController extends Controller
{
    /**
     * Lists all Profession entities.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $itemsOnPage = $this->container->getParameter('items_on_page');

        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('UmbriaOpenApiBundle:Tourism\Profession')->getAllQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $itemsOnPage/*limit per page*/
        );

        return $this->render('UmbriaProLocoBundle:Profession:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Finds and displays a Profession entity.
     *
     * @param Profession $profession
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Profession $profession)
    {
        $baseUrlRisorsa = $this->container->getParameter('base_url_res_profession');
        return $this->render('UmbriaProLocoBundle:Profession:show.html.twig', array(
            'profession' => $profession, 'baseUrlRisorsa' => $baseUrlRisorsa
        ));
    }
}
