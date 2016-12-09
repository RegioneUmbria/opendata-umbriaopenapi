<?php

namespace Umbria\ProLocoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class SUAPEController
 * @package Umbria\ProLocoBundle\Controller
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class SUAPEController extends Controller
{

    public function indexAction()
    {
        return $this->render('UmbriaProLocoBundle:SUAPE:index.html.twig');
    }

}
