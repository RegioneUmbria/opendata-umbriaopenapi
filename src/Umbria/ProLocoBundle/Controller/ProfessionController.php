<?php

namespace Umbria\ProLocoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\ProfessionRepository;
use Umbria\ProLocoBundle\Entity\SearchFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class ProfessionController
 * @package Umbria\ProLocoBundle\Controller
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
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

        /**@var ProfessionRepository $repository */
        $repository = $this->getDoctrine()
            ->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Profession');
        $qb = $repository->createQueryBuilder('a');
        $query = $qb
            ->where($qb->expr()->orX(
                $qb->expr()->like('a.firstName', '?1'),
                $qb->expr()->like('a.lastName', '?1')
            )
            )
            ->setParameter(1, '%' . $text . '%');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $itemsOnPage/*limit per page*/
        );

        return $this->render('UmbriaProLocoBundle:Profession:index.html.twig', array(
            'pagination' => $pagination,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Profession entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        /**@var ProfessionRepository $repository */
        $repository = $this->getDoctrine()
            ->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Profession');
        $profession = $repository->findById($id);
        return $this->render('UmbriaProLocoBundle:Profession:show.html.twig', array(
            'profession' => $profession[0]
        ));
    }
}
