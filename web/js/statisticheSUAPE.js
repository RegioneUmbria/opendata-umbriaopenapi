/**
 * Created by Lorenzo Franco Ranucci on 13/12/2016.
 */

var endpointSparqlQueryServerSide;

function mainDrawCharts() {
    setControllerUrl();
    setComuneSelectOptions();
}

function setControllerUrl() {
    var currentUrl = window.location.href;
    var tmpUrl = currentUrl.concat("/execute_sparql_query/");
    endpointSparqlQueryServerSide = tmpUrl.replace("statistiche/execute_sparql_query/", "statistiche/SUAPE/execute_sparql_query/");
}

function drawCharts(dataSelectorButtonPressed) {
    if (document.getElementById("evaseDatasetSelector").classList.contains("btn-primary"))
        drawEvaseCharts();
    else if (document.getElementById("tipologieDatasetSelector").classList.contains("btn-primary"))
        drawTipologieCharts();
    else if (document.getElementById("categorieDatasetSelector").classList.contains("btn-primary"))
        drawCategorieCharts();

}

function drawEvaseCharts() {
    drawEvaseAnnotationAndTableCharts();
}

function drawTipologieCharts() {
    drawTipologieAnnotationChart();
    drawTipologiePieChart();
}

function drawCategorieCharts() {
    drawCategorieAnnotationChart();
    drawCategoriePieChart();
}


function drawEvaseAnnotationAndTableCharts() {
    var comune = $("#comuneFilter").val();
    var sparqlQuery =
        "select ?anno ?mese  (SUM(?chiuse3) AS ?chiuse) (SUM(?totali3) AS ?totali)" +
        "where {" +
        "   {select ?anno ?mese  (SUM(?chiuse2) AS ?chiuse3) (SUM(?quantita) AS ?totali3)  where" +
        "       {?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/SUAPE/1> ." +
        "           ?s <http://dati.umbria.it/risorsa/dimensione/anno> ?anno." +
        "               ?s <http://dati.umbria.it/risorsa/dimensione/comune> ?comune." +
        "                   ?s <http://dati.umbria.it/risorsa/dimensione/mese> ?mese." +
        "                       ?s <http://dati.umbria.it/risorsa/dimensione/stato-SUAPE> ?statoURI." +
        "                           ?statoURI rdfs:label ?stato." +
        "           ?s <http://dati.umbria.it/risorsa/misura/quantita> ?quantita." +
        "           BIND (\"0\"^^<http://www.w3.org/2001/XMLSchema#long> AS ?chiuse2)." +
        "           FILTER regex(?comune, \"" + comune + "\", \"i\")." +
        "       }" +
        "       GROUP BY ?anno ?mese" +
        "       ORDER BY  ?anno ?mese}" +
        "   UNION" +
        "   {select ?anno ?mese  (?quantita AS ?chiuse3) ?totali3 where" +
        "       {?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/SUAPE/1> ." +
        "           ?s <http://dati.umbria.it/risorsa/dimensione/anno> ?anno." +
        "               ?s <http://dati.umbria.it/risorsa/dimensione/comune> ?comune." +
        "                   ?s <http://dati.umbria.it/risorsa/dimensione/mese> ?mese." +
        "                       ?s <http://dati.umbria.it/risorsa/dimensione/stato-SUAPE> <http://dati.umbria.it/risorsa/stato-pratica-SUAPE/CP>." +
        "                           ?statoURI rdfs:label ?stato." +
        "       ?s <http://dati.umbria.it/risorsa/misura/quantita> ?quantita." +
        "       BIND (\"0\"^^<http://www.w3.org/2001/XMLSchema#long> AS ?totali3)." +
        "       FILTER regex(?comune, \"" + comune + "\", \"i\")." +
        "   }" +
        "       GROUP BY ?anno ?mese" +
        "       ORDER BY  ?anno ?mese}" +
        " }" +
        " GROUP BY ?anno ?mese" +
        " ORDER BY  ?anno ?mese";

    var queryObj = {query: sparqlQuery};
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Data');
        data.addColumn('number', 'Chiuse positivamente');
        data.addColumn('number', 'Totali');
        var rows = JSON.parse(resp.data).results.bindings;
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var anno = row.anno.value;
            if (anno != '2105') {
                var mese = row.mese.value;
                var chiuse = parseInt(row.chiuse.value);
                var totali = parseInt(row.totali.value);
                var date = new Date(anno, new Date(Date.parse(mese + " 1, 2012")).getMonth());

                data.addRows([
                    [date, chiuse, totali]
                ]);
            }
        }
        drawEvaseAnnotationChart(data);
        drawEvaseTableChart(data);
    }, "json");
}
function drawEvaseAnnotationChart(data) {
    var evaseChartOptions = {
        dateFormat: 'MMMM, yyyy',
        fill: 10,
        legendPosition: 'newRow',
        thickness: 2
    };
    var evase_chart = new google.visualization.AnnotationChart(document.getElementById('chart_div_1'));
    evase_chart.draw(data, evaseChartOptions);
}

