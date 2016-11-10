<?php

namespace Umbria\OpenApiBundle\Service;

use Doctrine\ORM\EntityManager;
use EasyRdf_Graph;
use Exception;
use JMS\Serializer\Serializer;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Attractor;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Consortium;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Iat;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Profession;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\TravelAgency;
use EasyRdf_Sparql_Client;
use EasyRdf_Sparql_Result;

class TourismEntityUpdater
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Serializer
     */
    public $serializer;

    /**
     * @var EasyRdf_Sparql_Client
     */
    public $sparqlClient;



    public function __construct(EntityManager $em, Serializer $serializer, $sparqlEndpointUri)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->sparqlClient=new EasyRdf_Sparql_Client($sparqlEndpointUri,null);
    }

    public function executeSparqlQuery($query){


        //$namedGraphsList=$this->sparqlClient->listNamedGraphs();
        $sparqlResult=$this->sparqlClient->query($query);
        /**
         * @var EasyRdf_Graph
         */
        $graph=$this->getGraph("http://odnt-srv01/dataset/54480509-bf69-47e1-b735-de5ddac001a2/resource/e27179f1-4020-4d8b-90cb-6ec4f47471f3/download/attrattoriitIT.zipattrattoriitIT.rdf");
        $resources = $graph->resources();
        foreach ($resources as $resource) {
            $propertyUris = $graph->propertyUris($resource);
            $resourceType = $resource->get("rdf:type")->toRdfPhp()['value'];
            if (trim($resourceType) == "http://linkedgeodata.org/ontology/Attraction") {//is attractor
                echo "is attractor";
                // $resourceTempiDiViaggio = $resourceObject->get("umb:tempiDiViaggio")->toRdfPhp()['value'];
            }
        }
        $sparqlResult->rewind();
        while($sparqlResult->valid()) {
            $current = $sparqlResult->current();

            $sparqlResult->next();
        }
    }

    private function getGraph($uri)
    {
        $graph= EasyRdf_Graph::newAndLoad($uri);
        return $graph;
    }


    public function updateEntities($url, $entityType, $urlSameAs = null, $urlLocatedIn = null)
    {


        // recupero rdf elementi
        $rdf = $this->getResource($url);
        $xml = $this->xmlConversion($rdf, $entityType);

        // recupero rdf silk samAs
        $sameAsEntities = null;
        if ($urlSameAs != null) {
            $rdfSilkSameAs = $this->getResource($urlSameAs);
            $xmlSameAs = simplexml_load_string($this->xmlConversion($rdfSilkSameAs));
            $sameAsEntities = $xmlSameAs->Description;
        }

        //recupero rdf silk locatedIn
        $locatedInEntities = null;
        if ($urlLocatedIn != null) {
            $rdfSilkLocatedIn = $this->getResource($urlLocatedIn);
            $xmlLocatedIn = simplexml_load_string($this->xmlConversion($rdfSilkLocatedIn));
            $locatedInEntities = $xmlLocatedIn->Description;
        }
        // Deserializzazione
        /** @var RDF $data */
        $data = $this->serializer->deserialize($xml, 'Umbria\OpenApiBundle\Entity\Tourism\RDF', 'xml');

        if ($entityType == 'tourism-attractor') {
            $entities = $data->getAttrattori();
        } elseif ($entityType == 'tourism-proposal') {
            $entities = $data->getProposte();
        } elseif ($entityType == 'tourism-event') {
            $entities = $data->getEventi();
        } elseif ($entityType == 'tourism-travel-agency') {
            $entities = $data->getAgenzieViaggio();
        } elseif ($entityType == 'tourism-consortium') {
            $entities = $data->getConsorzi();
        } elseif ($entityType == 'tourism-profession') {
            $entities = $data->getProfessioni();
        } elseif ($entityType == 'tourism-iat') {
            $entities = $data->getIat();
        }

        
        if ($entityType == 'tourism-attractor') {
            /** @var Attractor $attractor */
            /* @noinspection PhpUndefinedVariableInspection */
            foreach ($entities as $attractor) {
                try {
                    if ($sameAsEntities != null) {
                        // recupero sameAs DBpedia
                        $dbpediaResource = null;
                        /* @noinspection PhpUndefinedVariableInspection */
                        foreach ($sameAsEntities as $dbpediaEntity) {
                            /* @noinspection PhpUndefinedMethodInspection */
                            $attributes = $dbpediaEntity[0]->attributes();
                            foreach ($attributes as $attribute) {
                                // se id dell'elemento è uguale
                                $arrayURI = explode("/", (string)$attribute[0]);
                                $idSameAs = end($arrayURI);
                                if ($idSameAs == $attractor->getIdElemento()) {
                                    $dbpediaAttributes = $dbpediaEntity[0]->sameAs->attributes();
                                    foreach ($dbpediaAttributes as $dbpediaAttribute) {
                                        $dbpediaResource = (string)$dbpediaAttribute[0];
                                    };
                                };
                            }
                        }

                        if ($dbpediaResource != null) {

                            // DBpedia
                            $dbpediaUrl = 'http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=select+distinct+*+where+%7B%3C' . $dbpediaResource . '%3E+%3Fp+%3Fo%7D+LIMIT+100&format=application%2Fsparql-results%2Bjson';
                            $dbpediaResp = json_decode($this->getResource($dbpediaUrl, false), true);
                            $bindings = $dbpediaResp['results']['bindings'];

                            // Aggiunta risorsa DBpedia
                            $attractor->setDbpediaResource($dbpediaResource);


                            // Aggiunta abstract
                            foreach ($bindings as $binding) {
                                if ($binding['p']['value'] == 'http://dbpedia.org/ontology/abstract' and $binding['o']['xml:lang'] == 'it') {
                                    $attractor->setDbpediaAbstract($binding['o']['value']);
                                    $attractor->setDbpediaInfo(true);
                                }
                            }

                            // Aggiunta link Wikipedia
                            foreach ($bindings as $binding) {
                                if ($binding['p']['value'] == 'http://xmlns.com/foaf/0.1/isPrimaryTopicOf') {
                                    $attractor->setWikipediaLink($binding['o']['value']);
                                    $attractor->setDbpediaInfo(true);
                                }
                            }
                        }


                    }

                    if ($locatedInEntities != null) {
                        //recupero locatedIn DBpedia
                        $dbpediaLocatedIn = null;
                        /* @noinspection PhpUndefinedVariableInspection */
                        foreach ($locatedInEntities as $dbpediaEntity) {
                            /* @noinspection PhpUndefinedMethodInspection */
                            $attributes = $dbpediaEntity[0]->attributes();
                            foreach ($attributes as $attribute) {
                                // se id dell'elemento è uguale
                                $arrayURI = explode("/", (string)$attribute[0]);
                                $idLocatedIn = end($arrayURI);
                                if ($idLocatedIn == $attractor->getIdElemento()) {
                                    $dbpediaAttributes = $dbpediaEntity[0]->locatedIn->attributes();
                                    foreach ($dbpediaAttributes as $dbpediaAttribute) {
                                        $dbpediaLocatedIn = (string)$dbpediaAttribute[0];
                                    };
                                };
                            }
                        }
                        // Aggiunta risorsa DBpedia locatedIn
                        $attractor->setLocatedIn($dbpediaLocatedIn);
                    }

                    // Arricchimento coordinate
                    $coordinates = $attractor->getCoordinate();
                    /** @var Coordinate $coordinate */
                    foreach ($coordinates as $coordinate) {
                        if ($coordinate->getLatitude() == 0 | $coordinate->getLatitude() == null | $coordinate->getLongitude() == 0 | $coordinate->getLongitude() == null) {
                            foreach ($bindings as $binding) {
                                if ($binding['p']['value'] == 'http://www.w3.org/2003/01/geo/wgs84_pos#lat') {
                                    $coordinate->setDbpediaLatitude($binding['o']['value']);
                                    $attractor->setDbpediaInfo(true);
                                } elseif ($binding['p']['value'] == 'http://www.w3.org/2003/01/geo/wgs84_pos#long') {
                                    $coordinate->setDbpediaLongitude($binding['o']['value']);
                                    $attractor->setDbpediaInfo(true);
                                }
                            }

                            // Google Maps Api --------------------------
                            $comune = $attractor->getComune();
                            $codiceIstat = $attractor->getCodiceIstatComune();

                            $url = 'http://maps.google.com/maps/api/geocode/json?address=' . urlencode($comune) . '+' . $codiceIstat . '+Umbria+Italia';

                            $resp = json_decode($this->getResource($url, false), true);

                            // response status will be 'OK', if able to geocode given address
                            if ($resp['status'] == 'OK') {

                                // get the important data
                                $lati = $resp['results'][0]['geometry']['location']['lat'];
                                $longi = $resp['results'][0]['geometry']['location']['lng'];

                                // verify if data is complete
                                if ($lati && $longi) {
                                    $coordinate->setGoogleLatitude($lati);
                                    $coordinate->setGoogleLongitude($longi);
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                }
            }
        } elseif ($entityType == 'tourism-travel-agency') {
            /* @noinspection PhpUndefinedVariableInspection */
            /** @var TravelAgency $travelAgency */
            foreach ($entities as $travelAgency) {
                /** @var Address $address */
                foreach ($travelAgency->getAddress() as $address) {
                    $streetAddress = $address->getStreetAddress();
                    $postalCode = $address->getPostalCode();
                    $addressLocality = $address->getAddressLocality();
                    $addressRegion = $address->getAddressRegion();

                    // TODO: aggiungere controllo completezza

                    try {
                        // Google Maps Api --------------------------
                        $url = 'http://maps.google.com/maps/api/geocode/json?address=' . urlencode($streetAddress) . '+' . $postalCode . '+' . $addressLocality . '+' . $addressRegion . '+Umbria+Italia';

                        $resp = json_decode($this->getResource($url, false), true);

                        // response status will be 'OK', if able to geocode given address
                        if ($resp['status'] == 'OK') {

                            // get the important data
                            $lati = $resp['results'][0]['geometry']['location']['lat'];
                            $longi = $resp['results'][0]['geometry']['location']['lng'];

                            // verify if data is complete
                            if ($lati && $longi) {
                                $address->setLatitude($lati);
                                $address->setLongitude($longi);
                            }
                        }
                    } catch (Exception $e) {

                    }
                }
            }
        } elseif ($entityType == 'tourism-consortium') {
            /* @noinspection PhpUndefinedVariableInspection */
            /** @var Consortium $consortium */
            foreach ($entities as $consortium) {
                /** @var Address $address */
                foreach ($consortium->getAddress() as $address) {
                    $streetAddress = $address->getStreetAddress();
                    $postalCode = $address->getPostalCode();
                    $addressLocality = $address->getAddressLocality();
                    $addressRegion = $address->getAddressRegion();

                    // TODO: aggiungere controllo completezza

                    // Google Maps Api --------------------------
                    $url = 'http://maps.google.com/maps/api/geocode/json?address='.urlencode($streetAddress).'+'.$postalCode.'+'.$addressLocality.'+'.$addressRegion.'+Umbria+Italia';

                    $resp = json_decode($this->getResource($url), true);

                    // response status will be 'OK', if able to geocode given address
                    if ($resp['status'] == 'OK') {

                        // get the important data
                        $lati = $resp['results'][0]['geometry']['location']['lat'];
                        $longi = $resp['results'][0]['geometry']['location']['lng'];

                        // verify if data is complete
                        if ($lati && $longi) {
                            $address->setLatitude($lati);
                            $address->setLongitude($longi);
                        }
                    }
                }
            }
        } elseif ($entityType == 'tourism-profession') {
            /* @noinspection PhpUndefinedVariableInspection */
            /** @var Profession $profession */
            foreach ($entities as $profession) {
                /** @var Address $address */
                foreach ($profession->getAddress() as $address) {
                    $coordinates = $address->getCoordinateGeografiche();
                    if ($coordinates == '') {
                        $streetAddress = $address->getStreetAddress();
                        $postalCode = $address->getPostalCode();
                        $addressLocality = $address->getAddressLocality();
                        $addressRegion = $address->getAddressRegion();

                        // TODO: aggiungere controllo completezza
                        // Google Maps Api --------------------------
                        $url = 'http://maps.google.com/maps/api/geocode/json?address='.urlencode($streetAddress).'+'.$postalCode.'+'.$addressLocality.'+'.$addressRegion.'+Umbria+Italia';

                        $resp = json_decode($this->getResource($url), true);

                        // response status will be 'OK', if able to geocode given address
                        if ($resp['status'] == 'OK') {

                            // get the important data
                            $lati = $resp['results'][0]['geometry']['location']['lat'];
                            $longi = $resp['results'][0]['geometry']['location']['lng'];

                            // verify if data is complete
                            if ($lati && $longi) {
                                $address->setLatitude($lati);
                                $address->setLongitude($longi);
                            }
                        }
                    }
                }
            }
        } elseif ($entityType == 'tourism-iat') {
            /* @noinspection PhpUndefinedVariableInspection */
            /** @var Iat $iat */
            foreach ($entities as $iat) {
                /* @var Address $address */
                $streetAddress = $iat->getStreetAddress();
                $postalCode = $iat->getPostalCode();
                $addressLocality = $iat->getAddressLocality();
                $addressRegion = $iat->getAddressRegion();

                // TODO: aggiungere controllo completezza
                // Google Maps Api --------------------------
                $url = 'http://maps.google.com/maps/api/geocode/json?address='.urlencode($streetAddress).'+'.$postalCode.'+'.$addressLocality.'+'.$addressRegion.'+Umbria+Italia';

                $resp = json_decode($this->getResource($url), true);

                // response status will be 'OK', if able to geocode given address
                if ($resp['status'] == 'OK') {

                    // get the important data
                    $lati = $resp['results'][0]['geometry']['location']['lat'];
                    $longi = $resp['results'][0]['geometry']['location']['lng'];

                    // verify if data is complete
                    if ($lati && $longi) {
                        $iat->setLatitude($lati);
                        $iat->setLongitude($longi);
                    }
                }
            }
        }

        // rimozione vecchie entità
        $rdfs = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\RDF')->findAll();

        /** @var RDF $rdf */
        foreach ($rdfs as $rdf) {
            if ($rdf->has($entityType)) {
                $this->em->remove($rdf);
            }
        }
        $this->em->flush();

        // Salvattaggio nel DB
        $entity = $this->em->merge($data);
        $this->em->persist($entity);
        $this->em->flush();
    }

    private function deleteEntities($entities)
    {
        $removed = array();
        foreach ($entities as $entity) {
            if (isset($entity)) {
                $removed[] = $entity;
            }
        }

        foreach ($removed as $entity) {
            $this->em->remove($entity);
        }
    }

    public function getResource($url = 'null', $writeError = true)
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

    public function xmlConversion($rdf, $entityType = null)
    {
        if ($entityType == 'tourism-attractor') {
            $tagName = "attrattori";
            $rdf = str_replace('<umb:contenuto_relazionato>', null, $rdf);
            $rdf = str_replace('</umb:contenuto_relazionato>', null, $rdf);
            $rdf = str_replace('<umb:categoria>', '<categorie>', $rdf);
            $rdf = str_replace('</umb:categoria>', '</categorie>', $rdf);
            $rdf = preg_replace('/<\/categorie>[ \n\r]*<categorie>/', "", $rdf);
            $rdf = str_replace('<umb:descrizione>', '<descrizioni>', $rdf);
            $rdf = str_replace('</umb:descrizione>', '</descrizioni>', $rdf);
            $rdf = preg_replace('/<\/descrizioni>[ \n\r]*<descrizioni>/', "", $rdf);
            $rdf = str_replace('<umb:coordinate>', '<coordinate>', $rdf);
            $rdf = str_replace('</umb:coordinate>', '</coordinate>', $rdf);
            $rdf = preg_replace('/<\/coordinate>[ \n\r]*<coordinate>/', "", $rdf);

            $rdf = str_replace('<umb:abstract>', '<umb_abstract>', $rdf);
            $rdf = str_replace('</umb:abstract>', '</umb_abstract>', $rdf);

            $rdf = str_replace('<umb:tempo_di_viaggio>', '<tempi_di_viaggio>', $rdf);
            $rdf = str_replace('</umb:tempo_di_viaggio>', '</tempi_di_viaggio>', $rdf);
            $rdf = preg_replace('/<\/tempi_di_viaggio>[ \n\r]*<tempi_di_viaggio>/', "", $rdf);
        } elseif ($entityType == 'tourism-proposal') {
            $tagName = "proposte";
            $rdf = str_replace('<umb:contenuto_relazionato>', null, $rdf);
            $rdf = str_replace('</umb:contenuto_relazionato>', null, $rdf);
            $rdf = str_replace('<umb:categoria>', '<categorie>', $rdf);
            $rdf = str_replace('</umb:categoria>', '</categorie>', $rdf);
            $rdf = preg_replace('/<\/categorie>[ \n\r]*<categorie>/', "", $rdf);
            $rdf = str_replace('<umb:descrizione>', '<descrizioni>', $rdf);
            $rdf = str_replace('</umb:descrizione>', '</descrizioni>', $rdf);
            $rdf = preg_replace('/<\/descrizioni>[ \n\r]*<descrizioni>/', "", $rdf);
            $rdf = str_replace('<umb:coordinate>', '<coordinate>', $rdf);
            $rdf = str_replace('</umb:coordinate>', '</coordinate>', $rdf);
            $rdf = preg_replace('/<\/coordinate>[ \n\r]*<coordinate>/', "", $rdf);

            $rdf = str_replace('<umb:abstract>', '<umb_abstract>', $rdf);
            $rdf = str_replace('</umb:abstract>', '</umb_abstract>', $rdf);

            $rdf = str_replace('<umb:tempo_di_viaggio>', '<tempi_di_viaggio>', $rdf);
            $rdf = str_replace('</umb:tempo_di_viaggio>', '</tempi_di_viaggio>', $rdf);
            $rdf = preg_replace('/<\/tempi_di_viaggio>[ \n\r]*<tempi_di_viaggio>/', "", $rdf);

            $rdf = str_replace('<umb:informazione>', '<informazioni>', $rdf);
            $rdf = str_replace('</umb:informazione>', '</informazioni>', $rdf);
            $rdf = preg_replace('/<\/informazioni>[ \n\r]*<informazioni>/', "", $rdf);
        } elseif ($entityType == 'tourism-event') {
            $tagName = "eventi";
            $rdf = str_replace('<umb:contenuto_relazionato>', null, $rdf);
            $rdf = str_replace('</umb:contenuto_relazionato>', null, $rdf);
            $rdf = str_replace('<umb:categoria>', '<categorie>', $rdf);
            $rdf = str_replace('</umb:categoria>', '</categorie>', $rdf);
            $rdf = preg_replace('/<\/categorie>[ \n\r]*<categorie>/', "", $rdf);
            $rdf = str_replace('<umb:descrizione>', '<descrizioni>', $rdf);
            $rdf = str_replace('</umb:descrizione>', '</descrizioni>', $rdf);
            $rdf = preg_replace('/<\/descrizioni>[ \n\r]*<descrizioni>/', "", $rdf);
            $rdf = str_replace('<umb:coordinate>', '<coordinate>', $rdf);
            $rdf = str_replace('</umb:coordinate>', '</coordinate>', $rdf);
            $rdf = preg_replace('/<\/coordinate>[ \n\r]*<coordinate>/', "", $rdf);

            $rdf = str_replace('<umb:abstract>', '<umb_abstract>', $rdf);
            $rdf = str_replace('</umb:abstract>', '</umb_abstract>', $rdf);

            $rdf = str_replace('<umb:immagine>', '<immagini>', $rdf);
            $rdf = str_replace('</umb:immagine>', '</immagini>', $rdf);
            $rdf = preg_replace('/<\/immagini>[ \n\r]*<immagini>/', "", $rdf);
        } elseif ($entityType == 'tourism-travel-agency') {
            $tagName = "agenzie_viaggio";
            $rdf = str_replace('<umb:indirizzo>', '<address>', $rdf);
            $rdf = str_replace('</umb:indirizzo>', '</address>', $rdf);
            $rdf = preg_replace('/<\/address>[ \n\r]*<address>/', "", $rdf);
            $rdf = str_replace('<schema:telephone>', '<phone><Description><num_telefono>', $rdf);
            $rdf = str_replace('</schema:telephone>', '</num_telefono></Description></phone>', $rdf);
            $rdf = preg_replace('/<\/phone>[ \n\r]*<phone>/', "", $rdf);
            $rdf = str_replace('<schema:faxNumber>', '<fax_number><Description><num_fax>', $rdf);
            $rdf = str_replace('</schema:faxNumber>', '</num_fax></Description></fax_number>', $rdf);
            $rdf = preg_replace('/<\/fax_number>[ \n\r]*<fax_number>/', "", $rdf);
            $rdf = str_replace('<schema:email>', '<mbox><Description><email>', $rdf);
            $rdf = str_replace('</schema:email>', '</email></Description></mbox>', $rdf);
            $rdf = preg_replace('/<\/mbox>[ \n\r]*<mbox>/', "", $rdf);
            $rdf = str_replace('<foaf:homepage>', '<homepage><Description><url>', $rdf);
            $rdf = str_replace('</foaf:homepage>', '</url></Description></homepage>', $rdf);
            $rdf = preg_replace('/<\/homepage>[ \n\r]*<homepage>/', "", $rdf);
        } elseif ($entityType == 'tourism-consortium') {
            $tagName = "consorzi";
            $rdf = str_replace('<umb:indirizzo>', '<address>', $rdf);
            $rdf = str_replace('</umb:indirizzo>', '</address>', $rdf);
            $rdf = preg_replace('/<\/address>[ \n\r]*<address>/', "", $rdf);
            $rdf = str_replace('<schema:telephone>', '<phone><Description><num_telefono>', $rdf);
            $rdf = str_replace('</schema:telephone>', '</num_telefono></Description></phone>', $rdf);
            $rdf = preg_replace('/<\/phone>[ \n\r]*<phone>/', "", $rdf);
            $rdf = str_replace('<schema:faxNumber>', '<fax_number><Description><num_fax>', $rdf);
            $rdf = str_replace('</schema:faxNumber>', '</num_fax></Description></fax_number>', $rdf);
            $rdf = preg_replace('/<\/fax_number>[ \n\r]*<fax_number>/', "", $rdf);
            $rdf = str_replace('<schema:email>', '<mbox><Description><email>', $rdf);
            $rdf = str_replace('</schema:email>', '</email></Description></mbox>', $rdf);
            $rdf = preg_replace('/<\/mbox>[ \n\r]*<mbox>/', "", $rdf);
            $rdf = str_replace('<foaf:homepage>', '<homepage><Description><url>', $rdf);
            $rdf = str_replace('</foaf:homepage>', '</url></Description></homepage>', $rdf);
            $rdf = preg_replace('/<\/homepage>[ \n\r]*<homepage>/', "", $rdf);
        } elseif ($entityType == 'tourism-profession') {
            $tagName = "professioni";
            $rdf = str_replace('<umb:indirizzo>', '<address>', $rdf);
            $rdf = str_replace('</umb:indirizzo>', '</address>', $rdf);
            $rdf = preg_replace('/<\/address>[ \n\r]*<address>/', "", $rdf);
            $rdf = str_replace('<schema:telephone>', '<phone><Description><num_telefono>', $rdf);
            $rdf = str_replace('</schema:telephone>', '</num_telefono></Description></phone>', $rdf);
            $rdf = preg_replace('/<\/phone>[ \n\r]*<phone>/', "", $rdf);
            $rdf = str_replace('<schema:faxNumber>', '<fax_number><Description><num_fax>', $rdf);
            $rdf = str_replace('</schema:faxNumber>', '</num_fax></Description></fax_number>', $rdf);
            $rdf = preg_replace('/<\/fax_number>[ \n\r]*<fax_number>/', "", $rdf);
            $rdf = str_replace('<schema:email>', '<mbox><Description><email>', $rdf);
            $rdf = str_replace('</schema:email>', '</email></Description></mbox>', $rdf);
            $rdf = preg_replace('/<\/mbox>[ \n\r]*<mbox>/', "", $rdf);
            $rdf = str_replace('<foaf:homepage>', '<homepage><Description><url>', $rdf);
            $rdf = str_replace('</foaf:homepage>', '</url></Description></homepage>', $rdf);
            $rdf = preg_replace('/<\/homepage>[ \n\r]*<homepage>/', "", $rdf);

            $rdf = str_replace('<umb:lingua_parlata>', '<language><Description><lingua_parlata>', $rdf);
            $rdf = str_replace('</umb:lingua_parlata>', '</lingua_parlata></Description></language>', $rdf);
            $rdf = preg_replace('/<\/language>[ \n\r]*<language>/', "", $rdf);
            $rdf = str_replace('<umb:specializzazione>', '<specializzazione><Description><spec>', $rdf);
            $rdf = str_replace('</umb:specializzazione>', '</spec></Description></specializzazione>', $rdf);
            $rdf = preg_replace('/<\/specializzazione>[ \n\r]*<specializzazione>/', "", $rdf);
        } elseif ($entityType == 'tourism-iat') {
            $tagName = "iat";
            $rdf = str_replace('<schema:telephone>', '<phone><Description><num_telefono>', $rdf);
            $rdf = str_replace('</schema:telephone>', '</num_telefono></Description></phone>', $rdf);
            $rdf = preg_replace('/<\/phone>[ \n\r]*<phone>/', "", $rdf);
            $rdf = str_replace('<schema:faxNumber>', '<fax_number><Description><num_fax>', $rdf);
            $rdf = str_replace('</schema:faxNumber>', '</num_fax></Description></fax_number>', $rdf);
            $rdf = preg_replace('/<\/fax_number>[ \n\r]*<fax_number>/', "", $rdf);
            $rdf = str_replace('<schema:email>', '<mbox><Description><email>', $rdf);
            $rdf = str_replace('</schema:email>', '</email></Description></mbox>', $rdf);
            $rdf = preg_replace('/<\/mbox>[ \n\r]*<mbox>/', "", $rdf);

        }


        if (isset($tagName)) {
            $rdf = preg_replace('/<rdf:RDF.*?>/', "<rdf:RDF> <$tagName>", $rdf);
            $rdf = preg_replace('/<\/rdf:RDF?>/', "</$tagName> </rdf:RDF>", $rdf);
        }


        $rdf = str_replace('<rdf:', '<', $rdf);
        $rdf = str_replace('</rdf:', '</', $rdf);
        $rdf = str_replace('<umb:', '<', $rdf);
        $rdf = str_replace('</umb:', '</', $rdf);
        $rdf = str_replace('<foaf:', '<', $rdf);
        $rdf = str_replace('</foaf:', '</', $rdf);
        $rdf = str_replace('<schema:', '<', $rdf);
        $rdf = str_replace('</schema:', '</', $rdf);
        $rdf = str_replace('<dcterms:', '<', $rdf);
        $rdf = str_replace('</dcterms:', '</', $rdf);
        $rdf = str_replace('<geo:', '<', $rdf);
        $rdf = str_replace('</geo:', '</', $rdf);
        $rdf = str_replace('<rdfs:', '<', $rdf);
        $rdf = str_replace('</rdfs:', '</', $rdf);
        $rdf = str_replace('<dbpedia-owl:', '<', $rdf);
        $rdf = str_replace('</dbpedia-owl:', '</', $rdf);
        $rdf = str_replace('<bibo:', '<', $rdf);
        $rdf = str_replace('</bibo:', '</', $rdf);

        $rdf = str_replace('rdf:about', 'rdf_about', $rdf);
        $rdf = str_replace('rdf:resource', 'rdf_resource', $rdf);

        $rdf = trim(preg_replace('/\r|\n/', '', $rdf));

        return $rdf;
    }
}
