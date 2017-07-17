<?php

/* UmbriaProLocoBundle::base.html.twig */
class __TwigTemplate_0c058c27a8bdc6df1931e0a2bbcf931f629954c1bb9283f744ff6db89130a595 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"utf-8\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <title>Umbria - Open API</title>

    ";
        // line 9
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 13
        echo "
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src=\"https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js\"></script>
    <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>
    <![endif]-->
</head>
<body>
";
        // line 22
        $this->displayBlock('body', $context, $blocks);
        // line 23
        echo "
";
        // line 24
        $this->displayBlock('javascripts', $context, $blocks);
        // line 50
        echo "
</body>
</html>";
    }

    // line 9
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 10
        echo "        <link href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("css/bootstrap.min.css"), "html", null, true);
        echo "\" rel=\"stylesheet\">
        <link href=\"";
        // line 11
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("css/dev.css"), "html", null, true);
        echo "\" rel=\"stylesheet\">
    ";
    }

    // line 22
    public function block_body($context, array $blocks = array())
    {
    }

    // line 24
    public function block_javascripts($context, array $blocks = array())
    {
        // line 25
        echo "    <script src=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("js/jquery-2.2.4.js"), "html", null, true);
        echo "\"></script>
    <script src=\"";
        // line 26
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("js/markerclusterer_compiled.js"), "html", null, true);
        echo "\"></script>
    <script src=\"";
        // line 27
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("js/bootstrap.min.js"), "html", null, true);
        echo "\"></script>
    <script src=\"";
        // line 28
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("js/js.cookie.js"), "html", null, true);
        echo "\"></script>

    <script>
        function toggleGroup(type, element) {
            for (var i = 0; i < markerGroups[type].length; i++) {
                var marker = markerGroups[type][i];
                if (element.checked) {
                    marker.setVisible(true);
                } else {
                    marker.setVisible(false);
                }
            }
        }
        function setCoordCookie(lat, lng, layer) {
            Cookies.set('lat', lat);
            Cookies.set('lng', lng);
            Cookies.set('zoom', 18);
            Cookies.set('layer', layer);
        }

    </script>
";
    }

    public function getTemplateName()
    {
        return "UmbriaProLocoBundle::base.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  93 => 28,  89 => 27,  85 => 26,  80 => 25,  77 => 24,  72 => 22,  66 => 11,  61 => 10,  58 => 9,  52 => 50,  50 => 24,  47 => 23,  45 => 22,  34 => 13,  32 => 9,  22 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "UmbriaProLocoBundle::base.html.twig", "C:\\xampp\\htdocs\\opendata-umbriaopenapi_Nigel\\src\\Umbria\\ProLocoBundle/Resources/views/base.html.twig");
    }
}
