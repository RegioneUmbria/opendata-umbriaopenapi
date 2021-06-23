<?php


namespace Umbria\OpenApiBundle\Controller;

use DateTime;
use Doctrine\ORM\EntityManager;
use EasyRdf_Graph;
use EasyRdf_Literal;
use EasyRdf_Resource;
use EasyRdf_Sparql_Client;
use mysql_xdevapi\Exception;
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
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\TouristLocation;
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
                    if ($isUpdating == true) {
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
                    } catch (Throwable $t) {
                        $has_errors = true;
                        $responseObj->error = $t->getMessage();
                        $logger->error($entityType.' update failed with error: ' . $t->getTraceAsString());
                    }catch (Exception $e) {
                    $has_errors = true;
                    $responseObj->error = $e->getMessage();
                    $logger->error($entityType.' update failed with error: ' . $e->getTraceAsString());
                }finally {
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
                            $logger->error($entityType.' update failed. Check the log file to see the error or debug the project');
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

            if ($entityType == "accomodation" or $entityType == "event" or $entityType == "tourist_location") {
                $sparqlClient = new EasyRdf_Sparql_Client("https://odn.regione.umbria.it/sparql?format=text%2Fturtle");
                if ($entityType == "accomodation") {
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
                }
                if ($entityType == "event"){
                    $query = "
                        CONSTRUCT {?s ?p ?o.}
                        WHERE{
                            SELECT DISTINCT ?s ?p ?o
                            FROM <http://dati.umbria.it/graph/eventi>
                            WHERE{
                                ?s ?p ?o .
                                ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://schema.org/Event>.
                                FILTER( ?p = <http://www.w3.org/2000/01/rdf-schema#label> ).
                                FILTER langMatches(lang(?o),'it')			
                            }
                        } ";

                }
                if ($entityType == "tourist_location") {
                    $query = "
                        CONSTRUCT {?s ?p ?o.}
                        WHERE{
                            SELECT DISTINCT ?s ?p ?o
                            FROM <http://dati.umbria.it/graph/locazioni-turistiche>
                            WHERE{
                                ?s ?p ?o .
                                ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://purl.org/acco/ns#House>.
                                FILTER( ?p = <http://www.w3.org/2000/01/rdf-schema#label> ).			
                            }
                        }
                        ";
                }
                $graph = $sparqlClient->query($query);
            }

            $graphURL = $this->getGraphURLByEntityType($entityType);
            if ($graphURL != "") {
                $graph = EasyRdf_Graph::newAndLoad($graphURL);
            }


        return $graph;
    }

    /**
     * Update entities already persisted. If entity uri is not found in RDF graph, then logically delete entity.
     */
    private function updateDeleteEntities($graph, $entityType, $errors_only)
    {
        $has_error = true;
        $entityClassName = $this->getEntityTypeClassName($entityType);
        /**get all entities from db by type*/
        /**
         * @var Doctrine\ORM\EntityRepository $entityRepo
         */
        $entityRepo = $this->em->getRepository(trim("UmbriaOpenApiBundle:Tourism\GraphsEntities\ ") . $entityClassName);
        $entities = $entityRepo->findAll();
        if($graph != null) {
            //controllo per gestione fatal error non gestibile con php 5, da php 7 gestibile con la throwable
            $has_error = false;
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
                            $this->get('logger')->info("Now updating -> $entity_uri");
                            if ($updatedEntity != null) {
                                $updatedEntity->setLastUpdateAt(new \DateTime('now'));
                                $updatedEntity->setIsDeleted(false);
                                $updatedEntity->setIsInError(false);

                            } else {
                                $is_entity_error = true;
                            }
                            $this->get('logger')->info("End updating -> $entity_uri");
                        } else {
                            $is_to_delete = true;
                            $this->get('logger')->info("Delete URI -> $entity_uri");
                        }
                        if ($is_to_delete == true) {
                            //logically delete entity
                            $entity->setLastUpdateAt(new \DateTime('now'));
                            $entity->setIsInError(false);
                            $entity->setIsDeleted(true);
                            $this->em->flush();
                        }
                    } catch (Throwable $t) {
                        $is_entity_error = true;
                        $this->get('logger')->error("$entity_uri create error:" . $t->getTraceAsString());

                    } catch (Exception $e) {
                        $is_entity_error = true;
                        $this->get('logger')->error("$entity_uri create error:" . $e->getTraceAsString());
                    }
                    if ($is_entity_error == true) {
                        $has_error = true;

                        try {
                            $this->get('logger')->info("$entity_uri trying to save with state in error");
                            $entity->setLastUpdateAt(new \DateTime('now'));
                            $entity->setIsDeleted(false);
                            $entity->setIsInError(true);
                            $this->em->flush();
                            $this->get('logger')->info("$entity_uri saved with state in error");
                        } catch (Throwable $t2) {
                            $this->get('logger')->error("$entity_uri trying to save with state in error:" . $t2->getTraceAsString());
                        } catch (Exception $e2) {
                            $this->get('logger')->error("$entity_uri trying to save with state in error:" . $e2->getTraceAsString());
                        }
                    }
                }
            }

            $this->get('logger')->info("Now flushing -> $entity_uri");
            $this->em->flush();
            $this->get('logger')->info("Flushing done -> $entity_uri");
        }
        return $has_error;
    }


    /**
     * Create new entites for those new resources founded in the RDF graph
     */
    private function createEntities($graph, $entityType)
    {
        $has_error = true;
        $entityClassName = $this->getEntityTypeClassName($entityType);
        $entityRepo = $this->em->getRepository(trim("UmbriaOpenApiBundle:Tourism\GraphsEntities\ ") . $entityClassName);
        //get all resources from graph by type
        if($graph != null) {
            //controllo per gestione fatal error grafo vuoto non gestibile con php 5, da php 7 gestibile con la throwable
            $has_error = false;
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
                            $this->get('logger')->info("-------- CREATE ENTITY -----------");
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
                } catch (Throwable $t) {
                    $is_entity_error = true;
                    $this->get('logger')->error("$resourceURI create error:" . $t->getTraceAsString());
                } catch (Exception $e) {
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

                            }
                        } catch (Throwable $t2) {
                            $this->get('logger')->error("$resourceURI error trying to save with state in error:" . $t2->getTraceAsString());
                        } catch (Exception $e2) {
                            $this->get('logger')->error("$resourceURI error trying to save with state in error:" . $e2->getTraceAsString());
                        }
                    }
                }
            }
            $this->em->flush();
        }
        return $has_error;
    }

    /** rdf resource */
    private function createOrUpdateAccomodation($accomodationResource)
    {
        $accomodationRepo = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Accomodation');
        /** @var Attractor $newAttractor */
        $newAccomodation = null;
        $uri = $accomodationResource->getUri();
        $sparqlClient = new EasyRdf_Sparql_Client("https://odn.regione.umbria.it/sparql");
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
            $query = "SELECT ?label ?typology ?resourceOriginUrl ?units ?beds ?toilets ?cir ?ema ?fa ?tel ?postalcode ?istat ?addressLocality ?addressRegion ?streetAddress ?coordx ?coordy
					FROM <http://dati.umbria.it/graph/strutture_ricettive>
		WHERE {
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/tipologia> ?typology}.
					OPTIONAL{<" . $uri . "> <http://schema.org/web> ?resourceOriginUrl}.
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/numeroUnita> ?units}.
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/numeroLetti> ?beds}.
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/numeroBagni> ?toilets}.
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/cir> ?cir}.
					OPTIONAL{select ?label (group_concat(?email;separator=' | ') as ?ema)
							 FROM <http://dati.umbria.it/graph/strutture_ricettive> 
							 WHERE {<" . $uri . "> <http://www.w3.org/2000/01/rdf-schema#label> ?label.
									<" . $uri . "> <http://schema.org/email> ?email .
							} group by ?label}.
					OPTIONAL{select ?label (group_concat(?fax;separator=' | ') as ?fa)
							 FROM <http://dati.umbria.it/graph/strutture_ricettive> 
							 WHERE {<" . $uri . "> <http://www.w3.org/2000/01/rdf-schema#label> ?label.
									<" . $uri . "> <http://schema.org/fax> ?fax .
							} group by ?label}.
					OPTIONAL{select ?label (group_concat(?telephone;separator=' | ') as ?tel)
							 FROM <http://dati.umbria.it/graph/strutture_ricettive> 
							 WHERE {<" . $uri . "> <http://www.w3.org/2000/01/rdf-schema#label> ?label.
									<" . $uri . "> <http://schema.org/telephone> ?telephone .
							} group by ?label}.   
					OPTIONAL{<" . $uri . "> <http://www.w3.org/2000/01/rdf-schema#label> ?label}.
					OPTIONAL{<" . $uri . "> <http://www.w3.org/2000/01/rdf-schema#comment> ?comment}.
					OPTIONAL{<" . $uri . "> <http://schema.org/address> ?uri}.
					OPTIONAL{?uri <http://schema.org/postalCode> ?postalcode}.
					OPTIONAL{?uri <http://dbpedia.org/ontology/istat> ?istat}.
					OPTIONAL{?uri <http://schema.org/addressLocality> ?addressLocality}.
					OPTIONAL{?uri <http://schema.org/addressRegion> ?addressRegion}.
					OPTIONAL{?uri <http://schema.org/streetAddress> ?streetAddress}.
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/localizzazione> ?uricoord}.
					OPTIONAL{?uricoord <http://dati.umbria.it/base/ontology/coordy> ?coordy}.
					OPTIONAL{?uricoord <http://dati.umbria.it/base/ontology/coordx> ?coordx}. 
			}limit 1";
            $sparqlResult = $sparqlClient->query($query);
            $sparqlResult->rewind();
            if ($sparqlResult->valid()) {
                try {
                    if (isset($sparqlResult->current()->resourceOriginUrl)) $newAccomodation->setResourceOriginUrl($sparqlResult->current()->resourceOriginUrl->getValue());
                    if (isset($sparqlResult->current()->typology)) $newAccomodation->setTypology($sparqlResult->current()->typology->getValue());
                    if (isset($sparqlResult->current()->units)){
                        $cu = $sparqlResult->current()->units;
                        if(strcmp($cu, "http://linked.opendata.cz/ontology/odcs/tabular/blank-cell") == 0){
                            $newAccomodation->setUnits(null);
                        }else {
                            $newAccomodation->setUnits($sparqlResult->current()->units->getValue());
                        }
                    }
                    else{
                        $newAccomodation->setUnits(null);
                    }
                    if (isset($sparqlResult->current()->beds)){
                        $cb = $sparqlResult->current()->beds;
                        if(strcmp($cb, "http://linked.opendata.cz/ontology/odcs/tabular/blank-cell") == 0){
                            $newAccomodation->setBeds(null);
                        }else {
                            $newAccomodation->setBeds($sparqlResult->current()->beds->getValue());
                        }
                    }
                    else{
                        $newAccomodation->setBeds(null);
                    }
                    if (isset($sparqlResult->current()->toilets)){
                        $ct = $sparqlResult->current()->toilets;
                        if(strcmp($ct, "http://linked.opendata.cz/ontology/odcs/tabular/blank-cell") == 0){
                            $newAccomodation->setToilets(null);
                        }else {
                            $newAccomodation->setToilets($sparqlResult->current()->toilets->getValue());
                        }
                    }
                    else{
                        $newAccomodation->setToilets(null);
                    }

                    if (isset($sparqlResult->current()->cir)) $newAccomodation->setCir($sparqlResult->current()->cir->getValue());
                    if (isset($sparqlResult->current()->ema)) $newAccomodation->setEmail($sparqlResult->current()->ema->getValue());
                    if (isset($sparqlResult->current()->fa)) $newAccomodation->setFax($sparqlResult->current()->fa->getValue());
                    if (isset($sparqlResult->current()->tel)) $newAccomodation->setTelephone($sparqlResult->current()->tel->getValue());
                    $tempAddress = new Address();
                    $currentAddress = $sparqlResult->current();
                    $tempAddress->setPostalCode(isset($currentAddress->postalCode) ? $currentAddress->postalCode->getValue() : null);
                    $tempAddress->setIstat(isset($currentAddress->istat) ? $currentAddress->istat->getValue() : null);
                    $tempAddress->setAddressLocality(isset($currentAddress->addressLocality) ? $currentAddress->addressLocality->getValue() : null);
                    $tempAddress->setAddressRegion(isset($currentAddress->addressRegion) ? $currentAddress->addressRegion->getValue() : null);
                    $tempAddress->setStreetAddress(isset($currentAddress->streetAddress) ? $currentAddress->streetAddress->getValue() : null);
                    if (isset($currentAddress->coordy)){
                        $cy = $currentAddress->coordy;
                        if(strcmp($cy, "http://linked.opendata.cz/ontology/odcs/tabular/blank-cell") == 0){
                            $tempAddress->setLat(43.0000);
                        }else {
                            $tempAddress->setLat($currentAddress->coordy->getValue());
                        }
                    }
                    else{
                        $tempAddress->setLat(43.0000);
                    }
                    if(isset($currentAddress->coordx)){
                        $cx = $currentAddress->coordx;
                        if(strcmp($cx, "http://linked.opendata.cz/ontology/odcs/tabular/blank-cell") == 0){
                            $tempAddress->setLng(12.0000);
                        }else{
                            $tempAddress->setLng($currentAddress->coordx->getValue());
                        }
                    }
                    else{
                        $tempAddress->setLng(12.0000);
                    }
                    $newAccomodation->setAddress($tempAddress);
                }
                catch (Throwable $t) {
                    $t->getMessage();
                    $this->get('logger')->error('Error getting values from: ' . $t);
                }
                catch (Exception $e) {
                    $e->getMessage();
                    $this->get('logger')->error('Error getting values from: ' . $e);
                }
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

            /**@var EasyRdf_Literal[] $labelArray */
            $labelArray = $attractorResource->all("<http://www.w3.org/2000/01/rdf-schema#label>");
            foreach ($labelArray as $label) {
                if ($label->getLang() == "it" || $label->getLang() == null) {
                    $newAttractor->setName($label->getValue());
                    break;
                }
            }


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
            $descriptionArray = $attractorResource->all("<http://dati.umbria.it/tourism/ontology/testo>");
            if ($descriptionArray != null) {
                $tempDescriptions = array();
                $cnt = 0;
                foreach ($descriptionArray as $descriptionResource) {
                    if ($descriptionResource->getValue() != null &&
                        $descriptionResource->getLang() == "it") {
                        $descriptionTitle = (($p = $attractorResource->get("<http://dati.umbria.it/tourism/ontology/titolo_testo>")) != null ? $p->getValue() : null);
                        $descriptionText = $descriptionResource->getValue();
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
                $newAttractor->setSameAs($this->getExternalResources($sameAsArray, "https://dbpedia.org/sparql",
                    "http://www.w3.org/2000/01/rdf-schema#label", "http://dbpedia.org/ontology/abstract", "http://www.w3.org/ns/prov#wasDerivedFrom"));
            }
            /**@var EasyRdf_Resource[] $locatedInArray */
            $locatedInArray = $attractorResource->all("<http://www.geonames.org/ontology#locatedIn>");
            if ($locatedInArray != null) {
                $newAttractor->setLocatedIn($this->getExternalResources($locatedInArray, "https://dbpedia.org/sparql",
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
        /** @var Event $newEvent*/
        $newEvent= null;
        $uri = $eventResource->getUri();
        $sparqlClient = new EasyRdf_Sparql_Client("https://odn.regione.umbria.it/sparql");
        if ($uri != null) {
            $oldEvent= $eventRepo->find($uri);
            $isAlreadyPersisted = $oldEvent != null;
            if ($isAlreadyPersisted) {
                $newEvent = $oldEvent;
            } else {
                $newEvent = new Event();
            }

            $newEvent->setUri($uri);
            $this->mapName($eventResource, $newEvent);
            $query = "SELECT ?label ?startDate ?endDate ?lat ?long ?ttesto ?imgcopertina ?descr ?municipality ?resourceOriginUrl
					FROM <http://dati.umbria.it/graph/eventi>
		WHERE {
					OPTIONAL{<" . $uri . "> <http://www.w3.org/2000/01/rdf-schema#label> ?label}.
					OPTIONAL{<" . $uri . "> <http://schema.org/end_date> ?endDate}.
					OPTIONAL{<" . $uri . "> <http://schema.org/start_date> ?startDate}.
					OPTIONAL{<" . $uri . "> <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat}.
					OPTIONAL{<" . $uri . "> <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?long}.
					OPTIONAL{<" . $uri . "> <http://dbpedia.org/ontology/municipality> ?municipality}.
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/tourism/ontology/titolo_testo> ?ttesto}.
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/tourism/ontology/immagine_copertina> ?imgcopertina}.
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/tourism/ontology/event_description> ?descr}.
					OPTIONAL{<" . $uri . "> <http://xmlns.com/foaf/0.1/homepage> ?resourceOriginUrl}.
                   
			}";
            $sparqlResult = $sparqlClient->query($query);
            $sparqlResult->rewind();
            if ($sparqlResult->valid()) {
                if (isset($sparqlResult->current()->label)) {
                    if($sparqlResult->current()->label->getLang() == "it"){
                        $newEvent->setName($sparqlResult->current()->label->getValue());
                    }
                }
                if (isset($sparqlResult->current()->startDate)){
                   $variable = $sparqlResult->current()->startDate->getValue();
                    $time = substr($variable, 0, strpos($variable, "T"));
                   $newEvent->setStartDate(new \DateTime($time));
                }
                if (isset($sparqlResult->current()->endDate)){
                    $variable = $sparqlResult->current()->endDate->getValue();
                    $time = substr($variable, 0, strpos($variable, "T"));
                    $newEvent->setEndDate(new \DateTime($time));
                }
                if (isset($sparqlResult->current()->lat)) $newEvent->setLat($sparqlResult->current()->lat->getValue());
                if (isset($sparqlResult->current()->long)) $newEvent->setLng($sparqlResult->current()->long->getValue());
                if (isset($sparqlResult->current()->municipality)) $newEvent->setMunicipality($sparqlResult->current()->municipality->getValue());
                if (isset($sparqlResult->current()->resourceOriginUrl)) $newEvent->setResourceOriginUrl($sparqlResult->current()->resourceOriginUrl->getValue());
                if (isset($sparqlResult->current()->imgcopertina)) $newEvent->setImages($sparqlResult->current()->imgcopertina->getValue());
                if (isset($sparqlResult->current()->ttesto)){
                    if($sparqlResult->current()->ttesto->getLang() == "it"){
                        $newEvent->setComment($sparqlResult->current()->ttesto->getValue());
                    }
                }
            }
            if ($isAlreadyPersisted && ($oldDescriptions = $newEvent->getDescriptions()) != null) {
                foreach ($oldDescriptions as $oldDescription) {
                    $this->em->remove($oldDescription);
                }
                $newEvent->setDescriptions(null);
            }

            $sparqlResult->rewind();
            if ($sparqlResult->valid() ) {
                $tempDescriptions = array();
                $descriptionTitle = null;
                $descriptionText = null;
            if($sparqlResult->current()->label->getLang() == "it"){
                $descriptionTitle = $sparqlResult->current()->label->getValue();
            }
            if($sparqlResult->current()->descr->getLang() == "it"){
                $descriptionText = $sparqlResult->current()->descr->getValue();
            }
            $descriptionObject = new EventDescription();
            $descriptionObject->setTitle($descriptionTitle);
            $descriptionObject->setText($descriptionText);
            $descriptionObject->setEvent($newEvent);
            $tempDescriptions[0] = $descriptionObject;
            $newEvent->setDescriptions($tempDescriptions);
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
            $descriptionArray = $proposalResource->all("<http://dati.umbria.it/tourism/ontology/testo>");
            if ($descriptionArray != null) {
                $tempDescriptions = array();
                $cnt = 0;
                foreach ($descriptionArray as $descriptionResource) {
                    if ($descriptionResource->getValue() != null &&
                        $descriptionResource->getLang() == "it"
                    ) {
                        $descriptionTitle = (($p = $proposalResource->get("<http://www.w3.org/2000/01/rdf-schema#label>")) != null ? $p->getValue() : null);
                        $descriptionText = $descriptionResource->getValue();
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

            return $newProposal;
        }
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
    private function createOrUpdateTouristLocation($TouristLocationResource)
    {
        $TouristLocationRepo = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\TouristLocation');
        /** @var TouristLocation $newTouristLocation */
        $newLocation = null;
        $uri = $TouristLocationResource->getUri();
        $sparqlClient = new EasyRdf_Sparql_Client("https://odn.regione.umbria.it/sparql");
        if ($uri != null) {
            $oldTouristLocation = $TouristLocationRepo->find($uri);
            $isAlreadyPersisted = $oldTouristLocation != null;
            if ($isAlreadyPersisted) {
                $newTouristLocation = $oldTouristLocation;
            } else {
                $newTouristLocation = new TouristLocation();
            }

            $newTouristLocation->setUri($uri);
            $this->mapName($TouristLocationResource, $newTouristLocation);
            $query = "SELECT ?label ?typology ?resourceOriginUrl ?units ?beds ?toilets ?cir ?postalcode ?istat ?addressLocality ?addressRegion ?streetAddress ?coordx ?coordy
					FROM <http://dati.umbria.it/graph/locazioni-turistiche>
		WHERE {
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/tipologia> ?typology}.
					OPTIONAL{<" . $uri . "> <http://schema.org/web> ?resourceOriginUrl}.
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/numeroUnita> ?units}.
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/numeroLetti> ?beds}.
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/numeroBagni> ?toilets}.
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/cir> ?cir}.
					OPTIONAL{<" . $uri . "> <http://www.w3.org/2000/01/rdf-schema#label> ?label}.
					OPTIONAL{<" . $uri . "> <http://www.w3.org/2000/01/rdf-schema#comment> ?comment}.
					OPTIONAL{<" . $uri . "> <http://schema.org/address> ?uri}.
					OPTIONAL{?uri <http://schema.org/postalCode> ?postalcode}.
					OPTIONAL{?uri <http://dbpedia.org/ontology/istat> ?istat}.
					OPTIONAL{?uri <http://schema.org/addressLocality> ?addressLocality}.
					OPTIONAL{?uri <http://schema.org/addressRegion> ?addressRegion}.
					OPTIONAL{?uri <http://schema.org/streetAddress> ?streetAddress}.
					OPTIONAL{<" . $uri . "> <http://dati.umbria.it/base/ontology/localizzazione> ?uricoord}.
					OPTIONAL{?uricoord <http://dati.umbria.it/base/ontology/coordy> ?coordy}.
					OPTIONAL{?uricoord <http://dati.umbria.it/base/ontology/coordx> ?coordx}. 
			}limit 1";
            $sparqlResult = $sparqlClient->query($query);
            $sparqlResult->rewind();
            if ($sparqlResult->valid()) {
                try {
                    if (isset($sparqlResult->current()->resourceOriginUrl)) $newTouristLocation->setResourceOriginUrl($sparqlResult->current()->resourceOriginUrl->getValue());
                    if (isset($sparqlResult->current()->typology)) $newTouristLocation->setTypology($sparqlResult->current()->typology->getValue());
                    if (isset($sparqlResult->current()->units)){
                        $cu = $sparqlResult->current()->units;
                        if(strcmp($cu, "http://linked.opendata.cz/ontology/odcs/tabular/blank-cell") == 0){
                            $newTouristLocation->setUnits(null);
                        }else {
                            $newTouristLocation->setUnits($sparqlResult->current()->units->getValue());
                        }
                    }
                    else{
                        $newTouristLocation->setUnits(null);
                    }
                    if (isset($sparqlResult->current()->beds)){
                        $cb = $sparqlResult->current()->beds;
                        if(strcmp($cb, "http://linked.opendata.cz/ontology/odcs/tabular/blank-cell") == 0){
                            $newTouristLocation>setBeds(null);
                        }else {
                            $newTouristLocation->setBeds($sparqlResult->current()->beds->getValue());
                        }
                    }
                    else{
                        $newTouristLocation->setBeds(null);
                    }
                    if (isset($sparqlResult->current()->toilets)){
                        $ct = $sparqlResult->current()->toilets;
                        if(strcmp($ct, "http://linked.opendata.cz/ontology/odcs/tabular/blank-cell") == 0){
                            $newTouristLocation->setToilets(null);
                        }else {
                            $newTouristLocation->setToilets($sparqlResult->current()->toilets->getValue());
                        }
                    }
                    else{
                        $newTouristLocation->setToilets(null);
                    }

                    if (isset($sparqlResult->current()->cir)) $newTouristLocation->setCir($sparqlResult->current()->cir->getValue());
                    $tempAddress = new Address();
                    $currentAddress = $sparqlResult->current();
                    $tempAddress->setPostalCode(isset($currentAddress->postalCode) ? $currentAddress->postalCode->getValue() : null);
                    $tempAddress->setIstat(isset($currentAddress->istat) ? $currentAddress->istat->getValue() : null);
                    $tempAddress->setAddressLocality(isset($currentAddress->addressLocality) ? $currentAddress->addressLocality->getValue() : null);
                    $tempAddress->setAddressRegion(isset($currentAddress->addressRegion) ? $currentAddress->addressRegion->getValue() : null);
                    $tempAddress->setStreetAddress(isset($currentAddress->streetAddress) ? $currentAddress->streetAddress->getValue() : null);
                    if (isset($currentAddress->coordy)){
                        $cy = $currentAddress->coordy;
                        if(strcmp($cy, "http://linked.opendata.cz/ontology/odcs/tabular/blank-cell") == 0){
                            $tempAddress->setLat(43.0000);
                        }else {
                            $tempAddress->setLat($currentAddress->coordy->getValue());
                        }
                    }
                    else{
                        $tempAddress->setLat(43.0000);
                    }
                    if(isset($currentAddress->coordx)){
                        $cx = $currentAddress->coordx;
                        if(strcmp($cx, "http://linked.opendata.cz/ontology/odcs/tabular/blank-cell") == 0){
                            $tempAddress->setLng(12.0000);
                        }else{
                            $tempAddress->setLng($currentAddress->coordx->getValue());
                        }
                    }
                    else{
                        $tempAddress->setLng(12.0000);
                    }
                    $newTouristLocation->setAddress($tempAddress);
                }
                catch (Throwable $t) {
                    $t->getMessage();
                    $this->get('logger')->error('Error getting values from: ' . $t);
                }
                catch (Exception $e) {
                    $e->getMessage();
                    $this->get('logger')->error('Error getting values from: ' . $e);
                }
            }
        }
        return $newTouristLocation;

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
                $url = 'http://maps.google.com/maps/api/geocode/json?address=' . $addressObject->getStreetAddress() .
                    '+' . $addressObject->getPostalCode() .
                    '+' . $addressObject->getAddressLocality() .
                    '+' . $addressObject->getAddressRegion() . '+Umbria+Italia&key=AIzaSyCUhLUsc6u6hiWzn8r9PYhIq-hqS-hHHn4';

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
        if ($entityType == "accomodation" or $entityType == "tourist_location" or $entityType == "event")
        {
            return "";
        }
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
            if ($entityType == 'tourist_location') {
            $isValidType = true;
            }
            if ($entityType == 'event') {
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
            case 'tourist_location':
                return "TouristLocation";
        }
    }

    private function send_error_mail($entityType)
    {
        require_once '/var/www/umbriaopenapi/vendor/autoload.php';
        try {
            $transport = (new \Swift_SmtpTransport('smtp.umbriadigitale.it', 25));

            $mailer = new \Swift_Mailer($transport);

            $message = (new \Swift_Message('Umbriaopenapi update error'))
                ->setFrom(['umbriaopenapi_sys@noreply.it' => 'UmbriaOpenApi'])
                ->setTo('progettazione.opendata@umbriadigitale.it')
                ->setBody(
                    "Errore durante l'aggiornamento di: " . $entityType,
                    'text/plain'
                );
            $result = $mailer->send($message);
            $this->get('logger')->error("Mail sent:" . $result);
        } catch (Throwable $t) {
            $this->get('logger')->error("Mail error:" . $t->getTraceAsString());
        }
    }
}
