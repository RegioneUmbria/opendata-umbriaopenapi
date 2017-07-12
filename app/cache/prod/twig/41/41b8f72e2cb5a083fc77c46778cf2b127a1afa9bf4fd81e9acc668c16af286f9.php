<?php

/* NelmioApiDocBundle::method.html.twig */
class __TwigTemplate_2aa2cc3dbf941eb9bc889db64be970646ebae03547c6894a15c974ceb3296dfe extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<li class=\"";
        echo twig_escape_filter($this->env, twig_lower_filter($this->env, $this->getAttribute(($context["data"] ?? null), "method", array())), "html", null, true);
        echo " operation\" id=\"";
        echo twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "id", array()), "html", null, true);
        echo "\">
    <div class=\"heading toggler";
        // line 2
        if ($this->getAttribute(($context["data"] ?? null), "deprecated", array())) {
            echo " deprecated";
        }
        echo "\" data-href=\"#";
        echo twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "id", array()), "html", null, true);
        echo "\">
        <h3>
            <span class=\"http_method\">
                <i>";
        // line 5
        echo twig_escape_filter($this->env, twig_upper_filter($this->env, $this->getAttribute(($context["data"] ?? null), "method", array())), "html", null, true);
        echo "</i>
            </span>

            ";
        // line 8
        if ($this->getAttribute(($context["data"] ?? null), "deprecated", array())) {
            // line 9
            echo "                <span class=\"deprecated\">
                <i>DEPRECATED</i>
            </span>
            ";
        }
        // line 13
        echo "
            ";
        // line 14
        if ($this->getAttribute(($context["data"] ?? null), "https", array())) {
            // line 15
            echo "                <span class=\"icon lock\" title=\"HTTPS\"></span>
            ";
        }
        // line 17
        echo "            ";
        if ($this->getAttribute(($context["data"] ?? null), "authentication", array())) {
            // line 18
            echo "                <span class=\"icon keys\"
                      title=\"Needs ";
            // line 19
            echo twig_escape_filter($this->env, (((twig_length_filter($this->env, $this->getAttribute(($context["data"] ?? null), "authenticationRoles", array())) > 0)) ? (twig_join_filter($this->getAttribute(($context["data"] ?? null), "authenticationRoles", array()), ", ")) : ("authentication")), "html", null, true);
            echo "\"></span>
            ";
        }
        // line 21
        echo "
            <span class=\"path\">
                ";
        // line 23
        if ($this->getAttribute(($context["data"] ?? null), "host", array(), "any", true, true)) {
            // line 24
            echo (($this->getAttribute(($context["data"] ?? null), "https", array())) ? ("https://") : ("http://"));
            // line 25
            echo twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "host", array()), "html", null, true);
        }
        // line 27
        echo twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "uri", array()), "html", null, true);
        echo "
            </span>
            ";
        // line 29
        if ($this->getAttribute(($context["data"] ?? null), "tags", array(), "any", true, true)) {
            // line 30
            echo "                ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["data"] ?? null), "tags", array()));
            foreach ($context['_seq'] as $context["tag"] => $context["color_code"]) {
                // line 31
                echo "                    <span class=\"tag\"
                          ";
                // line 32
                if ((array_key_exists("color_code", $context) &&  !twig_test_empty($context["color_code"]))) {
                    echo "style=\"background-color:";
                    echo twig_escape_filter($this->env, $context["color_code"], "html", null, true);
                    echo ";\"";
                }
                echo ">";
                echo twig_escape_filter($this->env, $context["tag"], "html", null, true);
                echo "</span>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['tag'], $context['color_code'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 34
            echo "            ";
        }
        // line 35
        echo "        </h3>
        <ul class=\"options\">
            ";
        // line 37
        if ($this->getAttribute(($context["data"] ?? null), "description", array(), "any", true, true)) {
            // line 38
            echo "                <li>";
            echo twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "description", array()), "html", null, true);
            echo "</li>
            ";
        }
        // line 40
        echo "        </ul>
    </div>

    <div class=\"content\"
         style=\"display: ";
        // line 44
        if ((array_key_exists("displayContent", $context) && (($context["displayContent"] ?? null) == true))) {
            echo "display";
        } else {
            echo "none";
        }
        echo ";\">
        <ul class=\"tabs\">
            ";
        // line 46
        if (($context["enableSandbox"] ?? null)) {
            // line 47
            echo "                <li class=\"selected\" data-pane=\"content\">Documentation</li>
                <li data-pane=\"sandbox\">Sandbox</li>
            ";
        }
        // line 50
        echo "        </ul>

        <div class=\"panes\">
            <div class=\"pane content selected\">
                ";
        // line 54
        if (($this->getAttribute(($context["data"] ?? null), "documentation", array(), "any", true, true) &&  !twig_test_empty($this->getAttribute(($context["data"] ?? null), "documentation", array())))) {
            // line 55
            echo "                    <h4>Documentation</h4>
                    <div>";
            // line 56
            echo $this->env->getExtension('Nelmio\ApiDocBundle\Twig\Extension\MarkdownExtension')->markdown($this->getAttribute(($context["data"] ?? null), "documentation", array()));
            echo "</div>
                ";
        }
        // line 58
        echo "
                ";
        // line 59
        if (($this->getAttribute(($context["data"] ?? null), "link", array(), "any", true, true) &&  !twig_test_empty($this->getAttribute(($context["data"] ?? null), "link", array())))) {
            // line 60
            echo "                    <h4>Link</h4>
                    <div><a href=\"";
            // line 61
            echo twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "link", array()), "html", null, true);
            echo "\" target=\"_blank\">";
            echo twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "link", array()), "html", null, true);
            echo "</a></div>
                ";
        }
        // line 63
        echo "
                ";
        // line 64
        if (($this->getAttribute(($context["data"] ?? null), "requirements", array(), "any", true, true) &&  !twig_test_empty($this->getAttribute(($context["data"] ?? null), "requirements", array())))) {
            // line 65
            echo "                    <h4>Requirements</h4>
                    <table class=\"fullwidth\">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Requirement</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        ";
            // line 76
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["data"] ?? null), "requirements", array()));
            foreach ($context['_seq'] as $context["name"] => $context["infos"]) {
                // line 77
                echo "                            <tr>
                                <td>";
                // line 78
                echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                echo "</td>
                                <td>";
                // line 79
                echo twig_escape_filter($this->env, (($this->getAttribute($context["infos"], "requirement", array(), "any", true, true)) ? ($this->getAttribute($context["infos"], "requirement", array())) : ("")), "html", null, true);
                echo "</td>
                                <td>";
                // line 80
                echo twig_escape_filter($this->env, (($this->getAttribute($context["infos"], "dataType", array(), "any", true, true)) ? ($this->getAttribute($context["infos"], "dataType", array())) : ("")), "html", null, true);
                echo "</td>
                                <td>";
                // line 81
                echo twig_escape_filter($this->env, (($this->getAttribute($context["infos"], "description", array(), "any", true, true)) ? ($this->getAttribute($context["infos"], "description", array())) : ("")), "html", null, true);
                echo "</td>
                            </tr>
                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['name'], $context['infos'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 84
            echo "                        </tbody>
                    </table>
                ";
        }
        // line 87
        echo "
                ";
        // line 88
        if (($this->getAttribute(($context["data"] ?? null), "filters", array(), "any", true, true) &&  !twig_test_empty($this->getAttribute(($context["data"] ?? null), "filters", array())))) {
            // line 89
            echo "                    <h4>Filters</h4>
                    <table class=\"fullwidth\">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Information</th>
                        </tr>
                        </thead>
                        <tbody>
                        ";
            // line 98
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["data"] ?? null), "filters", array()));
            foreach ($context['_seq'] as $context["name"] => $context["infos"]) {
                // line 99
                echo "                            <tr>
                                <td>";
                // line 100
                echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                echo "</td>
                                <td>
                                    <table>
                                        ";
                // line 103
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($context["infos"]);
                foreach ($context['_seq'] as $context["key"] => $context["value"]) {
                    // line 104
                    echo "                                            <tr>
                                                <td>";
                    // line 105
                    echo twig_escape_filter($this->env, twig_title_string_filter($this->env, $context["key"]), "html", null, true);
                    echo "</td>
                                                <td>";
                    // line 106
                    echo twig_escape_filter($this->env, twig_trim_filter(twig_replace_filter(twig_jsonencode_filter($context["value"]), array("\\\\" => "\\")), "\""), "html", null, true);
                    echo "</td>
                                            </tr>
                                        ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 109
                echo "                                    </table>
                                </td>
                            </tr>
                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['name'], $context['infos'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 113
            echo "                        </tbody>
                    </table>
                ";
        }
        // line 116
        echo "
                ";
        // line 117
        if (($this->getAttribute(($context["data"] ?? null), "parameters", array(), "any", true, true) &&  !twig_test_empty($this->getAttribute(($context["data"] ?? null), "parameters", array())))) {
            // line 118
            echo "                    <h4>Parameters</h4>
                    <table class='fullwidth'>
                        <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Type</th>
                            <th>Required?</th>
                            <th>Format</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        ";
            // line 130
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["data"] ?? null), "parameters", array()));
            foreach ($context['_seq'] as $context["name"] => $context["infos"]) {
                // line 131
                echo "                            ";
                if ( !$this->getAttribute($context["infos"], "readonly", array())) {
                    // line 132
                    echo "                                <tr>
                                    <td>";
                    // line 133
                    echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                    echo "</td>
                                    <td>";
                    // line 134
                    echo twig_escape_filter($this->env, (($this->getAttribute($context["infos"], "dataType", array(), "any", true, true)) ? ($this->getAttribute($context["infos"], "dataType", array())) : ("")), "html", null, true);
                    echo "</td>
                                    <td>";
                    // line 135
                    echo (($this->getAttribute($context["infos"], "required", array())) ? ("true") : ("false"));
                    echo "</td>
                                    <td>";
                    // line 136
                    echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "format", array()), "html", null, true);
                    echo "</td>
                                    <td>";
                    // line 137
                    echo twig_escape_filter($this->env, (($this->getAttribute($context["infos"], "description", array(), "any", true, true)) ? ($this->getAttribute($context["infos"], "description", array())) : ("")), "html", null, true);
                    echo "</td>
                                </tr>
                            ";
                }
                // line 140
                echo "                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['name'], $context['infos'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 141
            echo "                        </tbody>
                    </table>
                ";
        }
        // line 144
        echo "
                ";
        // line 145
        if (($this->getAttribute(($context["data"] ?? null), "parsedResponseMap", array(), "any", true, true) &&  !twig_test_empty($this->getAttribute(($context["data"] ?? null), "parsedResponseMap", array())))) {
            // line 146
            echo "                    <h4>Return</h4>
                    <table class='fullwidth'>
                        <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Type</th>
                            <th>Versions</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        ";
            // line 156
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["data"] ?? null), "parsedResponseMap", array()));
            foreach ($context['_seq'] as $context["status_code"] => $context["response"]) {
                // line 157
                echo "                            <tbody>
                            <tr>
                                <td>
                                    <h4>
                                        ";
                // line 161
                echo twig_escape_filter($this->env, $context["status_code"], "html", null, true);
                echo "
                                        ";
                // line 162
                if ($this->getAttribute($this->getAttribute(($context["data"] ?? null), "statusCodes", array(), "any", false, true), $context["status_code"], array(), "array", true, true)) {
                    // line 163
                    echo "                                            - ";
                    echo twig_escape_filter($this->env, twig_join_filter($this->getAttribute($this->getAttribute(($context["data"] ?? null), "statusCodes", array()), $context["status_code"], array(), "array"), ", "), "html", null, true);
                    echo "
                                        ";
                }
                // line 165
                echo "                                    </h4>
                                </td>
                            </tr>
                            ";
                // line 168
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["response"], "model", array()));
                foreach ($context['_seq'] as $context["name"] => $context["infos"]) {
                    // line 169
                    echo "                                <tr>
                                    <td>";
                    // line 170
                    echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                    echo "</td>
                                    <td>";
                    // line 171
                    echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "dataType", array()), "html", null, true);
                    echo "</td>
                                    <td>";
                    // line 172
                    $this->loadTemplate("NelmioApiDocBundle:Components:version.html.twig", "NelmioApiDocBundle::method.html.twig", 172)->display(array("sinceVersion" => $this->getAttribute($context["infos"], "sinceVersion", array()), "untilVersion" => $this->getAttribute($context["infos"], "untilVersion", array())));
                    echo "</td>
                                    <td>";
                    // line 173
                    echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "description", array()), "html", null, true);
                    echo "</td>
                                </tr>
                            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['name'], $context['infos'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 176
                echo "                            </tbody>
                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['status_code'], $context['response'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 178
            echo "                    </table>
                ";
        }
        // line 180
        echo "
                ";
        // line 181
        if (($this->getAttribute(($context["data"] ?? null), "statusCodes", array(), "any", true, true) &&  !twig_test_empty($this->getAttribute(($context["data"] ?? null), "statusCodes", array())))) {
            // line 182
            echo "                    <h4>Status Codes</h4>
                    <table class=\"fullwidth\">
                        <thead>
                        <tr>
                            <th>Status Code</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        ";
            // line 191
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["data"] ?? null), "statusCodes", array()));
            foreach ($context['_seq'] as $context["status_code"] => $context["descriptions"]) {
                // line 192
                echo "                            <tr>
                                <td><a href=\"http://en.wikipedia.org/wiki/HTTP_";
                // line 193
                echo twig_escape_filter($this->env, $context["status_code"], "html", null, true);
                echo "\"
                                       target=\"_blank\">";
                // line 194
                echo twig_escape_filter($this->env, $context["status_code"], "html", null, true);
                echo "</a></td>
                                <td>
                                    <ul>
                                        ";
                // line 197
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($context["descriptions"]);
                foreach ($context['_seq'] as $context["_key"] => $context["description"]) {
                    // line 198
                    echo "                                            <li>";
                    echo twig_escape_filter($this->env, $context["description"], "html", null, true);
                    echo "</li>
                                        ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['description'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 200
                echo "                                    </ul>
                                </td>
                            </tr>
                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['status_code'], $context['descriptions'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 204
            echo "                        </tbody>
                    </table>
                ";
        }
        // line 207
        echo "
                ";
        // line 208
        if (($this->getAttribute(($context["data"] ?? null), "cache", array(), "any", true, true) &&  !twig_test_empty($this->getAttribute(($context["data"] ?? null), "cache", array())))) {
            // line 209
            echo "                    <h4>Cache</h4>
                    <div>";
            // line 210
            echo twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "cache", array()), "html", null, true);
            echo "s</div>
                ";
        }
        // line 212
        echo "
            </div>

            ";
        // line 215
        if (($context["enableSandbox"] ?? null)) {
            // line 216
            echo "                <div class=\"pane sandbox\">
                    ";
            // line 217
            if ((( !(null === $this->getAttribute(($context["app"] ?? null), "request", array())) && $this->getAttribute(($context["data"] ?? null), "https", array())) && ($this->getAttribute($this->getAttribute(($context["app"] ?? null), "request", array()), "secure", array()) != $this->getAttribute(($context["data"] ?? null), "https", array())))) {
                // line 218
                echo "                        Please reload the documentation using the scheme ";
                if ($this->getAttribute(($context["data"] ?? null), "https", array())) {
                    echo "HTTPS";
                } else {
                    echo "HTTP";
                }
                echo " if you want to use the sandbox.
                    ";
            } else {
                // line 220
                echo "                        <form method=\"\"
                              action=\"";
                // line 221
                if ($this->getAttribute(($context["data"] ?? null), "host", array(), "any", true, true)) {
                    echo (($this->getAttribute(($context["data"] ?? null), "https", array())) ? ("https://") : ("http://"));
                    echo twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "host", array()), "html", null, true);
                }
                echo twig_escape_filter($this->env, $this->getAttribute(($context["data"] ?? null), "uri", array()), "html", null, true);
                echo "\">
                            <fieldset class=\"parameters\">
                                <legend>Input</legend>
                                ";
                // line 224
                if ($this->getAttribute(($context["data"] ?? null), "requirements", array(), "any", true, true)) {
                    // line 225
                    echo "                                    <h4>Requirements</h4>
                                    ";
                    // line 226
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["data"] ?? null), "requirements", array()));
                    foreach ($context['_seq'] as $context["name"] => $context["infos"]) {
                        // line 227
                        echo "                                        <p class=\"tuple\">
                                            <input type=\"text\" class=\"key\" value=\"";
                        // line 228
                        echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                        echo "\" placeholder=\"Key\"/>
                                            <span>=</span>
                                            <input type=\"text\" class=\"value\"
                                                   placeholder=\"";
                        // line 231
                        if ($this->getAttribute($context["infos"], "description", array(), "any", true, true)) {
                            echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "description", array()), "html", null, true);
                        } else {
                            echo "Value";
                        }
                        echo "\" ";
                        if ($this->getAttribute($context["infos"], "default", array(), "any", true, true)) {
                            echo " value=\"";
                            echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "default", array()), "html", null, true);
                            echo "\" ";
                        }
                        echo "/>
                                            <span class=\"remove\">-</span>
                                        </p>
                                    ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['name'], $context['infos'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 235
                    echo "                                ";
                }
                // line 236
                echo "                                ";
                if ($this->getAttribute(($context["data"] ?? null), "filters", array(), "any", true, true)) {
                    // line 237
                    echo "                                    <h4>Filters</h4>
                                    ";
                    // line 238
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["data"] ?? null), "filters", array()));
                    foreach ($context['_seq'] as $context["name"] => $context["infos"]) {
                        // line 239
                        echo "                                        <p class=\"tuple\">
                                            <input type=\"text\" class=\"key\" value=\"";
                        // line 240
                        echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                        echo "\" placeholder=\"Key\"/>
                                            <span>=</span>
                                            <input type=\"text\" class=\"value\"
                                                   placeholder=\"";
                        // line 243
                        if ($this->getAttribute($context["infos"], "description", array(), "any", true, true)) {
                            echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "description", array()), "html", null, true);
                        } else {
                            echo "Value";
                        }
                        echo "\" ";
                        if ($this->getAttribute($context["infos"], "default", array(), "any", true, true)) {
                            echo " value=\"";
                            echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "default", array()), "html", null, true);
                            echo "\" ";
                        }
                        echo "/>
                                            <span class=\"remove\">-</span>
                                        </p>
                                    ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['name'], $context['infos'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 247
                    echo "                                ";
                }
                // line 248
                echo "                                ";
                if ($this->getAttribute(($context["data"] ?? null), "parameters", array(), "any", true, true)) {
                    // line 249
                    echo "                                    <h4>Parameters</h4>
                                    ";
                    // line 250
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["data"] ?? null), "parameters", array()));
                    foreach ($context['_seq'] as $context["name"] => $context["infos"]) {
                        // line 251
                        echo "                                        ";
                        if ( !$this->getAttribute($context["infos"], "readonly", array())) {
                            // line 252
                            echo "                                            <p class=\"tuple\"
                                               data-dataType=\"";
                            // line 253
                            if ($this->getAttribute($context["infos"], "dataType", array())) {
                                echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "dataType", array()), "html", null, true);
                            }
                            echo "\"
                                               data-format=\"";
                            // line 254
                            if ($this->getAttribute($context["infos"], "format", array())) {
                                echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "format", array()), "html", null, true);
                            }
                            echo "\"
                                               data-description=\"";
                            // line 255
                            if ($this->getAttribute($context["infos"], "description", array())) {
                                echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "description", array()), "html", null, true);
                            }
                            echo "\">
                                                <input type=\"text\" class=\"key\" value=\"";
                            // line 256
                            echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                            echo "\" placeholder=\"Key\"/>
                                                <span>=</span>
                                                <select class=\"tuple_type\">
                                                    <option value=\"\">Type</option>
                                                    <option value=\"string\">String</option>
                                                    <option value=\"boolean\">Boolean</option>
                                                    <option value=\"file\">File</option>
                                                    <option value=\"textarea\">Textarea</option>
                                                </select>
                                                <input type=\"text\" class=\"value\"
                                                       placeholder=\"";
                            // line 266
                            if ($this->getAttribute($context["infos"], "dataType", array())) {
                                echo "[";
                                echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "dataType", array()), "html", null, true);
                                echo "] ";
                            }
                            if ($this->getAttribute($context["infos"], "format", array())) {
                                echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "format", array()), "html", null, true);
                            }
                            if ($this->getAttribute($context["infos"], "description", array())) {
                                echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "description", array()), "html", null, true);
                            } else {
                                echo "Value";
                            }
                            echo "\" ";
                            if ($this->getAttribute($context["infos"], "default", array(), "any", true, true)) {
                                echo " value=\"";
                                echo twig_escape_filter($this->env, $this->getAttribute($context["infos"], "default", array()), "html", null, true);
                                echo "\" ";
                            }
                            echo "/>
                                                <span class=\"remove\">-</span>
                                            </p>
                                        ";
                        }
                        // line 270
                        echo "                                    ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['name'], $context['infos'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 271
                    echo "                                    <button type=\"button\" class=\"add_parameter\">New parameter</button>
                                ";
                }
                // line 273
                echo "
                            </fieldset>

                            <fieldset class=\"headers\">
                                ";
                // line 277
                $context["methods"] = twig_split_filter($this->env, twig_upper_filter($this->env, $this->getAttribute(($context["data"] ?? null), "method", array())), "|");
                // line 278
                echo "                                ";
                if ((twig_length_filter($this->env, ($context["methods"] ?? null)) > 1)) {
                    // line 279
                    echo "                                    <legend>Method</legend>
                                    <select name=\"header_method\">
                                        ";
                    // line 281
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(($context["methods"] ?? null));
                    foreach ($context['_seq'] as $context["_key"] => $context["method"]) {
                        // line 282
                        echo "                                            <option value=\"";
                        echo twig_escape_filter($this->env, $context["method"], "html", null, true);
                        echo "\">";
                        echo twig_escape_filter($this->env, $context["method"], "html", null, true);
                        echo "</option>
                                        ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['method'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 284
                    echo "                                    </select>
                                ";
                } else {
                    // line 286
                    echo "                                    <input type=\"hidden\" name=\"header_method\" value=\"";
                    echo twig_escape_filter($this->env, twig_join_filter(($context["methods"] ?? null)), "html", null, true);
                    echo "\"/>
                                ";
                }
                // line 288
                echo "
                                <legend>Headers</legend>

                                ";
                // line 291
                if (($context["acceptType"] ?? null)) {
                    // line 292
                    echo "                                    <p class=\"tuple\">
                                        <input type=\"text\" class=\"key\" value=\"Accept\"/>
                                        <span>=</span>
                                        <input type=\"text\" class=\"value\" value=\"";
                    // line 295
                    echo twig_escape_filter($this->env, ($context["acceptType"] ?? null), "html", null, true);
                    echo "\"/> <span
                                                class=\"remove\">-</span>
                                    </p>
                                ";
                }
                // line 299
                echo "
                                <p class=\"tuple\">
                                    <input type=\"text\" class=\"key\" placeholder=\"Key\"/>
                                    <span>=</span>
                                    <input type=\"text\" class=\"value\" placeholder=\"Value\"/> <span class=\"remove\">-</span>
                                </p>

                                <button type=\"button\" class=\"add_header\">New header</button>
                            </fieldset>

                            <fieldset class=\"request-content\">
                                <legend>Content</legend>

                                <textarea class=\"content\"
                                          placeholder=\"Content set here will override the parameters that do not match the url\"></textarea>

                                <p class=\"tuple\">
                                    <input type=\"text\" class=\"key content-type\" value=\"Content-Type\"
                                           disabled=\"disabled\"/>
                                    <span>=</span>
                                    <input type=\"text\" class=\"value\" placeholder=\"Value\"/>
                                    <button type=\"button\" class=\"set-content-type\">Set header</button>
                                    <small>Replaces header if set</small>
                                </p>
                            </fieldset>

                            <div class=\"buttons\">
                                <input type=\"submit\" value=\"Try!\"/>
                            </div>
                        </form>

                        <script type=\"text/x-tmpl\" class=\"parameters_tuple_template\">
                        <p class=\"tuple\">
                            <input type=\"text\" class=\"key\" placeholder=\"Key\" />
                            <span>=</span>
                            <select class=\"tuple_type\">
                                                <option value=\"\">Type</option>
                                                <option value=\"string\">String</option>
                                                <option value=\"boolean\">Boolean</option>
                                                <option value=\"file\">File</option>
                                                <option value=\"textarea\">Textarea</option>
                                            </select>
                            <input type=\"text\" class=\"value\" placeholder=\"Value\" /> <span class=\"remove\">-</span>
                        </p>

                        </script>

                        <script type=\"text/x-tmpl\" class=\"headers_tuple_template\">
                        <p class=\"tuple\">
                            <input type=\"text\" class=\"key\" placeholder=\"Key\" />
                            <span>=</span>
                            <input type=\"text\" class=\"value\" placeholder=\"Value\" /> <span class=\"remove\">-</span>
                        </p>

                        </script>


                        <div class=\"result\">
                            <h4>Request URL</h4>
                            <pre class=\"url\"></pre>

                            <h4>Response Headers&nbsp;
                                <small>[<a href=\"\" class=\"to-expand\">Expand</a>]</small>
                                &nbsp;
                                <small class=\"profiler\">[<a href=\"\" class=\"profiler-link\" target=\"_blank\">Profiler</a>]
                                </small>
                            </h4>
                            <pre class=\"headers to-expand\"></pre>

                            <h4>Response Body&nbsp;
                                <small>[<a href=\"\" class=\"to-raw\">Raw</a>]</small>
                            </h4>
                            <pre class=\"response prettyprint\"></pre>
                        </div>
                    ";
            }
            // line 374
            echo "                </div>
            ";
        }
        // line 376
        echo "        </div>
    </div>
</li>
";
    }

    public function getTemplateName()
    {
        return "NelmioApiDocBundle::method.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  850 => 376,  846 => 374,  769 => 299,  762 => 295,  757 => 292,  755 => 291,  750 => 288,  744 => 286,  740 => 284,  729 => 282,  725 => 281,  721 => 279,  718 => 278,  716 => 277,  710 => 273,  706 => 271,  700 => 270,  675 => 266,  662 => 256,  656 => 255,  650 => 254,  644 => 253,  641 => 252,  638 => 251,  634 => 250,  631 => 249,  628 => 248,  625 => 247,  605 => 243,  599 => 240,  596 => 239,  592 => 238,  589 => 237,  586 => 236,  583 => 235,  563 => 231,  557 => 228,  554 => 227,  550 => 226,  547 => 225,  545 => 224,  535 => 221,  532 => 220,  522 => 218,  520 => 217,  517 => 216,  515 => 215,  510 => 212,  505 => 210,  502 => 209,  500 => 208,  497 => 207,  492 => 204,  483 => 200,  474 => 198,  470 => 197,  464 => 194,  460 => 193,  457 => 192,  453 => 191,  442 => 182,  440 => 181,  437 => 180,  433 => 178,  426 => 176,  417 => 173,  413 => 172,  409 => 171,  405 => 170,  402 => 169,  398 => 168,  393 => 165,  387 => 163,  385 => 162,  381 => 161,  375 => 157,  371 => 156,  359 => 146,  357 => 145,  354 => 144,  349 => 141,  343 => 140,  337 => 137,  333 => 136,  329 => 135,  325 => 134,  321 => 133,  318 => 132,  315 => 131,  311 => 130,  297 => 118,  295 => 117,  292 => 116,  287 => 113,  278 => 109,  269 => 106,  265 => 105,  262 => 104,  258 => 103,  252 => 100,  249 => 99,  245 => 98,  234 => 89,  232 => 88,  229 => 87,  224 => 84,  215 => 81,  211 => 80,  207 => 79,  203 => 78,  200 => 77,  196 => 76,  183 => 65,  181 => 64,  178 => 63,  171 => 61,  168 => 60,  166 => 59,  163 => 58,  158 => 56,  155 => 55,  153 => 54,  147 => 50,  142 => 47,  140 => 46,  131 => 44,  125 => 40,  119 => 38,  117 => 37,  113 => 35,  110 => 34,  96 => 32,  93 => 31,  88 => 30,  86 => 29,  81 => 27,  78 => 25,  76 => 24,  74 => 23,  70 => 21,  65 => 19,  62 => 18,  59 => 17,  55 => 15,  53 => 14,  50 => 13,  44 => 9,  42 => 8,  36 => 5,  26 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "NelmioApiDocBundle::method.html.twig", "/var/www/html/opendata-umbriaopenapi-nigel/app/Resources/NelmioApiDocBundle/views/method.html.twig");
    }
}
