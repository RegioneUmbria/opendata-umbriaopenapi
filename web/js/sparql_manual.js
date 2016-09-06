/**
 * Created by Lorenzo Ranucci on 06/09/2016.
 */

document.getElementById("graphsFormSubmit").addEventListener("click", getGraphs);
document.getElementById("sparqlQuerySubmit").addEventListener("click", executeQuery);
function getGraphs() {
    var xhr = createCORSRequest('GET', 'https://odnt-srv01/sparql?default-graph-uri=http%3A%2F%2Fdati.umbria.it%2Fgraph%2Fattrattor&query=SELECT+DISTINCT+%3Fg%0D%0AWHERE%7B%0D%0A++++GRAPH+%3Fg+%7B%3Fs+a+%3Ft%7D%0D%0A%7D&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on');
    if (!xhr) {
        throw new Error('CORS not supported');
    }
    xhr.onload = function () {
        document.getElementById("graphsFormResult").value = xhr.responseText;
        setGraphsList(1, true);
    };
    xhr.send();

}


function executeQuery() {
    var graph = encodeURI(document.getElementById("sparqlQueryGraph").value);
    var requestUrl = "https://odnt-srv01/sparql?default-graph-uri=".concat(graph, "&query=select+%3Fs+%3Fp+%3Fo+where+%7B%3Fs+%3Fp+%3Fo%7D+limit+1000&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on");
    var xhr = createCORSRequest('GET', requestUrl);
    if (!xhr) {
        throw new Error('CORS not supported');
    }
    xhr.onload = function () {
        document.getElementById("sparqlQueryResult").value = xhr.responseText;
    };
    xhr.send();
}

function setGraphsList(page, resetPages) {
    var responseObj = JSON.parse(document.getElementById("graphsFormResult").value);
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

function setQueryGraph(button) {
    document.getElementById("sparqlQueryGraph").value = button.innerHTML;
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