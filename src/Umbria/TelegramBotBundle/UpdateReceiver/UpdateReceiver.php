<?php

namespace Umbria\TelegramBotBundle\UpdateReceiver;

use AnthonyMartin\GeoLocation\GeoLocation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\Parameter;
use JMS\DiExtraBundle\Annotation as DI;
use Shaygan\TelegramBotApiBundle\TelegramBotApi;
use Shaygan\TelegramBotApiBundle\Type\Update;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use Umbria\OpenApiBundle\Entity\Tourism\Attractor;
use Umbria\OpenApiBundle\Entity\Tourism\Coordinate;

class UpdateReceiver implements UpdateReceiverInterface
{
    private $telegramBotApi;
    private $config;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(TelegramBotApi $telegramBotApi, $config, $em)
    {
        $this->telegramBotApi = $telegramBotApi;
        $this->config = $config;
        $this->em = $em;
    }

    public function handleUpdate(Update $update)
    {
        $arrayOfArraysOfStrings = array(
            array("/about", "/hello","/help")
        );
        $newKeyboard = new ReplyKeyboardMarkup($arrayOfArraysOfStrings, true, true);
        $message = json_decode(json_encode($update->message), true);

        // LOCATION
        if (isset($message['location'])) {
            $latitude =$message['location']['latitude'];
            $longitude = $message['location']['longitude'];

            // Controllo se all'interno dell'Umbria
            if (($latitude >= 42.36 AND $latitude <= 43.60)
                AND ($longitude >= 11.88 AND $longitude <= 13.25)
            ) {
                $text = $this->createQuery($latitude, $longitude, 10, false);
            }
            else {
                $text = "Ciao " . $message['from']['first_name'] . ". Sei troppo lontano dall'Umbria. Da noi puoi trovare: " . $this->createQuery(43.105275, 12.391995, 100, true);
            }

            $this->telegramBotApi->sendMessage($message['chat']['id'], $text);
        }

        if (isset($message['text'])) {
            switch ($message['text']) {
                case "/about":
                    $text = "UmbriaTourismBot ti permette di ricevere informazioni turistiche. Invia la tua posizione per scoprire tutte le bellezze che la nostra regione ha in serbo per te";
                    break;
                case "/hello":
                    $text = "Ciao " . $message['from']['first_name'] . ". Oggi ti consiglio di visitare ". $this->createQuery(43.105275, 12.391995, 100, true);
                    break;
                case "/help":
                case "/start":
                    $text = "UmbriaTourismBot ti permette di ricevere informazioni turistiche. Invia la tua posizione per scoprire tutte le bellezze che la nostra regione ha in serbo per te\n\n";
                default :
                    $text .= "Lista comandi:\n";
                    $text .= "/about - Informazioni sul bot\n";
                    $text .= "/hello - Suggerimenti\n";
                    $text .= "/help - Visualizzazione comandi disponibili\n";
                    break;
            }

            $newKeyboardCond = $message['text'];
            if (strcmp($newKeyboardCond, "/start") XOR strcmp($newKeyboardCond, "/help")) {
                $this->telegramBotApi->sendMessage($message['chat']['id'], $text, null, false, null, $newKeyboard);
            } else $this->telegramBotApi->sendMessage($message['chat']['id'], $text);
        }

    }

    public function createQuery($lat, $lng, $radius, $rand)
    {
        $builder = $this->em->createQueryBuilder()
            ->select('c')
            ->from('UmbriaOpenApiBundle:Tourism\Coordinate', 'c');

        if ($lat && $lng) {
            $lat = floatval($lat);
            $lng = floatval($lng);
            $radius = floatval($radius);

            $location = GeoLocation::fromDegrees($lat, $lng);
            /** @var GeoLocation[] $bounds */
            /** @noinspection PhpInternalEntityUsedInspection */
            $bounds = $location->boundingCoordinates($radius, 'km');

            // AS HIDDEN consente di definire un campo di ordinamento (usabile in having) senza alterare
            // il risultato restituito né il formato di restituzione!
            $alias = 'HIDDEN distance';
            $builder->select("c, GEO_DISTANCE(:lat, :lng, c.latitude, c.longitude) AS $alias")
                ->andWhere('c.latitude BETWEEN :minLat and :maxLat')
                ->andWhere('c.longitude BETWEEN :minLng and :maxLng')
                ->andWhere('GEO_DISTANCE(:lat, :lng, c.latitude, c.longitude) < :radius')
                ->orderBy('distance');

            // è necessario specificare i tipi dei parametri come INTEGER per evitare che doctrine
            // inserisca gli apici, cosa che impedisce il funzionamento della query.
            $builder->setParameters(new ArrayCollection(array(
                new Parameter('lat', $lat, Type::INTEGER),
                new Parameter('lng', $lng, Type::INTEGER),
                new Parameter('minLat', $bounds[0]->getLatitudeInDegrees(), Type::INTEGER),
                new Parameter('minLng', $bounds[0]->getLongitudeInDegrees(), Type::INTEGER),
                new Parameter('maxLat', $bounds[1]->getLatitudeInDegrees(), Type::INTEGER),
                new Parameter('maxLng', $bounds[1]->getLongitudeInDegrees(), Type::INTEGER),
                new Parameter('radius', $radius, Type::INTEGER),
            )));
        }

        $query = $builder->getQuery();
        $coordinates = $query->getResult();

        $pois = array();
        /** @var Coordinate $poi */
        foreach ($coordinates as $poi) {
            $attractor = $poi->getAttrattore();
            if ($attractor != null) {
                $pois[] = $attractor;
            }
        }

        if ($rand) {
            $key = array_rand($pois);
            /** @var Attractor $poi */
            $poi = $pois[$key];
            return $poi->getDenominazione();
        } else {
            return $listOfResults = implode(", ", array_values($pois));
        }
    }
}
