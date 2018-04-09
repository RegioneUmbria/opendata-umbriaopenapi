<?php


namespace Umbria\OpenApiBundle\Controller;

use DateTime;
use Doctrine\ORM\EntityManager;
use EasyRdf_Graph;
use EasyRdf_Literal;
use EasyRdf_Resource;
use EasyRdf_Sparql_Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Umbria\OpenApiBundle\Entity\Address;
use Umbria\OpenApiBundle\Entity\Category;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Accomodation;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Attractor;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Consortium;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Event;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Iat;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Profession;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Proposal;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\SportFacility;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\TravelAgency;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntitiesInnerObjects\AttractorDescription;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntitiesInnerObjects\EventDescription;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntitiesInnerObjects\ProposalDescription;
use Umbria\OpenApiBundle\Entity\Tourism\Setting;
use Umbria\OpenApiBundle\Entity\Type;
use Umbria\OpenApiBundle\Repository\CategoryRepository;
use JMS\DiExtraBundle\Annotation as DI;
use Umbria\OpenApiBundle\Repository\TypeRepository;
use Throwable;
use ReflectionClass;

class EntitiesUpdateController extends BaseController
{

    /**@var CategoryRepository categoryRepo */
    private $categoryRepo;
    /**@var TypeRepository typeRepo */
    private $typeRepo;
    private $em;

    /**
     * @DI\InjectParams({
     *      "em" = @DI\Inject("doctrine.orm.entity_manager"),
     * })
     * @param $em EntityManager
     */
    public function __construct($em)
    {
        parent::__construct($em);
        $this->em = $em;
        $this->categoryRepo = $em->getRepository('UmbriaOpenApiBundle:Category');
        $this->typeRepo = $em->getRepository('UmbriaOpenApiBundle:Type');
    }

