<?php

namespace Umbria\ProLocoBundle\Controller;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Attractor;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Consortium;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Event;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Iat;
use Umbria\OpenApiBundle\Entity\Tourism\PlaceItem\PlaceDetails;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Profession;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Proposal;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\TravelAgency;

class DefaultController extends Controller
{
    /**
     * @var EntityManager
     * @DI\Inject("doctrine.orm.entity_manager")
     */
    public $em;

    public function indexAction()
    {
        $rdfs = $this->em->getRepository('UmbriaOpenApiBundle:Tourism\RDF')->findAll();


        $attrattori = array();
        $proposte = array();
        $eventi = array();
        $agenzieViaggio = array();
        $consorzi = array();
        $professioni = array();
        $iats = array();

        /** @var RDF $rdf */
        foreach ($rdfs as $rdf) {
            /** @var Attractor $attractor */
            foreach ($rdf->getAttrattori() as $attractor) {
                if (isset($attractor)) {
                    $place = new PlaceDetails();
                    $place->setId($attractor->getId());
                    $place->setName($attractor->getDenominazione());
                    $place->setType($attractor->getType());

                    // TODO: rivedere
                    $coordinates = $attractor->getCoordinate();

                    /** @var Coordinate $coordinate */
                    foreach ($coordinates as $coordinate) {
                        if ($coordinate->getLatitude() != '') {
                            $place->setLatitude($coordinate->getLatitude());
                            $place->setLongitude($coordinate->getLongitude());
                        } elseif ($coordinate->getDbpediaLatitude() != '') {
                            $place->setLatitude($coordinate->getDbpediaLatitude());
                            $place->setLongitude($coordinate->getDbpediaLongitude());
                        } else {
                            $place->setLatitude($coordinate->getGoogleLatitude());
                            $place->setLongitude($coordinate->getGoogleLongitude());
                        }
                    }

                    $uri = $this->get('router')->generate('attractor_show', array(
                        'id' => $attractor->getId(),
                    ), UrlGeneratorInterface::ABSOLUTE_URL);
                    $place->setHref($uri);

                    // TODO: rivedere
                    if ($place->getLatitude() != '') {
                        $attrattori[] = $place;
                    }
                }
            }
            /** @var Proposal $proposal */
            foreach ($rdf->getProposte() as $proposal) {
                if (isset($proposal)) {
                    $place = new PlaceDetails();
                    $place->setId($proposal->getId());
                    $place->setName($proposal->getNomeProposta());
                    $place->setType($proposal->getType());

                    $coordinates = $proposal->getCoordinate();
                    foreach ($coordinates as $coordinate) {
                        if ($coordinate->getLatitude() != '') {
                            $place->setLatitude($coordinate->getLatitude());
                            $place->setLongitude($coordinate->getLongitude());
                        }
                    }

                    $uri = $this->get('router')->generate('proposal_show', array(
                        'id' => $proposal->getId(),
                    ), UrlGeneratorInterface::ABSOLUTE_URL);
                    $place->setHref($uri);

                    // TODO: rivedere
                    if ($place->getLatitude() != '') {
                        $proposte[] = $place;
                    }
                }
            }
            /** @var Event $event */
            foreach ($rdf->getEventi() as $event) {
                if (isset($event)) {
                    $place = new PlaceDetails();
                    $place->setId($event->getId());
                    $place->setName($event->getTitle());
                    $place->setType($event->getType());

                    $coordinates = $event->getCoordinate();
                    foreach ($coordinates as $coordinate) {
                        if ($coordinate->getLatitude() != '') {
                            $place->setLatitude($coordinate->getLatitude());
                            $place->setLongitude($coordinate->getLongitude());
                        }
                    }

                    $uri = $this->get('router')->generate('event_show', array(
                        'id' => $event->getId(),
                    ), UrlGeneratorInterface::ABSOLUTE_URL);
                    $place->setHref($uri);

                    // TODO: rivedere
                    if ($place->getLatitude() != '') {
                        $eventi[] = $place;
                    }
                }
            }
            /** @var TravelAgency $travelAgency */
            foreach ($rdf->getAgenzieViaggio() as $travelAgency) {
                if (isset($travelAgency)) {
                    // Creazione di un numero di place pari al numero di indirizzi
                    /** @var Address $address */
                    foreach ($travelAgency->getAddress() as $address) {
                        if ($address->getLatitude() != '') {
                            $place = new PlaceDetails();
                            $place->setId($travelAgency->getId());
                            $place->setName($travelAgency->getDenominazione());
                            $place->setType($travelAgency->getType());
                            $place->setLatitude($address->getLatitude());
                            $place->setLongitude($address->getLongitude());

                            $uri = $this->get('router')->generate('travelagency_show', array(
                                'id' => $travelAgency->getId(),
                            ), UrlGeneratorInterface::ABSOLUTE_URL);
                            $place->setHref($uri);

                            $agenzieViaggio[] = $place;
                        }
                    }
                }
            }
            /** @var Consortium $consortium */
            foreach ($rdf->getConsorzi() as $consortium) {
                if (isset($consortium)) {
                    // Creazione di un numero di place pari al numero di indirizzi
                    /** @var Address $address */
                    foreach ($consortium->getAddress() as $address) {
                        if ($address->getLatitude() != '') {
                            $place = new PlaceDetails();
                            $place->setId($consortium->getId());
                            $place->setName($consortium->getDenominazione());
                            $place->setType($consortium->getType());
                            $place->setLatitude($address->getLatitude());
                            $place->setLongitude($address->getLongitude());

                            $uri = $this->get('router')->generate('consortium_show', array(
                                'id' => $consortium->getId(),
                            ), UrlGeneratorInterface::ABSOLUTE_URL);
                            $place->setHref($uri);

                            $consorzi[] = $place;
                        }
                    }
                }
            }
            /** @var Profession $profession */
            foreach ($rdf->getProfessioni() as $profession) {
                if (isset($profession)) {
                    // Creazione di un numero di place pari al numero di indirizzi
                    /** @var Address $address */
                    foreach ($profession->getAddress() as $address) {
                        $coordinates = $address->getCoordinateGeografiche();
                        if ($coordinates != '') {
                            $coordinates = explode(';', $coordinates);

                            $place = new PlaceDetails();
                            $place->setId($profession->getId());
                            $place->setName($profession->getFirstName().' '.$profession->getLastName());
                            $place->setType($profession->getType());
                            $place->setLatitude($coordinates[0]);
                            $place->setLongitude($coordinates[1]);

                            $uri = $this->get('router')->generate('profession_show', array(
                                'id' => $profession->getId(),
                            ), UrlGeneratorInterface::ABSOLUTE_URL);
                            $place->setHref($uri);

                            $professioni[] = $place;
                        } elseif ($address->getLatitude() != '' and $coordinates == '') {
                            $place = new PlaceDetails();
                            $place->setId($profession->getId());
                            $place->setName($profession->getFirstName().' '.$profession->getLastName());
                            $place->setType($profession->getType());
                            $place->setLatitude($address->getLatitude());
                            $place->setLongitude($address->getLongitude());

                            $uri = $this->get('router')->generate('profession_show', array(
                                'id' => $profession->getId(),
                            ), UrlGeneratorInterface::ABSOLUTE_URL);
                            $place->setHref($uri);

                            $professioni[] = $place;
                        }
                    }
                }
            }
            /** @var Iat $iat */
            foreach ($rdf->getIat() as $iat) {
                if (isset($iat)) {
                    if ($iat->getLatitude() != '') {
                        $place = new PlaceDetails();
                        $place->setId($iat->getId());
                        $place->setName($iat->getDenominazione());
                        $place->setType($iat->getType());
                        $place->setLatitude($iat->getLatitude());
                        $place->setLongitude($iat->getLongitude());

                        $uri = $this->get('router')->generate('iat_show', array(
                            'id' => $iat->getId(),
                        ), UrlGeneratorInterface::ABSOLUTE_URL);
                        $place->setHref($uri);

                        $iats[] = $place;
                    }
                }
            }
        }

        return $this->render('UmbriaProLocoBundle:Default:index.html.twig', array(
            'attrattori' => $attrattori, 'proposte' => $proposte, 'eventi' => $eventi,
            'agenzieViaggio' => $agenzieViaggio, 'professioni' => $professioni, 'consorzi' => $consorzi,
            'iat' => $iats
        ));
    }
}
