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
use JMS\DiExtraBundle\Annotation as DI;
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
        $response_image = new Response();
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
        // Get the Senders Graph ID
        $sender = $input['entry'][0]['messaging'][0]['sender']['id'];
        // Get the returned message
        $message = $input['entry'][0]['messaging'][0]['message']['text'];
        //API Url and Access Token, generate this token value on your Facebook App Page
        $url = 'https://graph.facebook.com/v2.6/me/messages?access_token=EAALdAertaysBALpNZANYDu5ZBiVG4TARAExZBJ3Ndvo78CDUS7q1AqvEZBEjdm8GCz6GQIBJMGuPHjXkOkF1f3QrjXkqJCtkPzjdMpNdSR83kGpxa1XLJVG2GKNNAhwZBlHVVQ31S5pZBZAwIoqIl7KMi8ueYiwiQv7ZAgjZCtH0q62yYsHOZCeVk5ZAs5myzJNA9kZD';
        //Initiate cURL.
        $ch = curl_init($url);
        
        // --------------------------------------------@20170718--------------------------------------------
        $sendermessage = $message;
        $image = "@";
        $text = "Welcome to UmbiraOpenApi";
        if (isset($sendermessage)) {
            switch ($sendermessage) {
                case "about":
                case "About":
                    $text = "UmbriaTourismBot ti permette di ricevere informazioni turistiche. Invia la tua posizione per scoprire tutte le bellezze che la nostra regione ha in serbo per te";
                    break;
                case "hello":
                case "Hello":
                    $arrayOfMessages = $this->executeAttractorQuery(43.105275, 12.391995, 100, true);
                    $text = "Ciao " . ". Oggi ti consiglio: " . $arrayOfMessages[0];
                    break;
                case "event":
                case "Event":
                    $arrayOfMessages = $this->executeEventQuery(43.105275, 12.391995, 100, true);
                    $text = "Ciao, Oggi ti consiglio: " . $arrayOfMessages[0];
                    break;
                case "travelagency":
                case "Travelagency":
                    $arrayOfMessages = $this->executeTravelAgencyQuery(43.105275, 12.391995, 100, true);
                    $text = "Ciao " . ". Oggi ti consiglio: \n" . $arrayOfMessages[0];
                    break;
                case "help":
                case "Help":
                case "start":
                case "Start":
                    $text = "UmbriaTourismBot ti permette di ricevere informazioni turistiche. Invia la tua posizione per scoprire tutte le bellezze che la nostra regione ha in serbo per te\n\n";
                default :
                    $text = "Lista comandi:\n";
                    $text .= "About - Informazioni sul bot\n";
                    $text .= "Event - Informazioni sul eventi\n";
                    $text .= "Travelagency -  - Informazioni sul agenzia di viaggi\n";
                    $text .= "Hello - Suggerimenti\n";
                    $text .= "Help - Visualizzazione comandi disponibili\n";
            }
        }
        //--------------------------------------------------------------------------------------------------

        $payload = array("recipient" => array("id" => $sender), "message" => array("text" => $text));
        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);
        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        //Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        //Execute the request but first check if the message is not empty.
        if (!empty($input['entry'][0]['messaging'][0]['message'])) {
            $result = curl_exec($ch);
        }
        $logger = $this->get('logger');
        $logger->info(json_encode($payload));
        $response->setContent(json_encode($payload));

        //For the Image Part ...
        if ($image!="@") {
            $payload_image = array("recipient" => array("id" => $sender), "message" => array("attachment" => array("type" => "image", "payload" => array("url" => $image, "is_reusable" => true,))));
            //Attach our encoded JSON string to the POST fields.
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload_image));
            //Set the content type to application/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $logger_image = $this->get('logger_image');
            $logger_image->info(json_encode($payload_image));
            $response_image->setContent(json_encode($payload_image));
        }

        return $response;
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
                $stringResult[0] = "\nNome : ".$poi->getName() . "\nDescrizione : \n" . str_replace('&nbsp;', ' ', strip_tags($poi->getShortDescription())) . "\n" . $poi->getResourceOriginUrl();
                return $stringResult;
            } else {
                $i = 0;
                foreach ($pois as $poi) {
                    $stringResult[$i] = "\nNome : ".$poi->getName() . "\nDescrizione : \n" . str_replace('&nbsp;', ' ', strip_tags($poi->getShortDescription())) . "\n" . $poi->getResourceOriginUrl();
                    $i++;
                }
                return $stringResult;
            }
        } else {
            throw new Exception();
        }
    }

    public function executeProposalQuery($lat, $lng, $radius)
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
            $key = array_rand($pois);
            $poi = $pois[$key];
            $stringResult[0] = $poi->getName() . "\n" . str_replace('&nbsp;', ' ', strip_tags($poi->getShortDescription())) . "\n" . $poi->getResourceOriginUrl();
            return $stringResult;

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

                $stringResult[0] = $poi->getName() . "\nDescriptions : " . str_replace('&nbsp;', ' ', strip_tags($poi->getDescriptions())) . "\n" . $poi->getResourceOriginUrl(). "\n" . $poi->getImages()[0];
                return $stringResult;
            } else {
                $i = 0;
                foreach ($pois as $poi) {
                    $stringResult[$i] = $poi->getName() . "\nDescriptions : " . str_replace('&nbsp;', ' ', strip_tags($poi->getDescriptions())) . "\n" . $poi->getResourceOriginUrl(). "\n" . $poi->getImages()[0];
                    $i++;
                }
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

        $pois = $travelagencyRepo->findByPosition(
            $bounds[1]->getLatitudeInDegrees(),
            $bounds[0]->getLatitudeInDegrees(),
            $bounds[1]->getLongitudeInDegrees(),
            $bounds[0]->getLongitudeInDegrees());

        if (sizeof($pois) > 0) {
            $key = array_rand($pois);
            $poi = $pois[$key];
            $stringResult[0] = $poi->getName() . "\n" . $poi->getResourceOriginUrl();
            return $stringResult;

        } else {
            throw new Exception();
        }
    }

}