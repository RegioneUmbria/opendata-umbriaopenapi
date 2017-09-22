<?php
/**
 * Created by PhpStorm.
 * User: DeveloperOspite
 * Date: 11/07/2017
 * Time: 10:30
 */

namespace Umbria\OpenApiBundle\Controller;
use Symfony\Component\HttpFoundation\Response;
use Umbria\OpenApiBundle\Controller\Tourism\BaseController;
use AnthonyMartin\GeoLocation\GeoLocation;
use Symfony\Component\Config\Definition\Exception\Exception;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\AttractorRepository;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\ProposalRepository;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\EventRepository;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\TravelAgencyRepository;
class FacebookMessengerBotController extends BaseController
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $response = new Response();
        $challenge = $_REQUEST['hub_challenge'];
        $verify_token = $_REQUEST['hub_verify_token'];
        if ($verify_token === 'testtoken') {
            // Set this Verify Token Value on your Facebook App

            $response->headers->set('Content-Type', 'text/plain');
            $response->sendHeaders();
            $response->setContent($challenge);
            return $response;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $logger = $this->get('logger');
        $logger->info(file_get_contents('php://input'));
        // Get the Senders Graph ID
        $sender = $input['entry'][0]['messaging'][0]['sender']['id'];
        // Get the returned message
        $message = strtolower($input['entry'][0]['messaging'][0]['message']['text']);

        $keywords = array();
        (strpos($message, "eventi") != false ||
            strpos($message, "evento") != false
        )
            ? $keywords[] = "events" : null;

        (strpos($message, "attrattori") != false ||
            strpos($message, "attrattore") != false ||
            strpos($message, "attrazione") != false ||
            strpos($message, "attrazioni") != false
        ) ? $keywords[] = "attractors" : null;

        (strpos($message, "agenzie") != false ||
            strpos($message, "agenzia") != false
        ) ? $keywords[] = "travel_agencies" : null;


        //====================================================Response of the Bot=======================================================
        //===========================Default Image=============================
        $imageurl = null;
        //===========================Default Response==========================
        $welcomeText = "Benvenuto su Umbria Digitale Open API. \n " .
            "Siamo sempre a disposizione per forniriti informazioni turistiche sulla regione Umbria.";

        $descriptionText = "Come possiamo aiutarti? \n" .
            "Vuoi conoscere i prossimi eventi? \n" .
            "Vuoi dei suggerimenti sugli attrattori da scoprire? \n" .
            "Oppure preferisci avere dei contatti sulle agenzie di viaggio? \n";
        //=====================================================================

        if (count($keywords) > 0) {
            $logger->info("ciao");
            foreach ($keywords as $keyword) {
                $arrayOfMessages = array();

                $startDate = null;
                $endDate = null;

                $telephone = null;
                $fax = null;
                $email = null;
                switch ($keyword) {
                    case "attractors":
                        $arrayOfMessages = $this->executeAttractorQuery(43.105275, 12.391995, 100, true);
                        break;
                    case "events":
                        $arrayOfMessages = $this->executeEventQuery(43.105275, 12.391995, 100, true);
                        $startDate = $arrayOfMessages[4];
                        $endDate = $arrayOfMessages[5];
                        break;
                    case "travel_agencies":
                        $arrayOfMessages = $this->executeTravelAgencyQuery(43.105275, 12.391995, 100, true);
                        $telephone = $arrayOfMessages[4];
                        $fax = $arrayOfMessages[5];
                        $email = $arrayOfMessages[6];
                        break;
                }

                $title = $arrayOfMessages[0];
                $imageurl = $arrayOfMessages[1];
                $subtitle = $arrayOfMessages[2];
                $ResourceOriginUrl = $arrayOfMessages[3];
                $text = $title . "\n";
                $content = "Descrizione : \n" . $subtitle . "\n";
                if (!is_null($telephone)) {
                    $content .= "\tTelephone : " . $telephone;
                }
                if (!is_null($fax)) {
                    $content .= "\n\tFax : " . $fax;
                }
                if (!is_null($email)) {
                    $content .= "\n\temail : " . $email;
                }
                if (!is_null($startDate) && !is_null($endDate)) {
                    $content .= "Durata : dal " . $startDate . " al " . $endDate . "\n" . "Descrizione : ";
                }
                $content .= "\n" . $ResourceOriginUrl;

                $payload = array("recipient" => array("id" => $sender), "message" => array("text" => $text));
                $this->sendResponse($payload, $response);
                //Set the Description and the ResourceOriginUrl
                $payload = array("recipient" => array("id" => $sender), "message" => array("text" => $content));
                $this->sendResponse($payload, $response);

                //Check any image is included
                if (strcasecmp($imageurl, "@") != 0) {
                    //Sending the Imamge
                    $payload = array("recipient" => array("id" => $sender), "message" => array("attachment" => array("type" => "image", "payload" => array("url" => $imageurl))));
                    $this->sendResponse($payload, $response);
                }
            }
        } else {
            $payload = array("recipient" => array("id" => $sender), "message" => array("text" => $welcomeText));
            $this->sendResponse($payload, $response);
            $payload = array("recipient" => array("id" => $sender), "message" => array("text" => $descriptionText));
            $this->sendResponse($payload, $response);
        }


        return $response;

    }

    private function sendResponse($payload, $response)
    {

        //API Url and Access Token, generate this token value on your Facebook App Page
        $url = 'https://graph.facebook.com/v2.6/me/messages?access_token=EAAcCBY9TweYBAP1NeBu61c7hxv6wa1yMYFGSKJ3YffbatUJ6eYy2jIn7deTc4noiIGubZAJA8UhEa5keA3fGohlMO8SZBWpDfnHEQvaYi4YfO8ErW9p6YBZBkUXqIrSMjdXTuhfiiW42Jb3EWmqWTHXyLhVuYFeuQZAqzgVNEAZDZD';

        //Initiate cURL.
        $ch = curl_init($url);

        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);
        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        //Set the content type to apsplication/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $logger = $this->get('logger');
        $logger->info($payload);
        //Execute the request but first check if the message is not empty.
        $result = curl_exec($ch);
        $logger->info("Risposta facebook: " . $result);
        $response->setContent($response->setContent . json_encode($payload));
    }

    public function executeAttractorQuery($lat, $lng, $radius, $rand)
    {
        /**@var AttractorRepository $attractorRepo */
        $attractorRepo = $this->getDoctrine()->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Attractor');

        $location = GeoLocation::fromDegrees($lat, $lng);
        /** @var GeoLocation[] $bounds */
        /** @noinspection PhpInternalEntityUsedInspection */
        $bounds = $location->boundingCoordinates($radius, 'km');

        $pois = $attractorRepo->findByPosition(
            $bounds[1]->getLatitudeInDegrees(),
            $bounds[0]->getLatitudeInDegrees(),
            $bounds[1]->getLongitudeInDegrees(),
            $bounds[0]->getLongitudeInDegrees());

        if (sizeof($pois) > 0) {
            if ($rand) {
                $key = array_rand($pois);
                $poi = $pois[$key];
                //$stringResult[0] = $poi->getName() . "\nDescriptions : " . str_replace('&nbsp;', ' ', strip_tags($poi->getDescriptions())) . "\n" . $poi->getResourceOriginUrl();
                $stringResult[0] = $poi->getName();
                $stringResult[1] = $poi->getImages()[0];
                $stringResult[2] = str_replace('&nbsp;', ' ', strip_tags($poi->getShortDescription())) ;
                $stringResult[3] = $poi->getResourceOriginUrl();
                return $stringResult;
            }
        } else {
            throw new Exception();
        }
    }

    public function executeProposalQuery($lat, $lng, $radius,$rand)
    {
        /**@var ProposalRepository $proposalRepo */
        $proposalRepo = $this->getDoctrine()->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Proposal');

        $location = GeoLocation::fromDegrees($lat, $lng);
        /** @var GeoLocation[] $bounds */
        /** @noinspection PhpInternalEntityUsedInspection */
        $bounds = $location->boundingCoordinates($radius, 'km');

        $pois = $proposalRepo->findByPosition(
            $bounds[1]->getLatitudeInDegrees(),
            $bounds[0]->getLatitudeInDegrees(),
            $bounds[1]->getLongitudeInDegrees(),
            $bounds[0]->getLongitudeInDegrees());

        if (sizeof($pois) > 0) {
            if ($rand) {
                $key = array_rand($pois);
                $poi = $pois[$key];
                //$stringResult[0] = $poi->getName() . "\nDescriptions : " . str_replace('&nbsp;', ' ', strip_tags($poi->getDescriptions())) . "\n" . $poi->getResourceOriginUrl();
                $stringResult[0] = $poi->getName();
                $stringResult[1] = $poi->getImages()[0];
                $stringResult[2] = str_replace('&nbsp;', ' ', strip_tags($poi->getshortDescription())) ;
                $stringResult[3] = $poi->getResourceOriginUrl();
                return $stringResult;
            }
        } else {
            throw new Exception();
        }
    }

    public function executeEventQuery($lat, $lng, $radius, $rand)
    {
        /**@var EventRepository $eventRepo */
        $eventRepo = $this->getDoctrine()->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\Event');

        //$pois = $eventRepo->findByID($id);

        $location = GeoLocation::fromDegrees($lat, $lng);
        /** @var GeoLocation[] $bounds */
        /** @noinspection PhpInternalEntityUsedInspection */
        $bounds = $location->boundingCoordinates($radius, 'km');

        $pois = $eventRepo->findByPosition(
            $bounds[1]->getLatitudeInDegrees(),
            $bounds[0]->getLatitudeInDegrees(),
            $bounds[1]->getLongitudeInDegrees(),
            $bounds[0]->getLongitudeInDegrees());


        if (sizeof($pois) > 0) {
            if ($rand) {
                $key = array_rand($pois);
                $poi = $pois[$key];
                //$stringResult[0] = $poi->getName() . "\nDescriptions : " . str_replace('&nbsp;', ' ', strip_tags($poi->getDescriptions())) . "\n" . $poi->getResourceOriginUrl();
                $stringResult[0] = $poi->getName();
                $stringResult[1] = $poi->getImages()[0];
                $stringResult[2] = str_replace('&nbsp;', ' ', strip_tags($poi->getDescriptions())) ;
                $stringResult[3] = $poi->getResourceOriginUrl();
                $stringResult[4] = date_format($poi-> getStartDate(),"d-m-Y");
                $stringResult[5] = date_format($poi-> getEndDate(),"d-m-Y");
                return $stringResult;
            }
        } else {
            throw new Exception();
        }
    }

    public function executeTravelAgencyQuery($lat, $lng, $radius, $rand)
    {
        /**@var TravelAgencyRepository $travelagencyRepo */
        $travelagencyRepo = $this->getDoctrine()->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\TravelAgency');

        //$pois = $eventRepo->findByID($id);

        $location = GeoLocation::fromDegrees($lat, $lng);
        /** @var GeoLocation[] $bounds */
        /** @noinspection PhpInternalEntityUsedInspection */
        $bounds = $location->boundingCoordinates($radius, 'km');

        $pois = $travelagencyRepo->findByPosition($bounds[1]->getLatitudeInDegrees(), $bounds[0]->getLatitudeInDegrees(), $bounds[1]->getLongitudeInDegrees(), $bounds[0]->getLongitudeInDegrees());

        if (sizeof($pois) > 0) {
            if ($rand) {
                $key = array_rand($pois);
                $poi = $pois[$key];
                //$stringResult[0] = $poi->getName() . "\nDescriptions : " . str_replace('&nbsp;', ' ', strip_tags($poi->getDescriptions())) . "\n" . $poi->getResourceOriginUrl();
                $stringResult[0] = $poi->getName();
                $stringResult[1] = "@";
                $stringResult[2] = "";
                $stringResult[3] = $poi->getResourceOriginUrl();
                $stringResult[4] = $poi->getTelephone()[0];
                $stringResult[5] = $poi->getFax()[0];
                $stringResult[6] = $poi->getEmail()[0];
                return $stringResult;
            }
        } else {
            throw new Exception();
        }
    }

}
