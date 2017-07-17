<?php

namespace Umbria\ProLocoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Umbria\ProLocoBundle\Entity\SearchFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class SportFacilityController
 * @package Umbria\ProLocoBundle\Controller
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class SportFacilityController extends Controller
{
    /**
     * Lists all SportFacility entities.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        }

    /**
     * Finds and displays a SportFacility entity.
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {

    }
}
