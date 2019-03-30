<?php
$pagina = basename(__FILE__);

// Paginas de '/includes' que se quieren a incluir en esta pagina.
$aIncludes = array('config.inc.php', 'funciones.inc.php', 'funciones.xajax.php');

// Carga las constantes.
(include(dirname(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT')) . "/cgi-bin/includes/constantes.inc.php")) or die("<p>Error al incluir <b>/cgi-bin/includes/constantes.inc.php</b></p>");

(require _LIBR_ . '/phplot/phplot.php') or die("<p>Error al incluir la libreria <b>phplot.php</b></p>");

// Tipo de grafico: horizontal (true) o vertical (false).
$v = ( filter_input(INPUT_POST, 'v') ) ? filter_input(INPUT_POST, 'v') : filter_input(INPUT_GET, 'v');

// Obtiene los datos de las deudas.
$oDeudas = new Deudas();
$aDeudas = $oDeudas->getDeudas();   // Todos los datos.
$aF = $oDeudas->getFechas();        // Obtiene las fechas de mayor a menor.
ksort($aF);                         // Ordena las fechas de menor a mayor.
$aFechas = array_values($aF);       // Se queda solo con las fechas en formato ISO.

// Recorre los datos para crear el array de datos para el grafico.
$imax = 0;  // Numero de indices maximo.
foreach ($aDeudas as $aFec) {
    foreach ($aFec as $iApa => $aApa) {
        $apa = $aApa[0] . '-' . $aApa[1] . $aApa[2];
        $deu = $aApa[6];
        $fec = $aApa[7];
        $ind = array_search($fec, $aFechas) + 1;
        $aDatos[$iApa][0] = $apa;
        $aDatos[$iApa][$ind] = $deu;
        $imax = ($ind > $imax) ? $ind : $imax;
    }
}

ksort($aDatos);     // Ordena los datos por numero de apartamento.

// Quita el indice de los arrays.
foreach ($aDatos as $aD) {
    $aGraph[] = $aD;
}

// Calcula el tamaño del grafico.
$ancho = count($aGraph) * 25 * $imax;
$alto = 900;
$incremento = 1000;
$horizontal = (!$v) ? true : false;

$plot = ($horizontal) ? new PHPlot($ancho,$alto) : new PHPlot($alto, $ancho);   
$plot->SetImageBorderType('plain');
$plot->SetDataValues($aGraph);
$plot->SetLegend($aFechas);
$plot->SetPlotType('bars');
$plot->SetShading(0);
$plot->SetTitle(utf8_decode('Evolución de la deuda por apartamento'));



if($horizontal) {
	// Horizontal.
    $plot->SetLegendPosition(0, 0, 'plot', 0, 0, 10, 0);
	$plot->SetDataType('text-data');
	$plot->SetYTitle(utf8_decode('Deuda en euros'));
	$plot->SetYDataLabelPos('plotin');
        $plot->SetYDataLabelAngle(90);
	$plot->SetXTickLabelPos('none');
	$plot->SetXTickPos('none');
        $plot->SetYTickIncrement($incremento);
} else {
	// Vertical.
	$plot->SetDataType('text-data-yx');
	$plot->SetXTitle('Deuda en euros');
	$plot->SetXDataLabelPos('plotin');
	$plot->SetYTickPos('none');
	//$plot->SetXDataLabelAngle(90);
	$plot->SetXTickIncrement($incremento);
}
$plot->DrawGraph();
