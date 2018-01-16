/**
 * Created by Lorenzo Franco Ranucci on 13/12/2016.
 */

var endpointSparqlQueryServerSide;

function mainDrawCharts() {
    setControllerUrl();
    setEnteSelectOptions();
}

function setControllerUrl() {
    var currentUrl = window.location.href;
    var tmpUrl = currentUrl.concat("/execute_sparql_query/");
    endpointSparqlQueryServerSide = tmpUrl.replace("statistiche/execute_sparql_query/", "statistiche/SUAPE/execute_sparql_query/");
}

function drawCharts() {
    if (document.getElementById("argomentiDatasetSelector").classList.contains("btn-primary"))
        drawArgomentiCharts();
    else if (document.getElementById("serviziDatasetSelector").classList.contains("btn-primary"))
        drawServiziCharts();

}


function drawArgomentiCharts() {
    drawArgomentiAnnotationChart();
    drawArgomentiPieChart();

}

function drawServiziCharts() {
    drawServiziAnnotationChart();
    drawServiziPieChart();
}


function drawArgomentiAnnotationChart() {
    var ente = $("#enteFilter").val();
    var sparqlQueryArgomenti = "SELECT ?argomento (SUM(?numero) AS ?numero)" +
        "WHERE{" +
        "     ?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/Istanze/0> ." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/istanze-ente> ?ente." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/istanze-dipartimento> ?argomento." +
        "     ?s <http://dati.umbria.it/risorsa/misura/istanze-numero> ?numero." +

        "     FILTER regex(?ente, \"" + ente + "\", \"i\")." +
        "}" +
        "GROUP BY ?argomento " +
        "ORDER BY DESC (?numero) ";

    var queryObj = {query: sparqlQueryArgomenti};
    /*Get every argomento value that is argomento of at least one istanza of selected ente*/
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var argomentiArray = JSON.parse(resp.data).results.bindings;
        var select1 = "";
        var selectSubquery = "";
        var argomenti = [];
        var cntArgomenti = 0;

        for (var j = 0; j < argomentiArray.length; j++) {
            var argomento = argomentiArray[j].argomento.value;
            var argomentoForQuery = argomento.replace(/\W/g, '');
            argomenti[cntArgomenti] = argomento;
            select1 += " SUM(?" + argomentoForQuery + ") as ?" + argomentoForQuery;
            selectSubquery += " ?" + argomentoForQuery;
            cntArgomenti++;
        }

        /*Make a subquery for each tipologia. */
        var allSubqueries = "";
        for (var i = 0; i < argomenti.length; i++) {
            var currentSubQuery = "{ SELECT ?anno ?mese " + selectSubquery +
                "\n WHERE " +
                "\n {?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/Istanze/0>. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/anno> ?anno. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/istanze-ente> ?ente. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/mese> ?mese. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/istanze-dipartimento> ?argomento. " +
                "\n ?s <http://dati.umbria.it/risorsa/misura/istanze-numero> ?" + argomenti[i].replace(/\W/g, '') + ". " +
                "\n FILTER regex(?ente, \"" + ente + "\", \"i\"). " +
                "\n FILTER regex(?argomento, \"" + argomenti[i] + "\", \"i\"). " +
                getBindings(argomenti, i) +
                "\n} " +
                "\n GROUP BY ?anno ?mese " +
                "\n ORDER BY ?anno ?mese }";
            if (i != 0) {
                allSubqueries += "\n UNION \n";
            }
            allSubqueries += currentSubQuery;
        }

        /*UNION of all suqueries*/
        var sparqlQuery = "SELECT ?anno ?mese " + select1 +
            "\n WHERE{\n" + allSubqueries + "}" +
            "\n GROUP BY ?anno ?mese" +
            "\n ORDER BY ?anno ?mese";
        var queryObj = {query: sparqlQuery};
        $.post(endpointSparqlQueryServerSide, queryObj, function (resp2, textStatus) {
            var dataIstanzePerArgomento = new google.visualization.DataTable();
            dataIstanzePerArgomento.addColumn('date', 'Data');
            for (var j = 0; j < argomenti.length; j++) {
                dataIstanzePerArgomento.addColumn('number', argomenti[j]);
            }
            var rowsIstanzePerArgomento = JSON.parse(resp2.data).results.bindings;
            for (var i = 0; i < rowsIstanzePerArgomento.length; i++) {
                var row = rowsIstanzePerArgomento[i];
                var date = new Date(row.anno.value, new Date(Date.parse(row.mese.value + " 1, 2016")).getMonth());
                var rowArray = Object.keys(row).map(function (key) {
                    return parseInt(row[key].value);
                });
                rowArray[1] = date;
                rowArray = rowArray.slice(1, rowArray.length);
                dataIstanzePerArgomento.addRow(rowArray);
            }

            var argomentiChartOptions = {
                dateFormat: 'MMMM, yyyy',
                fill: 10,
                legendPosition: 'newRow',
                thickness: 2
            };
            var argomentiChart = new google.visualization.AnnotationChart(document.getElementById('chart_div_1'));
            argomentiChart.draw(dataIstanzePerArgomento, argomentiChartOptions);
        }, "json");

    }, "json");


}

