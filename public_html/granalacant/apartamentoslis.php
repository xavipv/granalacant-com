<?php
$pagina = basename(__FILE__);

// Paginas de '/includes' que se quieren a incluir en esta pagina.
$aIncludes = array('config.inc.php', 'funciones.inc.php', 'funciones.xajax.php');

// Carga las constantes.
(include(dirname(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT')) . "/cgi-bin/includes/constantes.inc.php")) or die("<p>Error al incluir <b>/cgi-bin/includes/constantes.inc.php</b></p>");
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php echo f_getCabeceraHTML("Listado de apartamentos"); ?>
    </head>
    <body onload="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));">
        <div id="cabecera">
            <!-- Barra de navegacion -->
            <?php include 'menu.php'; ?>
            <br />
        </div>
        <!-- Contenido -->
        <div id="contenedor" class="container" style="width: 80%">
            <div class="row">
                <div id="divcontenido" class="col-sm-12">
                    <div id="divformulario">
                        <!-- Formulario para los datos -->
                        <form id="frmdatos" onsubmit="return false;">
                            <div class="form-group row">
                                <h2 class="col-sm-11">Listado de apartamentos</h2>
                                <div class="col-sm-1 text-right">
                                    <button class="btn btn-outline-success" id="imprimir" onclick=""><span class="oi oi-print"></span></button>
                                </div>
                            </div>
                            <div class="form-group row" data-animation="false" data-toggle="tooltip" data-placement="right" data-trigger="hover" title="Datos a filtrar">
                                <label for="tipo" class="col-sm-1 col-form-label text-right">Tipo:</label>
                                <div class="col-sm-1"><?php echo f_getSelectTipos('contipo', '', 'form-control', 'xajax_getListadoApartamentos(xajax.getFormValues(\'frmdatos\'))', TRUE); ?></div>
                                <label for="tipo" class="col-sm-1 col-form-label text-right">De portal:</label>
                                <div class="col-sm-1"><?php echo f_getSelectPortales('portal1', '1', 'form-control', 'js_rangoPortales();xajax_getListadoApartamentos(xajax.getFormValues(\'frmdatos\'))', FALSE); ?></div>
                                <label for="tipo" class="col-sm-1 col-form-label text-right">A portal:</label>
                                <div class="col-sm-1"><?php echo f_getSelectPortales('portal2', '26', 'form-control', 'js_rangoPortales();xajax_getListadoApartamentos(xajax.getFormValues(\'frmdatos\'))', FALSE); ?></div>
                                <div class="col-sm-2 offset-2">
                                    <input type="checkbox" class="form-check-input" id="congaraje" name="congaraje" onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));">
                                    <label for="congaraje" class="form-check-label">Con garaje</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="conterraza" name="conterraza" onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));">
                                    <label for="conterraza" class="form-check-label">Con terraza</label>
                                </div>
                            </div>
                            <hr />
                            <div class="form-group row" data-animation="false" data-toggle="tooltip" data-placement="right" data-trigger="hover" title="Datos a mostrar">
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="codigo" name="codigo"  onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="codigo" class="form-check-label">C&oacute;digo</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="finca" name="finca"  onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="finca" class="form-check-label">Finca</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="tipo" name="tipo"  onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="tipo" class="form-check-label">Tipo</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="fase" name="fase"  onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="fase" class="form-check-label">Fase</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="metros" name="metros"  onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="metros" class="form-check-label">Metros</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="terraza" name="terraza"  onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="terraza" class="form-check-label">Terraza</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="garajes" name="garajes"  onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="garajes" class="form-check-label">Garajes</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="urban" name="urban"  onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="urban" class="form-check-label">Urban. 100%</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="fase200" name="fase200"  onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="fase200" class="form-check-label">Fase 200%</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="fase100" name="fase100"  onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="fase100" class="form-check-label">Fase 100%</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="bloque" name="bloque"  onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="bloque" class="form-check-label">Bloque 100%</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="sumas" name="sumas"  onclick="xajax_getListadoApartamentos(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="sumas" class="form-check-label">Sumas</label>
                                </div>
                            </div>
                            <hr />
                        </form>
                        <br />
                    </div>
                    <div id="divbusqueda" class="listado"></div>
                </div>
            </div>    
        </div>
        <div id="ainicio" class=""><a href="#inicio" title="Ir al inicio" role="button" class="btn btn-outline-secondary"><span class="oi oi-arrow-thick-top"></span></a></div>
        <!-- JavaScript -->
        <?php echo f_getScripts(); ?>
  </body>
</html>
