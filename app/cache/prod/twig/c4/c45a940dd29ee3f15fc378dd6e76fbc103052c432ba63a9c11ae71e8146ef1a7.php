<?php

/* UmbriaProLocoBundle:SUAPE:index.html.twig */
class __TwigTemplate_b763fd733ae0369510f22369a69c837af3c5d67617bb6f41ba58d7cd027afe70 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("UmbriaProLocoBundle::base.html.twig", "UmbriaProLocoBundle:SUAPE:index.html.twig", 1);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "UmbriaProLocoBundle::base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        // line 4
        echo "    <nav class=\"navbar navbar-default navbar-fixed-top navbar-inverse\">
        <div class=\"container-fluid\">
            <div class=\"navbar-header\">
                <button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\"
                        data-target=\"#bs-example-navbar-collapse-1\" aria-expanded=\"false\">
                    <span class=\"sr-only\">Toggle navigation</span>
                    <span class=\"icon-bar\"></span>
                    <span class=\"icon-bar\"></span>
                    <span class=\"icon-bar\"></span>
                </button>
                <a class=\"navbar-brand\" href=\"";
        // line 14
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("umbria_open_api_homepage");
        echo "\">
                    <img alt=\"Brand\" src=\"";
        // line 15
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/logo_umbria.png"), "html", null, true);
        echo "\" height=\"70\">
                </a>
            </div>

            <div class=\"collapse navbar-collapse\" id=\"bs-example-navbar-collapse-1\">
                <ul class=\"nav navbar-nav\">
                    <li>
                        <a href=\"";
        // line 22
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("umbria_pro_loco_suape");
        echo "\">SUAPE</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class=\"container\">
        <div class=\"row\">
            <div class=\"page-header\">
                <h2>Statistiche SUAPE</h2>
            </div>
        </div>
        <div class=\"row\">
            <div class=\"col-md-4 col-md-offset-4\">
                <div class=\"btn-group\" role=\"group\" >
                    <button id=\"evaseDatasetSelector\" type=\"button\" class=\"btn btn-primary active\" aria-pressed=\"true\"
                    onclick=\"datasetSelectorChange(this)\">Pratiche evase</button>
                    <button id=\"tipologieDatasetSelector\" type=\"button\" class=\"btn btn-default\" onclick=\"datasetSelectorChange(this)\">Tipologie</button>
                    <button id=\"categorieDatasetSelector\" type=\"button\" class=\"btn btn-default\" onclick=\"datasetSelectorChange(this)\">Categorie</button>
                </div>
            </div>
        </div>
        <br>
        <div class=\"row\">
            <div id=\"chartDescription\" class=\"col-md-6 col-md-offset-3\">
                <div>Grafici che mostrano il rapporto tra le pratiche evase e le pratiche totali</div>
        </div>
        <br><br>
        <div class=\"row\" >
            <div class=\"row col-md-4 col-md-offset-4\" >
                <label for=\"comuneFilter\" class=\"col-md-3\">Comune</label>
                <select id=\"comuneFilter\" class=\"col-md-9\" onchange=\"setAnnoSelectOptions(); setMeseSelectOptions();drawCharts()\">
                </select>
            </div>
        </div>
        <br><br>
        <div class=\"row\">
            <div id=\"chart_div_1\"></div>
        </div>

        <div id=\"chart2_time_filter\" class=\"row\" style=\"visibility: hidden\">
            <br><br>
            <div class=\"row col-md-4 col-md-offset-4\" >
                <label for=\"annoFilter\" class=\"col-md-3\">Anno</label>
                <select id=\"annoFilter\" class=\"col-md-9\" onchange=\"drawCharts()\" >
                </select>
            </div>
            <div class=\"row col-md-4 col-md-offset-4\" >
                <label for=\"meseFilter\" class=\"col-md-3\">Mese</label>
                <select id=\"meseFilter\" class=\"col-md-9\" onchange=\"drawCharts()\" >
                </select>
            </div>
        </div>
        <br><br>
        <div class=\"row\">
            <div id=\"chart_div_2\" class=\"col-md-10 col-md-offset-1\"></div>
        </div>
    </div>



";
    }

    // line 85
    public function block_javascripts($context, array $blocks = array())
    {
        // line 86
        echo "     ";
        $this->displayParentBlock("javascripts", $context, $blocks);
        echo "
     <script src=\"";
        // line 87
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("js/statistiche_suape.js"), "html", null, true);
        echo "\"></script>
     <script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>
     <script type=\"text/javascript\">
        mainDrawCharts();
     </script>
 ";
    }

    public function getTemplateName()
    {
        return "UmbriaProLocoBundle:SUAPE:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  132 => 87,  127 => 86,  124 => 85,  58 => 22,  48 => 15,  44 => 14,  32 => 4,  29 => 3,  11 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "UmbriaProLocoBundle:SUAPE:index.html.twig", "/var/www/html/opendata-umbriaopenapi-nigel/src/Umbria/ProLocoBundle/Resources/views/SUAPE/index.html.twig");
    }
}