function drawArgomentiPieChart() {
    var ente = $("#enteFilter").val();
    var anno = $("#annoFilter").val();
    var mese = $("#meseFilter").val();
    var filterAnno = "";
    var filterMese = "";
    if (anno != "all" && anno != null) {
        filterAnno = "FILTER regex(?anno,\"" + anno + "\", \"i\").";
    }
    if (mese != "all" && mese != null) {
        filterMese = "FILTER regex(?mese,\"" + mese + "\", \"i\").";
    }

    var sparqlQueryArgomenti = "SELECT ?argomento (SUM(?quantita) AS ?quantita)" +
        " WHERE{" +
        "\n ?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/Istanze/0>. " +
        "\n ?s <http://dati.umbria.it/risorsa/dimensione/istanze-ente> ?ente. " +
        "\n     FILTER regex(?ente, \"" + ente + "\", \"i\")." +
        "\n ?s <http://dati.umbria.it/risorsa/dimensione/istanze-dipartimento> ?argomento. " +
        "     ?s <http://dati.umbria.it/risorsa/misura/istanze-numero> ?quantita." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/anno> ?anno." +
        "     " + filterAnno +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/mese> ?mese." +
        "     " + filterMese +
        " } " +
        "\n GROUP BY ?argomento " +
        "\n ORDER BY DESC(?quantita)";
    var queryObj = {query: sparqlQueryArgomenti};
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var dataPratichePerArgomento = new google.visualization.DataTable();
        dataPratichePerArgomento.addColumn('string', 'Argomento');
        dataPratichePerArgomento.addColumn('number', 'Quantità');
        var rowsPratichePerArgomento = JSON.parse(resp.data).results.bindings;
        for (var i = 0; i < rowsPratichePerArgomento.length; i++) {
            var row = rowsPratichePerArgomento[i];
            var argomentoLabel = row.argomento.value;
            var argomentoQuantita = parseInt(row.quantita.value);
            var rowArray = [argomentoLabel, argomentoQuantita];
            dataPratichePerArgomento.addRow(rowArray);
        }


        /*Todo remove this workaround*/
        var evase_chart = new google.visualization.Table(document.getElementById('chart_div_2'));
        evase_chart.draw(dataPratichePerArgomento);

        var argomentiChartOptions = {
            pieHole: 0.4,
            backgroundColor: "#EEEEEE",
            chartArea: {left: 250}
        };
        var argomentiChart = new google.visualization.PieChart(document.getElementById('chart_div_2'));
        argomentiChart.draw(dataPratichePerArgomento, argomentiChartOptions);

    }, "json");
}

