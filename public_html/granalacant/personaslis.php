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
        <?php echo f_getCabeceraHTML("Listado de personas"); ?>
    </head>
    <body onload="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));">
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
                                <h2 class="col-sm-11">Listado de personas</h2>
                                <div class="col-sm-1 text-right">
                                    <button class="btn btn-outline-success" id="imprimir" onclick=""><span class="oi oi-print"></span></button>
                                </div>
                            </div>
                            <div class="form-group row" data-animation="false" data-toggle="tooltip" data-placement="right" data-trigger="hover" title="Orden de los datos">
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <input type="radio" class="form-check-input" id="ordenA" name="orden" value="0" onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));" checked="checked">
                                        <label for="ordenA" class="form-check-label">Apellidos</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <input type="radio" class="form-check-input" id="ordenN" name="orden" value="1" onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));">
                                        <label for="ordenN" class="form-check-label">Nombre</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <input type="radio" class="form-check-input" id="ordenC" name="orden" value="2" onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));">
                                        <label for="ordenC" class="form-check-label">C&oacute;digo</label>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <div class="form-group row" data-animation="false" data-toggle="tooltip" data-placement="right" data-trigger="hover" title="Datos a filtrar">
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="propietarios" name="propietarios" onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));">
                                    <label for="propietarios" class="form-check-label">Propietarios</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="concorreo" name="concorreo" onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));">
                                    <label for="concorreo" class="form-check-label">Con correo</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="contelefono" name="contelefono" onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));">
                                    <label for="contelefono" class="form-check-label">Con tel&eacute;fono</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="hombres" name="hombres" onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));">
                                    <label for="hombres" class="form-check-label">Hombres</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="mujeres" name="mujeres"  onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));">
                                    <label for="mujeres" class="form-check-label">Mujeres</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="otros" name="otros"  onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));">
                                    <label for="otros" class="form-check-label">Otros</label>
                                </div>
                            </div>
                            <hr />
                            <div class="form-group row" data-animation="false" data-toggle="tooltip" data-placement="right" data-trigger="hover" title="Datos a mostrar">
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="codigo" name="codigo"  onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="codigo" class="form-check-label">C&oacute;digo</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="correo" name="correo"  onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="correo" class="form-check-label">Correo</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="enviar" name="enviar"  onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="enviar" class="form-check-label">Enviar</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="telefono" name="telefono"  onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="telefono" class="form-check-label">Tel&eacute;fono</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="sexo" name="sexo"  onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));" checked="checked">
                                    <label for="sexo" class="form-check-label">Sexo</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="notas" name="notas"  onclick="xajax_getListadoPersonas(xajax.getFormValues('frmdatos'));">
                                    <label for="notas" class="form-check-label">Notas</label>
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