function drawEvaseTableChart(data) {
    var evaseChartOptions = {
        sortColumn: 0,
        page: "enable",
        pageSize: 20,
        width: "100%"
    };
    var evase_chart = new google.visualization.Table(document.getElementById('chart_div_2'));
    evase_chart.draw(data, evaseChartOptions);
}

function drawTipologieAnnotationChart() {
    var comune = $("#comuneFilter").val();
    var sparqlQueryTipologie = "SELECT ?tipologia (SUM(?quantita) AS ?quantita)" +
        "WHERE{" +
        "     ?pratiche <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/SUAPE/2> ." +
        "     ?pratiche <http://dati.umbria.it/risorsa/dimensione/tipologia_SUAPE> ?tipologia." +
        "     ?pratiche <http://dati.umbria.it/risorsa/misura/quantita> ?quantita." +
        "     ?pratiche <http://dati.umbria.it/risorsa/dimensione/comune> ?comune." +
        "     FILTER regex(?comune, \"" + comune + "\", \"i\")." +
        "}" +
        "GROUP BY ?tipologia " +
        "ORDER BY DESC (?quantita) ";

    var queryObj = {query: sparqlQueryTipologie};
    /*Get every tipologia value that is tipologia of at least one pratica of selected comune*/
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var tipologieArray = JSON.parse(resp.data).results.bindings;
        var select1 = "";
        var selectSubquery = "";
        var tipologie = [];
        var cntTipologie = 0;

        for (var j = 0; j < tipologieArray.length; j++) {
            var tipologia = tipologieArray[j].tipologia.value;
            var tipologiaForQuery = tipologia.replace(/\W/g, '');
            tipologie[cntTipologie] = tipologia;
            select1 += " SUM(?" + tipologiaForQuery + ") as ?" + tipologiaForQuery;
            selectSubquery += " ?" + tipologiaForQuery;
            cntTipologie++;
        }

        /*Make a subquery for each tipologia. */
        var allSubqueries = "";
        for (var i = 0; i < tipologie.length; i++) {
            var currentSubQuery = "{ SELECT ?anno ?mese " + selectSubquery +
                "\n WHERE " +
                "\n {?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/SUAPE/2>. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/anno> ?anno. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/comune> ?comune. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/mese> ?mese. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/tipologia_SUAPE> ?tipologia. " +
                "\n ?s <http://dati.umbria.it/risorsa/misura/quantita> ?" + tipologie[i].replace(/\W/g, '') + ". " +
                "\n FILTER regex(?comune, \"" + comune + "\", \"i\"). " +
                "\n FILTER regex(?tipologia, \"" + tipologie[i] + "\", \"i\"). " +
                getBindings(tipologie, i) +
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
            var dataPratichePerTipologia = new google.visualization.DataTable();
            dataPratichePerTipologia.addColumn('date', 'Data');
            for (var j = 0; j < tipologie.length; j++) {
                dataPratichePerTipologia.addColumn('number', tipologie[j]);
            }
            var rowsPratichePerTipologia = JSON.parse(resp2.data).results.bindings;
            for (var i = 0; i < rowsPratichePerTipologia.length; i++) {
                var row = rowsPratichePerTipologia[i];
                var date = new Date(row.anno.value, new Date(Date.parse(row.mese.value + " 1, 2012")).getMonth());
                var rowArray = Object.keys(row).map(function (key) {
                    return parseInt(row[key].value);
                });
                rowArray[1] = date;
                rowArray = rowArray.slice(1, rowArray.length);
                dataPratichePerTipologia.addRow(rowArray);
            }

            var tipologieChartOptions = {
                dateFormat: 'MMMM, yyyy',
                fill: 10,
                legendPosition: 'newRow',
                thickness: 2
            };
            var tipologieChart = new google.visualization.AnnotationChart(document.getElementById('chart_div_1'));
            tipologieChart.draw(dataPratichePerTipologia, tipologieChartOptions);
        }, "json");

    }, "json");
}

