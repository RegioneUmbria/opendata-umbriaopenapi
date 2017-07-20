<?php
/**
 * Created by PhpStorm.
 * User: DeveloperOspite
 * Date: 11/07/2017
 * Time: 10:30
 */
namespace Umbria\OpenApiBundle\Controller;
use function GuzzleHttp\Promise\exception_for;
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
        //$url = 'https://graph.facebook.com/v2.6/me/messages?access_token=EAALdAertaysBAE4TLVKCiIsZBeJMhOl4dq0i7XT9QIy7Xfir87JIjC5ldDNulS8ZAhilEzoq7JZAmGTK1e3ZCPG9Lyv5EmJnrIekeJJD3eZB37M2TUaSoZAH53wdOuCJTdF2m6QZA5ZBoZB6RNPdXll5M6DGCEZAzqhwZCvVZCWRlAhtWK0xWd3ApwMXr5qf0ALo90UZD';
        //Initiate cURL.
        $ch = curl_init($url);

        //====================================================Response of the Bot======================================================
        //===========================Default Image=============================
        $imageurl = "@";
        //===========================Default Response==========================
        $text = "Welcome to UmbiraOpenApi";
        //========================The manuel of the Bot========================
        $description ="Lista comandi:\n";
        $description .= "About - Informazioni sul bot\n";
        $description .= "Event - Informazioni sul eventi\n";
        $description .= "Travelagency - Informazioni sul agenzia di viaggi\n";
        $description .= "Hello - Suggerimenti\n";
        $description .= "Help - Visualizzazione comandi disponibili\n";
        //=====================================================================
        if (isset($message)) {
            switch ($message) {
                case "about":
                case "About":
                    $text = "UmbriaTourismBot ti permette di ricevere informazioni turistiche. Invia la tua posizione per scoprire tutte le bellezze che la nostra regione ha in serbo per te";
                    break;
                case "hello":
                case "Hello":
                    $arrayOfMessages = $this->executeAttractorQuery(43.105275, 12.391995, 100, true);
                    $title = $arrayOfMessages[0];
                    $imageurl = $arrayOfMessages[1];
                    $subtitle = $arrayOfMessages[2];
                    $ResourceOriginUrl = $arrayOfMessages[3];
                    $text = "Ciao, Oggi ti consiglio: " . "\n" . $title;
                    $content = "Descrizione : \n" . $subtitle . "\n" . $ResourceOriginUrl;
                    break;
                case "event":
                case "Event":
                    $arrayOfMessages = $this->executeEventQuery(43.105275, 12.391995, 100, true);
                    $title = $arrayOfMessages[0];
                    $imageurl = $arrayOfMessages[1];
                    $subtitle = $arrayOfMessages[2];
                    $ResourceOriginUrl = $arrayOfMessages[3];
                    $startDate = $arrayOfMessages[4];
                    $endDate = $arrayOfMessages[5];
                    $text = "Ciao, Oggi ti consiglio: " . "\n" . $title;
                    $content = "Durata : dal " . $startDate . " al " . $endDate . "\n" . "Descrizione : ";//.$subtitle.
                    $content = $content . "\n" . $ResourceOriginUrl;
                    break;
                case "travelagency":
                case "Travelagency":
                    $arrayOfMessages = $this->executeTravelAgencyQuery(43.105275, 12.391995, 100, true);
                    $title = $arrayOfMessages[0];
                    $imageurl = $arrayOfMessages[1];
                    $subtitle = $arrayOfMessages[2];
                    $ResourceOriginUrl = $arrayOfMessages[3];
                    $telephone = $arrayOfMessages[4];
                    $fax = $arrayOfMessages[5];
                    $email = $arrayOfMessages[6];
                    $text = "Ciao, Oggi ti consiglio: " . "\n" . $title;
                    $content = "Descrizione : \n";
                    if (!is_null($telephone)) {
                        $content = $content . "\tTelephone : " . $telephone;
                    }
                    if (!is_null($fax)) {
                        $content = $content . "\n\tFax : " . $fax;
                    }
                    if (!is_null($email)) {
                        $content = $content . "\n\temail : " . $email;
                    }
                    $content = $content . "\n" . $ResourceOriginUrl;
                    break;
                case "help":
                case "Help":
                case "start":
                case "Start":
                    $text = "UmbriaTourismBot ti permette di ricevere informazioni turistiche. Invia la tua posizione per scoprire tutte le bellezze che la nostra regione ha in serbo per te\n\n";
                    $text .=$description;
                    break;
                default :
                    $text = $description;
            }
        }
        //============================================================================================================
        //================================Sending the title and Image (if any) =======================================
        //Check any image is included
        if (strcasecmp($imageurl, "@") != 0) {
            $payload = array("recipient" => array("id" => $sender), "message" => array("text" => $text));
            //Tell cURL that we want to send a POST request.
            curl_setopt($ch, CURLOPT_POST, 1);
            //Attach our encoded JSON string to the POST fields.
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            //Set the content type to apsplication/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            //Execute the request but first check if the message is not empty.
            if (!empty($input['entry'][0]['messaging'][0]['message'])) {
                $result = curl_exec($ch);
            }
            $logger = $this->get('logger');
            $logger->info(json_encode($payload));
            $response->setContent(json_encode($payload));

            //Sending the Imamge
            $payload = array("recipient" => array("id" => $sender), "message" => array("attachment" => array("type" => "image", "payload" => array("url" => $imageurl))));
            //Tell cURL that we want to send a POST request.
            curl_setopt($ch, CURLOPT_POST, 1);
            //Attach our encoded JSON string to the POST fields.
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            //Set the content type to apsplication/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            //Execute the request but first check if the message is not empty.
            if (!empty($input['entry'][0]['messaging'][0]['message'])) {
                $result = curl_exec($ch);
            }
            $logger = $this->get('logger');
            $logger->info(json_encode($payload));
            $response->setContent(json_encode($payload));
            //Set the Description and the ResourceOriginUrl
            $payload = array("recipient" => array("id" => $sender), "message" => array("text" => $content));
        } else {
            //Set the Description and the ResourceOriginUrl
            $payload = array("recipient" => array("id" => $sender), "message" => array("text" => $text . "\n" . $content));
        }
        //============================================================================================================
        //======================Sending the Description and the ResourceOriginUrl (if any) ===========================
        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);
        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        //Set the content type to apsplication/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        //Execute the request but first check if the message is not empty.
        if (!empty($input['entry'][0]['messaging'][0]['message'])) {
            $result = curl_exec($ch);
        }
        $logger = $this->get('logger');
        $logger->info(json_encode($payload));
        $response->setContent(json_encode($payload));
        //============================================================================================================
        // =======================================================The End  ==========================================================
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