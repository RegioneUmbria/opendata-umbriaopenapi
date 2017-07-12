<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2017/7/5
 * Time: 1:01
 */

namespace Umbria\OpenApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ENDefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UmbriaOpenApiBundle:ENDefault:index.html.twig');
    }

}