function drawServiziAnnotationChart() {
    var ente = $("#enteFilter").val();
    var argomento = $("#argomentoFilter").val();
    var sparqlQueryServizi = "SELECT ?servizio (SUM(?numero) AS ?numero)" +
        "WHERE{" +
        "     ?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/Istanze/0> ." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/istanze-ente> ?ente." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/istanze-dipartimento> ?argomento." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/istanze-family> ?servizio." +
        "     ?s <http://dati.umbria.it/risorsa/misura/istanze-numero> ?numero." +
        "     FILTER regex(?argomento, \"" + argomento + "\", \"i\")." +
        "     FILTER regex(?ente, \"" + ente + "\", \"i\")." +
        "}" +
        "GROUP BY ?servizio " +
        "ORDER BY DESC (?numero) ";

    var queryObj = {query: sparqlQueryServizi};
    /*Get every servizio value that is servizio of at least one istanza of selected ente and argomento*/
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var serviziArray = JSON.parse(resp.data).results.bindings;
        var select1 = "";
        var selectSubquery = "";
        var servizi = [];
        var cntServizi = 0;


        for (var j = 0; j < serviziArray.length; j++) {
            var servizio = serviziArray[j].servizio.value;
            var servizioForQuery = servizio.replace(/\W/g, '');
            servizi[cntServizi] = servizio;
            select1 += " SUM(?" + servizioForQuery + ") as ?" + servizioForQuery;
            selectSubquery += " ?" + servizioForQuery;
            cntServizi++;
        }

        /*Make a subquery for each servizio. */
        var allSubqueries = "";
        for (var i = 0; i < servizi.length; i++) {
            var currentSubQuery = "{ SELECT ?anno ?mese " + selectSubquery +
                "\n WHERE " +
                "\n {?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/Istanze/0>. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/anno> ?anno. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/istanze-ente> ?ente. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/mese> ?mese. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/istanze-dipartimento> ?argomento. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/istanze-family> ?servizio." +
                "\n ?s <http://dati.umbria.it/risorsa/misura/istanze-numero> ?" + servizi[i].replace(/\W/g, '') + ". " +
                "\n FILTER regex(?ente, \"" + ente + "\", \"i\"). " +
                "\n FILTER regex(?argomento, \"" + argomento + "\", \"i\"). " +
                "\n FILTER regex(?servizio, \"" + servizi[i] + "\", \"i\"). " +
                getBindings(servizi, i) +
                "\n} " +
                "\n GROUP BY ?anno ?mese " +
                "\n ORDER BY ?anno ?mese }";
            if (i != 0) {
                allSubqueries += "\n UNION \n";
            }
            allSubqueries += currentSubQuery;
        }

        /*UNION of all suqueries*/
        var sparqlQuery = "SELECT ?anno ?mese " + select1 +
            "\n WHERE{\n" + allSubqueries + "}" +
            "\n GROUP BY ?anno ?mese" +
            "\n ORDER BY ?anno ?mese";

        var queryObj = {query: sparqlQuery};
        $.post(endpointSparqlQueryServerSide, queryObj, function (resp2, textStatus) {
            var dataPratichePerServizio = new google.visualization.DataTable();
            dataPratichePerServizio.addColumn('date', 'Data');
            for (var j = 0; j < servizi.length; j++) {
                dataPratichePerServizio.addColumn('number', servizi[j]);
            }
            var rowsPratichePerServizio = JSON.parse(resp2.data).results.bindings;
            for (var i = 0; i < rowsPratichePerServizio.length; i++) {
                var row = rowsPratichePerServizio[i];
                var date = new Date(row.anno.value, new Date(Date.parse(row.mese.value + " 1, 2016")).getMonth());
                var rowArray = Object.keys(row).map(function (key) {
                    return parseInt(row[key].value);
                });
                rowArray[1] = date;
                rowArray = rowArray.slice(1, rowArray.length);
                dataPratichePerServizio.addRow(rowArray);
            }

            var serviziChartOptions = {
                dateFormat: 'MMMM, yyyy',
                fill: 10,
                legendPosition: 'newRow',
                thickness: 2,
                displayAnnotations: true,
                allowHtml: true,
                annotationsWidth: 80
            };
            var serviziChart = new google.visualization.AnnotationChart(document.getElementById('chart_div_1'));
            serviziChart.draw(dataPratichePerServizio, serviziChartOptions);
        }, "json");
    }, "json");

}

