<?php

/* UmbriaProLocoBundle::frontend.html.twig */
class __TwigTemplate_c378e599168d6fe638132827cedc7b3641c30cff7425956c5ee8c0b2a0a1bde5 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("UmbriaProLocoBundle::base.html.twig", "UmbriaProLocoBundle::frontend.html.twig", 1);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
            'content' => array($this, 'block_content'),
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
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("umbria_pro_loco_homepage");
        echo "\">Mappa</a>
                    </li>
                    <li>
                        <a href=\"";
        // line 25
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("attractor_index");
        echo "\">Attrattori</a>
                    </li>
                    <li>
                        <a href=\"";
        // line 28
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("proposal_index");
        echo "\">Proposte</a>
                    </li>
                    <li>
                        <a href=\"";
        // line 31
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("event_index");
        echo "\">Eventi</a>
                    </li>
                    <li>
                        <a href=\"";
        // line 34
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("accomodation_index");
        echo "\">Strutture Ricettive</a>
                    </li>
                    <li>
                        <a href=\"";
        // line 37
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("travelagency_index");
        echo "\">Agenzie di Viaggio</a>
                    </li>
                    <li>
                        <a href=\"";
        // line 40
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("consortium_index");
        echo "\">Consorzi</a>
                    </li>
                    <li>
                        <a href=\"";
        // line 43
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("profession_index");
        echo "\">Professioni Turistiche</a>
                    </li>
                    <li>
                        <a href=\"";
        // line 46
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("iat_index");
        echo "\">Iat</a>
                    </li>
                    <li>
                        <a href=\"";
        // line 49
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("sport_facility_index");
        echo "\">Impianti sportivi</a>
                    </li>
                </ul>
                <ul class=\"nav navbar-nav navbar-right\">
                    <li><a href=\"";
        // line 53
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("nelmio_api_doc_index");
        echo "\" target=\"_blank\">Open Api DOCS</a></li>
                </ul>
            </div>
        </div>
    </nav>

    ";
        // line 59
        $this->displayBlock('content', $context, $blocks);
        // line 61
        echo "
";
    }

    // line 59
    public function block_content($context, array $blocks = array())
    {
        // line 60
        echo "    ";
    }

    public function getTemplateName()
    {
        return "UmbriaProLocoBundle::frontend.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  138 => 60,  135 => 59,  130 => 61,  128 => 59,  119 => 53,  112 => 49,  106 => 46,  100 => 43,  94 => 40,  88 => 37,  82 => 34,  76 => 31,  70 => 28,  64 => 25,  58 => 22,  48 => 15,  44 => 14,  32 => 4,  29 => 3,  11 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "UmbriaProLocoBundle::frontend.html.twig", "C:\\xampp\\htdocs\\opendata-umbriaopenapi_Nigel\\src\\Umbria\\ProLocoBundle/Resources/views/frontend.html.twig");
    }
}
