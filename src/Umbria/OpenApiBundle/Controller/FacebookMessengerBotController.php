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
use Symfony\Component\HttpFoundation\Response;
use Umbria\OpenApiBundle\Controller\Tourism\BaseController;
use AnthonyMartin\GeoLocation\GeoLocation;
use Symfony\Component\Config\Definition\Exception\Exception;
use Umbria\OpenApiBundle\Entity\FacebookUsersMessages;
use Umbria\OpenApiBundle\Repository\FacebookUsersMessagesRepository;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\AttractorRepository;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\ProposalRepository;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\EventRepository;
use Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities\TravelAgencyRepository;
use JMS\DiExtraBundle\Annotation as DI;

class FacebookMessengerBotController extends BaseController
{
    private $response;

    public function fakeAction()
    {
        /*retrieve last user's message from db*/
        $em = $this->getDoctrine()->getManager();
        /**@var FacebookUsersMessagesRepository $messagesRepo */
        $messagesRepo = $em->getRepository(FacebookUsersMessages::class);
        $lastSavedMessage = $messagesRepo->findLastUserMessage("2147483647");
        if ($lastSavedMessage !== null) {
            $logger = $this->get('logger');
            $logger->info("Saved message time:" . $lastSavedMessage->getTimeStamp()->format('Y-m-d H:i:s'));


            $messageEntity = new FacebookUsersMessages();
            $messageEntity->setEntry(json_encode($lastSavedMessage->getEntry()));
            $messageEntity->setSender($lastSavedMessage->getEntry()[0]['messaging'][0]['sender']['id']);
            $messageEntity->setTimeStamp($lastSavedMessage->getTimeStamp());
            $em->persist($messageEntity);
            $em->flush();
        }
    }

    /**
     * @return Response
     */
    public function indexAction()
    {

        $isUserFirstMessageToday = false;
        $isGreeting = false;
        $isValidQuery = false;
        $repeatOldQuery = false;
        $refuseRepeatOldQuery = false;
        $isConfirm = false;

        $this->response = new Response();
        $challenge = $_REQUEST['hub_challenge'];
        $verify_token = $_REQUEST['hub_verify_token'];
        if ($verify_token === 'testtoken') {
            // Set this Verify Token Value on your Facebook App

            $this->response->headers->set('Content-Type', 'text/plain');
            $this->response->sendHeaders();
            $this->response->setContent($challenge);
            return $this->response;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $logger = $this->get('logger');
        $logger->info(file_get_contents('php://input'));
        // Get the Senders Graph ID
        $sender = $input['entry'][0]['messaging'][0]['sender']['id'];
        $logger->info($sender);


        /*retrieve last user's message from db*/
        $em = $this->getDoctrine()->getManager();
        /**@var FacebookUsersMessagesRepository $messagesRepo */
        $messagesRepo = $em->getRepository(FacebookUsersMessages::class);
        $lastSavedMessage = $messagesRepo->findLastUserMessage($sender);
        $oldKeywords = null;

        if ($this->getIntent($input) === "greeting") {
            $isGreeting = true;
        }
        if ($lastSavedMessage !== null) {
            $logger->info("Saved message time:" . $lastSavedMessage->getTimeStamp()->format('Y-m-d H:i:s'));
            $today = new DateTime(); // This object represents current date/time
            $today->setTime(0, 0, 0);
            $diff = $today->diff($lastSavedMessage->getTimeStamp());
            $diffDays = (integer)$diff->format("%R%a"); // Extract days count in interval
            if ($diffDays == 0) {
                $isUserFirstMessageToday = false;
            }

            if ($this->getIntent($input) === "confirm") {
                $isConfirm = true;
                $oldKeywords = $this->getKeywords($lastSavedMessage->getEntry());
                if (count($oldKeywords) > 0) {
                    $repeatOldQuery = true;
                }
            } else if ($this->getIntent($input) === "refuse") {
                $refuseRepeatOldQuery = true;
            }

        }

        /*save received user's message*/
        $messageEntity = new FacebookUsersMessages();
        $messageEntity->setEntry($input);
        $messageEntity->setSender($sender);
        $date = new DateTime();
        $date->setTimestamp(substr($input['entry'][0]['time'], 0, 10));
        $messageEntity->setTimeStamp($date);
        $em->persist($messageEntity);
        $em->flush();


        $keywords = $repeatOldQuery ? $oldKeywords : $this->getKeywords($input);

        $logger->info("Keywords: " . json_encode($keywords));

        //====================================================Response of the Bot=======================================================
        //===========================Default Image=============================
        $imageurl = null;
        //===========================Default Response==========================
        $welcomeText = "Benvenuto su Umbria Digitale Open API. \n";

        $descriptionText = "Vuole conoscere le attrazioni da vedere, i prossimi eventi o le agenzie turistiche?";
        $notRecognizedQuery = "Scusi, ma non ho capito la sua richiesta. \n";
        //=====================================================================


        if (count($keywords) > 0) {
            $isValidQuery = true;
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
                    $content .= "\n\tEmail : " . $email;
                }
                if (!is_null($startDate) && !is_null($endDate)) {
                    $content .= "Durata : dal " . $startDate . " al " . $endDate . "\n" . "Descrizione : ";
                }
                $content .= "\n" . $ResourceOriginUrl;

                $payload = array("recipient" => array("id" => $sender), "message" => array("text" => $text . "\n" . $content));
                $this->sendResponse($payload);

                //Check any image is included
                if (strcasecmp($imageurl, "@") != 0) {
                    //Sending the Imamge
                    $payload = array("recipient" => array("id" => $sender), "message" => array("attachment" => array("type" => "image", "payload" => array("url" => $imageurl))));
                    $this->sendResponse($payload);
                }
            }
        } else {
            if ($isGreeting || $isUserFirstMessageToday) {
                $payload = array("recipient" => array("id" => $sender), "message" => array("text" => $welcomeText . $descriptionText));
            } else {
                if (!$refuseRepeatOldQuery) {
                    $text = $notRecognizedQuery . $descriptionText;
                    if ($isConfirm) {
                        $text = $descriptionText;
                    }
                    $payload = array("recipient" => array("id" => $sender), "message" => array("text" => $text));
                } else {
                    $payload = array("recipient" => array("id" => $sender), "message" => array("text" => "Va bene, per qualsiasi altra richiesta siamo sempre a sua disposizione."));
                }
            }
            $this->sendResponse($payload);
        }

        if ($isValidQuery) {
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
            $payload = array("recipient" => array("id" => $sender), "message" => array("text" => $responseMessage));
            $this->sendResponse($payload);
        }

        return $this->response;

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
        $logger = $this->get('logger');
        $logger->info("Payload: " . json_encode($payload));
        //Execute the request but first check if the message is not empty.
        $result = curl_exec($ch);
        $logger->info("Risposta facebook: " . json_encode($result));
        $this->response->setContent($this->response->getContent() . json_encode($payload));
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

    public function getKeywords($messageEntry)
    {
        $nlpEntities = $messageEntry['entry'][0]['messaging'][0]['message']['nlp']['entities'];
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
        return null;
    }

}
