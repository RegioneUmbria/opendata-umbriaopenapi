/**
 * Created by Lorenzo Ranucci on 06/09/2016.
 */

document.getElementById("graphsFormSubmit").addEventListener("click", executeGraphsQuery);
document.getElementById("sparqlQueryTypeSubmit").addEventListener("click", executeTypeQuery);
document.getElementById("sparqlQuerySubmit").addEventListener("click", executeQuery);

function executeGraphsQuery() {
    var xhr = createCORSRequest('GET', 'https://odnt-srv01/sparql?default-graph-uri=http%3A%2F%2Fdati.umbria.it%2Fgraph%2Fattrattor&query=SELECT+DISTINCT+%3Fg%0D%0AWHERE%7B%0D%0A++++GRAPH+%3Fg+%7B%3Fs+a+%3Ft%7D%0D%0A%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on');
    if (!xhr) {
        throw new Error('CORS not supported');
    }
    xhr.onload = function () {
        document.getElementById("graphsFormResult").innerHTML = JSON.stringify(JSON.parse(xhr.responseText), null, "\t");
        setGraphsResults(1, true);
        document.getElementById("graphsFormSubmit").className = "btn btn-success";
        document.getElementById("show_json_graphs").style.display = "initial";
        document.getElementById("show_result_graphs").style.display = "initial";

    };
    xhr.send();

}

function executeTypeQuery() {
    var graph = encodeURIComponent(document.getElementById("sparqlQueryTypeGraph").value);
    var requestUrl = "https://odnt-srv01/sparql?default-graph-uri=".concat(graph, "&query=SELECT+DISTINCT+%3Fo%0D%0AWHERE%7B%0D%0A++++%3Fs+%3Chttp%3A%2F%2Fwww.w3.org%2F1999%2F02%2F22-rdf-syntax-ns%23type%3E+%3Fo%0D%0A%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on");
    var xhr = createCORSRequest('GET', requestUrl);
    if (!xhr) {
        throw new Error('CORS not supported');
    }
    xhr.onload = function () {
        document.getElementById("sparqlQueryTypeResult").innerHTML = JSON.stringify(JSON.parse(xhr.responseText), null, "\t");
        setTypesResults(1, true);
        document.getElementById("sparqlQueryTypeSubmit").className = "btn btn-success";
        document.getElementById("show_json_types").style.display = "initial";
        document.getElementById("show_result_types").style.display = "initial";
    };
    xhr.send();
}

function executeQuery() {
    var graph = encodeURIComponent(document.getElementById("sparqlQueryGraph").value);
    var type = encodeURIComponent(document.getElementById("sparqlQueryTypeHidden").value);
    var requestUrl = "https://odnt-srv01/sparql?default-graph-uri=".concat(graph, "&query=SELECT+DISTINCT+%3Fs+%3Fp+%3Fo+WHERE%7B+%3Fs+%3Fp+%3Fo+.+%3Fs+%3Chttp%3A%2F%2Fwww.w3.org%2F1999%2F02%2F22-rdf-syntax-ns%23type%3E+%3C", type, "%3E+%7DLIMIT+50&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on");
    var xhr = createCORSRequest('GET', requestUrl);
    if (!xhr) {
        throw new Error('CORS not supported');
    }
    xhr.onload = function () {
        document.getElementById("sparqlQueryResult").innerHTML = JSON.stringify(JSON.parse(xhr.responseText), null, "\t");
        setResourcesResults(1, true);
        document.getElementById("sparqlQuerySubmit").className = "btn btn-success";
        document.getElementById("show_json_resources").style.display = "initial";
        document.getElementById("show_result_resources").style.display = "initial";
    };
    xhr.send();
}

