<?php

namespace Umbria\ProLocoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Umbria\OpenApiBundle\Entity\Tourism\Proposal;

/**
 * Proposal controller.
 */
class ProposalController extends Controller
{
    /**
     * Lists all Proposal entities.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $itemsOnPage = $this->container->getParameter('items_on_page');

        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('UmbriaOpenApiBundle:Tourism\Proposal')->getAllQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $itemsOnPage/*limit per page*/
        );

        return $this->render('UmbriaProLocoBundle:Proposal:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Finds and displays a Proposal entity.
     *
     * @param Proposal $proposal
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Proposal $proposal)
    {
        return $this->render('UmbriaProLocoBundle:Proposal:show.html.twig', array(
            'proposal' => $proposal,
        ));
    }
}