function drawTipologiePieChart() {
    var comune = $("#comuneFilter").val();
    var anno = $("#annoFilter").val();
    var mese = $("#meseFilter").val();
    var filterAnno = "";
    var filterMese = "";
    if (anno != "all") {
        filterAnno = "FILTER (?anno=" + anno + ").";
    }
    if (mese != "all") {
        filterMese = "FILTER regex(?mese,\"" + mese + "\", \"i\").";
    }

    var sparqlQueryTipologie = "SELECT ?tipologia (SUM(?quantita) AS ?quantita)" +
        " WHERE{" +
        "     ?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/SUAPE/2> ." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/tipologia_SUAPE> ?tipologia." +
        "     ?s <http://dati.umbria.it/risorsa/misura/quantita> ?quantita." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/comune> ?comune." +
        "     FILTER regex(?comune, \"" + comune + "\", \"i\")." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/anno> ?anno." +
        "     " + filterAnno +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/mese> ?mese." +
        "     " + filterMese +
        " }" +
        "GROUP BY ?tipologia " +
        "ORDER BY DESC(?quantita)";
    var queryObj = {query: sparqlQueryTipologie};
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var dataPratichePerTipologia = new google.visualization.DataTable();
        dataPratichePerTipologia.addColumn('string', 'Tipologia');
        dataPratichePerTipologia.addColumn('number', 'Quantità');
        var rowsPratichePerTipologia = JSON.parse(resp.data).results.bindings;
        for (var i = 0; i < rowsPratichePerTipologia.length; i++) {
            var row = rowsPratichePerTipologia[i];
            var tipologiaLabel = row.tipologia.value;
            var tipologiaQuantita = parseInt(row.quantita.value);
            var rowArray = [tipologiaLabel, tipologiaQuantita];
            dataPratichePerTipologia.addRow(rowArray);
        }
        var tipologieChartOptions = {
            pieHole: 0.4,
            backgroundColor: "#EEEEEE",
            chartArea: {left: 250}
        };
        var tipologieChart = new google.visualization.PieChart(document.getElementById('chart_div_2'));
        tipologieChart.draw(dataPratichePerTipologia, tipologieChartOptions);

    }, "json");
}

function drawCategorieAnnotationChart() {
    var comune = $("#comuneFilter").val();
    var sparqlQueryCategorie = "SELECT ?categorie (SUM(?quantita) AS ?quantita)" +
        "WHERE{" +
        "     ?pratiche <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/SUAPE/3> ." +
        "     ?pratiche <http://dati.umbria.it/risorsa/dimensione/categoria_SUAPE> ?categorie." +
        "     ?pratiche <http://dati.umbria.it/risorsa/misura/quantita> ?quantita." +
        "     ?pratiche <http://dati.umbria.it/risorsa/dimensione/comune> ?comune." +
        "     FILTER regex(?comune, \"" + comune + "\", \"i\")." +
        "}" +
        "GROUP BY ?categorie " +
        "ORDER BY DESC (?quantita) ";

    var queryObj = {query: sparqlQueryCategorie};
    /*Get every categoria value that is categoria of at least one pratica of selected comune*/
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var categorieArray = JSON.parse(resp.data).results.bindings;
        var select1 = "";
        var selectSubquery = "";
        var categorie = [];
        var cntCategorie = 0;


        for (var j = 0; j < categorieArray.length; j++) {
            var categoria = categorieArray[j].categorie.value;
            var categoriaForQuery = categoria.replace(/\W/g, '');
            categorie[cntCategorie] = categoria;
            select1 += " SUM(?" + categoriaForQuery + ") as ?" + categoriaForQuery;
            selectSubquery += " ?" + categoriaForQuery;
            cntCategorie++;
        }

        /*Make a subquery for each categoria. */
        var allSubqueries = "";
        for (var i = 0; i < categorie.length; i++) {
            var currentSubQuery = "{ SELECT ?anno ?mese " + selectSubquery +
                "\n WHERE " +
                "\n {?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/SUAPE/3>. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/anno> ?anno. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/comune> ?comune. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/mese> ?mese. " +
                "\n ?s <http://dati.umbria.it/risorsa/dimensione/categoria_SUAPE> ?categoria. " +
                "\n ?s <http://dati.umbria.it/risorsa/misura/quantita> ?" + categorie[i].replace(/\W/g, '') + ". " +
                "\n FILTER regex(?comune, \"" + comune + "\", \"i\"). " +
                "\n FILTER regex(?categoria, \"" + categorie[i].replace(new RegExp("\\(", 'g'), "\\\\(").replace(new RegExp("\\)", 'g'), "\\\\)") + "\", \"i\"). " +
                getBindings(categorie, i) +
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
            var dataPratichePerCategoria = new google.visualization.DataTable();
            dataPratichePerCategoria.addColumn('date', 'Data');
            for (var j = 0; j < categorie.length; j++) {
                dataPratichePerCategoria.addColumn('number', categorie[j]);
            }
            var rowsPratichePerCategoria = JSON.parse(resp2.data).results.bindings;
            for (var i = 0; i < rowsPratichePerCategoria.length; i++) {
                var row = rowsPratichePerCategoria[i];
                var date = new Date(row.anno.value, new Date(Date.parse(row.mese.value + " 1, 2012")).getMonth());
                var rowArray = Object.keys(row).map(function (key) {
                    return parseInt(row[key].value);
                });
                rowArray[1] = date;
                rowArray = rowArray.slice(1, rowArray.length);
                dataPratichePerCategoria.addRow(rowArray);
            }

            var categorieChartOptions = {
                dateFormat: 'MMMM, yyyy',
                fill: 10,
                legendPosition: 'newRow',
                thickness: 2,
                displayAnnotations: true,
                allowHtml: true,
                annotationsWidth: 80
            };
            var categorieChart = new google.visualization.AnnotationChart(document.getElementById('chart_div_1'));
            categorieChart.draw(dataPratichePerCategoria, categorieChartOptions);
        }, "json");
    }, "json");

}

