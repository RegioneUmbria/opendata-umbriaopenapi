<?php

namespace Umbria\ProLocoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Umbria\OpenApiBundle\Entity\Tourism\Event;
use Umbria\ProLocoBundle\Entity\SearchFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class EventController
 * @package Umbria\ProLocoBundle\Controller
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class EventController extends Controller
{
    /**
     * Lists all Event entities.
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
            ->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Event');
        $qb = $repository->createQueryBuilder('a');
        $query = $qb
            ->where($qb->expr()->like('a.name', '?1'))
            ->setParameter(1, '%' . $text . '%');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            $itemsOnPage/*limit per page*/
        );

        return $this->render('UmbriaProLocoBundle:Event:index.html.twig', array(
            'pagination' => $pagination,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Event entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $repository = $this->getDoctrine()
            ->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Event');
        $event = $repository->findById($id);
        return $this->render('UmbriaProLocoBundle:Event:show.html.twig', array(
            'event' => $event[0]
        ));
    }
}
