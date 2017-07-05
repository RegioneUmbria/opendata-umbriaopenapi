<?php

namespace Umbria\OpenApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class enlangController extends Controller
{
    public function indexAction()
    {
        return $this->render('UmbriaOpenApiBundle:enlang:index_en.html.twig');
    }
}
