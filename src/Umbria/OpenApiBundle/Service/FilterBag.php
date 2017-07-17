<?php

namespace Umbria\OpenApiBundle\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class FilterBag
{
    /**
     * @param Request $request
     *
     * @return ParameterBag
     */
    public function getFilterBag(Request $request)
    {
        $filterBag = new ParameterBag();

        $queryBag = $request->query->all();
        if (isset($queryBag['filter'])) {
            $filterString = $queryBag['filter'];
            unset($queryBag['filter']);
            $filters = json_decode($filterString);
            foreach ($filters as $filter) {
                $filterBag->set($filter->property, $filter->value);
            }
        }
        $filterBag->add($queryBag);

        return $filterBag;
    }
}
