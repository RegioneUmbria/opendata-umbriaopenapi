<?php

/* UmbriaOpenApiBundle:Default:index.html.twig */
class __TwigTemplate_fd10b066ea2c6e5c4b89a815a9514216477272bcab6af0e41f309152a9fab031 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("UmbriaOpenApiBundle::base.html.twig", "UmbriaOpenApiBundle:Default:index.html.twig", 1);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "UmbriaOpenApiBundle::base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        // line 4
        echo "
    <nav class=\"navbar navbar-default navbar-fixed-top navbar-inverse\">
        <div class=\"container\">
            <div class=\"navbar-header\">
                <button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\"#navbar\"
                        aria-expanded=\"false\" aria-controls=\"navbar\">
                    <span class=\"sr-only\">Toggle navigation</span>
                    <span class=\"icon-bar\"></span>
                    <span class=\"icon-bar\"></span>
                    <span class=\"icon-bar\"></span>
                </button>
                <a class=\"navbar-brand\" href=\"";
        // line 15
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("umbria_open_api_homepage");
        echo "\">Umbria - Open API</a>
            </div>
            <div id=\"navbar\" class=\"navbar-collapse collapse\">
                <ul class=\"nav navbar-nav\">
                    <li><a href=\"";
        // line 19
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("umbria_pro_loco_homepage");
        echo "\">Turismo</a></li>
                    <li><a href=\"";
        // line 20
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("umbria_pro_loco_statistiche");
        echo "\">Statistiche SUAPE</a></li>
                    <li><a href=\"";
        // line 21
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("nelmio_api_doc_index");
        echo "\">API docs</a></li>
                    <li><a href=\"";
        // line 22
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("umbria_open_api_sparql_manual");
        echo "\">SPARQL Manual</a></li>
                </ul>

                <ul class=\"nav narbar-nav navbar-right\">
                    <li class=\"dropdown\">
                        <a  class=\"dropdown-toggle\"  style=\"color:gray\" data-toggle=\"dropdown\">Language <b class=\"caret\"></b></a>
                        <ul class=\"dropdown-menu\">
                            <li>
                                <a href=\"";
        // line 30
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("umbria_open_api_ENhomepage");
        echo "\">English</a>
                            </li>
                            <li>
                                <a href=\"";
        // line 33
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("umbria_open_api_homepage");
        echo "\">Italian</a>
                            </li>
                        </ul>
                    </li>
                </ul>

            </div>
        </div>
    </nav>


    <!-- Carousel
    ================================================== -->

    <div id=\"myCarousel\" class=\"carousel slide\" data-ride=\"carousel\">
        <!-- Indicators -->
        <ol class=\"carousel-indicators\">
            <li data-target=\"#myCarousel\" data-slide-to=\"0\" class=\"active\"></li>
            <li data-target=\"#myCarousel\" data-slide-to=\"1\"></li>
            <li data-target=\"#myCarousel\" data-slide-to=\"2\"></li>
        </ol>
        <div class=\"carousel-inner\" role=\"listbox\">
            <div class=\"item active\">
                <img class=\"first-slide\"
                     src=\"";
        // line 57
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/castelluccio.jpg"), "html", null, true);
        echo "\"
                     alt=\"Turismo\">
                <div class=\"container\">
                    <div class=\"carousel-caption\">
                        <h1>Turismo</h1>
                        <p> Tutti i punti di interesse del portale <b>UmbriaTourism</b> in una sola mappa</p>
                        <p><a class=\"btn btn-lg btn-primary\" href=\"";
        // line 63
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("umbria_pro_loco_homepage");
        echo "\"
                              role=\"button\">Vai</a></p>
                    </div>
                </div>
            </div>
            <div class=\"item\">
                <img class=\"second-slide\"
                     src=\"";
        // line 70
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/innovation-lightbulb-image.jpg"), "html", null, true);
        echo "\"
                     alt=\"OpenApi\">
                <div class=\"container\">
                    <div class=\"carousel-caption\">s
                        <h1>Sei un developer?</h1>
                        <p>I dati di UmbriaTourism disponibili in formato JSON</p>
                        <p>Grazie alle nostre Open RESTful APIs
                            puoi ottenere, in formato JSON, tutti i dati
                            dei punti di interesse della Regione Umbria</p>
                        <p><a class=\"btn btn-lg btn-primary\" href=\"";
        // line 79
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("nelmio_api_doc_index");
        echo "\"
                              role=\"button\">Vai</a></p>
                    </div>
                </div>
            </div>
            <div class=\"item\">
                <img class=\"third-slide\"
                     src=\"";
        // line 86
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/telegram2.jpg"), "html", null, true);
        echo "\"
                     alt=\"Telegram\">
                <div class=\"container\">
                    <div class=\"carousel-caption\">
                        <h1 style=\"color:#5A5A5A\">UmbriaTurismo Bot</h1>
                        <p style=\"color:#5A5A5A\">Se utilizzi Telegram prova il nostro nuovo Bot.Invia la tua posizione
                            per ottenere interessanti informazioni.</p>
                        <p><a class=\"btn btn-lg btn-primary\" href=\"https://telegram.me/UmbriaTurismo_bot\" role=\"button\"
                              target=\"_blank\">Vai</a></p>
                    </div>
                </div>
            </div>
        </div>
        <a class=\"left carousel-control\" href=\"#myCarousel\" role=\"button\" data-slide=\"prev\">
            <span class=\"glyphicon glyphicon-chevron-left\" aria-hidden=\"true\"></span>
            <span class=\"sr-only\">Previous</span>
        </a>s
        <a class=\"right carousel-control\" href=\"#myCarousel\" role=\"button\" data-slide=\"next\">
            <span class=\"glyphicon glyphicon-chevron-right\" aria-hidden=\"true\"></span>
            <span class=\"sr-only\">Next</span>
        </a>
    </div><!-- /.carousel -->


    <!-- Marketing messaging and featurettes
    ================================================== -->
    <!-- Wrap the rest of the page in another container to center all the content. -->

    ";
        // line 149
        echo "

    <!-- START THE FEATURETTES -->
    <hr class=\"featurette-divider\">
    <div class=\"row featurette\">
        <div class=\"col-md-1 \"></div>
        <div class=\"col-md-4 \">
            <a class=\"homepage_link \" href=\"http://dati.umbria.it/dataset\">
                <img class=\"featurette-image img-responsive center-block\"
                     src=\"";
        // line 158
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/open-data-umbria-logo.jpg"), "html", null, true);
        echo "\"
                     alt=\"logo open data umbria\"
                     style=\"width: 240px\"
                />
            </a>
        </div>
        <div class=\"col-md-6 \">
            <h2 class=\"featurette-heading\"
                style=\"margin-top: 10px\">
                <a class=\"homepage_link\" href=\"http://dati.umbria.it/dataset\">Linked Open Data Umbria</a>
            </h2>
            <p class=\"lead\">
            <p>
                <a class=\"homepage_link\" href=\"http://dati.umbria.it/dataset\">Visita il <b>Portale Open Data
                        dell'Umbria</b></a>
            </p>
            <p>
                <a class=\"homepage_link\" href=\"http://dati.umbria.it/dataset\">
                    I dati della Pubblica Amministrazione Umbra a disposizione dei cittadini
                </a>
            </p>
            </p>
        </div>
        <div class=\"col-md-1 \"></div>

    </div>
    <hr class=\"featurette-divider\"/>
    <div class=\"row featurette\">
        <div class=\"col-md-1 \"></div>
        <div class=\"col-md-6 \">
            <h2 class=\"featurette-heading\" style=\"margin-top:0\">
                <a class=\"homepage_link\" href=\"http://dati.umbria.it/risorsa/attrattori/4435021\">
                    LodView
                </a>
            </h2>
            <p class=\"lead\">
                <a class=\"homepage_link\" href=\"http://dati.umbria.it/risorsa/attrattori/4435021\">
                    Visualizza i Linked Open Data della regione Umbria nel browser LodView
                </a>
            </p>
        </div>
        <div class=\"col-md-4\">
            <a class=\"homepage_link\" href=\"http://dati.umbria.it/risorsa/attrattori/4435021\">
                <img src=\"";
        // line 201
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/logo-header-lodview.png"), "html", null, true);
        echo "\"
                     class=\"featurette-image img-responsive center-block\"
                     alt=\"Generic placeholder image\"
                     style=\"width: 240px\"
                />
            </a>
        </div>
        <div class=\"col-md-1 \"></div>

    </div>
    <hr class=\"featurette-divider\">



    <!-- /END THE FEATURETTES -->


    <!-- FOOTER -->
    <footer style=\" border-top: 20px solid #eee \">
        <p class=\"pull-right\"><a href=\"#\">Back to top</a></p>
        <div class=\"col-md-1\"></div>
        <div class=\"col-md-3\">


            <address style=\"margin: 30px\">
                <strong><a href=\"http://www.regione.umbria.it\" target=\"_blank\">Regione Umbria</a></strong><br>
                Corso Vannucci, 96<br>
                06121 Perugia<br>
                <a href=\"mailto:opendata@regione.umbria.it\">opendata@regione.umbria.it</a><br>
                <a href=\"mailto:regione.giunta@postacert.umbria.it\">regione.giunta@postacert.umbria.it</a><br>
                P.IVA 01212820540<br>
            </address>


        </div>
        <div class=\"col-md-6\">
            <div class=\"col-md-4\">
                <a href=\"http://www.dps.mef.gov.it/\"><img src=\" http://dati.umbria.it/images/Logo_DPS.jpg\"
                                                          title=\"Dipartimento per lo Sviluppo e la Coesione Economica\"
                                                          alt=\"DPS\"></a>
            </div>
            <div class=\"col-md-4\">
                <a href=\"http://www.dps.gov.it/it/politiche_e_attivita/Fondo_per_lo_Sviluppo_e_la_Coesione\"><img
                            src=\"http://dati.umbria.it/images/FSC.jpg\" title=\"Fondo per lo Sviluppo e la Coesione\"
                            alt=\"FSC\" style=\"
    width: 100%;
\"></a>
            </div>
            <div class=\"col-md-4\">
                <a href=\"http://www.regione.umbria.it/che-cos-e-il-fondo-di-sviluppo-e-coesione-fsc\"><img
                            src=\"http://dati.umbria.it/images/FSC_Umbria.jpg\"
                            title=\"Fondo per lo Sviluppo e la Coesione Umbria\" alt=\"FSC Umbria\"></a>
            </div>
        </div>
    </footer>


";
    }

    public function getTemplateName()
    {
        return "UmbriaOpenApiBundle:Default:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  236 => 201,  190 => 158,  179 => 149,  148 => 86,  138 => 79,  126 => 70,  116 => 63,  107 => 57,  80 => 33,  74 => 30,  63 => 22,  59 => 21,  55 => 20,  51 => 19,  44 => 15,  31 => 4,  28 => 3,  11 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "UmbriaOpenApiBundle:Default:index.html.twig", "/var/www/html/opendata-umbriaopenapi-nigel/src/Umbria/OpenApiBundle/Resources/views/Default/index.html.twig");
    }
}