function drawServiziPieChart() {
    var ente = $("#enteFilter").val();
    var argomento = $("#argomentoFilter").val();
    var anno = $("#annoFilter").val();
    var mese = $("#meseFilter").val();
    var filterAnno = "";
    var filterMese = "";
    if (anno != "all" && anno != null) {
        filterAnno = "FILTER regex(?anno,\"" + anno + "\", \"i\").";
    }
    if (mese != "all" && mese != null) {
        filterMese = "FILTER regex(?mese,\"" + mese + "\", \"i\").";
    }

    var sparqlQueryServizi = "SELECT ?servizio (SUM(?quantita) AS ?quantita)" +
        " WHERE{" +
        "\n  ?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/Istanze/0>. " +
        "\n  ?s <http://dati.umbria.it/risorsa/dimensione/istanze-ente> ?ente. " +
        "\n  FILTER regex(?ente, \"" + ente + "\", \"i\")." +
        "\n  ?s <http://dati.umbria.it/risorsa/dimensione/istanze-dipartimento> ?argomento. " +
        "\n  FILTER regex(?argomento, \"" + argomento + "\", \"i\"). " +
        "\n  ?s <http://dati.umbria.it/risorsa/dimensione/istanze-family> ?servizio. " +
        "\n  ?s <http://dati.umbria.it/risorsa/misura/istanze-numero> ?quantita." +
        "\n  ?s <http://dati.umbria.it/risorsa/dimensione/anno> ?anno." +
        "\n  " + filterAnno +
        "\n  ?s <http://dati.umbria.it/risorsa/dimensione/mese> ?mese." +
        "\n  " + filterMese +
        "\n } " +
        "\n GROUP BY ?servizio " +
        "\n ORDER BY DESC(?quantita)";
    var queryObj = {query: sparqlQueryServizi};
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var dataPratichePerServizio = new google.visualization.DataTable();
        dataPratichePerServizio.addColumn('string', 'Servizio');
        dataPratichePerServizio.addColumn('number', 'Quantità');
        var rowsPratichePerServizio = JSON.parse(resp.data).results.bindings;
        for (var i = 0; i < rowsPratichePerServizio.length; i++) {
            var row = rowsPratichePerServizio[i];
            var servizioLabel = row.servizio.value;
            var servizioQuantita = parseInt(row.quantita.value);
            var rowArray = [servizioLabel, servizioQuantita];
            dataPratichePerServizio.addRow(rowArray);
        }

        var serviziChartOptions = {
            pieHole: 0.4,
            backgroundColor: "#EEEEEE",
            chartArea: {left: 250}
        };
        var serviziChart = new google.visualization.PieChart(document.getElementById('chart_div_2'));
        serviziChart.draw(dataPratichePerServizio, serviziChartOptions);

    }, "json");
}


/*Return SPARQL bindings to long value 0 for all properties in "properties" except the property at position "j"*/
function getBindings(properties, j) {
    var result = "";
    for (var i = 0; i < properties.length; i++) {
        if (i == j) continue;
        result += "\n BIND (\"0\"^^<http://www.w3.org/2001/XMLSchema#long> AS ?" + properties[i].replace(/\W/g, '') + "). ";
    }
    return result;
}

function setEnteSelectOptions() {
    var sparqlQuery = "select distinct ?ente" +
        " WHERE{" +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/istanze-ente> ?ente." +
        " }" +
        "ORDER BY  (?ente) ";
    var queryObj = {query: sparqlQuery};
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var rows = JSON.parse(resp.data).results.bindings;
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var selected = "";
            $("#enteFilter").html($("#enteFilter").html() + " <option value=\"" + row.ente.value + "\" " + selected + " >" + row.ente.value + "</option>");
        }


        if (document.getElementById("argomentiDatasetSelector").classList.contains("btn-primary")) {
            google.charts.load('current', {'packages': ['corechart', 'controls', 'annotationchart', 'table']});
            google.charts.setOnLoadCallback(drawCharts);
            setAnnoSelectOptions();
        }
        else {
            setArgomentiSelectOptions();
        }
    });
}

