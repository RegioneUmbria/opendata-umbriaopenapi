<?php

namespace Umbria\ProLocoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Attractor;
use Umbria\ProLocoBundle\Entity\SearchFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
            ->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Attractor');
        $qb = $repository->createQueryBuilder('a');
        $query = $qb
            ->where($qb->expr()->like('a.name', '?1'))
            ->setParameter(1, '%' . $text . '%');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $itemsOnPage/*limit per page*/
        );

        return $this->render('UmbriaProLocoBundle:Attractor:index.html.twig', array(
            'pagination' => $pagination,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Attractor entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $repository = $this->getDoctrine()
            ->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Attractor');
        $attractor = $repository->findById($id);
        $baseUrlRisorsa = $this->container->getParameter('base_url_res_attractor');
        return $this->render('UmbriaProLocoBundle:Attractor:show.html.twig', array(
            'attractor' => $attractor[0], 'baseUrlRisorsa' => $baseUrlRisorsa
        ));
    }
}
