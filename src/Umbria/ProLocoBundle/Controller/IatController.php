<?php

namespace Umbria\ProLocoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\IatRepository;
use Umbria\ProLocoBundle\Entity\SearchFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class IatController
 * @package Umbria\ProLocoBundle\Controller
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
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

        /**@var IatRepository $repository */
        $repository = $this->getDoctrine()
            ->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Iat');
        $qb = $repository->createQueryBuilder('a');
        $query = $qb
            ->where($qb->expr()->like('a.name', '?1'))
            ->setParameter(1, '%' . $text . '%');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $itemsOnPage
        );

        return $this->render('UmbriaProLocoBundle:Iat:index.html.twig', array(
            'pagination' => $pagination,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Iat entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        /**@var IatRepository $repository */
        $repository = $this->getDoctrine()
            ->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Iat');
        $iat = $repository->findById($id);
        return $this->render('UmbriaProLocoBundle:Iat:show.html.twig', array(
            'iat' => $iat[0]
        ));
    }
}