function drawCategoriePieChart() {
    var comune = $("#comuneFilter").val();
    var anno = $("#annoFilter").val();
    var mese = $("#meseFilter").val();
    var filterAnno = "";
    var filterMese = "";
    if (anno != "all") {
        filterAnno = "FILTER (?anno=" + anno + ").";
    }
    if (mese != "all") {
        filterMese = "FILTER regex(?mese,\"" + mese + "\", \"i\").";
    }

    var sparqlQueryCategorie = "SELECT ?categoria (SUM(?quantita) AS ?quantita)" +
        " WHERE{" +
        "     ?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/SUAPE/3> ." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/categoria_SUAPE> ?categoria." +
        "     ?s <http://dati.umbria.it/risorsa/misura/quantita> ?quantita." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/comune> ?comune." +
        "     FILTER regex(?comune, \"" + comune + "\", \"i\")." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/anno> ?anno." +
        "     " + filterAnno +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/mese> ?mese." +
        "     " + filterMese +
        " }" +
        "GROUP BY ?categoria " +
        "ORDER BY DESC (?quantita) ";
    var queryObj = {query: sparqlQueryCategorie};
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var dataPratichePerCategoria = new google.visualization.DataTable();
        dataPratichePerCategoria.addColumn('string', 'Categoria');
        dataPratichePerCategoria.addColumn('number', 'Quantità');
        var rowsPratichePerCategoria = JSON.parse(resp.data).results.bindings;
        for (var i = 0; i < rowsPratichePerCategoria.length; i++) {
            var row = rowsPratichePerCategoria[i];
            var categoriaLabel = row.categoria.value;
            var categoriaQuantita = parseInt(row.quantita.value);
            var rowArray = [categoriaLabel, categoriaQuantita];
            dataPratichePerCategoria.addRow(rowArray);
        }
        var categorieChartOptions = {
            pieHole: 0.4,
            backgroundColor: "#EEEEEE",
            chartArea: {left: 250}
        };
        var categorieChart = new google.visualization.PieChart(document.getElementById('chart_div_2'));
        categorieChart.draw(dataPratichePerCategoria, categorieChartOptions);

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

function setComuneSelectOptions() {
    var sparqlQuery = "select distinct ?comune" +
        " WHERE{" +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/comune> ?comune." +
        " }" +
        "ORDER BY  (?comune) ";
    var queryObj = {query: sparqlQuery};
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var rows = JSON.parse(resp.data).results.bindings;
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var selected = "";
            if (row.comune.value == "GUBBIO") {
                selected = "selected";
            }
            $("#comuneFilter").html($("#comuneFilter").html() + " <option value=\"" + row.comune.value + "\" " + selected + " >" + row.comune.value + "</option>");
        }
        google.charts.load('current', {'packages': ['corechart', 'controls', 'annotationchart', 'table']});
        google.charts.setOnLoadCallback(drawCharts);
    });
}

