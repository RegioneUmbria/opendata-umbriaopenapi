<?php
/**
 * Created by PhpStorm.
 * User: DeveloperOspite
 * Date: 11/07/2017
 * Time: 10:30
 */

namespace Umbria\OpenApiBundle\Controller;

use DateTime;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Response;
use AnthonyMartin\GeoLocation\GeoLocation;
use Umbria\OpenApiBundle\Entity\FacebookUsersMessages;
use Umbria\OpenApiBundle\Repository\FacebookUsersMessagesRepository;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\AttractorRepository;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\ProposalRepository;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\EventRepository;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\TravelAgencyRepository;

class FacebookMessengerBotController extends BaseController
{
    const NO_INTENT = -1;
    const INTENT_GREET = 0;
    const INTENT_SEND_LOCATION = 1;
    const INTENT_TOURISM_QUERY = 2;
    const INTENT_CONFIRM = 5;
    const INTENT_REFUSE = 6;
    const INTENT_FIRST_DAILY_GREET = 7;
    const INTENT_GREET_AND_TOURISM_QUERY = 8;
    const INTENT_REFUSE_AND_TOURISM_QUERY = 9;
    const INTENT_LOCATION_SENT = 10;

    /**@var array $input */
    private $input;
    /**@var int $sender */
    private $sender;
    /**@var array $previousInput */
    private $previousInput;
    /**@var FacebookUsersMessages $previousMessage */
    private $previousMessage;
    /**@var Response $response */
    private $response;
    /**@var FacebookUsersMessagesRepository $messagesRepo */
    private $messagesRepo;
    /**@var EntityManager $em */
    private $em;
    /**@var Logger $logger */
    private $logger;
    private $lat;
    private $lng;


    private function initObject()
    {
        $this->logger = $this->get('logger');
        $this->input = json_decode(file_get_contents('php://input'), true);
        $this->sender = $this->input['entry'][0]['messaging'][0]['sender']['id'];
        /*retrieve last user's message from db*/
        $this->em = $this->getDoctrine()->getManager();
        $this->messagesRepo = $this->em->getRepository(FacebookUsersMessages::class);
        $this->previousMessage = $this->messagesRepo->findLastUserMessage($this->sender);
        $this->previousInput = $this->previousMessage != null ? $this->previousMessage->getEntry() : null;
        if ($this->previousMessage != null) {
            $this->lat = $this->previousMessage->getLat();
            $this->lng = $this->previousMessage->getLng();
        }
    }

    /**
     * @return Response
     */
    public function indexAction()
    {
        $this->response = new Response();
        if ($this->verifyToken()) {
            return $this->response;
        }

        $this->initObject();

        $this->logger->debug(file_get_contents('php://input'));
        $this->logger->debug($this->sender);


        $intent = $this->retrieveIntent();

        if ($intent === self::INTENT_FIRST_DAILY_GREET) {
            $responseMessage = "Benvenuto su Umbria Digitale Open API.\nVuole conoscere le attrazioni da vedere, i prossimi eventi o le agenzie turistiche?";
            $this->sendTextResponse($responseMessage);
            $this->sendLocationResponse();
        } elseif ($intent === self::INTENT_GREET) {
            $responseMessage = "Vuole conoscere le attrazioni da vedere, i prossimi eventi o le agenzie turistiche?";
            $this->sendTextResponse($responseMessage);
            $this->sendLocationResponse();
        } elseif ($intent === self::INTENT_GREET_AND_TOURISM_QUERY ||
            $intent === self::INTENT_TOURISM_QUERY ||
            $intent === self::INTENT_CONFIRM ||
            $intent === self::INTENT_REFUSE_AND_TOURISM_QUERY
        ) {
            if ($intent === self::INTENT_GREET_AND_TOURISM_QUERY) {
                $responseMessage = "Salve!\nSu Umbria Digitale Open API puoi conoscere le attrazioni da vedere, i prossimi eventi o le agenzie turistiche.";
                $this->sendTextResponse($responseMessage);
            }

            $keywords = $intent === self::INTENT_CONFIRM ? $this->getKeywords($this->previousInput) : $this->getKeywords($this->input);
            $this->sendTourismInformations($keywords);

            $responseMessage = "Vuole un altro suggerimento ";
            if (count($keywords) == 1) {
                if ($keywords[0] == "attractors") {
                    $responseMessage .= "sulle attrazioni?";
                } else if ($keywords[0] == "events") {
                    $responseMessage .= "sugli eventi?";
                } else {
                    $responseMessage .= "sulle agenzie di viaggio?";
                }
            } else {
                $responseMessage = "Vuole altri suggerimenti simili?";
            }
            $this->sendTextResponse($responseMessage);
        } elseif ($intent === self::INTENT_REFUSE) {
            $responseMessage = "Va bene, per qualsiasi altra richiesta siamo sempre a sua disposizione.";
            $this->sendTextResponse($responseMessage);
        } elseif ($intent === self::INTENT_SEND_LOCATION) {
            $this->sendLocationResponse();
        } elseif ($intent === self::INTENT_LOCATION_SENT) {
            $responseMessage = "Effettueremo le prossime ricerche nei dintorni della posizione che ci ha inviato.\nSe vuole cambiare localizzazione basta chiedere.";
            $this->sendTextResponse($responseMessage);
        } else {
            $responseMessage = "Scusi, ma non ho capito la sua richiesta. \nVuole conoscere le attrazioni da vedere, i prossimi eventi o le agenzie turistiche?";
            $this->sendTextResponse($responseMessage);
        }

        /*save message*/
        if ($intent !== self::INTENT_CONFIRM) {
            $messageEntity = new FacebookUsersMessages();
            $messageEntity->setEntry($this->input);
            $messageEntity->setSender($this->sender);
            $this->lat != null ? $messageEntity->setLat($this->lat) : true;
            $this->lng != null ? $messageEntity->setLng($this->lng) : true;
            $date = new DateTime();
            $date->setTimestamp(substr($this->input['entry'][0]['time'], 0, 10));
            $messageEntity->setTimeStamp($date);
            $this->em->persist($messageEntity);
            $this->em->flush();
        }

        return $this->response;

    }

