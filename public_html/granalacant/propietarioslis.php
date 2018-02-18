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
        <?php echo f_getCabeceraHTML("Listado de propietarios"); ?>
    </head>
    <body onload="js_calendario(true, false); xajax_getListadoPropietarios(xajax.getFormValues('frmdatos'));">
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
                                <h2 class="col-sm-11">Listado de propietarios</h2>
                                <div class="col-sm-1 text-right">
                                    <button class="btn btn-outline-success" id="imprimir" onclick=""><span class="oi oi-print"></span></button>
                                </div>
                            </div>
                            <div class="form-group row" data-animation="false" data-toggle="tooltip" data-placement="right" data-trigger="hover" title="Datos a filtrar">
                                <div class="col-sm-2 offset-1">
                                    <input type="radio" class="form-check-input" id="completo" name="intervalo" value="1" onclick="js_controlFechas(); xajax_getListadoPropietarios(xajax.getFormValues('frmdatos'));" disabled="disabled" checked="checked">
                                    <label for="completo" class="form-check-label">Intervalo completo</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="radio" class="form-check-input" id="puntuales" name="intervalo" value="0" onclick="js_controlFechas(); xajax_getListadoPropietarios(xajax.getFormValues('frmdatos'));" disabled="disabled">
                                    <label for="puntuales" class="form-check-label">Fechas puntuales</label>
                                </div>
                                
                                <label for="fechaini" class="col-sm-1 col-form-label text-right">Fecha inicial:</label>
                                <div class="col-sm-2"><input type="text" class="form-control calendario" id="fechaini" name="fechaini" value="24-08-1984" onchange="xajax_getListadoPropietarios(xajax.getFormValues('frmdatos'));" placeholder="Fecha inicial" readonly="readonly" disabled="disabled"></div>
                                <label for="fechafin" class="col-sm-1 col-form-label text-right">Fecha final:</label>
                                <div class="col-sm-2"><input type="text" class="form-control calendario" id="fechafin" name="fechafin" value="<?php echo date('d-m-Y'); ?>" onchange="xajax_getListadoPropietarios(xajax.getFormValues('frmdatos'));" placeholder="Fecha final" readonly="readonly" disabled="disabled"></div>
                            </div>
                            <hr />
                            <div class="form-group row" data-animation="false" data-toggle="tooltip" data-placement="right" data-trigger="hover" title="Datos a mostrar">
                                <div class="col-sm-2 offset-1">
                                    <input type="radio" class="form-check-input" id="porapar" name="verpor" value="0" onclick="xajax_getListadoPropietarios(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="porapar" class="form-check-label">Por apartamentos</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="radio" class="form-check-input" id="porpers" name="verpor" value="1" onclick="xajax_getListadoPropietarios(xajax.getFormValues('frmdatos'));">
                                    <label for="porpers" class="form-check-label">Por personas</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="radio" class="form-check-input" id="actuales" name="verque" value="0" onclick="js_controlFechas(); xajax_getListadoPropietarios(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="actuales" class="form-check-label">Solo actuales</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="radio" class="form-check-input" id="bajas" name="verque" value="1" onclick="js_controlFechas(); xajax_getListadoPropietarios(xajax.getFormValues('frmdatos'));">
                                    <label for="bajas" class="form-check-label">Tambien bajas</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="distintos" name="distintos" onclick="xajax_getListadoPropietarios(xajax.getFormValues('frmdatos'));" disabled="disabled">
                                    <label for="distintos" class="form-check-label">Solo distintos</label>
                                </div>
                                <div class="col-sm-1">
                                    <input type="checkbox" class="form-check-input" id="mayusculas" name="mayusculas" onclick="xajax_getListadoPropietarios(xajax.getFormValues('frmdatos'));">
                                    <label for="mayusculas" class="form-check-label">May&uacute;sculas</label>
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
        
        <!-- JavaScript -->
        <?php echo f_getScripts(); ?>
  </body>
</html>
