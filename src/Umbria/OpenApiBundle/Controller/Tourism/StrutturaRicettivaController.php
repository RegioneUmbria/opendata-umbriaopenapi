<?php


namespace Umbria\OpenApiBundle\Controller\Tourism;

use DateTime;
use Doctrine\ORM\EntityManager;
use EasyRdf_Sparql_Client;
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
     * @Rest\Get(pattern="/open-api/tourism-accomodation")
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
                $this->updateEntities();
                $setting->setDatasetName(self::DATASET_TOURISM_STRUTTURA_RICETTIVA);
                $setting->setUpdatedAtValue();
                $this->em->persist($setting);
                $this->em->flush();
            }
        } else {
            $this->updateEntities();
            $setting = new Setting();
            $setting->setDatasetName(self::DATASET_TOURISM_STRUTTURA_RICETTIVA);
            $setting->setUpdatedAtValue();
            $this->em->persist($setting);
            $this->em->flush();
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

        if ($latMax != null ||
            $latMin != null ||
            $lngMax != null ||
            $lngMin != null
        ) {
            $builder = $qb->join('a.address', 'address');
            if ($latMax != null) {
                $builder =
                    $qb->andWhere(
                        $qb->expr()->lte("address.lat", ':latMax'),
                        $qb->expr()->isNotNull("address.lat"),
                        $qb->expr()->gt("address.lat", ':empty')
                    )
                        ->setParameter('latMax', $latMax)
                        ->setParameter('empty', '0');
            }
            if ($latMin != null) {
                $builder =
                    $qb->andWhere(
                        $qb->expr()->gte("address.lat", ':latMin'),
                        $qb->expr()->isNotNull("address.lat"),
                        $qb->expr()->gt("address.lat", ":empty")
                    )
                        ->setParameter('latMin', $latMin)
                        ->setParameter('empty', '0');
            }
            if ($lngMax != null) {
                $builder =
                    $qb->andWhere(
                        $qb->expr()->lte("address.lng", ':lngMax'),
                        $qb->expr()->isNotNull("address.lng"),
                        $qb->expr()->gt("address.lng", ":empty")
                    )
                        ->setParameter('lngMax', $lngMax)
                        ->setParameter('empty', '0');
            }
            if ($lngMin != null) {
                $builder =
                    $qb->andWhere(
                        $qb->expr()->gte("address.lng", ':lngMin'),
                        $qb->expr()->isNotNull("address.lng"),
                        $qb->expr()->gt("address.lng", ":empty")
                    )
                        ->setParameter('lngMin', $lngMin)
                        ->setParameter('empty', '0');
            }
        }

        /** @var AbstractPagination $resultsPagination */
        $resultsPagination = $this->paginator->paginate($builder, $page, $limit);
        /** @var AbstractPagination $countPagination */
        $countPagination = $this->paginator->paginate($builder, 1, 1);


        $view = new View(new EntityResponse($resultsPagination->getItems(), count($resultsPagination), $countPagination->getTotalItemCount()));

        return $view;
    }

    private function updateEntities()
    {

        $sparqlClient = new EasyRdf_Sparql_Client("http://dati.umbria.it/sparql");
        $query ="    SELECT ?uri ?name ?provenance ?typology ?resourceOriginUrl ?units ?beds ?toilets
                     FROM <http://dati.umbria.it/graph/strutture_ricettive>
                     WHERE {
                        ?uri <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://purl.org/acco/ns#Accomodation>.
                            OPTIONAL{?uri <http://www.w3.org/2000/01/rdf-schema#label> ?name}.
                            OPTIONAL{?uri <http://purl.org/dc/elements/1.1/provenance> ?provenance}.
                            OPTIONAL{?uri <http://dati.umbria.it/base/ontology/tipologia> ?typology}.
                            OPTIONAL{?uri <http://schema.org/web> ?resourceOriginUrl}.
                            OPTIONAL{?uri <http://dati.umbria.it/base/ontology/numeroUnita> ?units}.
                            OPTIONAL{?uri <http://dati.umbria.it/base/ontology/numeroLetti> ?beds}.
                            OPTIONAL{?uri <http://dati.umbria.it/base/ontology/numeroBagni> ?toilets}.
                    } ";
        $sparqlResult = $sparqlClient->query($query);
        $sparqlResult->rewind();
        while ($sparqlResult->valid()) {
            /** @var StrutturaRicettiva $newStrutturaRicettiva */
            $newStrutturaRicettiva = null;

            $current = $sparqlResult->current();
            $uri=($current->uri->getUri());
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
                $newStrutturaRicettiva->setName(isset($current->name) ? $current->name->getValue() : null);
                $newStrutturaRicettiva->setProvenance(isset($current->provenance) ? $current->provenance->getValue() : null);
                $newStrutturaRicettiva->setTypology(isset($current->typology) ? $current->typology->getValue() : null);
                $newStrutturaRicettiva->setResourceOriginUrl(isset($current->resourceOriginUrl) ? $current->resourceOriginUrl->getValue() : null);
                $newStrutturaRicettiva->setUnits(isset($current->units) ? $current->units->getValue() : null);
                $newStrutturaRicettiva->setBeds(isset($current->beds) ? $current->beds->getValue() : null);
                $newStrutturaRicettiva->setToilets(isset($current->toilets) ? $current->toilets->getValue() : null);

                $queryEmail = "SELECT ?email FROM <http://dati.umbria.it/graph/strutture_ricettive> WHERE { <" . $uri . "> <http://schema.org/email> ?email. } ";
                $sparqlResultEmail = $sparqlClient->query($queryEmail);
                $sparqlResultEmail->rewind();
                if ($sparqlResultEmail->valid()) {
                    $tempEmail = array();
                    $cnt = 0;
                    while ($sparqlResultEmail->valid()) {
                        $tempEmail[$cnt] = $sparqlResultEmail->current()->email->getValue();
                        $cnt++;
                        $sparqlResultEmail->next();

                    }
                    count($tempEmail) > 0 ? $newStrutturaRicettiva->setEmail($tempEmail) : $newStrutturaRicettiva->setEmail(null);
                }

                $queryTelephone = "SELECT ?telephone FROM <http://dati.umbria.it/graph/strutture_ricettive> WHERE { <" . $uri . "> <http://schema.org/telephone> ?telephone. } ";
                $sparqlResultTelephone = $sparqlClient->query($queryTelephone);
                $sparqlResultTelephone->rewind();
                if ($sparqlResultTelephone->valid()) {
                    $tempTelephone = array();
                    $cnt = 0;
                    while ($sparqlResultTelephone->valid()) {
                        $tempTelephone[$cnt] = $sparqlResultTelephone->current()->telephone->getValue();
                        $cnt++;
                        $sparqlResultTelephone->next();
                    }
                    count($tempTelephone) > 0 ? $newStrutturaRicettiva->setTelephone($tempTelephone) : $newStrutturaRicettiva->setTelephone(null);
                }

                $queryFax = "SELECT ?fax FROM <http://dati.umbria.it/graph/strutture_ricettive> WHERE { <" . $uri . "> <http://schema.org/fax> ?fax. } ";
                $sparqlResultFax = $sparqlClient->query($queryFax);
                $sparqlResultFax->rewind();
                if ($sparqlResultFax->valid()) {
                    $tempFax = array();
                    $cnt = 0;
                    while ($sparqlResultFax->valid()) {
                        $tempFax[$cnt] = $sparqlResultFax->current()->fax->getValue();
                        $cnt++;
                        $sparqlResultFax->next();
                    }
                    count($tempFax) > 0 ? $newStrutturaRicettiva->setFax($tempFax) : $newStrutturaRicettiva->setFax(null);
                }

                $queryType = "SELECT ?type ?label ?comment
                FROM <http://dati.umbria.it/graph/strutture_ricettive>
                WHERE {
                    <" . $uri . "> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?type.
                    OPTIONAL{?type <http://www.w3.org/2000/01/rdf-schema#label> ?label}.
                    OPTIONAL{?type <http://www.w3.org/2000/01/rdf-schema#comment> ?comment}.
                } ";
                $sparqlResultType = $sparqlClient->query($queryType);
                $sparqlResultType->rewind();
                if ($sparqlResultType->valid()) {
                    /**@var Type[] $tempType */
                    $tempType = array();
                    $cnt = 0;
                    while ($sparqlResultType->valid()) {
                        $currentType=$sparqlResultType->current();
                        $typeUri= $currentType->type->getUri();
                        $oldType = $this->typeRepo->find($typeUri);
                        if ($oldType != null) {
                            $tempType[$cnt] = $oldType;
                        } else {
                            $tempType[$cnt] = new Type();
                            $tempType[$cnt]->setUri($typeUri);
                            $tempType[$cnt]->setName(isset($currentType->label) ? $currentType->label->getValue() : null);
                            $tempType[$cnt]->setComment(isset($currentType->comment) ? $currentType->comment->getValue() : null);
                        }
                        $cnt++;
                        $sparqlResultType->next();
                    }
                    count($tempType) > 0 ? $newStrutturaRicettiva->setTypes($tempType) : $newStrutturaRicettiva->setTypes(null);
                }

                $queryCategory = "SELECT ?category ?label ?comment
                FROM <http://dati.umbria.it/graph/strutture_ricettive>
                WHERE {
                    <" . $uri . "> <http://dati.umbria.it/tourism/ontology/categoria> ?category.
                    OPTIONAL{?category <http://www.w3.org/2000/01/rdf-schema#label> ?label}.
                    OPTIONAL{?category <http://www.w3.org/2000/01/rdf-schema#comment> ?comment}.
                } ";
                $sparqlResultCategory = $sparqlClient->query($queryCategory);
                $sparqlResultCategory->rewind();
                if ($sparqlResultCategory->valid()) {
                    /**@var Category[] $tempCategory */
                    $tempCategory = array();
                    $cnt = 0;
                    while ($sparqlResultCategory->valid()) {
                        $currentCategory=$sparqlResultCategory->current();
                        $categoryUri= $currentCategory->category->getUri();
                        $oldCategory = $this->categoryRepo->find($categoryUri);
                        if ($oldCategory != null) {
                            $tempCategory[$cnt] = $oldCategory;
                        } else {
                            $tempCategory[$cnt] = new Category();
                            $tempCategory[$cnt]->setUri($categoryUri);
                            $tempCategory[$cnt]->setName(isset($currentCategory->label) ? $currentCategory->label->getValue() : null);
                            $tempCategory[$cnt]->setComment(isset($currentCategory->comment) ? $currentCategory->comment->getValue() : null);
                        }
                        $cnt++;
                        $sparqlResultCategory->next();
                    }
                    count($tempCategory) > 0 ? $newStrutturaRicettiva->setCategories($tempCategory) : $newStrutturaRicettiva->setCategories(null);
                }

                $queryAddress = "SELECT ?uri ?postalcode ?istat ?addressLocality ?addressRegion ?streetAddress ?lat ?lng
                                FROM <http://dati.umbria.it/graph/strutture_ricettive>
                                WHERE {
                                     <" . $uri . "> <http://schema.org/address> ?uri.
                                    OPTIONAL{?uri <http://schema.org/postalCode> ?postalcode}.
                                    OPTIONAL{?uri <http://dbpedia.org/ontology/istat> ?istat}.
                                    OPTIONAL{?uri <http://schema.org/addressLocality> ?addressLocality}.
                                    OPTIONAL{?uri <http://schema.org/addressRegion> ?addressRegion}.
                                    OPTIONAL{?uri <http://schema.org/streetAddress> ?streetAddress}.
                                    OPTIONAL{?uri <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat}.
                                    OPTIONAL{?uri <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lng}.
                                }";
                $sparqlResultAddress = $sparqlClient->query($queryAddress);
                $sparqlResultAddress->rewind();
                if ($sparqlResultAddress->valid()) {
                    /**@var Address $tempAddress */
                    $tempAddress =  new Address();
                    $currentAddress=$sparqlResultAddress->current();
                    $tempAddress->setPostalCode(isset($currentAddress->postalCode) ? $currentAddress->postalCode->getValue() : null);
                    $tempAddress->setIstat(isset($currentAddress->istat) ? $currentAddress->istat->getValue() : null);
                    $tempAddress->setAddressLocality(isset($currentAddress->addressLocality) ? $currentAddress->addressLocality->getValue() : null);
                    $tempAddress->setAddressRegion(isset($currentAddress->addressRegion) ? $currentAddress->addressRegion->getValue() : null);
                    $tempAddress->setStreetAddress(isset($currentAddress->streetAddress) ? $currentAddress->streetAddress->getValue() : null);
                    $tempAddress->setLat(isset($currentAddress->lat) ? $currentAddress->lat->getValue() : null);
                    $tempAddress->setLng(isset($currentAddress->lng) ? $currentAddress->lng->getValue() : null);
                    $newStrutturaRicettiva->setAddress($tempAddress);
                }

                if (!$isAlreadyPersisted) {
                    $this->em->persist($newStrutturaRicettiva);
                }
                $this->em->flush();
            }
            $sparqlResult->next();
        }
        $now = new \DateTime();
        $this->deleteOldEntities($now);
    }

        private function deleteOldEntities($olderThan)
    {
        $this->strutturaRicettivaRepo->removeLastUpdatedBefore($olderThan);
    }

}
