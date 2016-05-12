<?php

namespace Umbria\OpenApiBundle\Service;

use Doctrine\ORM\EntityManager;
use Exception;
use JMS\Serializer\Serializer;
use Umbria\OpenApiBundle\Entity\Tourism\Address;
use Umbria\OpenApiBundle\Entity\Tourism\Attractor;
use Umbria\OpenApiBundle\Entity\Tourism\Consortium;
use Umbria\OpenApiBundle\Entity\Tourism\Coordinate;
use Umbria\OpenApiBundle\Entity\Tourism\Iat;
use Umbria\OpenApiBundle\Entity\Tourism\Profession;
use Umbria\OpenApiBundle\Entity\Tourism\RDF;
use Umbria\OpenApiBundle\Entity\Tourism\TravelAgency;

class CurlBuilder
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Serializer
     */
    public $serializer;

    public function __construct(EntityManager $em, Serializer $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    public function updateEntities($url, $entityType, $urlSameAs = null, $urlLocatedIn = null)
    {
        // rimozione vecchie entità
        $rdfs = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\RDF')->findAll();

        /** @var RDF $rdf */
        foreach ($rdfs as $rdf) {
            if ($rdf->has($entityType)) {
                $this->em->remove($rdf);
            }
        }
        $this->em->flush();

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

                    // DBpedia
                    $dbpediaUrl = 'http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=select+distinct+*+where+%7B%3C' . $dbpediaResource . '%3E+%3Fp+%3Fo%7D+LIMIT+100&format=application%2Fsparql-results%2Bjson';
                    $dbpediaResp = json_decode($this->getResource($dbpediaUrl), true);
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
                    if ($coordinate->getLatitude() == ' ' | $coordinate->getLatitude() == '' | $coordinate->getLatitude() == null | $coordinate->getLongitude() == ' ' | $coordinate->getLongitude() == '' | $coordinate->getLongitude() == null) {
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

                        $url = 'http://maps.google.com/maps/api/geocode/json?address='.urlencode($comune).'+'.$codiceIstat.'+Umbria+Italia';

                        $resp = json_decode($this->getResource($url), true);

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

    public function getResource($url = 'null')
    {
        try {
            $ch = curl_init();

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
            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);
        }

        return;
    }

    public function xmlConversion($rdf, $entityType = null)
    {
        if ($entityType == 'tourism-attractor') {
            $tagName = "attrattori";
        } elseif ($entityType == 'tourism-proposal') {
            $tagName = "proposte";
        } elseif ($entityType == 'tourism-event') {
            $tagName = "eventi";
        }/* elseif ($entityType == 'tourism-travel-agency') {
            $tagName = "agenzie_viaggio";
        } elseif ($entityType == 'tourism-consortium') {
            $tagName = "consorzi";
        } elseif ($entityType == 'tourism-profession') {
            $tagName = "professioni";
        } elseif ($entityType == 'tourism-iat') {
            $tagName = "iat";
        }*/


        if (isset($tagName)) {
            $rdf = preg_replace('/<rdf:RDF.*?>/', "<rdf:RDF> <$tagName>", $rdf);
            $rdf = preg_replace('/<\/rdf:RDF?>/', "</$tagName> </rdf:RDF>", $rdf);
        }

        /*Attrattori,Eventi,Proposte*/
        $rdf = str_replace('<umb:contenuto_relazionato>', null, $rdf);
        $rdf = str_replace('</umb:contenuto_relazionato>', null, $rdf);
        $rdf = str_replace('<umb:categoria>', '<categorie>', $rdf);
        $rdf = str_replace('</umb:categoria>', '</categorie>', $rdf);
        $rdf = str_replace('<umb:descrizione>', '<descrizioni>', $rdf);
        $rdf = str_replace('</umb:descrizione>', '</descrizioni>', $rdf);
        $rdf = str_replace('<umb:coordinate>', '<coordinate>', $rdf);
        $rdf = str_replace('</umb:coordinate>', '</coordinate>', $rdf);

        /*Attrattori, Proposte*/
        $rdf = str_replace('<umb:tempo_di_viaggio>', '<tempi_di_viaggio>', $rdf);
        $rdf = str_replace('</umb:tempo_di_viaggio>', '</tempi_di_viaggio>', $rdf);

        /*Eventi*/
        $rdf = str_replace('<umb:immagine>', '<immagini>', $rdf);
        $rdf = str_replace('</umb:immagine>', '</immagini>', $rdf);

        $rdf = str_replace('<umb:abstract>', '<umb_abstract>', $rdf);
        $rdf = str_replace('</umb:abstract>', '</umb_abstract>', $rdf);

        /*Proposte*/
        $rdf = str_replace('<umb:informazione>', '<informazioni>', $rdf);
        $rdf = str_replace('</umb:informazione>', '</informazioni>', $rdf);

        $rdf = str_replace('<umb:categoria>', null, $rdf);
        $rdf = str_replace('</umb:categoria>', null, $rdf);
        $rdf = str_replace('<umb:informazione>', null, $rdf);
        $rdf = str_replace('</umb:informazione>', null, $rdf);
        $rdf = str_replace('<umb:indirizzo>', null, $rdf);
        $rdf = str_replace('</umb:indirizzo>', null, $rdf);
        $rdf = str_replace('<umb:telefono>', null, $rdf);
        $rdf = str_replace('</umb:telefono>', null, $rdf);
        $rdf = str_replace('<umb:fax>', null, $rdf);
        $rdf = str_replace('</umb:fax>', null, $rdf);
        $rdf = str_replace('<umb:mail>', null, $rdf);
        $rdf = str_replace('</umb:mail>', null, $rdf);
        $rdf = str_replace('<umb:homepage>', null, $rdf);
        $rdf = str_replace('</umb:homepage>', null, $rdf);
        $rdf = str_replace('<umb:language>', null, $rdf);
        $rdf = str_replace('</umb:language>', null, $rdf);
        $rdf = str_replace('<umb:specializzazione>', null, $rdf);
        $rdf = str_replace('</umb:specializzazione>', null, $rdf);

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
        $rdf = str_replace('<!--<group:', '<', $rdf);
        $rdf = str_replace('<!--</group:', '</', $rdf);
        $rdf = str_replace('>-->', '>', $rdf);

        $rdf = str_replace('rdf:about', 'rdf_about', $rdf);
        $rdf = str_replace('rdf:resource', 'rdf_resource', $rdf);

        $rdf = trim(preg_replace('/\r|\n/', '', $rdf));

        return $rdf;
    }
}
