<?php
$pagina = basename(__FILE__);

// Paginas de '/includes' que se quieren a incluir en esta pagina.
$aIncludes = array('config.inc.php', 'funciones.inc.php', 'funciones.xajax.php');

// Carga las constantes.
(include(dirname(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT')) . "/cgi-bin/includes/constantes.inc.php")) or die("<p>Error al incluir <b>/cgi-bin/includes/constantes.inc.php</b></p>");

$fec = ( filter_input(INPUT_POST, 'fecha') ) ? filter_input(INPUT_POST, 'fecha') : filter_input(INPUT_GET, 'fecha');

if(!$fec) {
    $oActas = new Actas();
    $fec = $oActas->getUltimaActa();
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php echo f_getCabeceraHTML("Actas"); ?>
    </head>
    <body onload="xajax_getActa('<?php echo $fec; ?>')">
        <div id="cabecera">
            <!-- Barra de navegacion -->
            <?php include 'menu.php'; ?>
            <!-- Submenu -->
            <ul class="nav submenu">
                <li class="nav-item text-left col-sm-10">
                    <div id="submenu1" class="nav-link"><?php echo f_getActasAnyos(); ?></div>
                </li>
                <li class="nav-item text-right col-sm-2">
                    <div id="submenu3">
                        <a id="aedit" href="actasedit.php?fecha=<?php echo $fec; ?>" role="button" title="Editar" class="btn btn-outline-success"><span class="oi oi-pencil"></span></a>
                        <button type="button" onclick="" title="Imprimir" class="btn btn-outline-success"><span class="oi oi-print"></span></button>
                    </div>
                </li>
            </ul>
        </div>
        <!-- Contenido -->
        <div id="contenedor" class="container" style="width: 95%">
            <div class="row">
                <div id="divlistado" class="col-sm-1 hidden-ms hidden-xs listado" style=""><?php echo f_getActasListado(); ?></div>
                <div id="contenido" class="col-sm-11">
                    <div id="divcontenido" class="listadowrap"></div>
                </div>
            </div>
        </div>
        <div id="ainicio" class=""><a href="#inicioacta" title="Ir al inicio" role="button" class="btn btn-outline-secondary"><span class="oi oi-arrow-thick-top"></span></a></div>
        <!-- JavaScript -->
        <?php echo f_getScripts(); ?>
  </body>
</html>