    /**this controller should be secured (in the web server configurations) against not allowed IP addresses*/
    public function indexAction(Request $request)
    {
        $response = array();
        $entityTypes = array();
        $errors_only = false;
        foreach ($request->attributes->all()["_route_params"] as $attribute => $value) {
            if ($attribute == "error" and $value == "1") {
                $errors_only = true;
            } else {
                $entityTypes[$attribute] = $value;
            }
        }
        foreach ($entityTypes as $entityType => $updateEntityType) {
            $responseObj = new \stdClass();
            $responseObj->entityType = $entityType;
            if ($updateEntityType == "1") {
                $settingsRepo = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\Setting');
                $setting = $settingsRepo->findOneBy(array('datasetName' => $entityType));

                $has_errors = false;
                $isFirstEntityTypeRetrieve = false;
                $isUpdating = false;
                if ($setting == null) {
                    $setting = new Setting();
                    $setting->setDatasetName($entityType);
                    $setting->setIsUpdating(false);
                    $setting->setHasErrors($has_errors);
                    $isFirstEntityTypeRetrieve = true;
                    $this->em->persist($setting);
                } else {
                    $isUpdating = $setting->getIsUpdating();
                    if ($isUpdating == null) {
                        $isUpdating = false;
                    }
                }

                if (!$isUpdating) {
                    $setting->setIsUpdating(true);
                    $this->em->flush();

                    $logger = $this->get('logger');
                    try {
                        $logger->info("$entityType update start");
                        $responseObj->start = new \DateTime();
                        $has_errors = $this->createUpdateDeleteEntities($entityType, $errors_only);
                        $this->em->flush();
                    } catch (Throwable $e) {
                        $has_errors = true;
                        $responseObj->error = $e->getMessage();
                        $logger->error('$entityType update failed with error: ' . $e->getTraceAsString());
                    } finally {
                        $setting->setUpdatedAt(new \DateTime());
                        $setting->setIsUpdating(false);
                        $setting->setHasErrors($has_errors);
                        $this->em->flush();
                        $logger->info("$entityType update end");
                        $responseObj->end = new \DateTime();
                        if ($has_errors == true) {
                            /*invia email con tipo di entità*/
                            $this->send_error_mail($entityType);
                            $responseObj->error = "Check entity table for individual errors!";
                        }

                    }
                    $response[] = $responseObj;
                }
            }
        }
        $response = new Response(json_encode($response, JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    private function createUpdateDeleteEntities($entityType, $errors_only)
    {
        $has_errors_create = false;
        $has_errors_update = false;
        $graph = $this->getGraph($entityType);
        $has_errors_update = $this->updateDeleteEntities($graph, $entityType, $errors_only);
        if ($errors_only != true) {
            $has_errors_create = $this->createEntities($graph, $entityType);
        }
        return $has_errors_create || $has_errors_update;
    }


    private function getGraph($entityType)
    {
        $graph = null;
        if ($entityType == "accomodation") {
            $sparqlClient = new EasyRdf_Sparql_Client("http://dati.umbria.it/sparql?format=text%2Fturtle");
            $query = "
CONSTRUCT {?s ?p ?o.}
WHERE{
	SELECT DISTINCT ?s ?p ?o
	FROM <http://dati.umbria.it/graph/strutture_ricettive>
	WHERE{
		?s ?p ?o .
		?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://purl.org/acco/ns#Accomodation>.
		FILTER( ?p = <http://www.w3.org/2000/01/rdf-schema#label> ).			
	}
}
";

            $graph = $sparqlClient->query($query);
        }
        else{
            $graphURL = $this->getGraphURLByEntityType($entityType);
            if ($graphURL != "") {
                $graph = EasyRdf_Graph::newAndLoad($graphURL);
            }
        }

        return $graph;
    }

    /**
     * Update entities already persisted. If entity uri is not found in RDF graph, then logically delete entity.
     */
    private function updateDeleteEntities($graph, $entityType, $errors_only)
    {
        $has_error = false;
        $entityClassName = $this->getEntityTypeClassName($entityType);
        /**get all entities from db by type*/
        /**
         * @var Doctrine\ORM\EntityRepository $entityRepo
         */
        $entityRepo = $this->em->getRepository(trim("UmbriaOpenApiBundle:Tourism\GraphsEntities\ ") . $entityClassName);
        $entities = $entityRepo->findAll();
        $resources = $graph->resources();
        foreach ($entities as $entity) {
            if ($errors_only == false || $entity->isInError() == true) {
                $entity_uri = "";
                $isResourceToBeSaved = false;
                $is_entity_error = false;
                $is_to_delete = false;
                try {
                    $entity_uri = $entity->getUri();
                    if (isset($resources[$entity_uri])) {
                        $RDFResource = $graph->resource($entity_uri);
                        //update entity
                        $entityTypeURI = $this->getResourceTypeURIByEntityType($entityType);
                        //check if resource match type and provenance (dati.umbria.it)
                        $updatedEntity = $this->{"createOrUpdate" . $entityClassName}($RDFResource);
                        if ($updatedEntity != null) {
                            $updatedEntity->setLastUpdateAt(new \DateTime('now'));
                            $updatedEntity->setIsDeleted(false);
                            $updatedEntity->setIsInError(false);
                            $this->em->flush();
                        } else {
                            $is_entity_error = true;
                        }
                    } else {
                        $is_to_delete = true;
                        $this->get('logger')->info("Delete URI " + $entity_uri);
                    }
                    if ($is_to_delete == true) {
                        //logically delete entity
                        $entity->setLastUpdateAt(new \DateTime('now'));
                        $entity->setIsInError(false);
                        $entity->setIsDeleted(true);
                        $this->em->flush();
                    }
                } catch (Throwable $e) {
                    $is_entity_error = true;
                    $this->get('logger')->error("$resourceURI create error:" . $e->getTraceAsString());
                }
                if ($is_entity_error == true) {
                    $has_error = true;

                    try {
                        $this->get('logger')->info("$entity->getUri() trying to save with state in error");
                        $entity->setLastUpdateAt(new \DateTime('now'));
                        $entity->setIsDeleted(false);
                        $entity->setIsInError(true);
                        $this->em->flush();
                        $this->get('logger')->info("$entity->getUri() saved with state in error");
                    } catch (Throwable $e2) {
                        $this->get('logger')->error($entity->getUri() . "error trying to save with state in error:" . $e->getTraceAsString());
                    }
                }
            }
        }
        return $has_error;
    }


    /**
     * Create new entites for those new resources founded in the RDF graph
     */
    private function createEntities($graph, $entityType)
    {
        $has_error = false;
        $entityClassName = $this->getEntityTypeClassName($entityType);
        $entityRepo = $this->em->getRepository(trim("UmbriaOpenApiBundle:Tourism\GraphsEntities\ ") . $entityClassName);
        //get all resources from graph by type
        $resources = $graph->resources();
        foreach ($resources as $RDFResource) {
            $isResourceToBeSaved = false;
            $is_entity_error = false;
            $resourceURI = "";
            try {
                $resourceURI = $RDFResource->getURI();
                //check if resource match type and provenance (dati.umbria.it)
                $entityTypeURI = $this->getResourceTypeURIByEntityType($entityType);
                $isResourceToBeSaved = $this->isResourceToBeSaved($RDFResource, $entityType, $entityTypeURI);
                if ($isResourceToBeSaved) {
                    //check if there is a resource with the same uri and same type on db
                    $entity = $entityRepo->find($resourceURI);
                    $isAlreadyPersisted = $entity != null;
                    if (!$isAlreadyPersisted) {
                        //create new resource
                        $createdEntity = $this->{"createOrUpdate" . $entityClassName}($RDFResource);
                        if ($createdEntity != null) {
                            $createdEntity->setLastUpdateAt(new \DateTime('now'));
                            $createdEntity->setIsDeleted(false);
                            $createdEntity->setIsInError(false);
                            $this->em->persist($createdEntity);
                            $this->em->flush();
                        } else {
                            $is_entity_error = true;
                        }

                    } else {
                        $this->get('logger')->info("Skip URI (already be saved) " + $resourceURI);
                    }
                } else {
                    $this->get('logger')->info("Skip URI (not to be saved) " + $resourceURI);
                }
            } catch (Throwable $e) {
                $is_entity_error = true;
                $this->get('logger')->error("$resourceURI create error:" . $e->getTraceAsString());
            }
            if ($is_entity_error) {
                $has_error = true;
                if ($resourceURI != null) {
                    try {
                        if ($resourceURI != "" && $isResourceToBeSaved == true) {
                            $entityClassNameComplete = "Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\\$entityClassName";
                            $inErrorEntity = new $entityClassNameComplete();
                            $inErrorEntity->setUri($resourceURI);
                            $inErrorEntity->setIsDeleted(false);
                            $inErrorEntity->setIsInError(true);
                            $inErrorEntity->setLastUpdateAt(new \DateTime('now'));
                            $this->em->persist($inErrorEntity);
                            $this->em->flush();
                        }
                    } catch (Throwable $e2) {
                        $this->get('logger')->error("$resourceURI error trying to save with state in error:" . $e2->getTraceAsString());
                    }
                }
            }
        }
        return $has_error;
    }


    private function createOrUpdateAccomodation($accomodationResource)
    {
        $accomodationRepo = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Accomodation');
        /** @var Attractor $newAttractor */
        $newAccomodation = null;
        $uri = $accomodationResource->getUri();
        $sparqlClient = new EasyRdf_Sparql_Client("http://dati.umbria.it/sparql");
        if ($uri != null) {
            $oldAccomodation = $accomodationRepo->find($uri);
            $isAlreadyPersisted = $oldAccomodation != null;
            if ($isAlreadyPersisted) {
                $newAccomodation = $oldAccomodation;
            } else {
                $newAccomodation = new Accomodation();
            }

            $newAccomodation->setUri($uri);
            $this->mapName($accomodationResource, $newAccomodation);

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
                    $currentType = $sparqlResultType->current();
                    $typeUri = $currentType->type->getUri();
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
                count($tempType) > 0 ? $newAccomodation->setTypes($tempType) : $newAccomodation->setTypes(null);
            }

            $query = "    SELECT  ?provenance ?typology ?resourceOriginUrl ?units ?beds ?toilets
                     FROM <http://dati.umbria.it/graph/strutture_ricettive>
                     WHERE {
                        
                            OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/tipologia> ?typology}.
                            OPTIONAL{<" . $uri . "> <http://schema.org/web> ?resourceOriginUrl}.
                            OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/numeroUnita> ?units}.
                            OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/numeroLetti> ?beds}.
                            OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/numeroBagni> ?toilets}.
                    } ";


            $sparqlResult = $sparqlClient->query($query);
            $sparqlResult->rewind();
            if ($sparqlResult->valid()) {
                if (isset($sparqlResult->current()->resourceOriginUrl)) $newAccomodation->setResourceOriginUrl($sparqlResult->current()->resourceOriginUrl->getValue());
                if (isset($sparqlResult->current()->typology)) $newAccomodation->setTypology($sparqlResult->current()->typology->getValue());
                if (isset($sparqlResult->current()->units)) $newAccomodation->setUnits($sparqlResult->current()->units->getValue());
                if (isset($sparqlResult->current()->beds)) $newAccomodation->setBeds($sparqlResult->current()->beds->getValue());
                if (isset($sparqlResult->current()->toilets)) $newAccomodation->setToilets($sparqlResult->current()->toilets->getValue());
            }


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
                count($tempEmail) > 0 ? $newAccomodation->setEmail($tempEmail) : $newAccomodation->setEmail(null);
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
                count($tempTelephone) > 0 ? $newAccomodation->setTelephone($tempTelephone) : $newAccomodation->setTelephone(null);
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
                count($tempFax) > 0 ? $newAccomodation->setFax($tempFax) : $newAccomodation->setFax(null);
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
                    $currentCategory = $sparqlResultCategory->current();
                    $categoryUri = $currentCategory->category->getUri();
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
                count($tempCategory) > 0 ? $newAccomodation->setCategories($tempCategory) : $newAccomodation->setCategories(null);
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
                $tempAddress = new Address();
                $currentAddress = $sparqlResultAddress->current();
                $tempAddress->setPostalCode(isset($currentAddress->postalCode) ? $currentAddress->postalCode->getValue() : null);
                $tempAddress->setIstat(isset($currentAddress->istat) ? $currentAddress->istat->getValue() : null);
                $tempAddress->setAddressLocality(isset($currentAddress->addressLocality) ? $currentAddress->addressLocality->getValue() : null);
                $tempAddress->setAddressRegion(isset($currentAddress->addressRegion) ? $currentAddress->addressRegion->getValue() : null);
                $tempAddress->setStreetAddress(isset($currentAddress->streetAddress) ? $currentAddress->streetAddress->getValue() : null);
                $tempAddress->setLat(isset($currentAddress->lat) ? $currentAddress->lat->getValue() : null);
                $tempAddress->setLng(isset($currentAddress->lng) ? $currentAddress->lng->getValue() : null);
                $newAccomodation->setAddress($tempAddress);
            }


        }
        return $newAccomodation;
    }

    /**
     * @param EasyRdf_Resource $attractorResource
     */
    private function createOrUpdateAttractor($attractorResource)
    {
        $attractorRepo = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Attractor');
        /** @var Attractor $newAttractor */
        $newAttractor = null;
        $uri = $attractorResource->getUri();
        if ($uri != null) {
            $oldAttractor = $attractorRepo->find($uri);
            $isAlreadyPersisted = $oldAttractor != null;
            if ($isAlreadyPersisted) {
                $newAttractor = $oldAttractor;
            } else {
                $newAttractor = new Attractor();
            }
            $newAttractor->setUri($uri);
            $newAttractor->setLastUpdateAt(new \DateTime('now'));

            $this->mapName($attractorResource, $newAttractor);
            $this->mapTypes($attractorResource, $newAttractor);
            $this->mapCategories($attractorResource, $newAttractor);
            $this->mapComment($attractorResource, $newAttractor);
            $this->mapTitle($attractorResource, $newAttractor);
            $this->mapImages($attractorResource, $newAttractor);
            $newAttractor->setResourceOriginUrl(($p = $attractorResource->get("<http://dati.umbria.it/tourism/ontology/url_risorsa>")) != null ? $p->getValue() : null);
            $newAttractor->setProvenance(($p = $attractorResource->get("<http://purl.org/dc/elements/1.1/provenance>")) != null ? $p->getValue() : null);
            $newAttractor->setMunicipality(($p = $attractorResource->get("<http://dbpedia.org/ontology/municipality>")) != null ? $p->getValue() : null);
            $newAttractor->setIstat(($p = $attractorResource->get("<http://dbpedia.org/ontology/istat>")) != null ? $p->getValue() : null);
            $newAttractor->setSubject(($p = $attractorResource->get("<http://purl.org/dc/elements/1.1/subject>")) != null ? $p->getValue() : null);
            $newAttractor->setLat(($p = $attractorResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#lat>")) != null ? (float)$p->getValue() : null);
            $newAttractor->setLng(($p = $attractorResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#long>")) != null ? (float)$p->getValue() : null);


            /**@var EasyRdf_Literal[] $shortDescriptionArray */
            $shortDescriptionArray = $attractorResource->all("<http://dati.umbria.it/tourism/ontology/descrizione_sintetica>");
            foreach ($shortDescriptionArray as $shortDescription) {
                if ($shortDescription->getLang() == "it") {
                    $newAttractor->setShortDescription($shortDescription->getValue());
                    break;
                }
            }

            if ($isAlreadyPersisted && ($oldDescriptions = $newAttractor->getDescriptions()) != null) {
                foreach ($oldDescriptions as $oldDescription) {
                    $this->em->remove($oldDescription);
                }
                $newAttractor->setDescriptions(null);
            }
            /**@var EasyRdf_Resource[] $descriptionArray */
            $descriptionArray = $attractorResource->all("<http://dati.umbria.it/tourism/ontology/descrizione>");
            if ($descriptionArray != null) {
                $tempDescriptions = array();
                $cnt = 0;
                foreach ($descriptionArray as $descriptionResource) {
                    if ($descriptionResource->get("<http://dati.umbria.it/tourism/ontology/testo>") != null &&
                        $descriptionResource->get("<http://dati.umbria.it/tourism/ontology/testo>")->getLang() == "it"
                    ) {
                        $descriptionTitle = ($p = $descriptionResource->get("<http://dati.umbria.it/tourism/ontology/titolo>")) != null ? $p->getValue() : null;
                        $descriptionText = $descriptionResource->get("<http://dati.umbria.it/tourism/ontology/testo>")->getValue();
                        $descriptionObject = new AttractorDescription();
                        $descriptionObject->setTitle($descriptionTitle);
                        $descriptionObject->setText($descriptionText);
                        $descriptionObject->setAttractor($newAttractor);
                        $tempDescriptions[$cnt] = $descriptionObject;
                        $cnt++;
                    }
                }
                if (count($tempDescriptions) > 0) {
                    $newAttractor->setDescriptions($tempDescriptions);
                }
            }

            /**@var EasyRdf_Resource[] $sameAsArray */
            $sameAsArray = $attractorResource->all("<http://www.w3.org/2002/07/owl#sameAs>");
            if ($sameAsArray != null) {
                $newAttractor->setSameAs($this->getExternalResources($sameAsArray, "http://dbpedia.org/sparql",
                    "http://www.w3.org/2000/01/rdf-schema#label", "http://dbpedia.org/ontology/abstract", "http://www.w3.org/ns/prov#wasDerivedFrom"));
            }
            /**@var EasyRdf_Resource[] $locatedInArray */
            $locatedInArray = $attractorResource->all("<http://www.geonames.org/ontology#locatedIn>");
            if ($locatedInArray != null) {
                $newAttractor->setLocatedIn($this->getExternalResources($locatedInArray, "http://it.dbpedia.org/sparql",
                    "http://www.w3.org/2000/01/rdf-schema#label", "http://dbpedia.org/ontology/abstract", "http://www.w3.org/ns/prov#wasDerivedFrom"));
            }
        }
        return $newAttractor;
    }

    /**
     * @param \EasyRdf_Resource $consortiumResource
     */
    private function createOrUpdateConsortium($consortiumResource)
    {
        $consortiumRepo = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Consortium');
        /** @var Consortium $newConsortium */
        $newConsortium = null;
        $uri = $consortiumResource->getUri();
        if ($uri != null) {
            $oldConsortium = $consortiumRepo->find($uri);
            $isAlreadyPersisted = $oldConsortium != null;
            if ($isAlreadyPersisted) {
                $newConsortium = $oldConsortium;
            } else {
                $newConsortium = new Consortium();
            }
            $newConsortium->setUri($uri);
            $newConsortium->setLastUpdateAt(new \DateTime('now'));

            $this->mapName($consortiumResource, $newConsortium);
            $this->mapTypes($consortiumResource, $newConsortium);
            $this->mapEmail($consortiumResource, $newConsortium);
            $this->mapTelephone($consortiumResource, $newConsortium);
            $this->mapFax($consortiumResource, $newConsortium);
            $this->mapAddress($consortiumResource, $newConsortium, $isAlreadyPersisted);
            $newConsortium->setResourceOriginUrl(($p = $consortiumResource->get("<http://xmlns.com/foaf/0.1/homepage>")) != null ? $p->getValue() : null);
            $newConsortium->setLanguage(($p = $consortiumResource->get("<http://purl.org/dc/elements/1.1/language>")) != null ? $p->getUri() : null);

        }
        return $newConsortium;
    }

    /**
     * @param \EasyRdf_Resource $eventResource
     */
    private function createOrUpdateEvent($eventResource)
    {
        $eventRepo = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Event');
        /** @var Event $newEvent */
        $newEvent = null;
        $uri = $eventResource->getUri();
        if ($uri != null) {
            $oldEvent = $eventRepo->find($uri);
            $isAlreadyPersisted = $oldEvent != null;
            if ($isAlreadyPersisted) {
                $newEvent = $oldEvent;
            } else {
                $newEvent = new Event();
            }
            $newEvent->setUri($uri);
            $newEvent->setLastUpdateAt(new \DateTime('now'));

            $this->mapName($eventResource, $newEvent);
            $this->mapTypes($eventResource, $newEvent);
            $this->mapImages($eventResource, $newEvent);
            $newEvent->setResourceOriginUrl(($p = $eventResource->get("<http://xmlns.com/foaf/0.1/homepage>")) != null ? $p->getValue() : null);
            $newEvent->setMunicipality(($p = $eventResource->get("<http://dbpedia.org/ontology/municipality>")) != null ? $p->getValue() : null);
            $newEvent->setIstat(($p = $eventResource->get("<http://dbpedia.org/ontology/istat>")) != null ? $p->getValue() : null);

            /**@var EasyRdf_Literal[] $commentArray */
            $commentArray = $eventResource->all("<http://dati.umbria.it/tourism/ontology/event_description>");
            foreach ($commentArray as $comment) {
                if ($comment->getLang() == "it") {
                    $newEvent->setComment($comment->getValue());
                    break;
                }
            }
            $newEvent->setLat(($p = $eventResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#lat>")) != null ? (float)$p->getValue() : null);
            $newEvent->setLng(($p = $eventResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#long>")) != null ? (float)$p->getValue() : null);
            $startDate = $eventResource->get("<http://schema.org/start_date>");
            if ($startDate != null) {
                $startDateObj = DateTime::createFromFormat('d/m/Y', $startDate);
                $newEvent->setStartDate($startDateObj);
            }
            $endDate = $eventResource->get("<http://schema.org/end_date>");
            if ($endDate != null) {
                $endDateObj = DateTime::createFromFormat('d/m/Y', $endDate);
                $newEvent->setEndDate($endDateObj);
            }

            if ($isAlreadyPersisted && ($oldDescriptions = $newEvent->getDescriptions()) != null) {
                foreach ($oldDescriptions as $oldDescription) {
                    $this->em->remove($oldDescription);
                }
                $newEvent->setDescriptions(null);
            }
            /**@var EasyRdf_Resource[] $descriptionArray */
            $descriptionArray = $eventResource->all("<http://dati.umbria.it/tourism/ontology/descrizione>");
            if ($descriptionArray != null) {
                $tempDescriptions = array();
                $cnt = 0;
                foreach ($descriptionArray as $descriptionResource) {
                    if ($descriptionResource->get("<http://dati.umbria.it/tourism/ontology/titolo>") != null &&
                        $descriptionResource->get("<http://dati.umbria.it/tourism/ontology/titolo>")->getLang() == "it"
                    ) {
                        $descriptionText = ($p = $descriptionResource->get("<http://dati.umbria.it/tourism/ontology/text>")) != null ? $p->getValue() : null;
                        $descriptionTitle = $descriptionResource->get("<http://dati.umbria.it/tourism/ontology/titolo>")->getValue();
                        $descriptionObject = new EventDescription();
                        $descriptionObject->setTitle($descriptionTitle);
                        $descriptionObject->setText($descriptionText);
                        $descriptionObject->setEvent($newEvent);
                        $tempDescriptions[$cnt] = $descriptionObject;
                        $cnt++;
                    }
                }
                if (count($tempDescriptions) > 0) {
                    $newEvent->setDescriptions($tempDescriptions);
                }
            }
        }
        return $newEvent;
    }

    /**
     * @param \EasyRdf_Resource $iatResource
     */
    private function createOrUpdateIat($iatResource)
    {
        $iatRepo = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Iat');
        /** @var Iat $newIat */
        $newIat = null;
        $uri = $iatResource->getUri();
        if ($uri != null) {
            $oldIat = $iatRepo->find($uri);
            $isAlreadyPersisted = $oldIat != null;
            if ($isAlreadyPersisted) {
                $newIat = $oldIat;
            } else {
                $newIat = new Iat();
            }
            $newIat->setUri($uri);
            $newIat->setLastUpdateAt(new \DateTime('now'));

            $this->mapName($iatResource, $newIat);
            $this->mapTypes($iatResource, $newIat);
            $this->mapEmail($iatResource, $newIat);
            $this->mapTelephone($iatResource, $newIat);
            $this->mapFax($iatResource, $newIat);
            $this->mapAddress($iatResource, $newIat, $isAlreadyPersisted);

            $newIat->setLanguage(($p = $iatResource->get("<http://purl.org/dc/elements/1.1/language>")) != null ? $p->getUri() : null);
            $newIat->setMunicipalitiesList(($p = $iatResource->get("<http://dati.umbria.it/tourism/ontology/lista_comuni>")) != null ? $p->getValue() : null);
        }
        return $newIat;
    }

    /**
     * @param \EasyRdf_Resource $sportFacilityResource
     */
    private function createOrUpdateSportFacility($sportFacilityResource)
    {
        $sportFacilityRepo = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\SportFacility');
        /** @var SportFacility $newSportFacility */
        $newSportFacility = null;
        $uri = $sportFacilityResource->getUri();
        if ($uri != null) {
            $oldSportFacility = $sportFacilityRepo->find($uri);
            $isAlreadyPersisted = $oldSportFacility != null;
            if ($isAlreadyPersisted) {
                $newSportFacility = $oldSportFacility;
            } else {
                $newSportFacility = new SportFacility();
            }
            $newSportFacility->setUri($uri);
            $newSportFacility->setLastUpdateAt(new \DateTime('now'));
            $this->mapName($sportFacilityResource, $newSportFacility);
            $this->mapTypes($sportFacilityResource, $newSportFacility);
            $this->mapAddress($sportFacilityResource, $newSportFacility, $isAlreadyPersisted);


            $newSportFacility->setMunicipality(($p = $sportFacilityResource->get("<http://dbpedia.org/ontology/municipality>")) != null ? $p->getValue() : null);
            $newSportFacility->setPublicTransport(($p = $sportFacilityResource->get("<http://dati.umbria.it/base/ontology/trasportoPubblico>")) != null ? $p->getValue() : null);
            $newSportFacility->setParkings(($p = $sportFacilityResource->get("<http://dati.umbria.it/base/ontology/numeroPostiMacchina>")) != null ? $p->getValue() : null);
            $newSportFacility->setDisabledAccess(($p = $sportFacilityResource->get("<http://dati.umbria.it/base/ontology/accessoDisabili>")) != null && strtoupper($p->getValue()) == "TRUE" ? 1 : 0);
            $newSportFacility->setEmployees(($p = $sportFacilityResource->get("<http://dbpedia.org/ontology/numberOfEmployees>")) != null ? $p->getValue() : null);

            /**@var EasyRdf_Resource[] $sportarray */
            $sportarray = $sportFacilityResource->all("<http://schema.org/sport>");
            if ($sportarray != null) {
                $tempSport = array();
                $cnt = 0;
                foreach ($sportarray as $sport) {
                    $tempSport[$cnt] = $sport->toRdfPhp()['value'];
                    $cnt++;
                }
                count($tempSport) > 0 ? $newSportFacility->setSport($tempSport) : $newSportFacility->setSport(null);
            }
        }
        return $newSportFacility;
    }

    /**
     * @param \EasyRdf_Resource $professionResource
     */
    private function createOrUpdateProfession($professionResource)
    {
        $professionRepo = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Profession');
        /** @var Profession $newProfession */
        $newProfession = null;
        $uri = $professionResource->getUri();
        if ($uri != null) {
            $oldProfession = $professionRepo->find($uri);
            $isAlreadyPersisted = $oldProfession != null;
            if ($isAlreadyPersisted) {
                $newProfession = $oldProfession;
            } else {
                $newProfession = new Profession();
            }
            $newProfession->setUri($uri);
            $newProfession->setLastUpdateAt(new \DateTime('now'));
            $newProfession->setFirstName(($p = $professionResource->get("<http://xmlns.com/foaf/0.1/firstName>")) != null ? $p->getValue() : null);
            $newProfession->setLastName(($p = $professionResource->get("<http://xmlns.com/foaf/0.1/lastName>")) != null ? $p->getValue() : null);
            $this->mapTypes($professionResource, $newProfession);
            $this->mapTelephone($professionResource, $newProfession);
            $this->mapFax($professionResource, $newProfession);
            $this->mapEmail($professionResource, $newProfession);
            $this->mapAddress($professionResource, $newProfession, $isAlreadyPersisted);

            $newProfession->setResourceOriginUrl(($p = $professionResource->get("<http://dati.umbria.it/tourism/ontology/url_risorsa>")) != null ? $p->getValue() : null);

            $spokenLanguagearray = $professionResource->all("<http://dati.umbria.it/tourism/ontology/lingua_parlata>");
            if ($spokenLanguagearray != null) {
                $tempSpokenLanguage = array();
                $newProfession->setSpokenLanguage(array());
                $cnt = 0;
                foreach ($spokenLanguagearray as $spokenLanguage) {
                    if ($spokenLanguage->getLang() == "it") {
                        $tempSpokenLanguage[$cnt] = $spokenLanguage->toRdfPhp()['value'];
                        $cnt++;
                    }
                }
                count($tempSpokenLanguage) > 0 ? $newProfession->setSpokenLanguage($tempSpokenLanguage) : $newProfession->setSpokenLanguage(null);
            }

            $specializationarray = $professionResource->all("<http://dati.umbria.it/tourism/ontology/specializzazione>");
            if ($specializationarray != null) {
                $tempSpecialization = array();
                $newProfession->setSpecialization(array());
                $cnt = 0;
                foreach ($specializationarray as $specialization) {
                    if ($specialization->getLang() == "it") {
                        $tempSpecialization[$cnt] = $specialization->toRdfPhp()['value'];
                        $cnt++;
                    }
                }
                count($tempSpecialization) > 0 ? $newProfession->setSpecialization($tempSpecialization) : $newProfession->setSpecialization(null);
            }
        }
        return $newProfession;
    }

    /**
     * @param \EasyRdf_Resource $proposalResource
     */
    private function createOrUpdateProposal($proposalResource)
    {
        $proposalRepo = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Proposal');
        /** @var Proposal $newProposal */
        $newProposal = null;
        $uri = $proposalResource->getUri();
        if ($uri != null) {
            $oldProposal = $proposalRepo->find($uri);
            $isAlreadyPersisted = $oldProposal != null;
            if ($isAlreadyPersisted) {
                $newProposal = $oldProposal;
            } else {
                $newProposal = new Proposal();
            }
            $newProposal->setUri($uri);
            $newProposal->setLastUpdateAt(new \DateTime('now'));

            $this->mapName($proposalResource, $newProposal);
            if ($newProposal->getName() == null) return;
            $this->mapTypes($proposalResource, $newProposal);
            $this->mapCategories($proposalResource, $newProposal);
            $this->mapImages($proposalResource, $newProposal);
            $newProposal->setResourceOriginUrl(($p = $proposalResource->get("<http://dati.umbria.it/tourism/ontology/url_risorsa>")) != null ? $p->getValue() : null);
            $newProposal->setPlaceFrom(($p = $proposalResource->get("<http://dati.umbria.it/tourism/ontology/luogo_da>")) != null ? $p->getValue() : null);
            $newProposal->setPlaceTo(($p = $proposalResource->get("<http://dati.umbria.it/tourism/ontology/luogo_a>")) != null ? $p->getValue() : null);
            $newProposal->setLat(($p = $proposalResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#lat>")) != null ? (float)$p->getValue() : null);
            $newProposal->setLng(($p = $proposalResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#long>")) != null ? (float)$p->getValue() : null);

            /**@var EasyRdf_Literal[] $subjectArray */
            $subjectArray = $proposalResource->all("<http://purl.org/dc/elements/1.1/subject>");
            foreach ($subjectArray as $subject) {
                if ($subject->getLang() == "it") {
                    $newProposal->setSubject($subject->getValue());
                    break;
                }
            }

            /**@var EasyRdf_Literal[] $textTitleArray */
            $textTitleArray = $proposalResource->all("<http://dati.umbria.it/tourism/ontology/titolo_testo>");
            foreach ($textTitleArray as $textTitle) {
                if ($textTitle->getLang() == "it") {
                    $newProposal->setTextTitle($textTitle->getValue());
                    break;
                }
            }

            /**@var EasyRdf_Literal[] $shortDescriptionArray */
            $shortDescriptionArray = $proposalResource->all("<http://dati.umbria.it/tourism/ontology/descrizione_sintetica>");
            foreach ($shortDescriptionArray as $shortDescription) {
                if ($shortDescription->getLang() == "it") {
                    $newProposal->setShortDescription($shortDescription->getValue());
                    break;
                }
            }
            /**@var EasyRdf_Literal[] $abstractArray */
            $abstractArray = $proposalResource->all("<http://purl.org/ontology/bibo/abstract>");
            foreach ($abstractArray as $abstract) {
                if ($abstract->getLang() == "it") {
                    $newProposal->setComment($abstract->getValue());
                    break;
                }
            }

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
                    if ($descriptionResource->get("<http://dati.umbria.it/tourism/ontology/testo>") != null &&
                        $descriptionResource->get("<http://dati.umbria.it/tourism/ontology/testo>")->getLang() == "it"
                    ) {
                        $descriptionTitle = ($p = $descriptionResource->get("<http://dati.umbria.it/tourism/ontology/titolo>")) != null ? $p->getValue() : null;
                        $descriptionText = $descriptionResource->get("<http://dati.umbria.it/tourism/ontology/testo>")->getValue();
                        $descriptionObject = new ProposalDescription();
                        $descriptionObject->setTitle($descriptionTitle);
                        $descriptionObject->setText($descriptionText);
                        $descriptionObject->setProposal($newProposal);
                        $tempDescriptions[$cnt] = $descriptionObject;
                        $cnt++;
                    }
                }
                if (count($tempDescriptions) > 0) {
                    $newProposal->setDescriptions($tempDescriptions);
                }
            }
        }
        return $newProposal;
    }

    /**
     * @param \EasyRdf_Resource $travelAgencyResource
     */
    private function createOrUpdateTravelAgency($travelAgencyResource)
    {
        $travelAgencyRepo = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\TravelAgency');
        /** @var TravelAgency $newTravelAgency */
        $newTravelAgency = null;
        $uri = $travelAgencyResource->getUri();
        if ($uri != null) {
            $oldTravelAgency = $travelAgencyRepo->find($uri);
            $isAlreadyPersisted = $oldTravelAgency != null;
            if ($isAlreadyPersisted) {
                $newTravelAgency = $oldTravelAgency;
            } else {
                $newTravelAgency = new TravelAgency();
            }
            $newTravelAgency->setUri($uri);
            $newTravelAgency->setLastUpdateAt(new \DateTime('now'));

            $this->mapName($travelAgencyResource, $newTravelAgency);
            $this->mapTypes($travelAgencyResource, $newTravelAgency);
            $this->mapTelephone($travelAgencyResource, $newTravelAgency);
            $this->mapEmail($travelAgencyResource, $newTravelAgency);
            $this->mapFax($travelAgencyResource, $newTravelAgency);
            $this->mapAddress($travelAgencyResource, $newTravelAgency, $isAlreadyPersisted);
            $newTravelAgency->setResourceOriginUrl(($p = $travelAgencyResource->get("<http://xmlns.com/foaf/0.1/homepage>")) != null ? $p->getValue() : null);

        }
        return $newTravelAgency;
    }


    /**
     * @param EasyRdf_Resource $resource
     * @param  $entity
     */
    private function mapName($resource, $entity)
    {
        /**@var EasyRdf_Literal[] $labelArray */
        $labelArray = $resource->all("rdfs:label");
        foreach ($labelArray as $label) {
            if ($label->getLang() == "it" || $label->getLang() == null) {
                $entity->setName($label->getValue());
                break;
            }
        }
    }

    /**
     * @param EasyRdf_Resource $resource
     * @param  $entity
     */
    private function mapTypes($resource, $entity)
    {
        /**@var EasyRdf_Resource[] $typesarray */
        $typesarray = $resource->all("rdf:type");
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
            count($tempTypes) > 0 ? $entity->setTypes($tempTypes) : $entity->setTypes(null);
        }
    }

    /**
     * @param EasyRdf_Resource $resource
     * @param  $entity
     */
    private function mapComment($resource, $entity)
    {
        /**@var EasyRdf_Literal[] $commentArray */
        $commentArray = $resource->all("<http://www.w3.org/2000/01/rdf-schema#comment>");
        foreach ($commentArray as $comment) {
            if ($comment->getLang() == "it") {
                $entity->setComment($comment->getValue());
                break;
            }
        }
    }

    /**
     * @param EasyRdf_Resource $resource
     * @param  $entity
     */
    private function mapTitle($resource, $entity)
    {
        /**@var EasyRdf_Literal[] $titleArray */
        $titleArray = $resource->all("<http://dati.umbria.it/tourism/ontology/titolo_testo>");
        foreach ($titleArray as $title) {
            if ($title->getLang() == "it") {
                $entity->setTextTitle($title->getValue());
                break;
            }
        }
    }

    /**
     * @param EasyRdf_Resource $resource
     * @param  $entity
     */
    private function mapEmail($resource, $entity)
    {
        $emailarray = $resource->all("<http://schema.org/email>");
        if ($emailarray != null) {
            $tempEmail = array();
            $entity->setEmail(array());
            $cnt = 0;
            foreach ($emailarray as $email) {
                $tempEmail[$cnt] = $email->toRdfPhp()['value'];
                $cnt++;
            }
            count($tempEmail) > 0 ? $entity->setEmail($tempEmail) : $entity->setEmail(null);
        }
    }

    /**
     * @param EasyRdf_Resource $resource
     * @param  $entity
     */
    private function mapTelephone($resource, $entity)
    {
        $telephonearray = $resource->all("<http://schema.org/telephone>");
        if ($telephonearray != null) {
            $tempTelephone = array();
            $cnt = 0;
            foreach ($telephonearray as $telephone) {
                $tempTelephone[$cnt] = $telephone->toRdfPhp()['value'];
                $cnt++;
            }
            count($tempTelephone) > 0 ? $entity->setTelephone($tempTelephone) : $entity->setTelephone(null);
        }
    }

    /**
     * @param EasyRdf_Resource $resource
     * @param  $entity
     */
    private function mapFax($resource, $entity)
    {
        $faxarray = $resource->all("<http://schema.org/faxNumber>");
        if ($faxarray != null) {
            $tempFax = array();
            $entity->setFax(array());
            $cnt = 0;
            foreach ($faxarray as $fax) {
                $tempFax[$cnt] = $fax->toRdfPhp()['value'];
                $cnt++;
            }
            count($tempFax) > 0 ? $entity->setFax($tempFax) : $entity->setFax(null);
        }
    }

    /**
     * @param EasyRdf_Resource $resource
     * @param $entity
     * @param $isAlreadyPersisted
     * @throws Exception
     */
    private function mapAddress($resource, $entity, $isAlreadyPersisted)
    {
        if ($isAlreadyPersisted && ($oldAddress = $entity->getAddress()) != null) {
            $this->em->remove($oldAddress);
            $entity->setAddress(null);
        }
        $addressResource = $resource->get("<http://dati.umbria.it/tourism/ontology/indirizzo>");
        if ($addressResource == null) {
            $addressResource = $resource->get("<http://schema.org/address>");
        }
        if ($addressResource != null) {
            $addressObject = new Address();
            $addressObject->setPostalCode(($p = $addressResource->get("<http://schema.org/postalCode>")) != null ? $p->getValue() : null);
            $addressObject->setIstat(($p = $addressResource->get("<http://dbpedia.org/ontology/istat>")) != null ? $p->getValue() : null);
            $addressObject->setAddressLocality(($p = $addressResource->get("<http://schema.org/addressLocality>")) != null ? $p->getValue() : null);
            $addressObject->setAddressRegion(($p = $addressResource->get("<http://schema.org/addressRegion>")) != null ? $p->getValue() : null);
            $addressObject->setStreetAddress(($p = $addressResource->get("<http://schema.org/streetAddress>")) != null ? $p->getValue() : null);
            $addressObject->setLat(($p = $addressResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#lat>")) != null ? $p->getValue() : null);
            $addressObject->setLng(($p = $addressResource->get("<http://www.w3.org/2003/01/geo/wgs84_pos#long>")) != null ? $p->getValue() : null);
            $entity->setAddress($addressObject);


            if ($addressObject->getLat() != null && $addressObject->getLng() != null) {
                $entity->setLat($addressObject->getLat());
                $entity->setLng($addressObject->getLng());
            } else {
                // Google Maps Api --------------------------
                $url = 'http://maps.google.com/maps/api/geocode/json?address=' . urlencode($addressObject->getStreetAddress()) .
                    '+' . $addressObject->getPostalCode() .
                    '+' . $addressObject->getAddressLocality() .
                    '+' . $addressObject->getAddressRegion() . '+Umbria+Italia';

                $resp = json_decode($this->getWebResource($url), true);

                // response status will be 'OK', if able to geocode given address
                if ($resp['status'] == 'OK') {

                    // get the important data
                    $lat = $resp['results'][0]['geometry']['location']['lat'];
                    $lng = $resp['results'][0]['geometry']['location']['lng'];

                    // verify if data is complete
                    if ($lat && $lng) {
                        $entity->setLat($lat);
                        $entity->setLng($lng);
                    }
                }
            }
        }
    }

    /**
     * @param EasyRdf_Resource $resource
     * @param  $entity
     */
    private function mapCategories($resource, $entity)
    {
        /**@var EasyRdf_Resource[] $categoriesarray */
        $categoriesarray = $resource->all("<http://dati.umbria.it/tourism/ontology/categoria>");
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
            count($tempCategories) > 0 ? $entity->setCategories($tempCategories) : $entity->setCategories(null);
        }
    }

    /**
     * @param EasyRdf_Resource $resource
     * @param  $entity
     */
    private function mapImages($resource, $entity)
    {
        $imagearray1 = $resource->all("<http://dati.umbria.it/tourism/ontology/immagine_copertina>");
        $imagearray2 = $resource->all("<http://dati.umbria.it/tourism/ontology/immagine_spalla_destra>");
        /**@var EasyRdf_Resource[] $imagearray */
        $imagearray = array_merge($imagearray1, $imagearray2);
        if ($imagearray != null) {
            $tempImage = array();
            $entity->setImages(array());
            $cnt = 0;
            foreach ($imagearray as $image) {
                $tempImage[$cnt] = $image->toRdfPhp()['value'];
                $cnt++;
            }
            count($tempImage) > 0 ? $entity->setImages($tempImage) : $entity->setImages(null);
        }
    }


    private function getGraphURLByEntityType($entityType)
    {
        if ($entityType == "accomodation") return "";
        else {
            return $this->container->getParameter($entityType . "_graph_url");
        }
    }

    private function getResourceTypeURIByEntityType($entityType)
    {
        return $this->container->getParameter($entityType . "_type_uri");
    }


    private function isResourceToBeSaved($RDFResource, $entityType, $entityTypeURI)
    {
        $resourceURI = $RDFResource->getURI();
        $isValidURI = substr($resourceURI, 0, strlen("http://dati.umbria.it/risorsa/")) === "http://dati.umbria.it/risorsa/";
        $isValidType = false;
        if ($isValidURI) {
            $resourceTypes = $RDFResource->all("<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>");
            foreach ($resourceTypes as $resourceType) {
                $isValidType = $resourceType->getUri() === $entityTypeURI;
                break;
            }
            if ($entityType == 'accomodation') {
                $isValidType = true;
            }
        }
        return ($isValidType && $isValidURI);
    }

    private function getEntityTypeClassName($entityType)
    {
        switch ($entityType) {
            case 'attractor':
                return "Attractor";
            case 'consortium':
                return "Consortium";
            case 'event':
                return "Event";
            case 'iat':
                return "Iat";
            case 'sport_facility':
                return "SportFacility";
            case 'profession':
                return "Profession";
            case 'proposal':
                return "Proposal";
            case 'travel_agency':
                return "TravelAgency";
            case 'accomodation':
                return "Accomodation";
        }
    }

    private function send_error_mail($entityType)
    {
        try {
            $message = (new \Swift_Message('Umbriaopenapi update error'))
                ->setFrom('send@example.com')
                ->setTo('recipient@example.com')
                ->setBody(
                    "Update error: " . $entityType,
                    'text/plain'
                );

            $mailer->send($message);
        } catch (Throwable $t) {
            $this->get('logger')->error("Mail error:" . $t->getTraceAsString());
        }

    }
}