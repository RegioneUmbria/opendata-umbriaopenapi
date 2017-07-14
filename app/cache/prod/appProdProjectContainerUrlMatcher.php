<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * appProdProjectContainerUrlMatcher.
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appProdProjectContainerUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = array();
        $pathinfo = rawurldecode($pathinfo);
        $context = $this->context;
        $request = $this->request;

        // umbria_open_api_homepage
        if (rtrim($pathinfo, '/') === '') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'umbria_open_api_homepage');
            }

            return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\DefaultController::indexAction',  '_route' => 'umbria_open_api_homepage',);
        }

        if (0 === strpos($pathinfo, '/sparql_manual')) {
            // umbria_open_api_sparql_manual
            if ($pathinfo === '/sparql_manual') {
                return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\SparqlManualController::indexAction',  '_route' => 'umbria_open_api_sparql_manual',);
            }

            if (0 === strpos($pathinfo, '/sparql_manual/sparql_query_')) {
                // umbria_open_api_sparql_manual_query_graphs
                if ($pathinfo === '/sparql_manual/sparql_query_graphs') {
                    return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\SparqlManualController::sparqlQueryGraphsAction',  '_route' => 'umbria_open_api_sparql_manual_query_graphs',);
                }

                // umbria_open_api_sparql_manual_query_types
                if (0 === strpos($pathinfo, '/sparql_manual/sparql_query_types') && preg_match('#^/sparql_manual/sparql_query_types/(?P<graph>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'umbria_open_api_sparql_manual_query_types')), array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\SparqlManualController::sparqlQueryTypesAction',));
                }

                // umbria_open_api_sparql_manual_query_data
                if (0 === strpos($pathinfo, '/sparql_manual/sparql_query_data') && preg_match('#^/sparql_manual/sparql_query_data/(?P<graph>[^/]++)/(?P<type>[^/]++)$#s', $pathinfo, $matches)) {
                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'umbria_open_api_sparql_manual_query_data')), array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\SparqlManualController::sparqlQueryDataAction',));
                }

            }

        }

        // umbria_open_api_entities_update
        if (0 === strpos($pathinfo, '/entities_update') && preg_match('#^/entities_update/(?P<attractor>0|1)/(?P<consortium>0|1)/(?P<event>0|1)/(?P<iat>0|1)/(?P<sport_facility>0|1)/(?P<profession>0|1)/(?P<proposal>0|1)/(?P<accomodation>0|1)/(?P<travel_agency>0|1)/$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'PUT') {
                $allow[] = 'PUT';
                goto not_umbria_open_api_entities_update;
            }

            return $this->mergeDefaults(array_replace($matches, array('_route' => 'umbria_open_api_entities_update')), array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\EntitiesUpdateController::indexAction',));
        }
        not_umbria_open_api_entities_update:

        // facebook_webhook
        if ($pathinfo === '/facebook-webhook') {
            if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                goto not_facebook_webhook;
            }

            return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\FacebookMessengerBotController::indexAction',  '_route' => 'facebook_webhook',);
        }
        not_facebook_webhook:

        if (0 === strpos($pathinfo, '/open-api')) {
            if (0 === strpos($pathinfo, '/open-api/tourism-')) {
                // get_tourism_proposal_list
                if ($pathinfo === '/open-api/tourism-proposal') {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_get_tourism_proposal_list;
                    }

                    return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\ProposalController::getTourismProposalListAction',  '_format' => 'json',  '_route' => 'get_tourism_proposal_list',);
                }
                not_get_tourism_proposal_list:

                if (0 === strpos($pathinfo, '/open-api/tourism-attractor')) {
                    // options_tourism_attractor
                    if ($pathinfo === '/open-api/tourism-attractor') {
                        if ($this->context->getMethod() != 'OPTIONS') {
                            $allow[] = 'OPTIONS';
                            goto not_options_tourism_attractor;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\AttractorController::optionsTourismAttractorAction',  '_format' => 'json',  '_route' => 'options_tourism_attractor',);
                    }
                    not_options_tourism_attractor:

                    // get_tourism_attractor_list
                    if ($pathinfo === '/open-api/tourism-attractor') {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_get_tourism_attractor_list;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\AttractorController::getTourismAttractorListAction',  '_format' => 'json',  '_route' => 'get_tourism_attractor_list',);
                    }
                    not_get_tourism_attractor_list:

                }

                // get_tourism_event_list
                if ($pathinfo === '/open-api/tourism-event') {
                    if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                        $allow = array_merge($allow, array('GET', 'HEAD'));
                        goto not_get_tourism_event_list;
                    }

                    return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\EventController::getTourismEventListAction',  '_format' => 'json',  '_route' => 'get_tourism_event_list',);
                }
                not_get_tourism_event_list:

                if (0 === strpos($pathinfo, '/open-api/tourism-travel-agency')) {
                    // options_tourism_proposal
                    if ($pathinfo === '/open-api/tourism-travel-agency') {
                        if ($this->context->getMethod() != 'OPTIONS') {
                            $allow[] = 'OPTIONS';
                            goto not_options_tourism_proposal;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\TravelAgencyController::optionsTourismProposalAction',  '_format' => 'json',  '_route' => 'options_tourism_proposal',);
                    }
                    not_options_tourism_proposal:

                    // get_tourism_travel_agency_list
                    if ($pathinfo === '/open-api/tourism-travel-agency') {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_get_tourism_travel_agency_list;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\TravelAgencyController::getTourismTravelAgencyListAction',  '_format' => 'json',  '_route' => 'get_tourism_travel_agency_list',);
                    }
                    not_get_tourism_travel_agency_list:

                }

                if (0 === strpos($pathinfo, '/open-api/tourism-consortium')) {
                    // options_tourism_consortium
                    if ($pathinfo === '/open-api/tourism-consortium') {
                        if ($this->context->getMethod() != 'OPTIONS') {
                            $allow[] = 'OPTIONS';
                            goto not_options_tourism_consortium;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\ConsortiumController::optionsTourismConsortiumAction',  '_format' => 'json',  '_route' => 'options_tourism_consortium',);
                    }
                    not_options_tourism_consortium:

                    // get_tourism_consortium_list
                    if ($pathinfo === '/open-api/tourism-consortium') {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_get_tourism_consortium_list;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\ConsortiumController::getTourismConsortiumListAction',  '_format' => 'json',  '_route' => 'get_tourism_consortium_list',);
                    }
                    not_get_tourism_consortium_list:

                }

                if (0 === strpos($pathinfo, '/open-api/tourism-profession')) {
                    // options_tourism_profession
                    if ($pathinfo === '/open-api/tourism-profession') {
                        if ($this->context->getMethod() != 'OPTIONS') {
                            $allow[] = 'OPTIONS';
                            goto not_options_tourism_profession;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\ProfessionController::optionsTourismProfessionAction',  '_format' => 'json',  '_route' => 'options_tourism_profession',);
                    }
                    not_options_tourism_profession:

                    // get_tourism_profession_list
                    if ($pathinfo === '/open-api/tourism-profession') {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_get_tourism_profession_list;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\ProfessionController::getTourismProfessionListAction',  '_format' => 'json',  '_route' => 'get_tourism_profession_list',);
                    }
                    not_get_tourism_profession_list:

                }

                if (0 === strpos($pathinfo, '/open-api/tourism-iat')) {
                    // options_tourism_iat
                    if ($pathinfo === '/open-api/tourism-iat') {
                        if ($this->context->getMethod() != 'OPTIONS') {
                            $allow[] = 'OPTIONS';
                            goto not_options_tourism_iat;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\IatController::optionsTourismIatAction',  '_format' => 'json',  '_route' => 'options_tourism_iat',);
                    }
                    not_options_tourism_iat:

                    // get_tourism_iat_list
                    if ($pathinfo === '/open-api/tourism-iat') {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_get_tourism_iat_list;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\IatController::getTourismIatListAction',  '_format' => 'json',  '_route' => 'get_tourism_iat_list',);
                    }
                    not_get_tourism_iat_list:

                }

                if (0 === strpos($pathinfo, '/open-api/tourism-accomodation')) {
                    // options_tourism_accomodation
                    if ($pathinfo === '/open-api/tourism-accomodation') {
                        if ($this->context->getMethod() != 'OPTIONS') {
                            $allow[] = 'OPTIONS';
                            goto not_options_tourism_accomodation;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\AccomodationController::optionsTourismAccomodationAction',  '_format' => 'json',  '_route' => 'options_tourism_accomodation',);
                    }
                    not_options_tourism_accomodation:

                    // get_tourism_accomodation_list
                    if ($pathinfo === '/open-api/tourism-accomodation') {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_get_tourism_accomodation_list;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\AccomodationController::getTourismAccomodationListAction',  '_format' => 'json',  '_route' => 'get_tourism_accomodation_list',);
                    }
                    not_get_tourism_accomodation_list:

                }

                if (0 === strpos($pathinfo, '/open-api/tourism-sports-facility')) {
                    // options_tourism_sport_facility
                    if ($pathinfo === '/open-api/tourism-sports-facility') {
                        if ($this->context->getMethod() != 'OPTIONS') {
                            $allow[] = 'OPTIONS';
                            goto not_options_tourism_sport_facility;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\SportFacilityController::optionsTourismSportFacilityAction',  '_format' => 'json',  '_route' => 'options_tourism_sport_facility',);
                    }
                    not_options_tourism_sport_facility:

                    // get_tourism_sport_facility_list
                    if ($pathinfo === '/open-api/tourism-sports-facility') {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_get_tourism_sport_facility_list;
                        }

                        return array (  '_controller' => 'Umbria\\OpenApiBundle\\Controller\\Tourism\\SportFacilityController::getTourismSportFacilityListAction',  '_format' => 'json',  '_route' => 'get_tourism_sport_facility_list',);
                    }
                    not_get_tourism_sport_facility_list:

                }

            }

            if (0 === strpos($pathinfo, '/open-api/example')) {
                if (0 === strpos($pathinfo, '/open-api/example/t')) {
                    if (0 === strpos($pathinfo, '/open-api/example/tourism')) {
                        if (0 === strpos($pathinfo, '/open-api/example/tourism/attractor')) {
                            // attractor_index
                            if (rtrim($pathinfo, '/') === '/open-api/example/tourism/attractor') {
                                if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                                    $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                                    goto not_attractor_index;
                                }

                                if (substr($pathinfo, -1) !== '/') {
                                    return $this->redirect($pathinfo.'/', 'attractor_index');
                                }

                                return array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\AttractorController::indexAction',  '_route' => 'attractor_index',);
                            }
                            not_attractor_index:

                            // attractor_show
                            if (preg_match('#^/open\\-api/example/tourism/attractor/(?P<id>[^/]++)/show$#s', $pathinfo, $matches)) {
                                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                                    $allow = array_merge($allow, array('GET', 'HEAD'));
                                    goto not_attractor_show;
                                }

                                return $this->mergeDefaults(array_replace($matches, array('_route' => 'attractor_show')), array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\AttractorController::showAction',));
                            }
                            not_attractor_show:

                        }

                        if (0 === strpos($pathinfo, '/open-api/example/tourism/proposal')) {
                            // proposal_index
                            if (rtrim($pathinfo, '/') === '/open-api/example/tourism/proposal') {
                                if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                                    $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                                    goto not_proposal_index;
                                }

                                if (substr($pathinfo, -1) !== '/') {
                                    return $this->redirect($pathinfo.'/', 'proposal_index');
                                }

                                return array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\ProposalController::indexAction',  '_route' => 'proposal_index',);
                            }
                            not_proposal_index:

                            // proposal_show
                            if (preg_match('#^/open\\-api/example/tourism/proposal/(?P<id>[^/]++)/show$#s', $pathinfo, $matches)) {
                                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                                    $allow = array_merge($allow, array('GET', 'HEAD'));
                                    goto not_proposal_show;
                                }

                                return $this->mergeDefaults(array_replace($matches, array('_route' => 'proposal_show')), array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\ProposalController::showAction',));
                            }
                            not_proposal_show:

                        }

                        if (0 === strpos($pathinfo, '/open-api/example/tourism/event')) {
                            // event_index
                            if (rtrim($pathinfo, '/') === '/open-api/example/tourism/event') {
                                if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                                    $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                                    goto not_event_index;
                                }

                                if (substr($pathinfo, -1) !== '/') {
                                    return $this->redirect($pathinfo.'/', 'event_index');
                                }

                                return array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\EventController::indexAction',  '_route' => 'event_index',);
                            }
                            not_event_index:

                            // event_show
                            if (preg_match('#^/open\\-api/example/tourism/event/(?P<id>[^/]++)/show$#s', $pathinfo, $matches)) {
                                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                                    $allow = array_merge($allow, array('GET', 'HEAD'));
                                    goto not_event_show;
                                }

                                return $this->mergeDefaults(array_replace($matches, array('_route' => 'event_show')), array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\EventController::showAction',));
                            }
                            not_event_show:

                        }

                    }

                    if (0 === strpos($pathinfo, '/open-api/example/travel-agency')) {
                        // travelagency_index
                        if (rtrim($pathinfo, '/') === '/open-api/example/travel-agency') {
                            if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                                $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                                goto not_travelagency_index;
                            }

                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($pathinfo.'/', 'travelagency_index');
                            }

                            return array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\TravelAgencyController::indexAction',  '_route' => 'travelagency_index',);
                        }
                        not_travelagency_index:

                        // travelagency_show
                        if (preg_match('#^/open\\-api/example/travel\\-agency/(?P<id>[^/]++)/show$#s', $pathinfo, $matches)) {
                            if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                                $allow = array_merge($allow, array('GET', 'HEAD'));
                                goto not_travelagency_show;
                            }

                            return $this->mergeDefaults(array_replace($matches, array('_route' => 'travelagency_show')), array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\TravelAgencyController::showAction',));
                        }
                        not_travelagency_show:

                    }

                }

                if (0 === strpos($pathinfo, '/open-api/example/consortium')) {
                    // consortium_index
                    if (rtrim($pathinfo, '/') === '/open-api/example/consortium') {
                        if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                            goto not_consortium_index;
                        }

                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($pathinfo.'/', 'consortium_index');
                        }

                        return array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\ConsortiumController::indexAction',  '_route' => 'consortium_index',);
                    }
                    not_consortium_index:

                    // consortium_show
                    if (preg_match('#^/open\\-api/example/consortium/(?P<id>[^/]++)/show$#s', $pathinfo, $matches)) {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_consortium_show;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'consortium_show')), array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\ConsortiumController::showAction',));
                    }
                    not_consortium_show:

                }

                if (0 === strpos($pathinfo, '/open-api/example/profession')) {
                    // profession_index
                    if (rtrim($pathinfo, '/') === '/open-api/example/profession') {
                        if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                            goto not_profession_index;
                        }

                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($pathinfo.'/', 'profession_index');
                        }

                        return array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\ProfessionController::indexAction',  '_route' => 'profession_index',);
                    }
                    not_profession_index:

                    // profession_show
                    if (preg_match('#^/open\\-api/example/profession/(?P<id>[^/]++)/show$#s', $pathinfo, $matches)) {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_profession_show;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'profession_show')), array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\ProfessionController::showAction',));
                    }
                    not_profession_show:

                }

                if (0 === strpos($pathinfo, '/open-api/example/iat')) {
                    // iat_index
                    if (rtrim($pathinfo, '/') === '/open-api/example/iat') {
                        if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                            goto not_iat_index;
                        }

                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($pathinfo.'/', 'iat_index');
                        }

                        return array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\IatController::indexAction',  '_route' => 'iat_index',);
                    }
                    not_iat_index:

                    // iat_show
                    if (preg_match('#^/open\\-api/example/iat/(?P<id>[^/]++)/show$#s', $pathinfo, $matches)) {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_iat_show;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'iat_show')), array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\IatController::showAction',));
                    }
                    not_iat_show:

                }

                if (0 === strpos($pathinfo, '/open-api/example/sport_facility')) {
                    // sport_facility_index
                    if (rtrim($pathinfo, '/') === '/open-api/example/sport_facility') {
                        if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                            goto not_sport_facility_index;
                        }

                        if (substr($pathinfo, -1) !== '/') {
                            return $this->redirect($pathinfo.'/', 'sport_facility_index');
                        }

                        return array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\SportFacilityController::indexAction',  '_route' => 'sport_facility_index',);
                    }
                    not_sport_facility_index:

                    // sport_facility_show
                    if (preg_match('#^/open\\-api/example/sport_facility/(?P<id>[^/]++)/show$#s', $pathinfo, $matches)) {
                        if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                            $allow = array_merge($allow, array('GET', 'HEAD'));
                            goto not_sport_facility_show;
                        }

                        return $this->mergeDefaults(array_replace($matches, array('_route' => 'sport_facility_show')), array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\SportFacilityController::showAction',));
                    }
                    not_sport_facility_show:

                }

                if (0 === strpos($pathinfo, '/open-api/example/tourism')) {
                    if (0 === strpos($pathinfo, '/open-api/example/tourism/accomodation')) {
                        // accomodation_index
                        if (rtrim($pathinfo, '/') === '/open-api/example/tourism/accomodation') {
                            if (!in_array($this->context->getMethod(), array('GET', 'POST', 'HEAD'))) {
                                $allow = array_merge($allow, array('GET', 'POST', 'HEAD'));
                                goto not_accomodation_index;
                            }

                            if (substr($pathinfo, -1) !== '/') {
                                return $this->redirect($pathinfo.'/', 'accomodation_index');
                            }

                            return array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\AccomodationController::indexAction',  '_route' => 'accomodation_index',);
                        }
                        not_accomodation_index:

                        // accomodation_show
                        if (preg_match('#^/open\\-api/example/tourism/accomodation/(?P<id>[^/]++)/show$#s', $pathinfo, $matches)) {
                            if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                                $allow = array_merge($allow, array('GET', 'HEAD'));
                                goto not_accomodation_show;
                            }

                            return $this->mergeDefaults(array_replace($matches, array('_route' => 'accomodation_show')), array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\AccomodationController::showAction',));
                        }
                        not_accomodation_show:

                    }

                    // umbria_pro_loco_homepage
                    if ($pathinfo === '/open-api/example/tourism/map') {
                        return array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\DefaultController::indexAction',  '_route' => 'umbria_pro_loco_homepage',);
                    }

                }

                if (0 === strpos($pathinfo, '/open-api/example/statistiche')) {
                    // umbria_pro_loco_statistiche
                    if ($pathinfo === '/open-api/example/statistiche') {
                        return array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\SUAPEController::indexAction',  '_route' => 'umbria_pro_loco_statistiche',);
                    }

                    if (0 === strpos($pathinfo, '/open-api/example/statistiche/SUAPE')) {
                        // umbria_pro_loco_suape
                        if ($pathinfo === '/open-api/example/statistiche/SUAPE') {
                            return array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\SUAPEController::indexAction',  '_route' => 'umbria_pro_loco_suape',);
                        }

                        // umbria_pro_loco_suape_execute_sparql_query
                        if ($pathinfo === '/open-api/example/statistiche/SUAPE/execute_sparql_query/') {
                            if ($this->context->getMethod() != 'POST') {
                                $allow[] = 'POST';
                                goto not_umbria_pro_loco_suape_execute_sparql_query;
                            }

                            return array (  '_controller' => 'Umbria\\ProLocoBundle\\Controller\\SUAPEController::executeSparqlQueryAction',  '_route' => 'umbria_pro_loco_suape_execute_sparql_query',);
                        }
                        not_umbria_pro_loco_suape_execute_sparql_query:

                    }

                }

            }

            // nelmio_api_doc_index
            if (0 === strpos($pathinfo, '/open-api/doc') && preg_match('#^/open\\-api/doc(?:/(?P<view>[^/]++))?$#s', $pathinfo, $matches)) {
                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'HEAD'));
                    goto not_nelmio_api_doc_index;
                }

                return $this->mergeDefaults(array_replace($matches, array('_route' => 'nelmio_api_doc_index')), array (  '_controller' => 'Nelmio\\ApiDocBundle\\Controller\\ApiDocController::indexAction',  'view' => 'default',));
            }
            not_nelmio_api_doc_index:

        }

        if (0 === strpos($pathinfo, '/telegram-bot')) {
            // shaygan_telegram_bot_api_webhook
            if (0 === strpos($pathinfo, '/telegram-bot/setup-webhook') && preg_match('#^/telegram\\-bot/setup\\-webhook(?:/(?P<secret>[^/]++))?$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'shaygan_telegram_bot_api_webhook')), array (  '_controller' => 'Shaygan\\TelegramBotApiBundle\\Controller\\DefaultController::setupWebhookAction',  'secret' => NULL,));
            }

            // shaygan_telegram_bot_api_update
            if (0 === strpos($pathinfo, '/telegram-bot/update') && preg_match('#^/telegram\\-bot/update(?:/(?P<secret>[^/]++))?$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'shaygan_telegram_bot_api_update')), array (  '_controller' => 'Shaygan\\TelegramBotApiBundle\\Controller\\DefaultController::updateAction',  'secret' => NULL,));
            }

        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
