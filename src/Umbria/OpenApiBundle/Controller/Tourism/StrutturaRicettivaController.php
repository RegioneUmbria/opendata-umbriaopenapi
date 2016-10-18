<?php


namespace Umbria\OpenApiBundle\Controller\Tourism;

use DateTime;
use Doctrine\ORM\EntityManager;
use EasyRdf_Graph;
use EasyRdf_Resource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\Paginator;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Umbria\OpenApiBundle\Entity\Address;
use Umbria\OpenApiBundle\Entity\Category;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\StrutturaRicettiva;
use Umbria\OpenApiBundle\Entity\Tourism\Setting;
use Umbria\OpenApiBundle\Entity\Type;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\StrutturaRicettivaRepository;
use Umbria\OpenApiBundle\Serializer\View\EntityResponse;
use Umbria\OpenApiBundle\Service\FilterBag;


/**
 * Class StrutturaRicettivaController
 * @package Umbria\OpenApiBundle\Controller\Tourism
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class StrutturaRicettivaController extends BaseController
{
    const DEFAULT_PAGE_SIZE = 100;
    const DATASET_TOURISM_STRUTTURA_RICETTIVA = 'tourism-struttura-ricettiva';
    private $filterBag;
    private $paginator;

    private $em;
    /**@var StrutturaRicettivaRepository strutturaRicettivaRepo */
    private $strutturaRicettivaRepo;

    private $settingsRepo;
    private $categoryRepo;
    private $typeRepo;

    private $graph;

    /**
     * @DI\InjectParams({
     *      "em" = @DI\Inject("doctrine.orm.entity_manager"),
     *      "filterBag" = @DI\Inject("umbria_open_api.filter_bag"),
     *      "paginator" = @DI\Inject("knp_paginator")
     * })
     * @param $em EntityManager
     * @param $filterBag FilterBag
     * @param $paginator Paginator
     */
    public function __construct($em, $filterBag, $paginator)
    {
        parent::__construct($em);
        $this->filterBag = $filterBag;
        $this->paginator = $paginator;
        $this->strutturaRicettivaRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\StrutturaRicettiva');
        $this->settingsRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\Setting');
        $this->categoryRepo = $em->getRepository('UmbriaOpenApiBundle:Category');
        $this->typeRepo = $em->getRepository('UmbriaOpenApiBundle:Type');
        $this->em = $em;
    }

    /**
     * @Rest\Options(pattern="/open-api/tourism-struttura-ricettiva")
     */
    public function optionsTourismStrutturaRicettivaAction()
    {
        $response = new Response();

        $response->setContent(json_encode(array(
            'Allow' => 'GET,OPTIONS',
            'GET' => array(
                'description' => 'A get request for strutture ricettive',
            ),
        )));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'json');

        return $response;
    }

    /**
     * @Rest\Get(pattern="/open-api/tourism-struttura-ricettiva")
     *
     * @param Request $request
     *
     * @return View
     *
     * @internal param Request $request
     *
     * @ApiDoc\ApiDoc(
     *  section = "Tourism",
     *  description = "Lista attrattori turistici regione Umbria",
     *  tags = {
     *      "beta"
     *  },
     *  parameters={
     *      {"name"="start", "dataType"="integer", "required"=false, "description"="Indice elemento iniziale"},
     *      {"name"="limit", "dataType"="integer", "required"=false, "description"="Numero di elementi"},
     *      {"name"="label_like", "dataType"="string", "required"=false, "description"="Condizione 'LIKE' su denominazione"},
     *      {"name"="category_like", "dataType"="string", "required"=false, "description"="Condizione 'LIKE' sulle categorie"},
     *      {"name"="lat_max", "dataType"="number", "required"=false, "description"="Latitudine massima"},
     *      {"name"="lat_min", "dataType"="number", "required"=false, "description"="Latitudine minima"},
     *      {"name"="lng_max", "dataType"="number", "required"=false, "description"="Longitudine massima"},
     *      {"name"="lng_min", "dataType"="number", "required"=false, "description"="Longitudine minima"}
     *
     *
     *  },
     *  statusCodes={
     *      200="Restituito in caso di successo"
     *  }
     * )
     */
    public function getTourismStrutturaRicettivaListAction(Request $request)
    {
        $daysToOld = $this->container->getParameter('struttura-ricettiva_days_to_old');
        $filters = $this->filterBag->getFilterBag($request);
        $offset = $filters->has('start') ? $filters->get('start') : 0;
        $limit = $filters->has('limit') ? $filters->get('limit') : self::DEFAULT_PAGE_SIZE;
        $labelLike = $filters->has('label_like') ? $filters->get('label_like') : null;
        $categoryLike = $filters->has('category_like') ? $filters->get('category_like') : null;
        $latMax = $filters->has('lat_max') && $filters->get('lat_max') ? floatval($filters->get('lat_max')) : null;
        $latMin = $filters->has('lat_min') && $filters->get('lat_min') ? floatval($filters->get('lat_min')) : null;
        $lngMax = $filters->has('lng_max') && $filters->get('lng_max') ? floatval($filters->get('lng_max')) : null;
        $lngMin = $filters->has('lng_min') && $filters->get('lng_min') ? floatval($filters->get('lng_min')) : null;
        if ($limit == 0) {
            $limit = self::DEFAULT_PAGE_SIZE;
        }
        $page = floor($offset / $limit) + 1;

        /** @var Setting $setting */
        $setting = $this->settingsRepo->findOneBy(array('datasetName' => self::DATASET_TOURISM_STRUTTURA_RICETTIVA));
        if ($setting != null) {
            $diff = $setting->getUpdatedAt()->diff(new DateTime('now'));
            if ($diff->days >= $daysToOld) {
                $setting->setDatasetName(self::DATASET_TOURISM_STRUTTURA_RICETTIVA);
                $setting->setUpdatedAtValue();
                $this->em->persist($setting);
                $this->em->flush();
                $this->updateEntities();
            }
        } else {
            $setting = new Setting();
            $setting->setDatasetName(self::DATASET_TOURISM_STRUTTURA_RICETTIVA);
            $setting->setUpdatedAtValue();
            $this->em->persist($setting);
            $this->em->flush();
            $this->updateEntities();
        }
        $qb = $this->em->createQueryBuilder();
        $builder = $qb
            ->select('a')
            ->from('UmbriaOpenApiBundle:Tourism\GraphsEntities\StrutturaRicettiva', 'a');


        if ($labelLike != null) {
            $builder = $qb
                ->andWhere($qb->expr()->like('a.name', '?2'))
                ->setParameter(2, $labelLike);
        }


        if ($categoryLike != null) {
            $builder = $qb
                ->leftJoin('a.categories', 'cat')
                ->andWhere(
                    $qb->expr()->like('cat.name', ':categoryLike')
                )
                ->setParameter("categoryLike", $categoryLike);

        }

//        if ($latMax != null ||
//            $latMin != null ||
//            $lngMax != null ||
//            $lngMin != null
//        ) {
//            if ($latMax != null) {
//                $builder =
//                    $qb->andWhere(
//                        $qb->expr()->lte("a.lat", ':latMax'),
//                        $qb->expr()->isNotNull("a.lat"),
//                        $qb->expr()->gt("a.lat", ':empty')
//                    )
//                        ->setParameter('latMax', $latMax)
//                        ->setParameter('empty', '0');
//            }
//            if ($latMin != null) {
//                $builder =
//                    $qb->andWhere(
//                        $qb->expr()->gte("a.lat", ':latMin'),
//                        $qb->expr()->isNotNull("a.lat"),
//                        $qb->expr()->gt("a.lat", ":empty")
//                    )
//                        ->setParameter('latMin', $latMin)
//                        ->setParameter('empty', '0');
//            }
//            if ($lngMax != null) {
//                $builder =
//                    $qb->andWhere(
//                        $qb->expr()->lte("a.lng", ':lngMax'),
//                        $qb->expr()->isNotNull("a.lng"),
//                        $qb->expr()->gt("a.longitude", ":empty")
//                    )
//                        ->setParameter('lngMax', $lngMax)
//                        ->setParameter('empty', '0');
//            }
//            if ($lngMin != null) {
//                $builder =
//                    $qb->andWhere(
//                        $qb->expr()->gte("a.lng", ':lngMin'),
//                        $qb->expr()->isNotNull("a.lng"),
//                        $qb->expr()->gt("a.lng", ":empty")
//                    )
//                        ->setParameter('lngMin', $lngMin)
//                        ->setParameter('empty', '0');
//            }
//        }

        /** @var AbstractPagination $resultsPagination */
        $resultsPagination = $this->paginator->paginate($builder, $page, $limit);
        /** @var AbstractPagination $countPagination */
        $countPagination = $this->paginator->paginate($builder, 1, 1);


        $view = new View(new EntityResponse($resultsPagination->getItems(), count($resultsPagination), $countPagination->getTotalItemCount()));

        return $view;
    }

    private function updateEntities()
    {
        $count = 0;
        $this->graph = EasyRdf_Graph::newAndLoad($this->container->getParameter('struttura-ricettiva_graph_url'));
        /**@var EasyRdf_Resource[] $resources */
        $resources = $this->graph->resources();
        foreach ($resources as $resource) {
            if ($count > 20) break;
            $resourceTypeArray = $resource->all("rdf:type");
            if ($resourceTypeArray != null) {
                foreach ($resourceTypeArray as $resourceType) {
                    if (trim($resourceType) == "http://purl.org/acco/ns#Accomodation") {//is strutturaRicettiva
                        $this->createOrUpdateEntity($resource);
                        $count++;
                        break;
                    }
                }
            }

        }
        $now = new \DateTime();
        $this->deleteOldEntities($now);
    }

    /**
     * @param EasyRdf_Resource $strutturaRicettivaResource
     */
    private function createOrUpdateEntity($strutturaRicettivaResource)
    {
        /** @var StrutturaRicettiva $newStrutturaRicettiva */
        $newStrutturaRicettiva = null;
        $uri = $strutturaRicettivaResource->getUri();
        if ($uri != null) {
            $oldStrutturaRicettiva = $this->strutturaRicettivaRepo->find($uri);
            $isAlreadyPersisted = $oldStrutturaRicettiva != null;
            if ($isAlreadyPersisted) {
                $newStrutturaRicettiva = $oldStrutturaRicettiva;
            } else {
                $newStrutturaRicettiva = new StrutturaRicettiva();
            }
            $newStrutturaRicettiva->setUri($uri);
            $newStrutturaRicettiva->setLastUpdateAt(new \DateTime('now'));
            $newStrutturaRicettiva->setName(($p = $strutturaRicettivaResource->get("rdfs:label")) != null ? $p->getValue() : null);

            /**@var EasyRdf_Resource[] $typesarray */
            $typesarray = $strutturaRicettivaResource->all("rdf:type");
            if ($typesarray != null) {
                /**@var Type[] $tempTypes */
                $tempTypes = array();
                $cnt = 0;
                foreach ($typesarray as $type) {
                    $oldType = $this->typeRepo->find($type->getUri());
                    if ($oldType != null) {
                        $tempTypes[$cnt] = $oldType;
                    } else {
                        $tempTypes[$cnt] = new Type();
                        $tempTypes[$cnt]->setUri($type->getUri());
                        $tempTypes[$cnt]->setName(($p = $type->get("rdfs:label")) != null ? $p->getValue() : null);
                        $tempTypes[$cnt]->setComment(($p = $type->get("<http://www.w3.org/2000/01/rdf-schema#comment>")) != null ? $p->getValue() : null);
                    }
                    $cnt++;
                }
                count($tempTypes) > 0 ? $newStrutturaRicettiva->setTypes($tempTypes) : $newStrutturaRicettiva->setTypes(null);
            }

            $newStrutturaRicettiva->setProvenance(($p = $strutturaRicettivaResource->get("<http://purl.org/dc/elements/1.1/provenance>")) != null ? $p->getValue() : null);
            $newStrutturaRicettiva->setTypology(($p = $strutturaRicettivaResource->get("<http://dati.umbria.it/base/ontology/tipologia>")) != null ? $p->getValue() : null);

            /**@var EasyRdf_Resource[] $emailarray */
            $emailarray = $strutturaRicettivaResource->all("<http://schema.org/email>");
            if ($emailarray != null) {
                $tempEmail = array();
                $newStrutturaRicettiva->setEmail(array());
                $cnt = 0;
                foreach ($emailarray as $email) {
                    $tempEmail[$cnt] = $email->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempEmail) > 0 ? $newStrutturaRicettiva->setEmail($tempEmail) : $newStrutturaRicettiva->setEmail(null);
            }

            /**@var EasyRdf_Resource[] $telephonearray */
            $telephonearray = $strutturaRicettivaResource->all("<http://schema.org/telephone>");
            if ($telephonearray != null) {
                $tempTelephone = array();
                $cnt = 0;
                foreach ($telephonearray as $telephone) {
                    $tempTelephone[$cnt] = $telephone->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempTelephone) > 0 ? $newStrutturaRicettiva->setTelephone($tempTelephone) : $newStrutturaRicettiva->setTelephone(null);
            }

            /**@var EasyRdf_Resource[] $faxarray */
            $faxarray = $strutturaRicettivaResource->all("<http://schema.org/faxNumber>");
            if ($faxarray != null) {
                $tempFax = array();
                $newStrutturaRicettiva->setFax(array());
                $cnt = 0;
                foreach ($faxarray as $fax) {
                    $tempFax[$cnt] = $fax->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempFax) > 0 ? $newStrutturaRicettiva->setFax($tempFax) : $newStrutturaRicettiva->setFax(null);
            }
            $newStrutturaRicettiva->setResourceOriginUrl(($p = $strutturaRicettivaResource->get("<http://schema.org/web>")) != null ? $p->getValue() : null);


            if ($isAlreadyPersisted && ($oldAddress = $newStrutturaRicettiva->getAddress()) != null) {
                $this->em->remove($oldAddress);
                $newStrutturaRicettiva->setAddress(null);
            }

            /**@var EasyRdf_Resource $addressResource */
            $addressResource = $strutturaRicettivaResource->get("<http://schema.org/address>");
            if ($addressResource != null) {
                $addressObject = new Address();
                $addressObject->setPostalCode(($p = $addressResource->get("<http://schema.org/postalCode>")) != null ? $p->getValue() : null);
                $addressObject->setIstat(($p = $addressResource->get("<http://dbpedia.org/ontology/istat>")) != null ? $p->getValue() : null);
                $addressObject->setAddressLocality(($p = $addressResource->get("<http://schema.org/addressLocality>")) != null ? $p->getValue() : null);
                $addressObject->setAddressRegion(($p = $addressResource->get("<http://schema.org/addressRegion>")) != null ? $p->getValue() : null);
                $addressObject->setStreetAddress(($p = $addressResource->get("<http://schema.org/streetAddress>")) != null ? $p->getValue() : null);
                $addressObject->setLat(($p = $addressResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#lat>")) != null ? $p->getValue() : null);
                $addressObject->setLng(($p = $addressResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#long>")) != null ? $p->getValue() : null);
                $newStrutturaRicettiva->setAddress($addressObject);
            }


            /**@var EasyRdf_Resource[] $categoriesarray */
            $categoriesarray = $strutturaRicettivaResource->all("<http://dati.umbria.it/tourism/ontology/categoria>");
            if ($categoriesarray != null) {
                /**@var Category[] $tempCategories */
                $tempCategories = array();
                $cnt = 0;
                foreach ($categoriesarray as $category) {
                    $oldCategory = $this->categoryRepo->find($category->getUri());
                    if ($oldCategory != null) {
                        $tempCategories[$cnt] = $oldCategory;
                    } else {
                        $tempCategories[$cnt] = new Category();
                        $tempCategories[$cnt]->setUri($category->getUri());
                        $tempCategories[$cnt]->setName(($p = $category->get("rdfs:label")) != null ? $p->getValue() : null);
                        $tempCategories[$cnt]->setComment(($p = $category->get("<http://www.w3.org/2000/01/rdf-schema#comment>")) != null ? $p->getValue() : null);
                    }
                    $cnt++;
                }
                count($tempCategories) > 0 ? $newStrutturaRicettiva->setCategories($tempCategories) : $newStrutturaRicettiva->setCategories(null);
            }

            $newStrutturaRicettiva->setUnits(($p = $strutturaRicettivaResource->get("<http://dati.umbria.it/base/ontology/numeroUnita>")) != null ? $p->getValue() : null);
            $newStrutturaRicettiva->setBeds(($p = $strutturaRicettivaResource->get("<http://dati.umbria.it/base/ontology/numeroLetti>")) != null ? $p->getValue() : null);
            $newStrutturaRicettiva->setToilets(($p = $strutturaRicettivaResource->get("<http://dati.umbria.it/base/ontology/numeroBagni>")) != null ? $p->getValue() : null);


            if (!$isAlreadyPersisted) {
                $this->em->persist($newStrutturaRicettiva);
            }

            $this->em->flush();
        }
    }

    private function deleteOldEntities($olderThan)
    {
        $this->strutturaRicettivaRepo->removeLastUpdatedBefore($olderThan);
    }
}
