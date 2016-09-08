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
        setGraphsList(1, true);
        document.getElementById("graphsFormSubmit").className = "btn btn-success";
        /*$("#graphs_list").animate({height: "300px"});*/
    };
    xhr.send();

}


function executeTypeQuery() {
    var graph = encodeURI(document.getElementById("sparqlQueryTypeGraph").value);
    var requestUrl = "https://odnt-srv01/sparql?default-graph-uri=".concat(graph, "&query=SELECT+DISTINCT+%3Fo%0D%0AWHERE%7B%0D%0A++++%3Fs+%3Chttp%3A%2F%2Fwww.w3.org%2F1999%2F02%2F22-rdf-syntax-ns%23type%3E+%3Fo%0D%0A%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on");
    var xhr = createCORSRequest('GET', requestUrl);
    if (!xhr) {
        throw new Error('CORS not supported');
    }
    xhr.onload = function () {
        document.getElementById("sparqlQueryTypeResult").innerHTML = JSON.stringify(JSON.parse(xhr.responseText), null, "\t");
        setTypesList(1, true);
        document.getElementById("sparqlQueryTypeSubmit").className = "btn btn-success";
    };
    xhr.send();
}
function executeQuery() {
    var graph = encodeURI(document.getElementById("sparqlQueryGraph").value);
    var type = encodeURI(document.getElementById("sparqlQueryTypeHidden").value);
    var requestUrl = "https://odnt-srv01/sparql?default-graph-uri=".concat(graph, "&query=SELECT+DISTINCT+%3Fs+%3Fp+%3Fo+WHERE%7B+%3Fs+%3Fp+%3Fo+.+%3Fs+%3Chttp%3A%2F%2Fwww.w3.org%2F1999%2F02%2F22-rdf-syntax-ns%23type%3E+%3C", type, "%3E+%7DLIMIT+200&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on");
    var xhr = createCORSRequest('GET', requestUrl);
    if (!xhr) {
        throw new Error('CORS not supported');
    }
    xhr.onload = function () {
        document.getElementById("sparqlQueryResult").innerHTML = JSON.stringify(JSON.parse(xhr.responseText), null, "\t");
        setResourcesList(1, true);
        document.getElementById("sparqlQuerySubmit").className = "btn btn-success";
    };
    xhr.send();
}

function setGraphsList(page, resetPages) {
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
                pageElementA.setAttribute("onclick", "setGraphsList(".concat(j.toString(), ",false)"));
                pageElementLi.appendChild(pageElementA);
                graphsPages.appendChild(pageElementLi);
            }
        }
    }

}

function setTypesList(page, resetPages) {
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
        /*clear graphs list*/
        while (typesList.firstChild) {
            typesList.removeChild(typesList.firstChild);
        }
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
            /*clear graphs pages*/
            while (typesPages.firstChild) {
                typesPages.removeChild(typesPages.firstChild);
            }
            /*set pagination buttons*/
            for (var j = 1; j <= pageCount; j++) {
                var pageElementLi = document.createElement("li");
                var pageElementA = document.createElement("a");
                pageElementA.href = "javascript:void(0);";
                pageElementA.innerHTML = j;
                pageElementA.setAttribute("onclick", "setTypesList(".concat(j.toString(), ",false)"));
                pageElementLi.appendChild(pageElementA);
                typesPages.appendChild(pageElementLi);
            }
        }
    }

}

