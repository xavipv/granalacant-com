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
        <?php echo f_getCabeceraHTML("Listado de juntas"); ?>
    </head>
    <body onload="xajax_getListadoJunta(xajax.getFormValues('frmdatos'));">
        <div id="cabecera">
            <!-- Barra de navegacion -->
            <?php include 'menu.php'; ?>
            <br />
        </div>
        <!-- Contenido -->
        <div id="contenedor" class="container" style="width: 80%">
            <div class="row">
                <div id="contenido" class="col-sm-12">
                    <div id="divcabecera">
                        <div id="divformulario">
                            <!-- Formulario para los datos -->
                            <form id="frmdatos" onsubmit="return false;">
                                <div class="form-group row">
                                    <h2 class="col-sm-11">Listado de juntas</h2>
                                    <div class="col-sm-1 text-right">
                                        <button class="btn btn-outline-success" id="imprimir" onclick=""><span class="oi oi-print"></span></button>
                                    </div>
                                </div>
                                <div class="form-group row" data-animation="false" data-toggle="tooltip" data-placement="right" data-trigger="hover" title="Orden de los datos">
                                    <label for="tipo" class="col-sm-1 col-form-label text-right">Fecha Junta:</label>
                                    <div class="col-sm-2"><?php echo f_getSelectAgrupadoFechas($oJuntas->getFechas(), 'fecha', '', 'form-control', 'xajax_getListadoJunta(xajax.getFormValues(\'frmdatos\'))', FALSE); ?></div>
                                    <div class="col-sm-2 offset-1">
                                        <input type="checkbox" class="form-check-input" id="mayus" name="mayus"  onclick="xajax_getListadoJunta(xajax.getFormValues('frmdatos'));">
                                        <label for="mayus" class="form-check-label">May&uacute;sculas</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="input-group">
                                            <input type="radio" class="form-check-input" id="ordenA" name="orden" value="0" onclick="$('#sumas').prop('disabled',!$(this).prop('checked')); xajax_getListadoJunta(xajax.getFormValues('frmdatos'));" checked="checked">
                                            <label for="ordenA" class="form-check-label">Apartamentos</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="input-group">
                                            <input type="radio" class="form-check-input" id="ordenP" name="orden" value="1" onclick="$('#sumas').prop('disabled',$(this).prop('checked')); xajax_getListadoJunta(xajax.getFormValues('frmdatos'));">
                                            <label for="ordenP" class="form-check-label">Propietarios</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="input-group">
                                            <input type="radio" class="form-check-input" id="ordenR" name="orden" value="2" onclick="$('#sumas').prop('disabled',$(this).prop('checked')); xajax_getListadoJunta(xajax.getFormValues('frmdatos'));">
                                            <label for="ordenR" class="form-check-label">Representantes</label>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="form-group row" data-animation="false" data-toggle="tooltip" data-placement="right" data-trigger="hover" title="Datos a filtrar">
                                    <div class="col-sm-2">
                                        <input type="checkbox" class="form-check-input" id="urba" name="urba" onclick="xajax_getListadoJunta(xajax.getFormValues('frmdatos'));">
                                        <label for="urba" class="form-check-label">Urbanizacion</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="checkbox" class="form-check-input" id="fase200" name="fase200" onclick="xajax_getListadoJunta(xajax.getFormValues('frmdatos'));" checked="checked">
                                        <label for="fase200" class="form-check-label">Fase 200%</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="checkbox" class="form-check-input" id="fase100" name="fase100" onclick="xajax_getListadoJunta(xajax.getFormValues('frmdatos'));">
                                        <label for="fase100" class="form-check-label">Fase 100%</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="checkbox" class="form-check-input" id="bloque" name="bloque" onclick="xajax_getListadoJunta(xajax.getFormValues('frmdatos'));">
                                        <label for="bloque" class="form-check-label">Bloque</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="checkbox" class="form-check-input" id="votos" name="votos"  onclick="xajax_getListadoJunta(xajax.getFormValues('frmdatos'));" checked="checked">
                                        <label for="votos" class="form-check-label">Votos</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="checkbox" class="form-check-input" id="sumas" name="sumas"  onclick="xajax_getListadoJunta(xajax.getFormValues('frmdatos'));" checked="checked">
                                        <label for="sumas" class="form-check-label">Sumas</label>
                                    </div>
                                </div>                                
                                <hr />
                            </form>
                            <br />
                        </div>
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
