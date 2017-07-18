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
//        $servername = "localhost";
//        $username = "root";
//        $password = "";
//        // Create connection
//        $conn = mysqli_connect($servername, $username, $password,"uoa");

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
        // Get the Senders Graph ID
        $sender = $input['entry'][0]['messaging'][0]['sender']['id'];
        // Get the returned message
        $message = $input['entry'][0]['messaging'][0]['message']['text'];
        //API Url and Access Token, generate this token value on your Facebook App Page
        $url = 'https://graph.facebook.com/v2.6/me/messages?access_token=EAALdAertaysBALpNZANYDu5ZBiVG4TARAExZBJ3Ndvo78CDUS7q1AqvEZBEjdm8GCz6GQIBJMGuPHjXkOkF1f3QrjXkqJCtkPzjdMpNdSR83kGpxa1XLJVG2GKNNAhwZBlHVVQ31S5pZBZAwIoqIl7KMi8ueYiwiQv7ZAgjZCtH0q62yYsHOZCeVk5ZAs5myzJNA9kZD';
        //Initiate cURL.
        $sendermessage=$message;
        $ch = curl_init($url);
        $text="Welcome to UmbiraOpenApi";
        $arrayOfMessages[0]="Error";
        if(isset($sendermessage)) {
            switch ($sendermessage) {
                case "about":
                case "About":
                    $text = "UmbriaTourismBot ti permette di ricevere informazioni turistiche. Invia la tua posizione per scoprire tutte le bellezze che la nostra regione ha in serbo per te";
                    break;
                case "hello":
                case "Hello":
                    $arrayOfMessages = $this->executeAttractorQuery(43.105275, 12.391995, 100, true);
                    $text = "Ciao " .". Oggi ti consiglio: " . $arrayOfMessages[0];

//                    $sql="SELECT name,shortDescription,resourceOriginUrl FROM tourism_attractor ORDER BY RAND()LIMIT 1";
//                    $result = mysqli_query($conn,$sql);
//                    while ($row =mysqli_fetch_array($result)){
//                        $aname=$row['name'];
//                        $ashortDescription=$row['shortDescription'];
//                        $aresourceOriginUrl=$row['resourceOriginUrl'];
//                        $text=$text."\nNome : ".$aname."\nDescrizione : ".$ashortDescription."\n".$aresourceOriginUrl;
//                    }
//                    break;
                case "event":
                case "Event":
                    $text = "Ciao, Oggi ti consiglio: ";
//                    $sql="SELECT name,resourceOriginUrl FROM tourism_event ORDER BY RAND()LIMIT 1";
//                    $result = mysqli_query($conn,$sql);
//                    while ($row =mysqli_fetch_array($result)){
//                        $ename=$row['name'];
//                        $eshortDescription=$row['shortDescription'];
//                        $eresourceOriginUrl=$row['resourceOriginUrl'];
//                        $text=$text."\nNome : ".$ename."\nDescrizione : \n".strip_tags(preg_replace("&nbsp"," ",$eshortDescription)).$eresourceOriginUrl;
//                    }
                    break;
                case "travelagency":
                case "Travelagency":
                    $text = "Ciao, Oggi ti consiglio: " ;
//                    $sql="SELECT name,telephone,email,resourceOriginUrl FROM tourism_travelagency ORDER BY RAND()LIMIT 1";
//                    $result = mysqli_query($conn,$sql);
//                    while ($row =mysqli_fetch_array($result)){
//                        $taname=$row['name'];
//                        //$tatelephone=$row['telephone'];
//                        //$taemail=$row['email'];
//                        $taresourceOriginUrl=$row['resourceOriginUrl'];
//                        $text=$text."\nNome : ".$taname;//."\nTelefono : ";//.$tatelephone."\ne-mail".$taemail."\n"
//                        $text=$text."\n".$taresourceOriginUrl;
//                    }
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

        $stringResult = "Error";
        if (sizeof($pois) > 0) {
            if ($rand) {
                $key = array_rand($pois);

                $poi = $pois[$key];
                $stringResult[0] = $poi->getName() . "\n" . str_replace('&nbsp;', ' ', strip_tags($poi->getShortDescription())) . "\n" . $poi->getResourceOriginUrl();
                return $stringResult;
            } else {
                $i = 0;
                foreach ($pois as $poi) {
                    $stringResult[$i] = $poi->getName() . "\n" . str_replace('&nbsp;', ' ', strip_tags($poi->getShortDescription())) . "\n" . $poi->getResourceOriginUrl();
                    $i++;
                }
                return sizeof($pois);
            }
        } else {
            //throw new Exception();
            return sizeof($pois);
        }
    }


}