function setGraphsResults(page, resetPages) {
    var responseObj = JSON.parse(document.getElementById("graphsFormResult").innerHTML);
    var bindings = responseObj.results.bindings;
    var countGraphs = bindings.length;
    var pageCount = Math.ceil(countGraphs / 5);
    if (pageCount > 0) {
        if (page > pageCount) {
            page = pageCount;
        }
        if (page < 1) {
            page = 1;
        }
        var startElementIdx = (page - 1) * 5;
        var endElementIdx = (startElementIdx + 5 < countGraphs) ? startElementIdx + 5 : countGraphs;

        var graphsList = document.getElementById("graphs_list");
        var graphsPages = document.getElementById("graphs_pages");
        /*clear graphs list*/
        while (graphsList.firstChild) {
            graphsList.removeChild(graphsList.firstChild);
        }
        /*set graphs list*/
        for (var i = startElementIdx; i < endElementIdx;) {
            var graphURI = bindings[i].g.value;
            //if(graphURI.indexOf("dati.umbria.it")!= -1){
            var graphButtonNode = document.createElement("button");
            graphButtonNode.type = "button";
            graphButtonNode.className = "list-group-item";
            graphButtonNode.innerHTML = graphURI;
            graphButtonNode.setAttribute("onclick", "setQueryGraph(this)");
            graphsList.appendChild(graphButtonNode);
            i++;
            //}
        }

        if (resetPages) {
            /*clear graphs pages*/
            while (graphsPages.firstChild) {
                graphsPages.removeChild(graphsPages.firstChild);
            }
            /*set pagination buttons*/
            for (var j = 1; j <= pageCount; j++) {
                var pageElementLi = document.createElement("li");
                var pageElementA = document.createElement("a");
                pageElementA.href = "javascript:void(0);";
                pageElementA.innerHTML = j;
                pageElementA.setAttribute("onclick", "setGraphsResults(".concat(j.toString(), ",false)"));
                pageElementLi.appendChild(pageElementA);
                graphsPages.appendChild(pageElementLi);
            }
        }
    }

}

function setTypesResults(page, resetPages) {
    var responseObj = JSON.parse(document.getElementById("sparqlQueryTypeResult").innerHTML);
    var bindings = responseObj.results.bindings;
    var countTypes = bindings.length;
    var pageCount = Math.ceil(countTypes / 5);
    if (pageCount > 0) {
        if (page > pageCount) {
            page = pageCount;
        }
        if (page < 1) {
            page = 1;
        }
        var startElementIdx = (page - 1) * 5;
        var endElementIdx = (startElementIdx + 5 < countTypes) ? startElementIdx + 5 : countTypes;

        var typesList = document.getElementById("types_list");
        var typesPages = document.getElementById("types_pages");
        /*clear types list*/
        clearTypesResults();
        /*set types list*/
        for (var i = startElementIdx; i < endElementIdx;) {
            var typeURI = bindings[i].o.value;
            var typeButtonNode = document.createElement("button");
            typeButtonNode.type = "button";
            typeButtonNode.className = "list-group-item";
            typeButtonNode.innerHTML = typeURI;
            typeButtonNode.setAttribute("onclick", "setQueryType(this)");
            typesList.appendChild(typeButtonNode);
            i++;
            //}
        }

        if (resetPages) {
            /*clear types pages*/
            clearTypesPages();
            /*set pagination buttons*/
            for (var j = 1; j <= pageCount; j++) {
                var pageElementLi = document.createElement("li");
                var pageElementA = document.createElement("a");
                pageElementA.href = "javascript:void(0);";
                pageElementA.innerHTML = j;
                pageElementA.setAttribute("onclick", "setTypesResults(".concat(j.toString(), ",false)"));
                pageElementLi.appendChild(pageElementA);
                typesPages.appendChild(pageElementLi);
            }
        }
    }

}

