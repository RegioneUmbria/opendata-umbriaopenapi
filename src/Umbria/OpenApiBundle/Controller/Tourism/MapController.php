<?php

namespace Umbria\OpenApiBundle\Controller\Tourism;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Umbria\OpenApiBundle\Entity\Address;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Attractor;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Consortium;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Event;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Iat;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\SportFacility;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Accomodation;
use Umbria\OpenApiBundle\Entity\Tourism\PlaceItem\PlaceDetails;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Profession;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Proposal;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\TravelAgency;

/**
 * Class MapController
 * @package Umbria\OpenApiBundle\Controller
 *
 * @author Lorenzo Franco Ranucci <loryzizu@gmail.com>
 */
class MapController extends Controller
{
    private $attractorRepo;
    private $proposalRepo;
    private $eventRepo;
    private $travelAgencyRepo;
    private $consortiumRepo;
    private $professionRepo;
    private $iatRepo;
    private $accomodationRepo;
    private $sportFacilityRepo;


    /**
     * @DI\InjectParams({
     *      "em" = @DI\Inject("doctrine.orm.entity_manager")
     * })
     * @param $em EntityManager
     */
    public function __construct($em)
    {
        $this->attractorRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Attractor');
        $this->proposalRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Proposal');
        $this->eventRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Event');
        $this->travelAgencyRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\TravelAgency');
        $this->consortiumRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Consortium');
        $this->professionRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Profession');
        $this->iatRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Iat');
        $this->accomodationRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Accomodation');
        $this->sportFacilityRepo = $em->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\SportFacility');
    }

