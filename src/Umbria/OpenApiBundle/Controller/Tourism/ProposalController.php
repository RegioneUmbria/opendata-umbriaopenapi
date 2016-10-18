<?php

namespace Umbria\OpenApiBundle\Controller\Tourism;

use DateTime;
use Doctrine\ORM\EntityManager;
use EasyRdf_Graph;
use EasyRdf_Resource;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\Paginator;
use Nelmio\ApiDocBundle\Annotation as ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Proposal;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntitiesInnerObjects\ProposalDescription;
use Umbria\OpenApiBundle\Entity\Tourism\Setting;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\ProposalRepository;
use Umbria\OpenApiBundle\Serializer\View\EntityResponse;
use Umbria\OpenApiBundle\Service\FilterBag;

/**
 * Class ProposalController
 * @package Umbria\OpenApiBundle\Controller\Tourism
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class ProposalController extends BaseController
{
    const DEFAULT_PAGE_SIZE = 100;
    const DATASET_TOURISM_PROPOSAL = 'tourism-proposal';

    private $filterBag;
    private $paginator;

    private $em;
    /**@var ProposalRepository $proposalRepo */
    private $proposalRepo;
    private $settingsRepo;

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
        $this->proposalRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Proposal');
        $this->settingsRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\Setting');
        $this->em = $em;
    }


    /**
     * @Rest\Options(pattern="/open-api/tourism-proposal")
     */
    public function optionsTourismProposalAction()
    {
        $response = new Response();

        $response->setContent(json_encode(array(
            'Allow' => 'GET,OPTIONS',
            'GET' => array(
                'description' => 'A get request for proposals',
            ),
        )));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'json');

        return $response;
    }

    /**
     * @Rest\Get(pattern="/open-api/tourism-proposal")
     *
     * @param Request $request
     *
     * @return View
     *
     * @internal param Request $request
     *
     * @ApiDoc\ApiDoc(
     *  section = "Tourism",
     *  description = "Lista proposte turistiche regione Umbria",
     *  tags = {
     *      "beta"
     *  },
     *  parameters={
     *      {"name"="start", "dataType"="integer", "required"=false, "description"="Elemento da cui partire"},
     *      {"name"="limit", "dataType"="integer", "required"=false, "description"="Numero di elementi restituiti"}
     *  },
     *  statusCodes={
     *      200="Returned when successful"
     *  }
     * )
     */
    public function getTourismProposalListAction(Request $request)
    {
        $daysToOld = $this->container->getParameter('proposal_days_to_old');

        $filters = $this->filterBag->getFilterBag($request);
        $offset = $filters->has('start') ? $filters->get('start') : 0;
        $limit = $filters->has('limit') ? $filters->get('limit') : self::DEFAULT_PAGE_SIZE;
        if ($limit == 0) {
            $limit = self::DEFAULT_PAGE_SIZE;
        }
        $page = floor($offset / $limit) + 1;

        /** @var Setting $setting */
        $setting = $this->settingsRepo->findOneBy(array('datasetName' => self::DATASET_TOURISM_PROPOSAL));
        if ($setting != null) {
            $diff = $setting->getUpdatedAt()->diff(new DateTime('now'));
            if ($diff->days >= $daysToOld) {
                $setting->setDatasetName(self::DATASET_TOURISM_PROPOSAL);
                $setting->setUpdatedAtValue();
                $this->em->persist($setting);
                $this->em->flush();

                $this->updateEntities();
            }
        } else {
            $setting = new Setting();
            $setting->setDatasetName(self::DATASET_TOURISM_PROPOSAL);
            $setting->setUpdatedAtValue();
            $this->em->persist($setting);
            $this->em->flush();

            $this->updateEntities();
        }

        $builder = $this->em->createQueryBuilder()
            ->select('a')
            ->from('UmbriaOpenApiBundle:Tourism\GraphsEntities\Proposal', 'a');

        /** @var AbstractPagination $resultsPagination */
        $resultsPagination = $this->paginator->paginate($builder, $page, $limit);
        /** @var AbstractPagination $countPagination */
        $countPagination = $this->paginator->paginate($builder, 1, 1);

        $view = new View(new EntityResponse($resultsPagination->getItems(), count($resultsPagination), $countPagination->getTotalItemCount()));

        return $view;
    }

    private function updateEntities()
    {
        $this->graph = EasyRdf_Graph::newAndLoad($this->container->getParameter('proposal_graph_url'));
        /**@var EasyRdf_Resource[] $resources */
        $resources = $this->graph->resources();
        foreach ($resources as $resource) {
            $resourceTypeArray = $resource->all("rdf:type");
            if ($resourceTypeArray != null) {
                foreach ($resourceTypeArray as $resourceType) {
                    if (trim($resourceType) == "http://dati.umbria.it/tourism/ontology/proposte") {
                        $this->createOrUpdateEntity($resource);
                        break;
                    }
                }
            }

        }
        $now = new \DateTime();
        $this->deleteOldEntities($now);
    }

    /**
     * @param \EasyRdf_Resource $proposalResource
     */
    private function createOrUpdateEntity($proposalResource)
    {
        /** @var Proposal $newProposal */
        $newProposal = null;
        $uri = $proposalResource->getUri();
        if ($uri != null) {
            $oldProposal = $this->proposalRepo->find($uri);
            $isAlreadyPersisted = $oldProposal != null;
            if ($isAlreadyPersisted) {
                $newProposal = $oldProposal;
            } else {
                $newProposal = new Proposal();
            }
            $newProposal->setUri($uri);
            $newProposal->setLastUpdateAt(new \DateTime('now'));
            $newProposal->setName(($p = $proposalResource->get("rdfs:label")) != null ? $p->getValue() : null);
            /*TODO link esterni associati*/

            $typesarray = $proposalResource->all("rdf:type");
            if ($typesarray != null) {
                $tempTypes = array();

                $cnt = 0;
                foreach ($typesarray as $type) {
                    $tempTypes[$cnt] = $type->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempTypes) > 0 ? $newProposal->setTypes($tempTypes) : $newProposal->setTypes(null);
            }

            $newProposal->setProvenance(($p = $proposalResource->get("<http://purl.org/dc/elements/1.1/provenance>")) != null ? $p->getValue() : null);
            $newProposal->setResourceOriginUrl(($p = $proposalResource->get("<http://dati.umbria.it/tourism/ontology/url_risorsa>")) != null ? $p->getValue() : null);
            $newProposal->setProvenance(($p = $proposalResource->get("<http://purl.org/dc/elements/1.1/provenance>")) != null ? $p->getValue() : null);
            $newProposal->setSubject(($p = $proposalResource->get("<http://purl.org/dc/elements/1.1/subject>")) != null ? $p->getValue() : null);


            $imagearray1 = $proposalResource->all("<http://dati.umbria.it/tourism/ontology/immagine_copertina>");
            $imagearray2 = $proposalResource->all("<http://dati.umbria.it/tourism/ontology/immagine_spalla_destra>");
            $imagearray = array_merge($imagearray1, $imagearray2);
            if ($imagearray != null) {
                $tempImage = array();
                $newProposal->setImages(array());
                $cnt = 0;
                foreach ($imagearray as $image) {
                    $tempImage[$cnt] = $image->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempImage) > 0 ? $newProposal->setImages($tempImage) : $newProposal->setImages(null);
            }

            $newProposal->setTextTitle(($p = $proposalResource->get("<http://dati.umbria.it/tourism/ontology/titolo_testo>")) != null ? $p->getValue() : null);
            /*TODO link esterni associati*/

            $newProposal->setResourceOriginUrl(($p = $proposalResource->get("<http://dati.umbria.it/tourism/ontology/url_risorsa>")) != null ? $p->getValue() : null);
            $newProposal->setShortDescription(($p = $proposalResource->get("<http://dati.umbria.it/tourism/ontology/descrizione_sintetica>")) != null ? $p->getValue() : null);
            $newProposal->setLanguage(($p = $proposalResource->get("<http://purl.org/dc/elements/1.1/language>")) != null ? $p->getUri() : null);
            $newProposal->setComment(($p = $proposalResource->get("<http://www.w3.org/2000/01/rdf-schema#comment>")) != null ? $p->getValue() : null);


            if ($isAlreadyPersisted && ($oldDescriptions = $newProposal->getDescriptions()) != null) {
                foreach ($oldDescriptions as $oldDescription) {
                    $this->em->remove($oldDescription);
                }
                $newProposal->setDescriptions(null);
            }
            /**@var EasyRdf_Resource[] $descriptionArray */
            $descriptionArray = $proposalResource->all("<http://dati.umbria.it/tourism/ontology/descrizione>");
            if ($descriptionArray != null) {
                $tempDescriptions = array();
                $cnt = 0;
                foreach ($descriptionArray as $descriptionResource) {
                    $descriptionTitle = $descriptionResource->get("<http://dati.umbria.it/tourism/ontology/titolo>")->getValue();
                    $descriptionText = $descriptionResource->get("<http://dati.umbria.it/tourism/ontology/testo>")->getValue();
                    $descriptionObject = new ProposalDescription();
                    $descriptionObject->setTitle($descriptionTitle);
                    $descriptionObject->setText($descriptionText);
                    $descriptionObject->setProposal($newProposal);
                    $tempDescriptions[$cnt] = $descriptionObject;
                    $cnt++;
                }
                if (count($tempDescriptions) > 0) {
                    $newProposal->setDescriptions($tempDescriptions);
                }
            }

            /*TODO travel time*/

            $categoriesarray = $proposalResource->all("<http://dati.umbria.it/turismo/ontology/categoria>");
            if ($categoriesarray != null) {
                $tempCategories = array();

                $cnt = 0;
                foreach ($categoriesarray as $category) {
                    $tempCategories[$cnt] = $category->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempCategories) > 0 ? $newProposal->setCategories($tempCategories) : $newProposal->setCategories(null);
            }

            $newProposal->setPlaceFrom(($p = $proposalResource->get("<http://dati.umbria.it/tourism/ontology/luogo_da>")) != null ? $p->getValue() : null);
            $newProposal->setPlaceTo(($p = $proposalResource->get("<http://dati.umbria.it/tourism/ontology/luogo_a>")) != null ? $p->getValue() : null);
            $newProposal->setLat(($p = $proposalResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#lat>")) != null ? (float)$p->getValue() : null);
            $newProposal->setLng(($p = $proposalResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#long>")) != null ? (float)$p->getValue() : null);


            if (!$isAlreadyPersisted) {
                $this->em->persist($newProposal);
            }

            $this->em->flush();
        }
    }

    private function deleteOldEntities($olderThan)
    {
        $this->proposalRepo->removeLastUpdatedBefore($olderThan);

    }

    public function getWebResource($url = 'null', $writeError = true)
    {
        $ch = curl_init();
        try {
            if (false === $ch) {
                throw new Exception('failed to initialize');
            }

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $content = curl_exec($ch);
            curl_close($ch);

            if (false === $content) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            return $content;
        } catch (Exception $e) {
            if ($writeError == true) {
                trigger_error(sprintf(
                    'Curl failed with error #%d: %s, URL: %s',
                    $e->getCode(), $e->getMessage(), $url),
                    E_USER_ERROR);
            } else {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
        }

        return;
    }
}