function setResourcesResults(page, resetPages) {
    var responseObj = JSON.parse(document.getElementById("sparqlQueryResult").innerHTML);
    var bindings = responseObj.results.bindings;
    var countResources = bindings.length;
    var pageCount = Math.ceil(countResources / 5);
    if (pageCount > 0) {
        if (page > pageCount) {
            page = pageCount;
        }
        if (page < 1) {
            page = 1;
        }
        var startElementIdx = (page - 1) * 5;
        var endElementIdx = (startElementIdx + 5 < countResources) ? startElementIdx + 5 : countResources;

        var resourcesListS = document.getElementById("resources_list_s");
        var resourcesListP = document.getElementById("resources_list_p");
        var resourcesListO = document.getElementById("resources_list_o");
        var resourcesPages = document.getElementById("resources_pages");

        clearResourcesResults();
        /*set resources list*/
        for (var i = startElementIdx; i < endElementIdx;) {
            var resourceDivValueS = bindings[i].s.value;
            var resourceTypeS = bindings[i].s.type;
            if (resourceTypeS == 'uri') {
                var resourceButtonNodeS = document.createElement("button");
                resourceButtonNodeS.type = "button";
                resourceButtonNodeS.className = "list-group-item btn btn-default resource_uri triple-result-heigth-fixed";
                resourceButtonNodeS.innerHTML = resourceDivValueS;
                var resourceANodeS = document.createElement("a");
                resourceANodeS.href = resourceDivValueS;
                resourceANodeS.className = "resource_uri";
                resourceANodeS.target = "_blank";
                resourcesListS.appendChild(resourceANodeS);
                resourceANodeS.appendChild(resourceButtonNodeS);
            }
            else {
                var resourceDivNodeS = document.createElement("div");
                resourceDivNodeS.className = "list-group-item triple-result-heigth-fixed";
                resourceDivNodeS.innerHTML = resourceDivValueS;
                resourcesListS.appendChild(resourceDivNodeS);
            }


            var resourceDivValueP = bindings[i].p.value;
            var resourceTypeP = bindings[i].p.type;
            if (resourceTypeP == 'uri') {
                var resourceButtonNodeP = document.createElement("button");
                resourceButtonNodeP.type = "button";
                resourceButtonNodeP.className = "list-group-item btn btn-default resource_uri triple-result-heigth-fixed";
                resourceButtonNodeP.innerHTML = resourceDivValueP;
                var resourceANodeP = document.createElement("a");
                resourceANodeP.href = resourceDivValueP;
                resourceANodeP.className = "resource_uri";
                resourceANodeP.target = "_blank";
                resourcesListP.appendChild(resourceANodeP);
                resourceANodeP.appendChild(resourceButtonNodeP);
            }
            else {
                var resourceDivNodeP = document.createElement("div");
                resourceDivNodeP.className = "list-group-item triple-result-heigth-fixed";
                resourceDivNodeP.innerHTML = resourceDivValueP;
                resourcesListP.appendChild(resourceDivNodeP);
            }

            var resourceDivValueO = bindings[i].o.value;
            var resourceTypeO = bindings[i].o.type;
            if (resourceTypeO == 'uri') {
                var resourceButtonNodeO = document.createElement("button");
                resourceButtonNodeO.type = "button";
                resourceButtonNodeO.className = "list-group-item btn btn-default resource_uri triple-result-heigth-fixed";
                resourceButtonNodeO.innerHTML = resourceDivValueO;
                var resourceANodeO = document.createElement("a");
                resourceANodeO.href = resourceDivValueO;
                resourceANodeO.className = "resource_uri";
                resourceANodeO.target = "_blank";
                resourcesListO.appendChild(resourceANodeO);
                resourceANodeO.appendChild(resourceButtonNodeO);
            }
            else {
                var resourceDivNodeO = document.createElement("div");
                resourceDivNodeO.className = "list-group-item triple-result-heigth-fixed";
                resourceDivNodeO.innerHTML = resourceDivValueO;
                resourcesListO.appendChild(resourceDivNodeO);
            }

            i++;
        }

        if (resetPages) {
            clearResourcesPages();
            /*set pagination buttons*/
            for (var j = 1; j <= pageCount; j++) {
                var pageElementLi = document.createElement("li");
                var pageElementA = document.createElement("a");
                pageElementA.href = "javascript:void(0);";
                pageElementA.innerHTML = j;
                pageElementA.setAttribute("onclick", "setResourcesResults(".concat(j.toString(), ",false)"));
                pageElementLi.appendChild(pageElementA);
                resourcesPages.appendChild(pageElementLi);
            }
        }
    }

}

function clearTypesAndResources() {
    clearTypesPages();
    clearTypesResults();
    document.getElementById("sparqlQueryTypeSubmit").className = "btn";
    document.getElementById("show_json_types").style.display = "none";
    document.getElementById("show_result_types").style.display = "none";
    clearResources();
}