function setArgomentiSelectOptions() {
    $("#argomentoFilter").html("");
    var ente = $("#enteFilter").val();
    var sparqlQuery = "select distinct ?argomento \n" +
        "WHERE{" +
        "        ?s <http://dati.umbria.it/risorsa/dimensione/istanze-ente> ?ente." +
        "            ?s <http://dati.umbria.it/risorsa/dimensione/istanze-dipartimento> ?argomento." +
        "            FILTER regex(?ente, \"" + ente + "\", \"i\")." +
        "}" +
        "ORDER BY (?argomento)";
    var queryObj = {query: sparqlQuery};
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var rows = JSON.parse(resp.data).results.bindings;
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var selected = "";
            $("#argomentoFilter").html($("#argomentoFilter").html() + " <option value=\"" + row.argomento.value + "\" " + selected + " >" + row.argomento.value + "</option>");
        }
        google.charts.load('current', {'packages': ['corechart', 'controls', 'annotationchart', 'table']});
        google.charts.setOnLoadCallback(drawCharts);
        setAnnoSelectOptions();
    });

}

function setAnnoSelectOptions() {
    $("#annoFilter").html("<option value=\"all\" selected>Tutti</option>");
    var ente = $("#enteFilter").val();
    var argomento = $("#argomentoFilter").val();
    if (document.getElementById("argomentiDatasetSelector").classList.contains("btn-primary")) {
        argomento = "";
    }

    var sparqlQuery = "SELECT DISTINCT ?anno" +
        " WHERE{" +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/anno> ?anno." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/istanze-ente> ?ente." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/istanze-dipartimento> ?argomento." +
        "     FILTER regex(?ente, \"" + ente + "\", \"i\")." +
        "     FILTER regex(?argomento, \"" + argomento + "\", \"i\")." +
        "}" +
        "ORDER BY ?anno";
    var queryObj = {query: sparqlQuery};
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var rows = JSON.parse(resp.data).results.bindings;
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var anno = row.anno.value;
            $("#annoFilter").html($("#annoFilter").html() + " <option value=\"" + anno + "\">" + anno + "</option>");
        }
        setMeseSelectOptions();
    }, "json");
}

function setMeseSelectOptions() {
    $("#meseFilter").html("<option value=\"all\" selected>Tutti</option>");
    var allMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    for (var j = 1; j <= 12; j++) {
        $("#meseFilter").html($("#meseFilter").html() + " <option value=\"" + j + "\">" + allMonths[j - 1] + "</option>");
    }
}


function datasetSelectorChange(buttonPressed) {
    datasetSelectorChangeColor(buttonPressed);
    changeChartDescription(buttonPressed);
    showCorrectFilters(buttonPressed);
    drawCharts();
}

function showCorrectFilters(buttonPressed) {
    var buttonId = buttonPressed.id;
    setAnnoSelectOptions();
    setMeseSelectOptions();
    if (buttonId == 'serviziDatasetSelector') {
        setArgomentiSelectOptions();
        $('#argomentoFilterContainer').css('visibility', 'visible');

    }
    else {
        $('#argomentoFilterContainer').css('visibility', 'hidden');
    }
}

function datasetSelectorChangeColor(buttonPressed) {
    $('#argomentiDatasetSelector').removeClass("btn-primary active");
    $('#argomentiDatasetSelector').addClass("btn-default");
    $('#serviziDatasetSelector').removeClass("btn-primary");
    $('#serviziDatasetSelector').addClass("btn-default");
    buttonPressed.className = "btn btn-primary";
}

function changeChartDescription(buttonPressed) {

    var buttonId = buttonPressed.id;
    if (buttonId == 'argomentiDatasetSelector') {
        $('#chartDescription').html("Istanze raggruppate per Argomento e filtrate per Ente");
    }
    else if (buttonId == 'serviziDatasetSelector') {
        $('#chartDescription').html("Istanze raggruppate per Servizio e filtrate per Ente e Argomento");
    }

}