    private function retrieveIntent()
    {
        $intent = $this->getIntent($this->input);
        if ($intent === "greeting") {
            if ($this->hasKeywords($this->input)) {
                return self::INTENT_GREET_AND_TOURISM_QUERY;
            } else if ($this->isFirstDailyMessage()) {
                return self::INTENT_FIRST_DAILY_GREET;
            } else {
                return self::INTENT_GREET;
            }
        } else if ($intent === "refuse") {
            if ($this->isAnswer()) {
                if ($this->hasKeywords($this->input)) {
                    return self::INTENT_REFUSE_AND_TOURISM_QUERY;
                }
                return self::INTENT_REFUSE;
            }
            return self::NO_INTENT;
        } else if ($intent === "confirm") {
            if ($this->isAnswer()) {
                return self::INTENT_CONFIRM;
            }
            return self::NO_INTENT;
        } else if ($intent === "tourism_query") {
            if ($this->hasKeywords($this->input)) {
                return self::INTENT_TOURISM_QUERY;
            }
            return self::NO_INTENT;
        } else if ($intent === "location_sent") {
            return self::INTENT_LOCATION_SENT;
        } else if ($intent === "send_location") {
            return self::INTENT_SEND_LOCATION;
        } else {
            return self::NO_INTENT;
        }

    }

    private function hasKeywords($input)
    {
        if ($input == null) {
            return false;
        }
        $keywords = $this->getKeywords($input);
        return $keywords != null && count($keywords) > 0;
    }

    private function isAnswer()
    {
        return $this->hasKeywords($this->previousInput);
    }

    private function isFirstDailyMessage()
    {
        if ($this->previousMessage !== null) {
            $today = new DateTime(); // This object represents current date/time
            $today->setTime(0, 0, 0);
            $diff = $today->diff($this->previousMessage->getTimeStamp());
            $diffDays = (integer)$diff->format("%R%a"); // Extract days count in interval
            if ($diffDays == 0) {
                return false;
            }
        }
        return true;
    }

    private function verifyToken()
    {
        $challenge = $_REQUEST['hub_challenge'];
        $verify_token = $_REQUEST['hub_verify_token'];
        if ($verify_token === 'testtoken') {
            // Set this Verify Token Value on your Facebook App

            $this->response->headers->set('Content-Type', 'text/plain');
            $this->response->sendHeaders();
            $this->response->setContent($challenge);
            return true;
        }
        return false;
    }

