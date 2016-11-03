<?php
/**
 * Created by PhpStorm.
 * User: DeveloperOspite
 * Date: 02/09/2016
 * Time: 12:24
 */

namespace Umbria\OpenApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class SparqlManualController extends Controller
{
    public function indexAction()
    {

        return $this->render('UmbriaOpenApiBundle:SparqlManual:index.html.twig'
        );

    }
}