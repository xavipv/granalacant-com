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
        <?php echo f_getCabeceraHTML("Actas"); ?>
    </head>
    <body>
        <div id="cabecera">
            <!-- Barra de navegacion -->
            <?php include 'menu.php'; ?>
            <!-- Submenu -->
            <ul class="nav submenu">
                <li class="nav-item text-center col-sm-12">
                    <div id="submenu1" class="nav-link">Transformaci&oacute;n</div>
                </li>
            </ul>
        </div>
        <!-- Contenido -->
        <div id="contenedor" class="container" style="width: 90%">
            <div class="row">
                <div id="divcontenido" class="col-sm-12">
                    <br />
                    <h1><a name="inicio"></a>Transformar textos</h1>
                    <p>
                        En esta p&aacute;gina se transformar&aacute;n los textos de la base de datos del formato est&aacute;ndar al formato codificado. 
                        Ejemplos:
                    </p>
                    <pre>       Codificar: &aacute; --> &amp;aacute;          Decodificar: &amp;ntilde; --> &ntilde;</pre>
                    <p>Las tablas y campos a transformar son los siguientes:</p>
                    <hr />
                    <form id="frmtrans">
                        <div class="form-group row">
                            <div class="form-check col-sm-6">
                                <input class="form-check-input" type="checkbox" id="todas" onclick="js_transformarCheck($(this).prop('checked')); if($(this).prop('checked')) { $('#tl').html('todas'); } else { $('#tl').html('ninguna'); }">
                                <label class="form-check-label" for="todas" id="todasl">Elegir tablas (<span id="tl" style="font-weight: bold">ninguna</span>)</label>
                            </div>
                            <div class="form-check col-sm-6">
                                <input class="form-check-input" type="checkbox" id="codificar" onclick="if($(this).prop('checked')) { $('#cl').html('codificar'); } else { $('#cl').html('decodificar'); }">
                                <label class="form-check-label" for="codificar" id="codificarl">Codificaci&oacute;n de datos (<span id="cl" style="font-weight: bold">decodificar</span>)</label>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group row">
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t1" name="t1[]" value="ACTAS_PUNTOS" onclick="js_transformar('t1', $(this).prop('checked'));">
                                <label class="form-check-label" for="t1" id="t1l"><b>ACTAS_PUNTOS</b>&nbsp;<span class="oi oi-bolt"></span></label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t1-1" name="t1[]" value="PUNTO">
                                <label class="form-check-label" for="t1-1">PUNTO</label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t1-2" name="t1[]" value="TITULO">
                                <label class="form-check-label" for="t1-2">TITULO</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t2" name="t2[]" value="ACTAS_TEXTOS" onclick="js_transformar('t2', $(this).prop('checked'));">
                                <label class="form-check-label" for="t2" id="t2l"><b>ACTAS_TEXTOS</b>&nbsp;<span class="oi oi-bolt"></span></label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t2-1" name="t2[]" value="APARTADO">
                                <label class="form-check-label" for="t2-1">APARTADO</label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t2-2" name="t2[]" value="SUBTITULO">
                                <label class="form-check-label" for="t2-2">SUBTITULO</label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t2-3" name="t2[]" value="TEXTO">
                                <label class="form-check-label" for="t2-3">TEXTO</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t3" name="t3[]" value="ADMINISTRACIONES" onclick="js_transformar('t3', $(this).prop('checked'));">
                                <label class="form-check-label" for="t3" id="t3l"><b>ADMINISTRACIONES</b>&nbsp;<span class="oi oi-bolt"></span></label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t3-1" name="t3[]" value="NOMBRE">
                                <label class="form-check-label" for="t3-1">NOMBRE</label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t3-2" name="t3[]" value="VIA">
                                <label class="form-check-label" for="t3-2">VIA</label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t3-3" name="t3[]" value="DIRECCION">
                                <label class="form-check-label" for="t3-3">DIRECCION</label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t3-4" name="t3[]" value="LOCALIDAD">
                                <label class="form-check-label" for="t3-4">LOCALIDAD</label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t3-5" name="t3[]" value="NOTAS">
                                <label class="form-check-label" for="t3-5">NOTAS</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t4" name="t4[]" value="GARAJES" onclick="js_transformar('t4', $(this).prop('checked'));">
                                <label class="form-check-label" for="t4" id="t4l"><b>GARAJES</b>&nbsp;<span class="oi oi-bolt"></span></label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t4-1" name="t4[]" value="NOTAS">
                                <label class="form-check-label" for="t4-1">NOTAS</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t5" name="t5[]" value="JUNTAS" onclick="js_transformar('t5', $(this).prop('checked'));">
                                <label class="form-check-label" for="t5" id="t5l"><b>JUNTAS</b>&nbsp;<span class="oi oi-bolt"></span></label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t5-1" name="t5[]" value="NOTAS">
                                <label class="form-check-label" for="t5-1">NOTAS</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t6" name="t6[]" value="PERSONAS" onclick="js_transformar('t6', $(this).prop('checked'));">
                                <label class="form-check-label" for="t6" id="t6l"><b>PERSONAS</b>&nbsp;<span class="oi oi-bolt"></span></label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t6-1" name="t6[]" value="APELLIDOS">
                                <label class="form-check-label" for="t6-1">APELLIDOS</label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t6-2" name="t6[]" value="NOMBRE">
                                <label class="form-check-label" for="t6-2">NOMBRE</label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t6-3" name="t6[]" value="NOTAS">
                                <label class="form-check-label" for="t6-3">NOTAS</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t7" name="t7[]" value="VOTACIONES" onclick="js_transformar('t7', $(this).prop('checked'));">
                                <label class="form-check-label" for="t7" id="t7l"><b>VOTACIONES</b>&nbsp;<span class="oi oi-bolt"></span></label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t7-1" name="t7[]" value="TEXTO">
                                <label class="form-check-label" for="t7-1">TEXTO</label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t7-2" name="t7[]" value="OPCION1">
                                <label class="form-check-label" for="t7-2">OPCION1</label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t7-3" name="t7[]" value="OPCION2">
                                <label class="form-check-label" for="t7-3">OPCION2</label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t7-4" name="t7[]" value="OPCION3">
                                <label class="form-check-label" for="t7-4">OPCION3</label>
                            </div>
                            <div class="form-check col-sm-2">
                                <input class="form-check-input" type="checkbox" id="t7-5" name="t7[]" value="OPCION4">
                                <label class="form-check-label" for="t7-5">OPCION4</label>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group row">
                            <button type="button" id="botontrans" class="btn btn-success" onclick="xajax_setTransformar(xajax.getFormValues('frmtrans')); $(this).hide()">Transformar campos seleccionados</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="ainicio" class=""><a href="#inicio" title="Ir al inicio" role="button" class="btn btn-outline-secondary"><span class="oi oi-arrow-thick-top"></span></a></div>
        <!-- JavaScript -->
        <?php echo f_getScripts(); ?>
  </body>
</html>