    private function sendTourismInformations($keywords)
    {
        foreach ($keywords as $keyword) {
            $arrayOfMessages = array();

            $startDate = null;
            $endDate = null;

            $telephone = null;
            $fax = null;
            $email = null;

            $lat = 43.105275;
            $lng = 12.391995;
            if ($this->lat != null && $this->lng != null) {
                $lat = $this->lat;
                $lng = $this->lng;
            }
            switch ($keyword) {
                case "attractors":
                    $arrayOfMessages = $this->executeAttractorQuery($lat, $lng);
                    break;
                case "events":
                    $arrayOfMessages = $this->executeEventQuery($lat, $lng);
                    $startDate = $arrayOfMessages[4];
                    $endDate = $arrayOfMessages[5];
                    break;
                case "travel_agencies":
                    $arrayOfMessages = $this->executeTravelAgencyQuery($lat, $lng);
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
            $content = $subtitle != null ? "Descrizione : \n" . $subtitle . "\n" : "Descrizione:  \n";
            if (!is_null($telephone)) {
                $content .= "\tTelefono : " . $telephone;
            }
            if (!is_null($fax)) {
                $content .= "\n\tFax : " . $fax;
            }
            if (!is_null($email)) {
                $content .= "\n\tEmail : " . $email;
            }
            if (!is_null($startDate) && !is_null($endDate)) {
                $content .= "Durata : dal " . $startDate . " al " . $endDate . "\n";
            }
            $content .= "\n" . $ResourceOriginUrl;

            $this->sendTextResponse($text . "\n" . $content);

            //Check any image is included
            if (strcasecmp($imageurl, "@") != 0) {
                //Sending the Imamge
                $this->sendImageResponse($imageurl);
            }
        }
    }


    private function sendTextResponse($text)
    {
        $payload = array("recipient" => array("id" => $this->sender), "message" => array("text" => $text));
        return $this->sendResponse($payload);
    }

    private function sendImageResponse($imageUrl)
    {
        $payload = array("recipient" => array("id" => $this->sender), "message" => array("attachment" => array("type" => "image", "payload" => array("url" => $imageUrl))));
        return $this->sendResponse($payload);
    }

    private function sendLocationResponse()
    {
        $responseMessage = "Condivida con noi la posizione geografica su cui vuole che effettuiamo la ricerca";
        $payload = array("recipient" => array("id" => $this->sender), "message" => array("text" => $responseMessage, "quick_replies" => array(array("content_type" => "location"))));
        return $this->sendResponse($payload);
    }

    private function sendResponse($payload)
    {
        //API Url and Access Token, generate this token value on your Facebook App Page
        $url = 'https://graph.facebook.com/v2.6/me/messages?access_token=EAAcCBY9TweYBAPARVvgoXnefMXoi1pDrAukZBSP4wyQP77Gv2uYkGZCps10R1RTYIL4qROi0pI2mNY57fDBz0ZC7ZBLZBxcwXDwqzivefaAyQUc3nqyVqjyKGCZCEICcPqZB6yjMwGllwrYxetff21mLmBQSo3whjjDznL1sTej8wZDZD';
        //Initiate cURL.
        $ch = curl_init($url);
        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);
        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        //Set the content type to apsplication/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $this->logger->debug("Payload: " . json_encode($payload));
        //Execute the request but first check if the message is not empty.
        $result = curl_exec($ch);
        $this->logger->debug("Risposta facebook: " . json_encode($result));
        $this->response->setContent($this->response->getContent() . json_encode($payload));
    }