function setResourcesList(page, resetPages) {
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
        /*clear graphs list*/
        while (resourcesListS.firstChild) {
            resourcesListS.removeChild(resourcesListS.firstChild);
        }
        while (resourcesListP.firstChild) {
            resourcesListP.removeChild(resourcesListP.firstChild);
        }
        while (resourcesListO.firstChild) {
            resourcesListO.removeChild(resourcesListO.firstChild);
        }

        /*set resources list*/
        for (var i = startElementIdx; i < endElementIdx;) {
            var resourceUriS = bindings[i].s.value;
            var resourceButtonNodeS = document.createElement("button");
            resourceButtonNodeS.type = "button";
            resourceButtonNodeS.className = "list-group-item btn btn-default";
            resourceButtonNodeS.innerHTML = resourceUriS;
            var resourceTypeS = bindings[i].s.type;
            var resourceANodeS = document.createElement("a");
            if (resourceTypeS == 'uri') {
                resourceANodeS.href = resourceUriS;
                resourceANodeS.target = "_blank";
            }
            else {
                resourceANodeS.href = "javascript:void(0);";
            }
            resourcesListS.appendChild(resourceANodeS);
            resourceANodeS.appendChild(resourceButtonNodeS);

            var resourceUriP = bindings[i].p.value;
            var resourceButtonNodeP = document.createElement("button");
            resourceButtonNodeP.type = "button";
            resourceButtonNodeP.className = "list-group-item btn btn-default";
            resourceButtonNodeP.innerHTML = resourceUriP;
            var resourceTypeP = bindings[i].p.type;
            var resourceANodeP = document.createElement("a");
            if (resourceTypeP == 'uri') {
                resourceANodeP.href = resourceUriP;
                resourceANodeP.target = "_blank";
            }
            else {
                resourceANodeP.href = "javascript:void(0);";
            }
            resourcesListP.appendChild(resourceANodeP);
            resourceANodeP.appendChild(resourceButtonNodeP);

            var resourceUriO = bindings[i].o.value;
            var resourceButtonNodeO = document.createElement("button");
            resourceButtonNodeO.type = "button";
            resourceButtonNodeO.className = "list-group-item btn btn-default";
            resourceButtonNodeO.innerHTML = resourceUriO;
            var resourceTypeO = bindings[i].o.type;
            var resourceANodeO = document.createElement("a");
            if (resourceTypeO == 'uri') {
                resourceANodeO.href = resourceUriO;
                resourceANodeO.target = "_blank";
            }
            else {
                resourceANodeO.href = "javascript:void(0);";
            }
            resourcesListO.appendChild(resourceANodeO);
            resourceANodeO.appendChild(resourceButtonNodeO);
            i++;
            //}
        }

        if (resetPages) {
            /*clear graphs pages*/
            while (resourcesPages.firstChild) {
                resourcesPages.removeChild(resourcesPages.firstChild);
            }
            /*set pagination buttons*/
            for (var j = 1; j <= pageCount; j++) {
                var pageElementLi = document.createElement("li");
                var pageElementA = document.createElement("a");
                pageElementA.href = "javascript:void(0);";
                pageElementA.innerHTML = j;
                pageElementA.setAttribute("onclick", "setResourcesList(".concat(j.toString(), ",false)"));
                pageElementLi.appendChild(pageElementA);
                resourcesPages.appendChild(pageElementLi);
            }
        }
    }

}

var $root = $('html, body');
function setQueryGraph(button) {

    document.getElementById("sparqlQueryGraph").value = button.innerHTML;
    document.getElementById("sparqlQueryTypeGraph").value = button.innerHTML;


    $root.animate({
        scrollTop: $('#graphsFormSubmit').offset().top
    }, 900);

    setTimeout(function () {
        document.getElementById("sparqlQueryTypeSubmit").className = "btn btn-primary";
    }, 1000);

}

function setQueryType(button) {
    var query = "SELECT DISTINCT ?s ?p ?o \nWHERE{\n    ?s ?p ?o . \n    ?s  &lt;http://www.w3.org/1999/02/22-rdf-syntax-ns#type&gt; ";
    query = query.concat("&lt;", button.innerHTML, "&gt;\n}\nLIMIT 200");
    document.getElementById("sparqlQuery").innerHTML = query;
    document.getElementById("sparqlQueryTypeHidden").value = button.innerHTML;

    $root.animate({
        scrollTop: $('#sparqlQueryTypeSubmit').offset().top
    }, 900);


    setTimeout(function () {
        document.getElementById("sparqlQuerySubmit").className = "btn btn-primary";
    }, 1000);
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

