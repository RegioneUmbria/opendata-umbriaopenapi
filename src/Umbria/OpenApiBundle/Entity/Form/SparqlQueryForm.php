<?php
namespace Umbria\OpenApiBundle\Entity\Form;
/**
 * Created by PhpStorm.
 * User: Lorenzo Ranucci
 * Date: 05/09/2016
 * Time: 11:08
 */
class SparqlQueryForm
{
    protected $query;

    protected $result;

    protected $graphURI;

    /**
     * @return mixed
     */
    public function getGraphURI()
    {
        return $this->graphURI;
    }

    /**
     * @param mixed $graphURI
     */
    public function setGraphURI($graphURI)
    {
        $this->graphURI = $graphURI;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $code
     */
    public function setQuery($code)
    {
        $this->query = $code;
    }


}