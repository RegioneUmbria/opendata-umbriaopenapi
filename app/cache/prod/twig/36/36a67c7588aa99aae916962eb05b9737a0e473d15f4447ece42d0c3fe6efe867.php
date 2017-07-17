<?php

/* UmbriaProLocoBundle:Default:index.html.twig */
class __TwigTemplate_34a16213ae2ece3f9175c1d8ab8cbc18c5d9c07423e602538411a1b73ca5445b extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("UmbriaProLocoBundle::frontend.html.twig", "UmbriaProLocoBundle:Default:index.html.twig", 1);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "UmbriaProLocoBundle::frontend.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "    <div id=\"map\" class=\"map-large\"></div>
    <div id=\"legend\">
    </div>
";
    }

    // line 9
    public function block_javascripts($context, array $blocks = array())
    {
        // line 10
        echo "    ";
        $this->displayParentBlock("javascripts", $context, $blocks);
        echo "

    <script>
        var map;
        var clusterer = null;
        var umbriaCenter = {lat: 42.96637, lng: 12.35635};

        var markerGroups = {
            \"tourism_attractor\": [],
            \"tourism_proposal\": [],
            \"tourism_event\": [],
            \"tourism_travel_agency\": [],
            \"tourism_consortium\": [],
            \"tourism_profession\": [],
            \"tourism_iat\": [],
            \"tourism_sport_facility\": [],
            \"accomodation\": []
        };

        // lista delle posizioni
        var attrattori = [
            ";
        // line 31
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["attrattori"]) ? $context["attrattori"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
            // line 32
            echo "            {
                id: ";
            // line 33
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "id", array()), "html", null, true);
            echo ",
                name: \"";
            // line 34
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "name", array()), "html", null, true);
            echo "\",
                type: \"";
            // line 35
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "type", array()), "html", null, true);
            echo "\",
                latitude: ";
            // line 36
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ",
                longitude: ";
            // line 37
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo ",
                latLng: {lat: ";
            // line 38
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ", lng: ";
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo "},
                href: \"";
            // line 39
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "href", array()), "html", null, true);
            echo "\"
            },
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 42
        echo "        ];
        var proposte = [
            ";
        // line 44
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["proposte"]) ? $context["proposte"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
            // line 45
            echo "            {
                id: ";
            // line 46
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "id", array()), "html", null, true);
            echo ",
                name: \"";
            // line 47
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "name", array()), "html", null, true);
            echo "\",
                type: \"";
            // line 48
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "type", array()), "html", null, true);
            echo "\",
                latitude: ";
            // line 49
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ",
                longitude: ";
            // line 50
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo ",
                latLng: {lat: ";
            // line 51
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ", lng: ";
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo "},
                href: \"";
            // line 52
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "href", array()), "html", null, true);
            echo "\"
            },
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 55
        echo "        ];
        var eventi = [
            ";
        // line 57
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["eventi"]) ? $context["eventi"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
            // line 58
            echo "            {
                id: ";
            // line 59
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "id", array()), "html", null, true);
            echo ",
                name: \"";
            // line 60
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "name", array()), "html", null, true);
            echo "\",
                type: \"";
            // line 61
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "type", array()), "html", null, true);
            echo "\",
                latitude: ";
            // line 62
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ",
                longitude: ";
            // line 63
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo ",
                latLng: {lat: ";
            // line 64
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ", lng: ";
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo "},
                href: \"";
            // line 65
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "href", array()), "html", null, true);
            echo "\"
            },
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 68
        echo "        ];
        var agenzieViaggio = [
            ";
        // line 70
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["agenzieViaggio"]) ? $context["agenzieViaggio"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
            // line 71
            echo "            {
                id: ";
            // line 72
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "id", array()), "html", null, true);
            echo ",
                name: \"";
            // line 73
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "name", array()), "html", null, true);
            echo "\",
                type: \"";
            // line 74
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "type", array()), "html", null, true);
            echo "\",
                latitude: ";
            // line 75
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ",
                longitude: ";
            // line 76
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo ",
                latLng: {lat: ";
            // line 77
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ", lng: ";
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo "},
                href: \"";
            // line 78
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "href", array()), "html", null, true);
            echo "\"
            },
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 81
        echo "        ];
        var consorzi = [
            ";
        // line 83
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["consorzi"]) ? $context["consorzi"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
            // line 84
            echo "            {
                id: ";
            // line 85
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "id", array()), "html", null, true);
            echo ",
                name: \"";
            // line 86
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "name", array()), "html", null, true);
            echo "\",
                type: \"";
            // line 87
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "type", array()), "html", null, true);
            echo "\",
                latitude: ";
            // line 88
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ",
                longitude: ";
            // line 89
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo ",
                latLng: {lat: ";
            // line 90
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ", lng: ";
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo "},
                href: \"";
            // line 91
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "href", array()), "html", null, true);
            echo "\"
            },
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 94
        echo "        ];
        var professioni = [
            ";
        // line 96
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["professioni"]) ? $context["professioni"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
            // line 97
            echo "            {
                id: ";
            // line 98
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "id", array()), "html", null, true);
            echo ",
                name: \"";
            // line 99
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "name", array()), "html", null, true);
            echo "\",
                type: \"";
            // line 100
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "type", array()), "html", null, true);
            echo "\",
                latitude: ";
            // line 101
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ",
                longitude: ";
            // line 102
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo ",
                latLng: {lat: ";
            // line 103
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ", lng: ";
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo "},
                href: \"";
            // line 104
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "href", array()), "html", null, true);
            echo "\"
            },
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 107
        echo "        ];
        var iat = [
            ";
        // line 109
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["iat"]) ? $context["iat"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
            // line 110
            echo "            {
                id: ";
            // line 111
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "id", array()), "html", null, true);
            echo ",
                name: \"";
            // line 112
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "name", array()), "html", null, true);
            echo "\",
                type: \"";
            // line 113
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "type", array()), "html", null, true);
            echo "\",
                latitude: ";
            // line 114
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ",
                longitude: ";
            // line 115
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo ",
                latLng: {lat: ";
            // line 116
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ", lng: ";
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo "},
                href: \"";
            // line 117
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "href", array()), "html", null, true);
            echo "\"
            },
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 120
        echo "        ];
        var sportFacility = [
            ";
        // line 122
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["sportFacility"]) ? $context["sportFacility"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
            // line 123
            echo "            {
                id: ";
            // line 124
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "id", array()), "html", null, true);
            echo ",
                name: \"";
            // line 125
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "name", array()), "html", null, true);
            echo "\",
                type: \"";
            // line 126
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "type", array()), "html", null, true);
            echo "\",
                latitude: ";
            // line 127
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ",
                longitude: ";
            // line 128
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo ",
                latLng: {lat: ";
            // line 129
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ", lng: ";
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo "},
                href: \"";
            // line 130
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "href", array()), "html", null, true);
            echo "\"
            },
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 133
        echo "        ];
        var accomodation = [
            ";
        // line 135
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["accomodation"]) ? $context["accomodation"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["entity"]) {
            // line 136
            echo "            {
                id: ";
            // line 137
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "id", array()), "html", null, true);
            echo ",
                name: \"";
            // line 138
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "name", array()), "html", null, true);
            echo "\",
                type: \"";
            // line 139
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "type", array()), "html", null, true);
            echo "\",
                latitude: ";
            // line 140
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ",
                longitude: ";
            // line 141
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo ",
                latLng: {lat: ";
            // line 142
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "latitude", array()), "html", null, true);
            echo ", lng: ";
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "longitude", array()), "html", null, true);
            echo "},
                href: \"";
            // line 143
            echo twig_escape_filter($this->env, $this->getAttribute($context["entity"], "href", array()), "html", null, true);
            echo "\"
            },
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entity'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 146
        echo "        ];

        var mcAttrattori;
        var mcProposte;
        var mcEventi;
        var mcAgenzieViaggio;
        var mcConsorzi;
        var mcProfessioni;
        var mcIat;
        var mcAccomodation;
        var mcSportFacility;

        function initMap() {
            // opzioni mappa
            var mapOptions = {
                mapTypeId: google.maps.MapTypeId.HYBRID,
                center: umbriaCenter,
                zoom: 11
            };

            // creazione mappa
            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            initLegend();
            setPositionFromCookies();
            // GEOLOCATION
            geolocation();

            //Registra cambiamenti posizione su cookie
            google.maps.event.addListener(map, 'click', function (event) {
                var center = map.getCenter();
                var zoom = map.getZoom();
                var lat = center.lat().toFixed(5);
                var lng = center.lng().toFixed(5);
                Cookies.set('lat', lat);
                Cookies.set('lng', lng);
                Cookies.set('zoom', zoom);
            });
            google.maps.event.addListener(map, 'bounds_changed', function (event) {
                var center = map.getCenter();
                var zoom = map.getZoom();
                var lat = center.lat().toFixed(5);
                var lng = center.lng().toFixed(5);
                Cookies.set('lat', lat);
                Cookies.set('lng', lng);
                Cookies.set('zoom', zoom);
            });
            google.maps.event.addListener(map, 'center_changed', function (event) {
                var center = map.getCenter();
                var zoom = map.getZoom();
                var lat = center.lat().toFixed(5);
                var lng = center.lng().toFixed(5);
                Cookies.set('lat', lat);
                Cookies.set('lng', lng);
                Cookies.set('zoom', zoom);
            });
            google.maps.event.addListener(map, 'zoom_changed', function (event) {
                var center = map.getCenter();
                var zoom = map.getZoom();
                var lat = center.lat().toFixed(5);
                var lng = center.lng().toFixed(5);
                Cookies.set('lat', lat);
                Cookies.set('lng', lng);
                Cookies.set('zoom', zoom);
            });

            initMarkerClusterers();
            updateMarkersAll();
        }

        function initMarkerClusterers() {
            var clusterStyles = [
                {
                    textColor: '#DA4F49',
                    url: '";
        // line 220
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/cluster_marker.png"), "html", null, true);
        echo "',
                    height: 52,
                    width: 53
                }
            ];
            var mcOptions = {maxZoom: 15, styles: clusterStyles};
            mcAttrattori = new MarkerClusterer(map, [], mcOptions);

            var clusterStyles2 = [
                {
                    textColor: '#FAA732',
                    url: '";
        // line 231
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/cluster_marker.png"), "html", null, true);
        echo "',
                    height: 52,
                    width: 53
                }
            ];
            var mcOptions2 = {maxZoom: 15, styles: clusterStyles2};
            mcProposte = new MarkerClusterer(map, [], mcOptions2);

            var clusterStyles3 = [
                {
                    textColor: '#5BB75B',
                    url: '";
        // line 242
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/cluster_marker.png"), "html", null, true);
        echo "',
                    height: 52,
                    width: 53
                }
            ];
            var mcOptions3 = {maxZoom: 15, styles: clusterStyles3};
            mcEventi = new MarkerClusterer(map, [], mcOptions3);

            var clusterStyles4 = [
                {
                    textColor: '#EFE926',
                    url: '";
        // line 253
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/cluster_marker.png"), "html", null, true);
        echo "',
                    height: 52,
                    width: 53
                }
            ];
            var mcOptions4 = {maxZoom: 15, styles: clusterStyles4};
            mcAgenzieViaggio = new MarkerClusterer(map, [], mcOptions4);

            var clusterStyles5 = [
                {
                    textColor: '#8824B2',
                    url: '";
        // line 264
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/cluster_marker.png"), "html", null, true);
        echo "',
                    height: 52,
                    width: 53
                }
            ];
            var mcOptions5 = {maxZoom: 15, styles: clusterStyles5};
            mcConsorzi = new MarkerClusterer(map, [], mcOptions5);

            var clusterStyles6 = [
                {
                    textColor: '#90C3F4',
                    url: '";
        // line 275
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/cluster_marker.png"), "html", null, true);
        echo "',
                    height: 52,
                    width: 53
                }
            ];
            var mcOptions6 = {maxZoom: 15, styles: clusterStyles6};
            mcProfessioni = new MarkerClusterer(map, [], mcOptions6);

            var clusterStyles7 = [
                {
                    textColor: '#011E3B',
                    url: '";
        // line 286
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/cluster_marker.png"), "html", null, true);
        echo "',
                    height: 52,
                    width: 53
                }
            ];
            var mcOptions7 = {maxZoom: 15, styles: clusterStyles7};
            mcIat = new MarkerClusterer(map, [], mcOptions7);

            var clusterStyles8 = [
                {
                    textColor: '#525052',
                    url: '";
        // line 297
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/cluster_marker.png"), "html", null, true);
        echo "',
                    height: 52,
                    width: 53
                }
            ];
            var mcOptions8 = {maxZoom: 15, styles: clusterStyles8};
            mcSportFacility = new MarkerClusterer(map, [], mcOptions8);

            var clusterStyles9 = [
                {
                    textColor: '#FA6603',
                    url: '";
        // line 308
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/cluster_marker.png"), "html", null, true);
        echo "',
                    height: 52,
                    width: 53
                }
            ];
            var mcOptions9 = {maxZoom: 15, styles: clusterStyles9};
            mcAccomodation = new MarkerClusterer(map, [], mcOptions9);

        }
        function initLegend() {
            //legend
            map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(
                    document.getElementById('legend'));

            var div = document.createElement('div');
            div.innerHTML =
                    '<div class=\"legendrow\">' +
                    '<div style=\"background-color:#DA4F49; width: 10px; height: 10px; display: inline-block\"></div> ' +
                    '<span class=\"legendlabel\">Attrattori</span> ' +
                    '<input id=\"tourism_attractor_cb\" class=\"legendcheckbox\" type=\"checkbox\" ' +
                    'onchange=\"updateMarkers(\\'tourism_attractor\\')\" checked=\"checked\">' +
                    '</div>';
            legend.appendChild(div);

            var div = document.createElement('div');
            div.innerHTML =
                    '<div class=\"legendrow\">' +
                    '<div style=\"background-color:#FAA732; width: 10px; height: 10px; display: inline-block\"></div> ' +
                    '<span class=\"legendlabel\">Proposte</span>' +
                    '<input id=\"tourism_proposal_cb\" class=\"legendcheckbox\" type=\"checkbox\" ' +
                    'onchange=\"updateMarkers(\\'tourism_proposal\\')\" >' +
                    '</div>';
            legend.appendChild(div);

            var div = document.createElement('div');
            div.innerHTML =
                    '<div class=\"legendrow\">' +
                    '<div style=\"background-color:#5BB75B; width: 10px; height: 10px; display: inline-block\"></div> ' +
                    '<span class=\"legendlabel\">Eventi</span>' +
                    '<input id=\"tourism_event_cb\" class=\"legendcheckbox\" type=\"checkbox\" ' +
                    'onchange=\"updateMarkers(\\'tourism_event\\')\" checked=\"checked\">' +
                    '</div>';
            legend.appendChild(div);

            var div = document.createElement('div');
            div.innerHTML =
                    '<div class=\"legendrow\">' +
                    '<div style=\"background-color:#EFE926; width: 10px; height: 10px; display: inline-block\"></div> ' +
                    '<span class=\"legendlabel\">Agenzie di viaggio</span>' +
                    '<input id=\"tourism_travel_agency_cb\" class=\"legendcheckbox\" type=\"checkbox\" ' +
                    'onchange=\"updateMarkers(\\'tourism_travel_agency\\')\" >' +
                    '</div>';
            legend.appendChild(div);

            var div = document.createElement('div');
            div.innerHTML =
                    '<div class=\"legendrow\">' +
                    '<div style=\"background-color:#8824B2; width: 10px; height: 10px; display: inline-block\"></div> ' +
                    '<span class=\"legendlabel\">Consorzi</span>' +
                    '<input id=\"tourism_consortium_cb\" class=\"legendcheckbox\" type=\"checkbox\" ' +
                    'onchange=\"updateMarkers(\\'tourism_consortium\\')\" >' +
                    '</div>';
            legend.appendChild(div);

            var div = document.createElement('div');
            div.innerHTML =
                    '<div class=\"legendrow\">' +
                    '<div style=\"background-color:#3D94EA; width: 10px; height: 10px; display: inline-block\"></div> ' +
                    '<span class=\"legendlabel\">Professioni turistiche</span>' +
                    '<input id=\"tourism_profession_cb\" class=\"legendcheckbox\" type=\"checkbox\" ' +
                    'onchange=\"updateMarkers(\\'tourism_profession\\')\" >' +
                    '</div>';
            legend.appendChild(div);

            var div = document.createElement('div');
            div.innerHTML =
                    '<div class=\"legendrow\">' +
                    '<div style=\"background-color:#006DCC; width: 10px; height: 10px; display: inline-block\"></div> ' +
                    '<span class=\"legendlabel\">Iat </span>' +
                    '<input id=\"tourism_iat_cb\" class=\"legendcheckbox\" type=\"checkbox\"' +
                    'onchange=\"updateMarkers(\\'tourism_iat\\')\">' +
                    '</div>';
            legend.appendChild(div);

            var div = document.createElement('div');
            div.innerHTML =
                    '<div class=\"legendrow\">' +
                    '<div style=\"background-color:#525052; width: 10px; height: 10px; display: inline-block\"></div> ' +
                    '<span class=\"legendlabel\">Impianti Sportivi </span>' +
                    '<input id=\"tourism_sport_facility_cb\" class=\"legendcheckbox\" type=\"checkbox\"' +
                    'onchange=\"updateMarkers(\\'tourism_sport_facility\\')\">' +
                    '</div>';
            legend.appendChild(div);

            var div = document.createElement('div');
            div.innerHTML =
                    '<div class=\"legendrow\">' +
                    '<div style=\"background-color:#fa6603; width: 10px; height: 10px; display: inline-block\"></div> ' +
                    '<span class=\"legendlabel\">Strutture Ricettive </span>' +
                    '<input id=\"accomodation_cb\" class=\"legendcheckbox\" type=\"checkbox\"' +
                    'onchange=\"updateMarkers(\\'accomodation\\')\">' +
                    '</div>';
            legend.appendChild(div);
            setLayerFromCookies();
        }

        function setPositionFromCookies() {
            // riposizionamento mappa in relazione all'ultimo place visitato
            var latCenter = Cookies.get('lat');
            var lngCenter = Cookies.get('lng');
            var zoom = Cookies.get('zoom');
            if (latCenter != null && lngCenter != null) {
                Cookies.remove('lat');
                Cookies.remove('lng');
                Cookies.remove('zoom');
            }
            else {
                latCenter = umbriaCenter.lat;
                lngCenter = umbriaCenter.lng;
                zoom = 9;
            }

            map.setCenter({lat: parseFloat(latCenter), lng: parseFloat(lngCenter)});
            map.setZoom(parseInt(zoom));


        }

        function setLayerFromCookies() {
            // riposizionamento layer in relazione all'ultimo place visitato
            var layer = Cookies.get('layer');
            if (layer != null) {
                Cookies.remove('layer');
                document.getElementById(\"tourism_attractor_cb\").checked = false;
                document.getElementById(\"tourism_proposal_cb\").checked = false;
                document.getElementById(\"tourism_event_cb\").checked = false;
                document.getElementById(\"tourism_travel_agency_cb\").checked = false;
                document.getElementById(\"tourism_consortium_cb\").checked = false;
                document.getElementById(\"tourism_profession_cb\").checked = false;
                document.getElementById(\"tourism_iat_cb\").checked = false;
                document.getElementById(\"tourism_sport_facility_cb\").checked = false;
                document.getElementById(\"accomodation_cb\").checked = false;
                document.getElementById(layer).checked = true;
            }


        }

        function addMarkerCall(place, bounds, interval, mc, markerIcon) {
            setTimeout(function () {
                addMarker(place, bounds, mc, markerIcon)
            }, interval);
        }

        function addMarker(place, bounds, mc, markerIcon) {
            var name = place.name;
            var type = place.type;
            var latLng = place.latLng;
            var href = place.href;

            var marker = new google.maps.Marker({
                position: latLng,
                map: map,
                title: name,
                draggable: false,
                icon: markerIcon,
                animation: google.maps.Animation.DROP
            });

            // creazione array markers
            if (!markerGroups[type]) markerGroups[type] = [];
            markerGroups[type].push(marker);

            mc.addMarker(marker);

            var contentString = '<div id=\"content\">' +
                    '<h5>' +
                    name +
                    '</h5>' +
                    '<a  href=\"' +
                    href +
                    '\">show</a>' +
                    '</div>';

            // infowindow
            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });
            marker.addListener('click', function () {
                infowindow.open(map, marker);
            });


        }

        //_______________________________________________ GEOLOCATION
        function geolocation() {
            // Try HTML5 geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {

                    var pos = {lat: position.coords.latitude, lng: position.coords.longitude};

                    var marker_green = '";
        // line 511
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/marker_user.png"), "html", null, true);
        echo "';

                    var marker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        title: 'La tua posizione',
                        draggable: false,
                        icon: marker_green,
                        animation: google.maps.Animation.DROP
                    });

                }, function () {
                    handleNoGeolocation(true);
                });
            } else {
                // Browser doesn't support Geolocation
                handleNoGeolocation(false);
            }
        }

        function handleNoGeolocation(errorFlag) {
            var content;
            if (errorFlag) {
                content = 'Error: The Geolocation service failed.';
            } else {
                content = 'Error: Your browser doesn\\'t support geolocation.';
            }
        }


        function getSelectedPOI() {
            var selectedPOI = {
                tourism_attractor: 0,
                tourism_proposal: 0,
                tourism_event: 0,
                tourism_travel_agency: 0,
                tourism_consortium: 0,
                tourism_profession: 0,
                tourism_iat: 0,
                tourism_sport_facility: 0,
                accomodation: 0
            };

            if (document.getElementById(\"tourism_attractor_cb\").checked) {
                selectedPOI.tourism_attractor = 1;
            }
            if (document.getElementById(\"tourism_proposal_cb\").checked) {
                selectedPOI.tourism_proposal = 1;
            }
            if (document.getElementById(\"tourism_event_cb\").checked) {
                selectedPOI.tourism_event = 1;
            }
            if (document.getElementById(\"tourism_travel_agency_cb\").checked) {
                selectedPOI.tourism_travel_agency = 1;
            }
            if (document.getElementById(\"tourism_consortium_cb\").checked) {
                selectedPOI.tourism_consortium = 1;
            }
            if (document.getElementById(\"tourism_profession_cb\").checked) {
                selectedPOI.tourism_profession = 1;
            }
            if (document.getElementById(\"tourism_iat_cb\").checked) {
                selectedPOI.tourism_iat = 1;
            }
            if (document.getElementById(\"tourism_sport_facility_cb\").checked) {
                selectedPOI.tourism_sport_facility = 1;
            }
            if (document.getElementById(\"accomodation_cb\").checked) {
                selectedPOI.accomodation = 1;
            }
            return selectedPOI;
        }

        function getSelectedPOI2() {
            var selectedPOI = [];
            var cnt = 0;
            if (document.getElementById(\"tourism_attractor_cb\").checked) {
                selectedPOI[cnt] = \"tourism_attractor\";
                cnt++;
            }
            if (document.getElementById(\"tourism_proposal_cb\").checked) {
                selectedPOI[cnt] = \"tourism_proposal\";
                cnt++;
            }
            if (document.getElementById(\"tourism_event_cb\").checked) {
                selectedPOI[cnt] = \"tourism_event\";
                cnt++;
            }
            if (document.getElementById(\"tourism_travel_agency_cb\").checked) {
                selectedPOI[cnt] = \"tourism_travel_agency\";
                cnt++;
            }
            if (document.getElementById(\"tourism_consortium_cb\").checked) {
                selectedPOI[cnt] = \"tourism_consortium\";
                cnt++;
            }
            if (document.getElementById(\"tourism_profession_cb\").checked) {
                selectedPOI[cnt] = \"tourism_profession\";
                cnt++;
            }
            if (document.getElementById(\"tourism_iat_cb\").checked) {
                selectedPOI[cnt] = \"tourism_iat\";
                cnt++;
            }
            if (document.getElementById(\"tourism_sport_facility_cb\").checked) {
                selectedPOI[cnt] = \"tourism_sport_facility\";
                cnt++;
            }
            if (document.getElementById(\"accomodation_cb\").checked) {
                selectedPOI[cnt] = \"accomodation\";
                cnt++;
            }
            return selectedPOI;
        }

        function updateMarkers(type) {
            if (isCheckedType(type)) {
                //deleteMarkersByType(type);
                var places = getPlacesByType(type);
                var mc = getClustererByType(type);
                var icon = getMarkerIconByType(type);
                var bounds = new google.maps.LatLngBounds();
                for (var i = 0; i < places.length; i++) {
                    addMarkerCall(places[i], bounds, 0, mc, icon);
                }
            }
            else {
                deleteMarkersByType(type);
            }

        }


        function updateMarkersAll() {
            var selectedPOI = getSelectedPOI2();
            for (var type in selectedPOI) {
                updateMarkers(selectedPOI[type]);
            }
        }

        function getPlacesByType(type) {
            if (type == 'tourism_attractor') {
                return attrattori;
            } else if (type == 'tourism_proposal') {
                return proposte;
            } else if (type == 'tourism_event') {
                return eventi;
            } else if (type == 'tourism_travel_agency') {
                return agenzieViaggio;
            } else if (type == 'tourism_consortium') {
                return consorzi;
            } else if (type == 'tourism_profession') {
                return professioni;
            } else if (type == 'tourism_iat') {
                return iat;
            } else if (type == 'tourism_sport_facility') {
                return sportFacility;
            } else if (type == 'accomodation') {
                return accomodation;
            }
            return [];
        }

        function getClustererByType(type) {
            if (type == 'tourism_attractor') {
                return mcAttrattori;
            } else if (type == 'tourism_proposal') {
                return mcProposte;
            } else if (type == 'tourism_event') {
                return mcEventi;
            } else if (type == 'tourism_travel_agency') {
                return mcAgenzieViaggio;
            } else if (type == 'tourism_consortium') {
                return mcConsorzi;
            } else if (type == 'tourism_profession') {
                return mcProfessioni;
            } else if (type == 'tourism_iat') {
                return mcIat;
            } else if (type == 'tourism_sport_facility') {
                return mcSportFacility;
            } else if (type == 'accomodation') {
                return mcAccomodation;
            }
            return [];
        }

        function getMarkerIconByType(type) {
            var markerIcon;
            if (type == 'tourism_attractor') {
                markerIcon = '";
        // line 700
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/marker_monument_red.png"), "html", null, true);
        echo "';
            } else if (type == 'tourism_proposal') {
                markerIcon = '";
        // line 702
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/marker_star_orange.png"), "html", null, true);
        echo "';
            } else if (type == 'tourism_event') {
                markerIcon = '";
        // line 704
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/marker_theatre_green.png"), "html", null, true);
        echo "';
            } else if (type == 'tourism_travel_agency') {
                markerIcon = '";
        // line 706
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/marker_travel_yellow.png"), "html", null, true);
        echo "';
            } else if (type == 'tourism_consortium') {
                markerIcon = '";
        // line 708
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/marker_consortium_purple.png"), "html", null, true);
        echo "';
            } else if (type == 'tourism_profession') {
                markerIcon = '";
        // line 710
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/marker_profession_blue.png"), "html", null, true);
        echo "';
            } else if (type == 'tourism_iat') {
                markerIcon = '";
        // line 712
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/marker_info_blue.png"), "html", null, true);
        echo "';
            } else if (type == 'tourism_sport_facility') {
                markerIcon = '";
        // line 714
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/marker_football_grey.png"), "html", null, true);
        echo "';
            } else if (type == 'accomodation') {
                markerIcon = '";
        // line 716
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/map/marker_star_orange_dark.png"), "html", null, true);
        echo "';
            }
            return markerIcon;
        }

        function getMarkersGroupByType(type) {
            if (type == 'tourism_attractor') {
                return markerGroups.tourism_attractor;
            } else if (type == 'tourism_proposal') {
                return markerGroups.tourism_proposal;
            } else if (type == 'tourism_event') {
                return markerGroups.tourism_event;
            } else if (type == 'tourism_travel_agency') {
                return markerGroups.tourism_travel_agency;
            } else if (type == 'tourism_consortium') {
                return markerGroups.tourism_consortium;
            } else if (type == 'tourism_profession') {
                return markerGroups.tourism_profession;
            } else if (type == 'tourism_iat') {
                return markerGroups.tourism_iat;
            } else if (type == 'tourism_sport_facility') {
                return markerGroups.tourism_sport_facility;
            } else if (type == 'accomodation') {
                return markerGroups.accomodation;
            }
            return [];
        }

        function deleteMarkersByType(type) {
            var places = getMarkersGroupByType(type);
            for (var i = 0; i < places.length; i++) {
                places[i].setMap(null);
            }
            getClustererByType(type).removeMarkers(getMarkersGroupByType(type));
        }

        function isCheckedType(type) {
            if (type == 'tourism_attractor') {
                if (document.getElementById(\"tourism_attractor_cb\").checked)
                    return true;
            } else if (type == 'tourism_proposal') {
                if (document.getElementById(\"tourism_proposal_cb\").checked)
                    return true;
            } else if (type == 'tourism_event') {
                if (document.getElementById(\"tourism_event_cb\").checked)
                    return true;
            } else if (type == 'tourism_travel_agency') {
                if (document.getElementById(\"tourism_travel_agency_cb\").checked)
                    return true;
            } else if (type == 'tourism_consortium') {
                if (document.getElementById(\"tourism_consortium_cb\").checked)
                    return true;
            } else if (type == 'tourism_profession') {
                if (document.getElementById(\"tourism_profession_cb\").checked)
                    return true;
            } else if (type == 'tourism_iat') {
                if (document.getElementById(\"tourism_iat_cb\").checked)
                    return true;
            } else if (type == 'tourism_sport_facility') {
                if (document.getElementById(\"tourism_sport_facility_cb\").checked)
                    return true;
            }
            else if (type == 'accomodation') {
                if (document.getElementById(\"accomodation_cb\").checked)
                    return true;
            }
            return false;
        }

        /*function deleteMarkersAll() {
         clearMarkersAll();
         for(var i = 0; i < markerGroups.length; i++) {
         var markerGroup = markerGroups[i];
         for(var j = 0; j < markerGroup.length; j++) {
         markerGroup[j]=[];
         }
         }
         }
         function clearMarkersAll() {
         setMapOnAll(null);
         }
         function setMapOnAll(map) {
         for(var i = 0; i < markerGroups.length; i++) {
         var markerGroup = markerGroups[i];
         for(var j = 0; j < markerGroup.length; j++) {
         markerGroup[j].setMap(map);
                }
            }
         }*/


    </script>
    <script async defer src=\"https://maps.googleapis.com/maps/api/js?callback=initMap\"></script>
";
    }

    public function getTemplateName()
    {
        return "UmbriaProLocoBundle:Default:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1103 => 716,  1098 => 714,  1093 => 712,  1088 => 710,  1083 => 708,  1078 => 706,  1073 => 704,  1068 => 702,  1063 => 700,  871 => 511,  665 => 308,  651 => 297,  637 => 286,  623 => 275,  609 => 264,  595 => 253,  581 => 242,  567 => 231,  553 => 220,  477 => 146,  468 => 143,  462 => 142,  458 => 141,  454 => 140,  450 => 139,  446 => 138,  442 => 137,  439 => 136,  435 => 135,  431 => 133,  422 => 130,  416 => 129,  412 => 128,  408 => 127,  404 => 126,  400 => 125,  396 => 124,  393 => 123,  389 => 122,  385 => 120,  376 => 117,  370 => 116,  366 => 115,  362 => 114,  358 => 113,  354 => 112,  350 => 111,  347 => 110,  343 => 109,  339 => 107,  330 => 104,  324 => 103,  320 => 102,  316 => 101,  312 => 100,  308 => 99,  304 => 98,  301 => 97,  297 => 96,  293 => 94,  284 => 91,  278 => 90,  274 => 89,  270 => 88,  266 => 87,  262 => 86,  258 => 85,  255 => 84,  251 => 83,  247 => 81,  238 => 78,  232 => 77,  228 => 76,  224 => 75,  220 => 74,  216 => 73,  212 => 72,  209 => 71,  205 => 70,  201 => 68,  192 => 65,  186 => 64,  182 => 63,  178 => 62,  174 => 61,  170 => 60,  166 => 59,  163 => 58,  159 => 57,  155 => 55,  146 => 52,  140 => 51,  136 => 50,  132 => 49,  128 => 48,  124 => 47,  120 => 46,  117 => 45,  113 => 44,  109 => 42,  100 => 39,  94 => 38,  90 => 37,  86 => 36,  82 => 35,  78 => 34,  74 => 33,  71 => 32,  67 => 31,  42 => 10,  39 => 9,  32 => 4,  29 => 3,  11 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "UmbriaProLocoBundle:Default:index.html.twig", "C:\\xampp\\htdocs\\opendata-umbriaopenapi_Nigel\\src\\Umbria\\ProLocoBundle/Resources/views/Default/index.html.twig");
    }
}