    public function executeAttractorQuery($lat, $lng, $radius = 20, $rand = true)
    {
        while ($radius < 200) {
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
                $key = 0;
                if ($rand) {
                    $key = array_rand($pois);
                }
                $poi = $pois[$key];
                //$stringResult[0] = $poi->getName() . "\nDescriptions : " . str_replace('&nbsp;', ' ', strip_tags($poi->getDescriptions())) . "\n" . $poi->getResourceOriginUrl();
                $stringResult[0] = $poi->getName();
                $stringResult[1] = $poi->getImages()[0];
                $stringResult[2] = str_replace('&nbsp;', ' ', strip_tags($poi->getShortDescription())) ;
                $stringResult[3] = $poi->getResourceOriginUrl();
                return $stringResult;
            } else {
                $radius += 10;
            }
        }
        return null;
    }

    public function executeProposalQuery($lat, $lng, $radius = 20, $rand = true)
    {
        while ($radius < 200) {
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
                $key = 0;
                if ($rand) {
                    $key = array_rand($pois);
                }
                $poi = $pois[$key];
                //$stringResult[0] = $poi->getName() . "\nDescriptions : " . str_replace('&nbsp;', ' ', strip_tags($poi->getDescriptions())) . "\n" . $poi->getResourceOriginUrl();
                $stringResult[0] = $poi->getName();
                $stringResult[1] = $poi->getImages()[0];
                $stringResult[2] = str_replace('&nbsp;', ' ', strip_tags($poi->getshortDescription())) ;
                $stringResult[3] = $poi->getResourceOriginUrl();
                return $stringResult;
            } else {
                $radius += 10;
            }
        }
        return null;
    }

    public function executeEventQuery($lat, $lng, $radius = 20, $rand = true)
    {
        while ($radius < 200) {
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
                $key = 0;
                if ($rand) {
                    $key = array_rand($pois);
                }
                $poi = $pois[$key];
                //$stringResult[0] = $poi->getName() . "\nDescriptions : " . str_replace('&nbsp;', ' ', strip_tags($poi->getDescriptions())) . "\n" . $poi->getResourceOriginUrl();
                $stringResult[0] = $poi->getName();
                $stringResult[1] = $poi->getImages()[0];
                $stringResult[2] = str_replace('&nbsp;', ' ', strip_tags($poi->getDescriptions())) ;
                $stringResult[3] = $poi->getResourceOriginUrl();
                $stringResult[4] = date_format($poi-> getStartDate(),"d-m-Y");
                $stringResult[5] = date_format($poi-> getEndDate(),"d-m-Y");
                return $stringResult;
            } else {
                $radius += 10;
            }
        }
        return null;
    }

    public function executeTravelAgencyQuery($lat, $lng, $radius = 20, $rand = true)
    {
        while ($radius < 200) {
            /**@var TravelAgencyRepository $travelagencyRepo */
            $travelagencyRepo = $this->getDoctrine()->getRepository('UmbriaOpenApiBundle:Tourism\GraphsEntities\TravelAgency');

            //$pois = $eventRepo->findByID($id);

            $location = GeoLocation::fromDegrees($lat, $lng);
            /** @var GeoLocation[] $bounds */
            /** @noinspection PhpInternalEntityUsedInspection */
            $bounds = $location->boundingCoordinates($radius, 'km');

            $pois = $travelagencyRepo->findByPosition($bounds[1]->getLatitudeInDegrees(), $bounds[0]->getLatitudeInDegrees(), $bounds[1]->getLongitudeInDegrees(), $bounds[0]->getLongitudeInDegrees());

            if (sizeof($pois) > 0) {
                $key = 0;
                if ($rand) {
                    $key = array_rand($pois);
                }
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
            } else {
                $radius += 10;
            }
        }
        return null;
    }

    public function getKeywords($input)
    {
        $keywords = null;
        $nlpEntities = $input['entry'][0]['messaging'][0]['message']['nlp']['entities'];
        if (isset($nlpEntities["events"])) {
            foreach ($nlpEntities["events"] as $eventEntity) {
                if ($eventEntity["confidence"] > 0.8) {
                    $keywords[] = "events";
                    break;
                }
            }
        }

        if (isset($nlpEntities["attractors"])) {
            foreach ($nlpEntities["attractors"] as $attractorEntity) {
                if ($attractorEntity["confidence"] > 0.8) {
                    $keywords[] = "attractors";
                    break;
                }
            }
        }

        if (isset($nlpEntities["travel_agencies"])) {
            foreach ($nlpEntities["travel_agencies"] as $taEntity) {
                if ($taEntity["confidence"] > 0.8) {
                    $keywords[] = "travel_agencies";
                    break;
                }
            }
        }
        return $keywords;
    }

    public function getIntent($messageEntry)
    {
        $nlpEntities = $messageEntry['entry'][0]['messaging'][0]['message']['nlp']['entities'];
        if (isset($nlpEntities["intent"])) {
            return $nlpEntities["intent"][0]["value"];
        }

        $lat = $messageEntry['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates']['lat'];
        $long = $messageEntry['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates']['long'];
        if (isset($lat) && isset($long)) {
            $this->lat = $lat;
            $this->lng = $long;
            return "location_sent";
        }
        return null;
    }

}
