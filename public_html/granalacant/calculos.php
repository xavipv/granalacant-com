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
        <?php echo f_getCabeceraHTML("C&aacute;lculos"); ?>
    </head>
    <body onload="$('#cantidad').focus();">
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
                        <!-- Formulario para los datos -->
                        <form id="frmdatos" onsubmit="return false;"> 
                            <div class="form-group row">
                            <h2 class="col-sm-11">C&aacute;lculo de cuotas mensuales</h2>
                            <div class="col-sm-1 text-right">
                                    <button class="btn btn-outline-success" id="imprimir" onclick="" disabled="disabled"><span class="oi oi-print"></span></button>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2"><b>Cantidades</b></div>
                                <label for="cantidad" class="col-sm-1 col-form-label">A pagar</label>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control solonumeros" id="cantidad" name="cantidad" value="" placeholder="Cantidad" onclick="$(this).select();" onkeyup="js_cuotasMensuales()">
                                        <span class="input-group-addon">€</span>
                                    </div>
                                </div>
                                <label for="meses" class="col-sm-1 col-form-label text-right">Meses</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control solonumeros" id="meses" name="meses" value="1" placeholder="Meses" onclick="$(this).select();" onkeyup="js_cuotasMensuales()">
                                </div>
                            </div>
                            <hr />
                            <div class="form-group row">
                                <div class="col-sm-2"><b>Coeficientes</b></div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="coeur" name="coeur" onclick="if($(this).prop('checked') && ($('#coef200').prop('checked') || $('#coef100').prop('checked'))){ $('#dife').prop('disabled',false); }else{ $('#dife').prop('disabled',true); };js_cuotasMensuales()" checked="checked">
                                    <label for="coeur" class="form-check-label">Urbanizaci&oacute;n 100%</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="coef200" name="coef200" onclick="if($('#coeur').prop('checked') && ($(this).prop('checked') || $('#coef100').prop('checked'))){ $('#dife').prop('disabled',false); }else{ $('#dife').prop('disabled',true); };js_cuotasMensuales()" checked="checked">
                                    <label for="coef200" class="form-check-label">Fase 200%</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="coef100" name="coef100" onclick="if($('#coeur').prop('checked') && ($('#coef200').prop('checked') || $(this).prop('checked'))){ $('#dife').prop('disabled',false); }else{ $('#dife').prop('disabled',true); };js_cuotasMensuales()" checked="checked">
                                    <label for="coef100" class="form-check-label">Fase 100%</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="coeblo" name="coeblo" onclick="js_cuotasMensuales()">
                                    <label for="coeblo" class="form-check-label">Bloque 100%</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="coegar" name="coegar"  onclick="js_cuotasMensuales()">
                                    <label for="coegar" class="form-check-label">Garajes 100%</label>
                                </div>
                            </div>
                            <hr />
                             <div class="form-group row">
                                <div class="col-sm-2"><b>Mostrar</b></div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="codigo" name="codigo"  onclick="js_cuotasMensuales()">
                                    <label for="codigo" class="form-check-label">C&oacute;digo</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="fase" name="fase"  onclick="js_cuotasMensuales()">
                                    <label for="fase" class="form-check-label">Fase</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="metros" name="metros"  onclick="js_cuotasMensuales()" checked="checked">
                                    <label for="metros" class="form-check-label">Metros</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="sumas" name="sumas"  onclick="js_cuotasMensuales()" checked="checked">
                                    <label for="sumas" class="form-check-label">Sumas</label>
                                </div>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="form-check-input" id="dife" name="dife"  onclick="js_cuotasMensuales()" checked="checked">
                                    <label for="dife" class="form-check-label">Diferencias</label>
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
