<?php

namespace Umbria\OpenApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UmbriaOpenApiBundle:Default:index.html.twig');
    }
}
