<?php

namespace Umbria\OpenApiBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Created by PhpStorm.
 * User: Lorenzo Ranucci
 * Date: 01/09/2016
 * Time: 10:50
 */

/**
 * Class SearchFilter
 * @package Umbria\OpenApiBundle\Entity
 */
class SearchFilter
{

    protected $text;

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

}