    public function indexAction()
    {


        $attrattori = array();
        $proposte = array();
        $eventi = array();
        $agenzieViaggio = array();
        $consorzi = array();
        $professioni = array();
        $iats = array();
        $accomodations = array();
        $sportFacilities = array();

            /** @var Attractor $attractor */
        foreach ($this->attractorRepo->findAll() as $attractor) {
            if (isset($attractor) && $attractor->getLat() != null && !$attractor->isDeleted()) {
                    $place = new PlaceDetails();
                    $place->setId($attractor->getId());
                    $place->setName($attractor->getName());
                    $place->setType("tourism_attractor");


                    $place->setLatitude($attractor->getLat());
                    $place->setLongitude($attractor->getLng());
                    $uri = $this->get('router')->generate('attractor_show', array(
                        'id' => $attractor->getId(),
                    ), UrlGeneratorInterface::ABSOLUTE_URL);
                    $place->setHref($uri);

                    // TODO: rivedere
                    if ($place->getLatitude() != null && $place->getLatitude() != '') {
                        $attrattori[] = $place;
                    }
                }
            }
        /** @var Proposal $proposal */
        foreach ($this->proposalRepo->findAll() as $proposal) {
            if (isset($proposal) && !$proposal->isDeleted()) {
                $place = new PlaceDetails();
                $place->setId($proposal->getId());
                $place->setName($proposal->getName());
                $place->setType('tourism_proposal');

                if ($proposal->getLat() != null && $proposal->getLat() != 0) {
                    $place->setLatitude($proposal->getLat());
                    $place->setLongitude($proposal->getLng());
                }

                $uri = $this->get('router')->generate('proposal_show', array(
                    'id' => $proposal->getId(),
                ), UrlGeneratorInterface::ABSOLUTE_URL);
                $place->setHref($uri);

                if ($place->getLatitude() != '') {
                    $proposte[] = $place;
                }
            }
        }
        /** @var Event $event */
        foreach ($this->eventRepo->findAll() as $event) {
            if (isset($event) && !$event->isDeleted()) {
                $place = new PlaceDetails();
                $place->setId($event->getId());
                $place->setName($event->getName());
                $place->setType('tourism_event');

                if ($event->getLat() != null) {
                    $place->setLatitude($event->getLat());
                    $place->setLongitude($event->getLng());
                    $eventi[] = $place;
                }

                $uri = $this->get('router')->generate('event_show', array(
                    'id' => $event->getId(),
                ), UrlGeneratorInterface::ABSOLUTE_URL);
                $place->setHref($uri);

            }
        }
        /** @var TravelAgency $travelAgency */
        foreach ($this->travelAgencyRepo->findAll() as $travelAgency) {
            if (isset($travelAgency)) {
                if ($travelAgency->getLat() != null && !$travelAgency->isDeleted()) {
                    $place = new PlaceDetails();
                    $place->setId($travelAgency->getId());
                    $place->setName($travelAgency->getName());
                    $place->setType('tourism_travel_agency');
                    $place->setLatitude($travelAgency->getLat());
                    $place->setLongitude($travelAgency->getLng());

                    $uri = $this->get('router')->generate('travel_agency_show', array(
                        'id' => $travelAgency->getId(),
                    ), UrlGeneratorInterface::ABSOLUTE_URL);
                    $place->setHref($uri);

                    $agenzieViaggio[] = $place;
                }
            }
        }
        /** @var Consortium $consortium */
        foreach ($this->consortiumRepo->findAll() as $consortium) {
            if (isset($consortium) && $consortium->getLat() != null && !$consortium->isDeleted()) {
                $place = new PlaceDetails();
                $place->setId($consortium->getId());
                $place->setName($consortium->getName());
                $place->setType("tourism_consortium");
                $place->setLatitude($consortium->getLat());
                $place->setLongitude($consortium->getLng());

                $uri = $this->get('router')->generate('consortium_show', array(
                    'id' => $consortium->getId(),
                ), UrlGeneratorInterface::ABSOLUTE_URL);
                $place->setHref($uri);
                $consorzi[] = $place;
            }
        }
        /** @var Profession $profession */
        foreach ($this->professionRepo->findAll() as $profession) {
            if (isset($profession) && !$profession->isDeleted()) {
                /** @var Address $address */
                if (($address = $profession->getAddress()) != null) {
                    if ($profession->getLat() != null && $profession->getLat() != '') {

                        $place = new PlaceDetails();
                        $place->setId($profession->getId());
                        $place->setName($profession->getFirstName() . ' ' . $profession->getLastName());
                        $place->setType('tourism_profession');
                        $place->setLatitude($profession->getLat());
                        $place->setLongitude($profession->getLng());

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
        foreach ($this->iatRepo->findAll() as $iat) {
            if (isset($iat) && !$iat->isDeleted()) {
                if ($iat->getLat() != null) {
                    $place = new PlaceDetails();
                    $place->setId($iat->getId());
                    $place->setName($iat->getName());
                    $place->setType("tourism_iat");
                    $place->setLatitude($iat->getLat());
                    $place->setLongitude($iat->getLng());

                    $uri = $this->get('router')->generate('iat_show', array(
                        'id' => $iat->getId(),
                    ), UrlGeneratorInterface::ABSOLUTE_URL);
                    $place->setHref($uri);

                    $iats[] = $place;
                }
            }
        }
        /** @var SportFacility $sportFacility */
        foreach ($this->sportFacilityRepo->findAll() as $sportFacility) {
            if (isset($sportFacility) && !$sportFacility->isDeleted()) {
                $place = new PlaceDetails();
                $place->setId($sportFacility->getId());
                $place->setName($sportFacility->getName());
                $place->setType('tourism_sport_facility');

                if ($sportFacility->getAddress() != null &&
                    $sportFacility->getAddress()->getLat() != null && $sportFacility->getAddress()->getLat() != 0
                ) {
                    $place->setLatitude($sportFacility->getAddress()->getLat());
                    $place->setLongitude($sportFacility->getAddress()->getLng());
                }

                $uri = $this->get('router')->generate('sport_facility_show', array(
                    'id' => $sportFacility->getId(),
                ), UrlGeneratorInterface::ABSOLUTE_URL);
                $place->setHref($uri);

                if ($place->getLatitude() != '') {
                    $sportFacilities[] = $place;
                }
            }
        }
        /** @var Accomodation $accomodation */
        foreach ($this->accomodationRepo->findAll() as $accomodation) {
            if (isset($accomodation) && !$accomodation->isDeleted()) {
                $place = new PlaceDetails();
                $place->setId($accomodation->getId());
                $place->setName($accomodation->getName());
                $place->setType('accomodation');

                if ($accomodation->getAddress() != null &&
                    $accomodation->getAddress()->getLat() != null && $accomodation->getAddress()->getLat() != 0
                ) {
                    $place->setLatitude($accomodation->getAddress()->getLat());
                    $place->setLongitude($accomodation->getAddress()->getLng());
                }

                $uri = $this->get('router')->generate('accomodation_show', array(
                    'id' => $accomodation->getId(),
                ), UrlGeneratorInterface::ABSOLUTE_URL);
                $place->setHref($uri);

                if ($place->getLatitude() != '') {
                    $accomodations[] = $place;
                }
            }
        }

        return $this->render('UmbriaOpenApiBundle:Map:index.html.twig', array(
            'attrattori' => $attrattori, 'proposte' => $proposte, 'eventi' => $eventi,
            'agenzieViaggio' => $agenzieViaggio, 'professioni' => $professioni, 'consorzi' => $consorzi,
            'iat' => $iats, 'accomodation' => $accomodations,
            'sportFacility' => $sportFacilities
        ));
    }
}