function setAnnoSelectOptions() {
    $("#annoFilter").html("<option value=\"all\" selected>Tutti</option>");
    var datasetId;
    if (document.getElementById("tipologieDatasetSelector").classList.contains("btn-primary")) {
        datasetId = "2";
    }
    else if (document.getElementById("categorieDatasetSelector").classList.contains("btn-primary")) {
        datasetId = "3";
    }
    else {
        return;
    }
    var comune = $("#comuneFilter").val();
    var sparqlQuery = "SELECT DISTINCT ?anno" +
        " WHERE{" +
        "     ?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/SUAPE/" + datasetId + "> ." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/comune> ?comune." +
        "     FILTER regex(?comune, \"" + comune + "\", \"i\")." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/anno> ?anno." +
        "}" +
        "ORDER BY ?anno";
    var queryObj = {query: sparqlQuery};
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var rows = JSON.parse(resp.data).results.bindings;
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var anno = row.anno.value;
            if (anno != '2105') {
                $("#annoFilter").html($("#annoFilter").html() + " <option value=\"" + anno + "\">" + anno + "</option>");
            }
        }
    }, "json");
}

function setMeseSelectOptions() {
    $("#meseFilter").html("<option value=\"all\" selected>Tutti</option>");
    var datasetId;
    if (document.getElementById("tipologieDatasetSelector").classList.contains("btn-primary")) {
        datasetId = "2";
    }
    else if (document.getElementById("categorieDatasetSelector").classList.contains("btn-primary")) {
        datasetId = "3";
    }
    else {
        return;
    }
    var comune = $("#comuneFilter").val();
    var sparqlQuery = "SELECT DISTINCT ?mese" +
        " WHERE{" +
        "     ?s <http://purl.org/linked-data/cube#dataSet> <http://dati.umbria.it/risorsa/dataset/SUAPE/" + datasetId + "> ." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/comune> ?comune." +
        "     FILTER regex(?comune, \"" + comune + "\", \"i\")." +
        "     ?s <http://dati.umbria.it/risorsa/dimensione/mese> ?mese." +
        "}" +
        "ORDER BY ?mese";

    var queryObj = {query: sparqlQuery};
    $.post(endpointSparqlQueryServerSide, queryObj, function (resp, textStatus) {
        var rows = JSON.parse(resp.data).results.bindings;
        var months = [];
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            months[i] = row.mese.value;
        }
        var allMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        for (var j = 0; j < 12; j++) {
            if ($.inArray(allMonths[j], months) >= 0) {
                $("#meseFilter").html($("#meseFilter").html() + " <option value=\"" + allMonths[j] + "\">" + allMonths[j] + "</option>");
            }
        }
    }, "json");
}


function datasetSelectorChange(buttonPressed) {
    datasetSelectorChangeColor(buttonPressed);
    changeChartDescription(buttonPressed);
    showCorrectFilters(buttonPressed);
    drawCharts();
}

function showCorrectFilters(buttonPressed) {
    var buttonId = buttonPressed.id;
    if (buttonId == 'tipologieDatasetSelector' || buttonId == 'categorieDatasetSelector') {
        $('#chart2_time_filter').css('visibility', 'visible');
        setAnnoSelectOptions();
        setMeseSelectOptions();
    }
    else {
        $('#chart2_time_filter').css('visibility', 'hidden');
    }
}

function datasetSelectorChangeColor(buttonPressed) {
    $('#evaseDatasetSelector').removeClass("btn-primary active");
    $('#evaseDatasetSelector').addClass("btn-default");
    $('#tipologieDatasetSelector').removeClass("btn-primary ");
    $('#tipologieDatasetSelector').addClass("btn-default");
    $('#categorieDatasetSelector').removeClass("btn-primary");
    $('#categorieDatasetSelector').addClass("btn-default");
    buttonPressed.className = "btn btn-primary";
}

function changeChartDescription(buttonPressed) {

    var buttonId = buttonPressed.id;
    if (buttonId == 'tipologieDatasetSelector') {
        $('#chartDescription').html("Pratiche raggruppate per tipologia delle proposte e filtrate per comune");
    }
    else if (buttonId == 'categorieDatasetSelector') {
        $('#chartDescription').html("Pratiche raggruppate per categoria delle proposte e filtrate per comune");
    }
    else {
        $('#chartDescription').html("Rapporto tra le pratiche evase e le pratiche totali filtrate per comune");
    }
}



