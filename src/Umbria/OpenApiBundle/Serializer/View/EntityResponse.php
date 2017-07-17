<?php

namespace Umbria\OpenApiBundle\Serializer\View;

use JMS\Serializer\Annotation as Ser;

class EntityResponse
{
    /**
     * @Ser\Groups({"response"})
     */
    public $entities;

    /**
     * @Ser\Groups({"response"})
     */
    public $queryCount;

    /**
     * @Ser\Groups({"response"})
     */
    public $total;

    /**
     * EntityResponse constructor.
     *
     * @param $entities
     * @param int $queryCount
     * @param int $total
     */
    public function __construct($entities, $queryCount, $total)
    {
        $this->entities = $entities;
        $this->queryCount = $queryCount;
        $this->total = $total;
    }
}