function clearResources() {
    clearResourcesPages();
    clearResourcesResults();
    document.getElementById("sparqlQuerySubmit").className = "btn";
    document.getElementById("show_json_resources").style.display = "none";
    document.getElementById("show_result_resources").style.display = "none";
    document.getElementById("sparqlQuerySubmit").disabled = true;
    document.getElementById("sparqlQuery").innerHTML = "\n\n\nSELECT DISTINCT ?s ?p ?o \nWHERE{\n    ?s ?p ?o . \n    ?s  &lt;http://www.w3.org/1999/02/22-rdf-syntax-ns#type&gt; &lt;&gt;\n}\nLIMIT 50\n\n";
}

function clearResourcesResults() {
    var resourcesListS = document.getElementById("resources_list_s");
    var resourcesListP = document.getElementById("resources_list_p");
    var resourcesListO = document.getElementById("resources_list_o");
    while (resourcesListS.firstChild) {
        resourcesListS.removeChild(resourcesListS.firstChild);
    }
    while (resourcesListP.firstChild) {
        resourcesListP.removeChild(resourcesListP.firstChild);
    }
    while (resourcesListO.firstChild) {
        resourcesListO.removeChild(resourcesListO.firstChild);
    }
}

function clearResourcesPages() {
    var resourcesPages = document.getElementById("resources_pages");
    while (resourcesPages.firstChild) {
        resourcesPages.removeChild(resourcesPages.firstChild);
    }
}

function clearTypesResults() {
    var typesList = document.getElementById("types_list");
    while (typesList.firstChild) {
        typesList.removeChild(typesList.firstChild);
    }
}

function clearTypesPages() {
    var typesPages = document.getElementById("types_pages");
    while (typesPages.firstChild) {
        typesPages.removeChild(typesPages.firstChild);
    }
}

var $root = $('html, body');
function setQueryGraph(button) {
    clearTypesAndResources();
    document.getElementById("sparqlQueryGraph").value = button.innerHTML;
    document.getElementById("sparqlQueryGraph").style.color = "#5cb85c";
    document.getElementById("sparqlQueryTypeGraph").value = button.innerHTML;
    document.getElementById("sparqlQueryTypeGraph").style.color = "#5cb85c";


    $root.animate({
        scrollTop: $('#graphs_pagination').offset().top
    }, 900);

    setTimeout(function () {
        document.getElementById("sparqlQueryTypeSubmit").className = "btn btn-primary";
    }, 1000);

}

function setQueryType(button) {
    clearResources();
    var query = "\n\n\nSELECT DISTINCT ?s ?p ?o \nWHERE{\n    ?s ?p ?o . \n    ?s  &lt;http://www.w3.org/1999/02/22-rdf-syntax-ns#type&gt; ";
    query = query.concat("<b style='color:#5cb85c'>&lt;", button.innerHTML, "&gt;</b>\n}\nLIMIT 50\n\n");
    document.getElementById("sparqlQuery").innerHTML = query;
    document.getElementById("sparqlQueryTypeHidden").value = button.innerHTML;

    $root.animate({
        scrollTop: $('#types_pagination').offset().top
    }, 900);


    setTimeout(function () {
        document.getElementById("sparqlQuerySubmit").className = "btn btn-primary";
    }, 1000);
    document.getElementById("sparqlQuerySubmit").disabled = false;
}

function createCORSRequest(method, url) {
    var xhr = new XMLHttpRequest();
    if ("withCredentials" in xhr) {

        // Check if the XMLHttpRequest object has a "withCredentials" property.
        // "withCredentials" only exists on XMLHTTPRequest2 objects.
        xhr.open(method, url, true);

    } else if (typeof XDomainRequest != "undefined") {

        // Otherwise, check if XDomainRequest.
        // XDomainRequest only exists in IE, and is IE's way of making CORS requests.
        xhr = new XDomainRequest();
        xhr.open(method, url);

    } else {

        // Otherwise, CORS is not supported by the browser.
        xhr = null;

    }
    return xhr;
}

