<?php

/**
 * Funciones generales.
 */

//--- GENERALES --------------------------------------------------------------//

/**
 * Comprueba si un fichero o una URL existen.
 * Si no existe, muestra un mensaje de error y detiene la ejecucion.
 * 
 * @param string $fich Nombre del fichero o de la URL.
 */
function f_comprobarFichero($fich) {
    if(substr($fich, 0, 4) == 'http') {
        // Es una URL.
        if(get_headers($fich)[0] == 'HTTP/1.1 404 Not Found') {
            die("<div class=\"alert alert-danger\" role=\"alert\">La URL <strong>$fich</strong> no se ha podido cargar.</div>");
        }
    } else {
        // Es un fichero.
        if(!file_exists($fich)) {
            die("<div class=\"alert alert-danger\" role=\"alert\">El fichero <strong>$fich</strong> no se ha podido cargar.</div>");
        }
    }
}

/**
 * Convierte las frases con la primera letra de cada palabra en mayuscula.
 * 
 * @param string $nom Frase a convertir.
 * @return string Frase convertida.
 */
function f_primeraMayuscula($nom) {
    //return ucwords(strtolower($nom)," \t\r\n\f\v-.");
    return mb_convert_case($nom, MB_CASE_TITLE, "UTF-8");
}

/**
 * Crea un select con los valores y textos iguales.
 * 
 * @param array $aDat Array del tipo array(v1, v2...).
 * @param string $id Identificador para el select.
 * @param string $sel Elemento seleccionado.
 * @param string $clase Clase para el select.
 * @param string $onch Funcion para 'onchange'.
 * @param boolean $bla Si es TRUE deja la primera opcion en blanco.
 * @return string Codigo HTML del select.
 */
function f_getSelectSimple($aDat, $id='', $sel='', $clase='', $onch='', $bla=FALSE) {
    $aNew = array();
    foreach ($aDat as $valor) {
        $aNew[$valor] = $valor;
    }
    return f_getSelect($aNew, $id, $sel, $clase, $onch, $bla);
}

/**
 * Crea un select con los valores y textos diferentes.
 * 
 * @param array $aDat Array del tipo array(k1=>v1, k2=>v2...).
 * @param string $id Identificador para el select.
 * @param string $sel Elemento seleccionado.
 * @param string $clase Clase para el select.
 * @param string $onch Funcion para 'onchange'.
 * @param boolean $bla Si es TRUE deja la primera opcion en blanco.
 * @return string Codigo HTML del select.
 */
function f_getSelect($aDat, $id='', $sel='', $clase='', $onch='' , $bla=FALSE) {
    $sI = ($id) ? "id=\"$id\" name=\"$id\"" : "";
    $sC = ($clase) ? "class=\"$clase\"" : "";
    $sA = ($onch) ? "onchange=\"$onch\"" : "";
    $s  = "<select $sI $sC $sA>"; 
    $s .= ($bla) ? "<option value=\"\"></option>" : "";
    foreach ($aDat as $key => $valor) {
        $se = ($sel == $key) ? "selected=\"selected\"" : "";
        $s .= "<option value=\"$key\" $se>$valor</option>";
    }
    return "$s</select>";
}

/**
 * Crea un select agrupado con los valores y textos diferentes.
 * 
 * @param array $aDat Array del tipo array(k1=>array(v1,gr1), k2=>(v2,gr2)...).
 * @param string $id Identificador para el select.
 * @param string $sel Elemento seleccionado.
 * @param string $clase Clase para el select.
 * @param string $onch Funcion para 'onchange'.
 * @param boolean $bla Si es TRUE deja la primera opcion en blanco.
 * @return string Codigo HTML del select.
 */
function f_getSelectAgrupado($aDat, $id='', $sel='', $clase='', $onch='' , $bla=FALSE) {
    $sI = ($id) ? "id=\"$id\" name=\"$id\"" : "";
    $sC = ($clase) ? "class=\"$clase\"" : "";
    $sA = ($onch) ? "onchange=\"$onch\"" : "";
    $s  = "<select $sI $sC $sA>"; 
    $s .= ($bla) ? "<option value=\"\"></option>" : "";
    $g  = "";
    foreach ($aDat as $key => $aVal) {
        if ($aVal[1] != $g) {
            $s .= (!$g) ? "<optgroup label=\"" . $aVal[1] . "\">" : "</optgroup><optgroup label=\"" . $aVal[1] . "\">";
            $g = $aVal[1];
        }
        $se = ($sel == $key) ? "selected=\"selected\"" : "";
        $s .= "<option value=\"$key\" $se>$aVal[0]</option>";
    }
    return "$s</optgroup></select>";
}

//TODO: ELIMINAR
function f_getBotonesPortales($id) {
    $html = "";
    // Botones de los portales.
    for($i=1; $i<=26; $i++) {
        $html .= "<a class=\"btn btn-outline-primary btn-portal\" href=\"#$id$i\" role=\"button\">$i</a>";
        $html .= ($i % 3 == 0) ? "<br />" : "";
    }
    // Boton de cancelar.
    $html .= "<a class=\"btn btn-outline-danger btn-portal\" href=\"#\" role=\"button\">x</a>";
    // Crea el popover
    
}

//--- CABECERA Y PIE DE PAGINA -----------------------------------------------//

/**
 * Obtiene la cabecera de la pagina.
 * 
 * @param string $titulo Titulo de la pagina.
 * @return string Codigo HTML de la cabecera.
 */
function f_getCabeceraHTML($titulo) {
    return f_getMetas($titulo) . f_getEstilos();
}

/**
 * Obtiene las etiqueta META y el título para la página web.
 * 
 * @param string $titulo Nombre de la página.
 * @return string Código HTML de los META.
 */
function f_getMetas($titulo) {
    return "<meta charset=\"utf-8\">
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
            <title>$titulo</title>";
}

/**
 * Obtiene los enlaces a las página CSS para la página web.
 * 
 * @return string Código HTML de los enlaces.
 */
function f_getEstilos() {
    $css = array(
        'bootstrap.min.css', 
        'open-iconic-bootstrap.min.css', 
        'bootstrap-datepicker.min.css', 
        'trumbowyg.min.css', 
        'personal.css'
    );
    $txt = "";
    foreach ($css as $f) {
        $fich = _ULIB_ . "/css/$f";
        f_comprobarFichero($fich);
        $txt .= "<link href=\"" . _ULIB_ . "/css/$f\" rel=\"stylesheet\">";
    }
    return $txt;
}

/**
 * Obtiene las llamadas a los ficheros JavaScript.
 * 
 * @global /xajax $xajax Instancia de XAJAX.
 * @return string Código HTML para los scripts.
 */
function f_getScripts() {
    global $xajax;
    $js = array(
        'jquery-3.2.1.min.js', 
        'tether.min.js', 
        'bootstrap.min.js', 
        'bootstrap-datepicker.min.js', 
        'bootstrap-datepicker.es.min.js', 
        'trumbowyg.min.js', 
        'trumbowyg.es.min.js',
        'trumbowyg.preformatted.xavi.min.js',
        'personal.js'
    );
    $txt = "";
    foreach ($js as $f) {
        $fich = _ULIB_ . "/js/$f";
        f_comprobarFichero($fich);
        $txt .= "<script src=\"$fich\"></script>";
    }
    return $txt . $xajax->printJavascript();
} 

//--- MENU PRINCIPAL ---------------------------------------------------------//

/**
 * Obtiene las acciones a aplicar segun la pagina.
 * 
 * @global boolean $busqueda Si es TRUE se muestra la busqueda, si es FALSE no se muestra.
 * @param string $pagina Nombre de la pagina.
 * @return array Indica si el menu esta activo o no.
 */
function f_getMenuAcciones($pagina) {
    global $busqueda;
    $activo = array('','','','','','','');
    switch ($pagina) {
        // Personas.
        case "personas.php": $activo[1] = "active"; $busqueda = TRUE; break;
        case "personaslis.php": $activo[1] = "active"; break;
        // Apartamentos.
        case "apartamentos.php": $activo[2] = "active"; $busqueda = TRUE; break;
        case "coeficientes.php": $activo[2] = "active"; break;
        case "apartamentoslis.php": $activo[2] = "active"; break;
        // Propietarios.
        case "propietarios.php": $activo[3] = "active"; $busqueda = TRUE; break;
        case "propper.php": $activo[3] = "active"; $busqueda = TRUE; break;
        case "propietarioslis.php": $activo[3] = "active"; break;
        // Juntas.
        case "juntas.php": $activo[4] = "active"; break;
        case "asistentes.php": $activo[4] = "active"; break;
        case "votaciones.php": $activo[4] = "active"; break;
        case "juntaslis.php": $activo[4] = "active"; break;
        // Actas.
        case "actas.php": $activo[5] = "active"; break;
        case "actasbuscar.php": $activo[5] = "active"; break;
        case "actasedit.php": $activo[5] = "active"; break;
        // Otros.
        case "calculos.php": $activo[6] = "active"; break;
        case "transformar.php": $activo[6] = "active"; break;
        // Inicio.
        default: $activo[0] = "active"; break;
    }
    return $activo;
}

//--- PERSONAS ---------------------------------------------------------------//

/**
 * Obtiene el listado de personas encontradas.
 * 
 * @global \Personas $oPers Instancia de Personas.
 * @param string $texto Texto a buscar.
 * @param string $func Nombre de la funcion a ejecutar cuando se haga clic.
 * @return string Codigo HTML del listado de personas encontradas.
 */
function f_buscarPersonas($texto, $func) {
    global $oPers;
    $html = ""; 
    if($texto) {
        $aDatos = $oPers->buscar($texto);
        $num = count($aDatos);
        $per = ($num == 1) ? "persona encontrada" : "personas encontradas";
        $html = "<h3>$num $per</h3><div style=\"display:table\">";
        
        foreach ($aDatos as $cod => $aNom) {
            $html .= "<div style=\"display:table-row\"><div class=\"listado-elem\" style=\"display:table-cell;text-align:right;width:10%\" onclick=\"$func($cod);\">$cod&nbsp;</div><div class=\"listado-elem\" style=\"display:table-cell;width:90%\" onclick=\"$func($cod);\">&nbsp;" . $aNom[0] . " " . $aNom[1] . "</div></div>";
        }
        $html .= "</div>";
    }
    return $html;
}

/**
 * Obtiene las iniciales de las personas.
 * 
 * @global \Personas $oPers Instancia de Personas.
 * @return string Codigo HTML de las iniciales.
 */
function f_getPersonasIniciales() {
    global $oPers;
    $aIni = $oPers->getIniciales();
    $sIni = "";
    foreach ($aIni as $ini) {
        $sIni .= "<a href=\"#ini$ini\">$ini</a>&nbsp;";
    }
    return $sIni;
}

/**
 * Obtiene un listado de todas las personas.
 * 
 * @global \Personas $oPers Instancia de Personas.
 * @return string Codigo HTML del listado de personas.
 */
function f_getPersonasListado() {
    global $pagina, $oPers;
    $aPer = $oPers->getNombresCompletos();
    $sIni = "";
    $sPer = "<div><a name=\"inicio\"></a></div>";
    foreach ($aPer as $cod => $nombre) {
        $ini = substr($nombre, 0, 1);
        $sPer .= ($ini != $sIni) ? "<div class=\"listado-tit\"><a name=\"ini$ini\" href=\"#inicio\">$ini</a></div>" : "";
        $sPer .= "<div class=\"listado-elem\" onclick=\"xajax_reenviarFuncion('$pagina', $cod);\" data-animation=\"false\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"$nombre\">$nombre</div>";
        $sIni = $ini;
    }
    return $sPer;
}

/**
 * Graba los datos de una persona.
 * 
 * @param array $frm Datos del formulario.
 * @return string Mensaje de error o de que todo ha ido bien.
 */
function f_grabarPersona($frm) {
    global $oPers;
    $cod = $frm['codigo'];
    $ape = $frm['apellidos'];
    $nom = $frm['nombre'];
    $cor = $frm['correo'];
    $env = (isset($frm['enviar'])) ? 'S' : 'N';
    $tel = $frm['telefono'];
    $sex = $frm['sexo'];
    $not = $frm['notas'];
    
    $oPer = new Persona($cod);
    $oPer->setApellidos($ape);
    $oPer->setNombre($nom);
    $oPer->setCorreo($cor);
    $oPer->setEnvios($env);
    $oPer->setTelefono($tel);
    $oPer->setSexo($sex);
    $oPer->setNotas($not);
    
    if ($oPer->grabar()) {
        $oPers->actualizar();   // Actualiza las personas.
        $msg = "Los datos se han guardado correctamente.";
    } else {
        $msg = "Error al guardar los datos.";
    }
    return $msg;
}

//--- APARTAMENTOS -----------------------------------------------------------//

/**
 * Busca apartamentos.
 * 
 * @global \Apartamentos $oApars Instancia de la clase apartamentos.
 * @param string $texto Texto a buscar.
 * @param string $func Nombre de la funcion a ejecutar cuando se haga clic.
 * @return string Listado con los apartamentos encontrados.
 */
function f_buscarApartamentos($texto, $func) { 
    global $oApars;
    $html = ""; 
    if($texto) {
        $aDatos = $oApars->buscar($texto);
        $num = count($aDatos);
        $apa = ($num == 1) ? "apartamento encontrado" : "apartamentos encontrados";
        $html = "<h3>$num $apa</h3><div style=\"display:table\">";
        foreach ($aDatos as $cod => $aNom) {
            $html .= "<div style=\"display:table-row\"><div class=\"listado-elem\" style=\"display:table-cell;text-align:right;width:10%\" onclick=\"$func($cod);\">$cod&nbsp;</div><div class=\"listado-elem\" style=\"display:table-cell;width:90%\" onclick=\"$func($cod);\">&nbsp;Portal " . $aNom[0] . "-" . $aNom[1] . $aNom[2] . "</div></div>";
        }
        $html .= "</div>";
    }
    return $html;
} 

function f_getApartamentosIniciales() {
    global $oApars;
    $aIni = $oApars->getPortalesLista();
    $sIni = "";
    foreach ($aIni as $ini) {
        $sIni .= "<a href=\"#ini$ini\">$ini</a>&nbsp;";
    }
    return $sIni;
}

function f_getApartamentosListado() {
    global $pagina, $oApars;
    $aApar = $oApars->getNombresCompletos();
    $aPort = $oApars->getPortales();
    $sIni = "";
    $sApa = "<div><a name=\"inicio\"></a></div>";
    foreach ($aApar as $cod => $nombre) {
        $ini = $aPort[$cod];
        $sApa .= ($ini != $sIni) ? "<div class=\"listado-tit\"><a name=\"ini$ini\" href=\"#inicio\">$ini</a></div>" : "";
        $sApa .= "<div class=\"listado-elem\" onclick=\"xajax_reenviarFuncion('$pagina', $cod);\" title=\"$nombre\">$nombre</div>";
        $sIni = $ini;
    }
    return $sApa;
}

function f_getSelectPortales($id='portal', $sel='', $clase='form-control', $onch='', $bla=FALSE) {
    global $oApars;
    $aDat = $oApars->getPortalesDistintos();
    return f_getSelectSimple($aDat, $id, $sel, $clase, $onch, $bla);
} 

function f_getSelectPisos($id='piso', $sel='', $clase='form-control', $onch='', $bla=FALSE) {
    global $oApars;
    $aDat = $oApars->getPisosDistintos();
    return f_getSelectSimple($aDat, $id, $sel, $clase, $onch, $bla);
}

function f_getSelectLetras($id='letra', $sel='', $clase='form-control', $onch='', $bla=FALSE) {
    global $oApars;
    $aDat = $oApars->getLetrasDistintas();
    return f_getSelectSimple($aDat, $id, $sel, $clase, $onch, $bla);
}

function f_getSelectFases($id='fase', $sel='', $clase='form-control', $onch='', $bla=FALSE) {
    global $oApars;
    $aDat = $oApars->getFasesDistintas();
    return f_getSelectSimple($aDat, $id, $sel, $clase, $onch, $bla);
}

function f_getSelectTipos($id='tipo', $sel='', $clase='form-control', $onch='', $bla=FALSE) { 
    global $oApars;
    $aDat = $oApars->getTiposDistintos();
    return f_getSelectSimple($aDat, $id, $sel, $clase, $onch, $bla);
}

function f_getGarajesPlano($oApa) {
    //$oApa = new Apartamento($cod);
    $aGar = $oApa->getGarajesApartamento();
    $aGarCorte = array(21,42,67,92,117);    // Números de las plazas de garaje donde termina cada fila.
    $aGarDoble = array(67,117);             // Números de las plazas de garaje con separación doble entre filas.
    $iGarFase2 = 68;                        // Primer número de plaza de garaje de la Fase II.
    $iGarMaxim = 117;                       // Número máximo de plazas de garaje.

    $f = "";
    $s = "";
    $gar = "<h4>Aparcamientos</h4>";
    for($p=1; $p<= $iGarMaxim; $p++) {
        if (in_array($p, $aGar)) {
            $s .= "<div class=\"divgarajesel\">$p</div>";
        } elseif ($p < $iGarFase2) {
            $s .= "<div class=\"divgaraje1\">$p</div>";
        } else {
            $s .= "<div class=\"divgaraje2\">$p</div>";
        }
        if (in_array($p, $aGarCorte)) {
            $f = (in_array($p, $aGarDoble)) ? "$s<div class=\"divvacio\">&nbsp;</div><div class=\"divvacio\">&nbsp;</div>$f" : "$s<div class=\"divvacio\">&nbsp;</div>$f";
            $s = "";
        }
    }
    $gar .= "$f<br><br><div class=\"divgaraje1\">&nbsp;</div>&nbsp;Fase I<br><br><div class=\"divgaraje2\">&nbsp;</div>&nbsp;Fase II";
    return $gar;
}

function f_getDatosApartamentos() {
    global $oApars;
    // array(portal,piso,letra,fase,tipo,finca,metros,terraza,coefurb,coeffase,coefbloq)
    $aApars = $oApars->getApartamentos();
    
    $iPortal = 1; $sFase = "I";
    $pap = 0; $pm2 = 0; $pt2 = 0; $pcu = 0; $pcf = 0; $pcr = 0; $pcb = 0;
    $fap = 0; $fm2 = 0; $ft2 = 0; $fcu = 0; $fcf = 0; $fcr = 0; $fcb = 0;
    $tap = 0; $tm2 = 0; $tt2 = 0; $tcu = 0; $tcf = 0; $tcr = 0; $tcb = 0;
    
    $sTabla  = "<a name=\"iniciopagina\"></a><table class=\"table table-sm\" style=\"width:100%\">";
    $sTabla .= "<tr><th class=\"text-center\" colspan=\"10\">Fase $sFase</th></tr>";
    $sTabla .= "<tr><th class=\"text-center\" colspan=\"10\"><a name=\"ini$iPortal\" href=\"#iniciopagina\">Portal $iPortal</a></th></tr><tr><th colspan=\"2\" class=\"text-center\">Apartamento</th><th class=\"text-center\">Superficie</th><th class=\"text-center\">Terraza</th><th class=\"text-center\">Urbanizaci&oacute;n</th><th class=\"text-center\">Fase 200%</th><th class=\"text-center\">Fase 100%</th><th class=\"text-center\">Escalera</th><th>&nbsp;</th><th>&nbsp;</th></tr>";
    
    // Recorre los apartamentos.
    foreach ($aApars as $cod => $aDat) {
        // Comprueba si hay un nuevo portal.
        if($aDat[0] != $iPortal) {
            $sTabla .= f_getCoeficientesNuevoPortal($iPortal, $pap, $pm2, $pt2, $pcu, $pcf, $pcr, $pcb);
            $iPortal = $aDat[0]; $pap = 0; $pm2 = 0; $pt2 = 0; $pcu = 0; $pcf = 0; $pcr = 0; $pcb = 0;
            // Comprueba si hay una nueva fase.
            if($aDat[3] != $sFase) {
                $sTabla .= f_getCoeficientesNuevaFase($sFase, $fap, $fm2, $ft2, $fcu, $fcf, $fcr, $fcb);
                $sFase = $aDat[3]; $fap = 0; $fm2 = 0; $ft2 = 0; $fcu = 0; $fcf = 0; $fcr = 0; $fcb = 0;
                $sTabla .= "<tr class=\"active\"><th class=\"text-center\" colspan=\"10\">Fase $sFase</th></tr>";
            }
            // Cabecera del portal.
            $sTabla .= "<tr class=\"active\"><th class=\"text-center\" colspan=\"10\"><a name=\"ini$iPortal\" href=\"#iniciopagina\">Portal $iPortal</a></th></tr><tr><th colspan=\"2\" class=\"text-center\">Apartamento</th><th class=\"text-center\">Superficie</th><th class=\"text-center\">Terraza</th><th class=\"text-center\">Urbanizaci&oacute;n</th><th class=\"text-center\">Fase 200%</th><th class=\"text-center\">Fase 100%</th><th class=\"text-center\">Escalera</th><th>&nbsp;</th><th>&nbsp;</th></tr>";
        }
        // Realiza las sumas.
        $pap++; $pm2 += $aDat[6]; $pt2 += $aDat[7]; $pcu += $aDat[8]; $pcf += $aDat[9]; $pcr += $aDat[9]/2; $pcb += $aDat[10];
        $fap++; $fm2 += $aDat[6]; $ft2 += $aDat[7]; $fcu += $aDat[8]; $fcf += $aDat[9]; $fcr += $aDat[9]/2; $fcb += $aDat[10];
        $tap++; $tm2 += $aDat[6]; $tt2 += $aDat[7]; $tcu += $aDat[8]; $tcf += $aDat[9]; $tcr += $aDat[9]/2; $tcb += $aDat[10];
        
        // Fila de datos. array(portal,piso,letra,fase,tipo,finca,metros,terraza,coefurb,coeffase,coefbloq)
        $sTabla .= f_getCoeficientesNuevoApartamento($cod, $aDat[0], $aDat[1], $aDat[2], $aDat[4], $aDat[6], $aDat[7], $aDat[8], $aDat[9], $aDat[9]/2, $aDat[10]);
    }
    $sTabla .= f_getCoeficientesNuevoPortal($aDat[0], $pap, $pm2, $pt2, $pcu, $pcf, $pcr, $pcb);
    $sTabla .= f_getCoeficientesNuevaFase($aDat[3], $fap, $fm2, $ft2, $fcu, $fcf, $fcr, $fcb);
    $sTabla .= f_getCoeficientesNuevaUrbanizacion($tap, $tm2, $tt2, $tcu, $tcf, $tcr, $tcb);
    return $sTabla . "</table>";
}

function f_getCoeficientesNuevoApartamento($cod, $por, $pis, $let, $tip, $met, $ter, $cou, $cof, $cor, $cob) {
    $o1 = "onchange=\"$('#boton$cod').prop('disabled',false);\"";
    $o2 = "onkeyup=\"js_sumar($por, this.id);\"";
    $o3 = "onkeyup=\"$('#cr$cod').val(($(this).val()/2).toFixed(5)); js_sumar($por, this.id);\"";
    
    $i1 = f_getCoeficientesInput("me$cod", number_format($met,2,'.',''), "m2", "$o1 $o2", TRUE);
    $i2 = f_getCoeficientesInput("te$cod", number_format($ter,2,'.',''), "m2", "$o1 $o2", TRUE);
    $i3 = f_getCoeficientesInput("cu$cod", number_format($cou,4,'.',''), "%", "$o1 $o2", TRUE);
    $i4 = f_getCoeficientesInput("cf$cod", number_format($cof,4,'.',''), "%", "$o1 $o3", TRUE);
    $i5 = f_getCoeficientesInput("cr$cod", number_format($cor,5,'.',''), "%", "", FALSE);
    $i6 = f_getCoeficientesInput("cb$cod", number_format($cob,2,'.',''), "%", "$o1 $o2", TRUE);
    $b1 = "<button type=\"button\" class=\"btn btn-warning\" onclick=\"xajax_getDatosCoeficientes($cod, $por);$('#boton$cod').prop('disabled',true);\"  title=\"Deshacer\"><span class=\"oi oi-loop-circular\" aria-hidden=\"true\"></span></button>";
    $b2 = "<button type=\"button\" class=\"btn btn-success\" id=\"boton$cod\" onclick=\"xajax_setDatosCoeficientes('$cod', $('#me$cod').val(), $('#te$cod').val(), $('#cu$cod').val(), $('#cf$cod').val(), $('#cb$cod').val());\" disabled=\"disabled\" title=\"Guardar\"><span class=\"oi oi-hard-drive\" aria-hidden=\"true\"></span></button>";
    
    return "<tr><td style=\"width:10%\" class=\"align-middle\"><input type=\"hidden\" id=\"po$cod\" value=\"$por\">Portal $por-$pis$let</td><td style=\"width:4%\" class=\"align-middle\">$tip</td><td style=\"width:13%\">$i1</td><td style=\"width:13%\">$i2</td><td style=\"width:13%\">$i3</td><td style=\"width:13%\">$i4</td><td style=\"width:13%\">$i5</td><td style=\"width:13%\">$i6</td><td style=\"width:4%\">$b1</td><td style=\"width:4%\">$b2</td></tr>"; 
}

function f_getCoeficientesNuevoPortal($por, $pap, $pm2, $pt2, $pcu, $pcf, $pcr, $pcb) {
    $i1 = f_getCoeficientesInput("pme$por", number_format($pm2,2,'.',''), "m2", "", FALSE);
    $i2 = f_getCoeficientesInput("pte$por", number_format($pt2,2,'.',''), "m2", "", FALSE);
    $i3 = f_getCoeficientesInput("pcu$por", number_format($pcu,4,'.',''), "%", "", FALSE);
    $i4 = f_getCoeficientesInput("pcf$por", number_format($pcf,4,'.',''), "%", "", FALSE);
    $i5 = f_getCoeficientesInput("pcr$por", number_format($pcr,5,'.',''), "%", "", FALSE);
    $i6 = f_getCoeficientesInput("pcb$por", number_format($pcb,2,'.',''), "%", "", FALSE);

    return "<tr><th class=\"align-middle\">Portal $por</th><th class=\"align-middle\">$pap</th><th>$i1</th><th>$i2</th><th>$i3</th><th>$i4</th><th>$i5</th><th>$i6</th><th>&nbsp;</th><th>&nbsp;</th></tr>"; 
}

function f_getCoeficientesNuevaFase($fas, $fap, $fm2, $ft2, $fcu, $fcf, $fcr, $fcb) {
    $i1 = f_getCoeficientesInput("fme$fas", number_format($fm2,2,'.',''), "m2", "", FALSE);
    $i2 = f_getCoeficientesInput("fte$fas", number_format($ft2,2,'.',''), "m2", "", FALSE);
    $i3 = f_getCoeficientesInput("fcu$fas", number_format($fcu,4,'.',''), "%", "", FALSE);
    $i4 = f_getCoeficientesInput("fcf$fas", number_format($fcf,4,'.',''), "%", "", FALSE);
    $i5 = f_getCoeficientesInput("fcr$fas", number_format($fcr,5,'.',''), "%", "", FALSE);
    $i6 = f_getCoeficientesInput("fcb$fas", number_format($fcb,2,'.',''), "%", "", FALSE);

    return "<tr><th class=\"align-middle\">Fase $fas</th><th class=\"align-middle\">$fap</th><th>$i1</th><th>$i2</th><th>$i3</th><th>$i4</th><th>$i5</th><th>$i6</th><th>&nbsp;</th><th>&nbsp;</th></tr>";
}

function f_getCoeficientesNuevaUrbanizacion($tap, $tm2, $tt2, $tcu, $tcf, $tcr, $tcb) {
    $i1 = f_getCoeficientesInput("tme", number_format($tm2,2,'.',''), "m2", "", FALSE);
    $i2 = f_getCoeficientesInput("tte", number_format($tt2,2,'.',''), "m2", "", FALSE);
    $i3 = f_getCoeficientesInput("tcu", number_format($tcu,4,'.',''), "%", "", FALSE);
    $i4 = f_getCoeficientesInput("tcf", number_format($tcf,4,'.',''), "%", "", FALSE);
    $i5 = f_getCoeficientesInput("tcr", number_format($tcr,5,'.',''), "%", "", FALSE);
    $i6 = f_getCoeficientesInput("tcb", number_format($tcb,2,'.',''), "%", "", FALSE);
        
    return "<tr><th class=\"align-middle\">Urbanizaci&oacute;n</th><th class=\"align-middle\">$tap</th><th>$i1</th><th>$i2</th><th>$i3</th><th>$i4</th><th>$i5</th><th>$i6</th><th>&nbsp;</th><th>&nbsp;</th></tr>";
}

function f_getCoeficientesInput($id, $val, $sim, $on='', $act=TRUE) {
    //$onchange = ($onch) ? "onchange=\"$onch\"" : "";
    $readonly = (!$act) ? "readonly=\"readonly\"" : "";
    return "<div class=\"input-group\">
                <input type=\"text\" id=\"$id\" name=\"$id\" class=\"form-control solonumeros\" value=\"$val\" $on $readonly>
                <div class=\"input-group-addon\">$sim</div>
            </div>";
}

function f_grabarApartamento($frm) {
    $cod = $frm['codigo'];
    $por = $frm['portal'];
    $pis = $frm['piso'];
    $let = $frm['letra'];
    $fas = $frm['fase'];
    $tip = $frm['tipo'];
    $met = $frm['metros'];
    $ter = $frm['terraza'];
    $cou = $frm['urba'];
    $co2 = $frm['fase200'];
    $cob = $frm['bloque'];

    $oApa = new Apartamento($cod);
    $oApa->setPortal($por);
    $oApa->setPiso($pis);
    $oApa->setLetra($let);
    $oApa->setFase($fas);
    $oApa->setTipo($tip);
    $oApa->setMetros($met);
    $oApa->setTerraza($ter);
    $oApa->setCoeficiente($cou);
    $oApa->setCoeficienteFase($co2);
    $oApa->setCoeficienteBloque($cob);
    
    return ($oApa->grabar()) ? "Los datos del apartamento se han guardado correctamente." : "Error al guardar los datos del apartamento.";
}

function f_grabarCoeficientes($cod, $met, $ter, $cou, $cof, $cob) {
    $oApa = new Apartamento($cod);
    $oApa->setMetros($met);
    $oApa->setTerraza($ter);
    $oApa->setCoeficiente($cou);
    $oApa->setCoeficienteFase($cof);
    $oApa->setCoeficienteBloque($cob);
    return ($oApa->grabar()) ? "Los metros y coeficientes del apartamento se han guardado correctamente." : "Error al guardar los metros y coeficientes del apartamento.";
}

//--- PROPIETARIOS -----------------------------------------------------------//

function f_getPropietarios($cod) { 
    $oPro = new Propiedad($cod);
    $aPro = $oPro->getPopietarios();
    $tabla = "<table class=\"table table-sm col-sm-12\"><tr><th class=\"col-sm-1\">&nbsp;</th><th class=\"col-sm-6\">Nombre del propietario</th><th class=\"col-sm-1\">Orden</th><th class=\"col-sm-2\">Fecha baja</th><th class=\"col-sm-1\">&nbsp;</th><th class=\"col-sm-1\">&nbsp;</th></tr>";
    foreach ($aPro as $per => $aDat) {
        $nom = $aDat[0];
        $dat = $aDat[1];
        $fec = $aDat[2];
        $ord = $aDat[3];
        $tabla .= f_getPropietario($cod, $per, $nom, $dat, $fec, $ord);
    }
    $tabla .= f_getPropietarioNuevo($aPro, $cod);
    
    return "$tabla</table>";
}

function f_getPropietario($cod, $per, $nom, $dat, $fec, $ord) {
    if($dat) {
        // Propietario de baja.
        $clase = "dangercolor";
        $verpe = "btn-outline-danger";
        $boton = "btn-danger";
    } else {
        // Propietario de alta.
        $clase = "";
        $verpe = "btn-outline-info";
        $boton = "btn-success";
    }
    $on = "$('#boton$per').prop('disabled',false)";
    $gr = f_getPropietarioBoton($cod, $per);
    
    return "<tr><td class=\"col-sm-1 align-middle\" title=\"Ver persona $per\"><a class=\"btn $verpe\" href=\"personas.php?persona=$per\" role=\"button\"><span class=\"oi oi-eye\"></span></a></td>
            <td class=\"col-sm-6 align-middle $clase\">$nom</td>
            <td class=\"col-sm-1 align-middle\">" . f_getSelectSimple(array(0,1,2,3,4,5,6,7,8,9), "orden$per", $ord, "form-control $clase", $on) . "</td>
            <td class=\"col-sm-2 align-middle\"><input type=\"text\" id=\"fecha$per\" name=\"fecha$per\" class=\"form-control text-center calendario $clase\" style=\"background-color:transparent\" readonly=\"readonly\" value=\"$fec\"></td>
            <td class=\"col-sm-1 align-middle text-right\"><button type=\"button\" id=\"boton$per\" onclick=\"$gr\" class=\"btn $boton\" title=\"Grabar\" disabled=\"disabled\"><span class=\"oi oi-hard-drive\"></span></button></td>
            <td class=\"col-sm-1 align-middle\"><button type=\"button\" id=\"borra$per\" onclick=\"xajax_eliminarPropietarioPregunta($cod, $per)\" class=\"btn btn-danger\" title=\"Eliminar\"><span class=\"oi oi-trash\"></span></button></td></tr>";
}

function f_getPropietarioNuevo($aPro, $apa) {
    global $oPers;
    
    // Lista de todas las personas menos los propietarios.
    $aPers = $oPers->getPersonasExcluyendo($aPro, TRUE);
    $on = "if($('#nombre0').val() == '') { $('#boton0').prop('disabled',true); } else { $('#boton0').prop('disabled',false); }";
    $gr = f_getPropietarioBoton($apa, 0);
    
    return "<tr><th class=\"col-sm-1\">&nbsp;</th><th class=\"col-sm-7\">Nuevo propietario</th><th class=\"col-sm-1\">Orden</th><th class=\"col-sm-3\">Fecha baja</th><th class=\"col-sm-1\">&nbsp;</th><th class=\"col-sm-1\">&nbsp;</th></tr>
            <tr><td class=\"col-sm-1 align-middle\" title=\"Nuevo propietario\"><div class=\"btn btn-outline-success\"><span class=\"oi oi-person\"></span></div></td>
            <td class=\"col-sm-6 align-middle\">" . f_getSelect($aPers, "nombre0", "", "form-control", $on, TRUE) . "</td>
            <td class=\"col-sm-1 align-middle\">" . f_getSelectSimple(array(0,1,2,3,4,5,6,7,8,9), "orden0", 0, "form-control", $on) . "</td>
            <td class=\"col-sm-2 align-middle\"><input type=\"text\" id=\"fecha0\" name=\"fecha0\" class=\"form-control text-center calendario\" style=\"background-color:transparent\" readonly=\"readonly\" value=\"\"></td>
            <td class=\"col-sm-1 align-middle text-right\"><button type=\"button\" id=\"boton0\" onclick=\"$gr\" class=\"btn btn-outline-success\" title=\"Grabar\" disabled=\"disabled\"><span class=\"oi oi-hard-drive\"></span></button></td>
            <td class=\"col-sm-1 align-middle\">&nbsp;</td></tr>";
}

function f_getPropietarioBoton($apa, $per) {
    $perso = ($per) ? $per : "$('#nombre$per').val()";
    return "xajax_grabarPropietario($apa, $perso, $('#orden$per').val(), $('#fecha$per').val())";
}

function f_grabarPropietario($apa, $per, $ord, $baj) {
    $oPro = new Propiedad($apa);
    $oPro->setPropietario($per, $baj, $ord);
    return $oPro->grabarPropietarios();
}

function f_eliminarPropietario($apa, $per) {
    $oPro = new Propiedad($apa);
    return $oPro->eliminarPropietario($per);
}

//--- PROPIEDADES ------------------------------------------------------------//

function f_getPropietariosListado() {
    global $pagina, $oProps; //$oPers;
    //$aPer = $oPers->getPropietarosNumPropiedadesAltaBaja();
    //$aPer = $oProps->getPropietarosNumPropiedadesAltaBaja();
    $aPer = $oProps->getPropietariosNumeroPropiedades(TRUE);
    $sIni = "";
    $sPer = "<div><a name=\"inicio\"></a></div>";
    foreach ($aPer as $per => $aDatos) {
        $nombre = $aDatos[0];
        $numpro = $aDatos[1];
        $numalt = $aDatos[2];
        $numbaj = $aDatos[3];
        $cla = f_getPropietariosClase($numpro, $numalt, $numbaj);
        $ini = substr($nombre, 0, 1);
        $sPer .= ($ini != $sIni) ? "<div class=\"listado-tit\"><a name=\"ini$ini\" href=\"#inicio\">$ini</a></div>" : "";
        $sPer .= "<div class=\"$cla\" onclick=\"xajax_reenviarFuncion('$pagina', $per);\" data-animation=\"false\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"$nombre ($numpro)\">$nombre</div>";
        $sIni = $ini;
    }
    return $sPer;
}

function f_getPropietariosClase($num, $alt, $baj) {
    if (!$num) {
        // No tiene propiedades.
        $cla = "listado-elem-des";
    } elseif ($alt) {
        // Tiene alguna propiedad de alta.
        $cla = "listado-elem";
    } elseif ($baj) {
        // Tiene propiedades, pero de baja.
        $cla = "listado-elem-baja";
    }
    return $cla;
}

function f_getPropiedades($per) { 
    global $oProps;
    //global $oPers;
    //$aPro = $oPers->getPropiedades($per);
    //$aPro = $oProps->getPropiedades($per);
    $aPro = $oProps->getPropiedadesPersona($per);
    $tabla = "<table class=\"table table-sm col-sm-12\"><tr><th class=\"col-sm-1\">&nbsp;</th><th class=\"col-sm-6\">Propiedad</th><th class=\"col-sm-1\">Orden</th><th class=\"col-sm-2\">Fecha baja</th><th class=\"col-sm-1\">&nbsp;</th><th class=\"col-sm-1\">&nbsp;</th></tr>";
    foreach ($aPro as $apa => $aDat) {
        $nom = $aDat[0];
        $dat = $aDat[1];
        $fec = $aDat[2];
        $ord = $aDat[3];
        $tabla .= f_getPropiedad($per, $apa, $nom, $dat, $fec, $ord);
    }
    $tabla .= f_getPropiedadNueva($aPro, $per);
    
    return "$tabla</table>"; 
}

function f_getPropiedad($per, $apa, $nom, $dat, $fec, $ord) {
    if($dat) {
        // Propiedad de baja.
        $clase = "dangercolor";
        $verpe = "btn-outline-danger";
        $boton = "btn-danger";
    } else {
        // Propiedad de alta.
        $clase = "";
        $verpe = "btn-outline-info";
        $boton = "btn-success";
    }
    $on = "$('#boton$apa').prop('disabled',false)";
    $gr = f_getPropiedadBoton($per, $apa);
    
    return "<tr><td class=\"col-sm-1 align-middle\" title=\"Ver apartamento $apa\"><a class=\"btn $verpe\" href=\"apartamentos.php?apartamento=$apa\" role=\"button\"><span class=\"oi oi-eye\"></span></a></td>
            <td class=\"col-sm-6 align-middle $clase\">$nom</td>
            <td class=\"col-sm-1 align-middle\">" . f_getSelectSimple(array(0,1,2,3,4,5,6,7,8,9), "orden$apa", $ord, "form-control $clase", $on) . "</td>
            <td class=\"col-sm-2 align-middle\"><input type=\"text\" id=\"fecha$apa\" name=\"fecha$apa\" class=\"form-control text-center calendario $clase\" style=\"background-color:transparent\" readonly=\"readonly\" value=\"$fec\"></td>
            <td class=\"col-sm-1 align-middle text-right\"><button type=\"button\" id=\"boton$apa\" onclick=\"$gr\" class=\"btn $boton\" title=\"Grabar\" disabled=\"disabled\"><span class=\"oi oi-hard-drive\"></span></button></td>
            <td class=\"col-sm-1 align-middle\"><button type=\"button\" id=\"borra$apa\" onclick=\"xajax_eliminarPropiedadPregunta($per, $apa)\" class=\"btn btn-danger\" title=\"Eliminar\"><span class=\"oi oi-trash\"></span></button></td></tr>";
}

function f_getPropiedadNueva($aPro, $per) {
    
    
    // Lista de todas las personas menos los propietarios.
    $aApar = f_getApartamentosConPortal($aPro);
    $on = "if($('#nombre0').val() == '') { $('#boton0').prop('disabled',true); } else { $('#boton0').prop('disabled',false); }";
    $gr = f_getPropiedadBoton($per, 0);
    
    return "<tr><th class=\"col-sm-1\">&nbsp;</th><th class=\"col-sm-7\">Nueva propiedad</th><th class=\"col-sm-1\">Orden</th><th class=\"col-sm-3\">Fecha baja</th><th class=\"col-sm-1\">&nbsp;</th><th class=\"col-sm-1\">&nbsp;</th></tr>
            <tr><td class=\"col-sm-1 align-middle\" title=\"Nueva propiedad\"><div class=\"btn btn-outline-success\"><span class=\"oi oi-home\"></span></div></td>
            <td class=\"col-sm-6 align-middle\">" . f_getSelectAgrupado($aApar, "nombre0", "", "form-control", $on, TRUE) . "</td>
            <td class=\"col-sm-1 align-middle\">" . f_getSelectSimple(array(0,1,2,3,4,5,6,7,8,9), "orden0", 0, "form-control", $on) . "</td>
            <td class=\"col-sm-2 align-middle\"><input type=\"text\" id=\"fecha0\" name=\"fecha0\" class=\"form-control text-center calendario\" style=\"background-color:transparent\" readonly=\"readonly\" value=\"\"></td>
            <td class=\"col-sm-1 align-middle text-right\"><button type=\"button\" id=\"boton0\" onclick=\"$gr\" class=\"btn btn-outline-success\" title=\"Grabar\" disabled=\"disabled\"><span class=\"oi oi-hard-drive\"></span></button></td>
            <td class=\"col-sm-1 align-middle\">&nbsp;</td></tr>";
}

function f_getApartamentosConPortal($aPro) {
    global $oApars;
    $aDat  = array();
    $aApar = $oApars->getApartamentosExcluyendo($aPro, FALSE);
    foreach ($aApar as $apa => $aDatos) {
        $aDat[$apa] = array("Portal " . $aDatos[0] . "-" . $aDatos[1] . $aDatos[2], "Portal " . $aDatos[0]);
    }
    return $aDat;
}

function f_getPropiedadBoton($per, $apa) {
    $apart = ($apa) ? $apa : "$('#nombre$apa').val()";
    return "xajax_grabarPropiedad($per, $apart, $('#orden$apa').val(), $('#fecha$apa').val())";
}

function f_grabarPropiedad($per, $apa, $ord, $baj) {
    $oPro = new Propietario($per);
    $oPro->setPropiedad($apa, $baj, $ord);
    return $oPro->grabarPropiedades();
}

function f_eliminarPropiedad($per, $apa) {
    $oPro = new Propietario($per);
    return $oPro->eliminarPropiedad($apa);
}

//--- JUNTAS - DATOS ---------------------------------------------------------//

function f_getJuntasAnyos() {
    $oJuntas = new Juntas();
    $aAnys = $oJuntas->getJuntasAnyos();
    $sAnys = "";
    foreach ($aAnys as $any) {
        $sAnys .= "<a href=\"#any$any\">$any</a>&nbsp;";
    }
    return $sAnys;
}


function f_getJuntasListado() {
    global $pagina;
    $oJuntas = new Juntas();
    $aJuntas = $oJuntas->getJuntas();
    $sJuntas = "<div><a name=\"inicio\"></a></div>";
    $sAny = "";
    
    foreach ($aJuntas as $date => $aJunta) {
        $any   = substr($date, 0, 4);
        $fecha = $aJunta[0];
        $tipo  = $aJunta[1];
        if ($tipo == 'O') {
            $clase = "";
        } elseif ($tipo == "I") {
            $clase = "";
        } else {
            $clase = "";
        }
        $sJuntas .= ($any != $sAny) ? "<div class=\"listado-tit\"><a name=\"any$any\" href=\"#inicio\">$any</a></div>" : "";
        $sJuntas .= "<div class=\"listado-elem $clase\" onclick=\"xajax_reenviarFuncion('$pagina', '$fecha');\">$fecha</div>";
        $sAny = $any;
    } 
    return $sJuntas;
}

function f_getUltimaJunta() {
    global $oJuntas;
    return $oJuntas->getUltimaJunta();
}

/**
 * Obtiene los datos de una Junta existente.
 * 
 * @param date $fecha Fecha de la Junta.
 * @return array Datos obtenidos del tipo array($fec, $ori, $tip, $con, $hor, $pre, $vi1, $vi2, $vo1, $vo2, $vo3, $vo4, $sec, $adm, $not, $tit, $src, $iok)
 */
function f_setJuntaDatosExistente($fecha) {
    // Obtiene los datos de la Junta.
    $oJunta = new Junta($fecha);
    $fec = $fecha;
    $ori = $oJunta->getFecha();
    $tip = $oJunta->getTipo();
    $con = $oJunta->getConvocatoria();
    $hor = $oJunta->getHora();
    $pre = $oJunta->getPresidente();
    $vi1 = $oJunta->getVicepresidente1();
    $vi2 = $oJunta->getVicepresidente2();
    $vo1 = $oJunta->getVocal1();
    $vo2 = $oJunta->getVocal2();
    $vo3 = $oJunta->getVocal3();
    $vo4 = $oJunta->getVocal4();
    $sec = $oJunta->getSecretario();
    $adm = $oJunta->getAdministracion();
    $not = $oJunta->getNotas();
    $tit = "Junta del $fec";
    $src = "$('.calendario').datepicker('destroy');$('#fecha').css('backgroundColor','transparent');$('#boasistentes').show(); $('#botongrabar').show(); $('#boeliminar').show();";
    $iok = 1;
    return array($fec, $ori, $tip, $con, $hor, $pre, $vi1, $vi2, $vo1, $vo2, $vo3, $vo4, $sec, $adm, $not, $tit, $src, $iok);
}

/**
 * Obtiene los datos para una Junta nueva.
 * Si la fecha de la nueva junta no existe, obtiene los datos principales de la junta anterior.
 * 
 * @global Juntas $oJuntas Instancia de Juntas.
 * @param date $fecha Fecha para la nueva Junta.
 * @return array Datos por omision del tipo array($fec, $ori, $tip, $con, $hor, $pre, $vi1, $vi2, $vo1, $vo2, $vo3, $vo4, $sec, $adm, $not, $tit, $src, $iok)
 */
function f_setJuntaDatosNueva($fecha) {
    global $oJuntas;
    
    if ($oJuntas->existeJunta($fecha)) {
        // En la nueva fecha ya existe una Junta.
        $fec = "";
        $ori = "";
        $tip = "";
        $con = "";
        $hor = "";
        $pre = "";
        $vi1 = "";
        $vi2 = "";
        $vo1 = "";
        $vo2 = "";
        $vo3 = "";
        $vo4 = "";
        $sec = "";
        $adm = "";
        $not = "";
        $tit = "";
        $src = "$('#fecha').css('backgroundColor','#F8D7DA'); $('#botongrabar').hide(); $('#boasistentes').hide(); $('#boeliminar').hide();";
        $iok = 0;
    } else {
        // En la nueva fecha no existe ninguna otra Junta. Busca los datos de la Junta anterior.
        $aDatos = $oJuntas->getJuntaAnterior($fecha);
        $fec = $fecha;
        $ori = "";
        $tip = "E";
        $con = "2";
        $hor = "10:00";
        $pre = $aDatos[2];
        $vi1 = $aDatos[3];
        $vi2 = $aDatos[4];
        $vo1 = $aDatos[5];
        $vo2 = $aDatos[6];
        $vo3 = $aDatos[7];
        $vo4 = $aDatos[8];
        $sec = $aDatos[9];
        $adm = $aDatos[10];
        $not = "";
        $tit = "Nueva Junta";
        $src = "$('#fecha').css('backgroundColor','#dff9df'); $('#botongrabar').show(); $('#boasistentes').hide(); $('#boeliminar').hide();";
        $iok = 1;
    }
    return array($fec, $ori, $tip, $con, $hor, $pre, $vi1, $vi2, $vo1, $vo2, $vo3, $vo4, $sec, $adm, $not, $tit, $src, $iok);
}

/**
 * Obtiene el numero de asistentes, representados y votos de una Junta.
 * 
 * @param date $fecha Fecha de la Junta.
 * @return array Sumas del tipo array(sumasis, sumrepr, sumvotos)
 */
function f_getAsistentesSumas($fecha='') {
    $aSumas = array(0, 0, 0);
    if ($fecha) {
        // Se trata de una Junta.
        $oAsis = new Asistentes($fecha);
        $aSumas = $oAsis->getSumas();   // array('prop' => array('propietarios', 'distintos', 'con voto', 'sin voto'), 'repr' => array('representados', 'distintos', 'con voto', 'sin voto'))
        $aProps = $aSumas['prop'];
        $aReprs = $aSumas['repr'];
        $aSumas[0] = $aProps[0] + $aReprs[0];
        $aSumas[1] = $aReprs[0];
        $aSumas[2] = $aProps[2] + $aReprs[2];
    } 
    return $aSumas;
}

function f_grabarJunta($frm) {
    global $oJuntas;
    
    $bok = FALSE;
    $fec = $frm['fecha'];
    $tip = $frm['tipo'];
    $con = $frm['convo'];
    $hor = $frm['hora'];
    $pre = $frm['presi'];
    $vi1 = $frm['vice1'];
    $vi2 = $frm['vice2'];
    $vo1 = $frm['vocal1'];
    $vo2 = $frm['vocal2'];
    $vo3 = $frm['vocal3'];
    $vo4 = $frm['vocal4'];
    $sec = $frm['secre'];
    $adm = $frm['admi'];
    $not = $frm['notas'];
    
    if ($fec) {
        $oJunta = new Junta($fec);
        $oJunta->setTipo($tip);
        $oJunta->setConvocatoria($con);
        $oJunta->setHora($hor);
        $oJunta->setPresidente($pre);
        $oJunta->setVicepresidente1($vi1);
        $oJunta->setVicepresidente2($vi2);
        $oJunta->setVocal1($vo1);
        $oJunta->setVocal2($vo2);
        $oJunta->setVocal3($vo3);
        $oJunta->setVocal4($vo4);
        $oJunta->setSecretario($sec);
        $oJunta->setAdministracion($adm);
        $oJunta->setNotas($not);      
        if ($oJunta->grabar()) {
            $oJuntas->recargar();   // Recarga las Juntas.
            $bok = TRUE;
        }
    }
    return $bok;
}

//--- JUNTA - ASISTENTES -----------------------------------------------------//

function f_getAsistentes($fecha) { 
    global $oApars;
    $oAsist = new Asistentes($fecha);
    
    $aApars = $oApars->getApartamentos();
    $aAsist = $oAsist->getAsistentes();
    $portal = 1;
    $flechaI = "<a href=\"#ini1\"><span class=\"oi oi-data-transfer-upload\"></span></a>";
    $flechaF = "<a href=\"#final\"><span class=\"oi oi-data-transfer-download\"></span></a>";
    $flecha1 = "<a href=\"#ini2\"><span class=\"oi oi-arrow-thick-bottom\" role=\"button\"></span></a>";
    $flecha2 = "<a href=\"#ini1\"><span class=\"oi oi-arrow-thick-top\" role=\"button\"></span></a>";
    $tabla  = "<table class=\"table table-sm\"><tr style=\"background-color:#F5F5F5\"><th class=\"align-middle\">&nbsp;<a name=\"ini$portal\">Portal $portal</a></th><th class=\"align-middle\">Voto</th><th class=\"align-middle\">Repr.</th><th class=\"align-middle\">Asistente a la Junta General</th><th class=\"align-middle text-center\">$flecha1&nbsp;&nbsp;$flecha2</th><th class=\"align-middle text-center\">$flechaF&nbsp;&nbsp;$flechaI</th></tr>";
    
    foreach ($aApars as $apa => $aApartamento) {
        if ($portal != $aApartamento[0]) {
            $portal = $aApartamento[0];
            $p1 = $portal + 1;
            $p2 = $portal - 1;
            $flecha1 = ($portal != 26) ? "<a href=\"#ini$p1\"><span class=\"oi oi-arrow-thick-bottom\" role=\"button\"></span></a>" : "<a href=\"#ini26\"><span class=\"oi oi-arrow-thick-bottom\" role=\"button\"></span></a>";
            $flecha2 = "<a href=\"#ini$p2\"><span class=\"oi oi-arrow-thick-top\" role=\"button\"></span></a>";
            $tabla .= "<tr style=\"background-color:#F5F5F5;\"><th class=\"align-middle\">&nbsp;<a name=\"ini$portal\">Portal $portal</a></th><th class=\"align-middle\">Voto</th><th class=\"align-middle\">Repr.</th><th class=\"align-middle\">Asistente a la Junta General</th><th class=\"align-middle text-center\">$flecha1&nbsp;&nbsp;$flecha2</th><th class=\"align-middle text-center\">$flechaF&nbsp;&nbsp;$flechaI</th></tr>";
        }
        
        if (isset($aAsist[$apa])) {
            $asis = $aAsist[$apa][1];   // Codigo de persona
            $repr = $aAsist[$apa][3];   // Representante S/N
            $voto = $aAsist[$apa][4];   // Voto S/N   
        } else {
            $asis = "";
            $repr = "N";
            $voto = "N";
        }
        
        
        
        $chk1 = ($voto == "S") ? "checked=\"checked\"" : "";
        $chk2 = ($repr == "S") ? "checked=\"checked\"" : "";
        $onCh = "xajax_setAsistenteMulti('$fecha', '$apa', $('#nombre$apa').val(), $('#voto$apa').prop('checked'), $('#repr$apa').prop('checked'), $('#multiples').prop('checked'));";
        $onCl = "if(this.checked){ xajax_getRepresentantes('$fecha',$apa); } else { xajax_getPropietarios('$fecha', $apa); }";
        $sele = f_getPropietariosRepresentantes($apa, $fecha, $asis, $repr, $onCh);
        $tabla .= "<tr><td class=\"align-middle col-sm-1\"><div id=\"apartamento$apa\">&nbsp;" . $aApartamento[0] . "-" . $aApartamento[1] . $aApartamento[2] . "</div></td>
                   <td class=\"align-middle col-sm-1\"><input type=\"checkbox\" id=\"voto$apa\" name=\"voto$apa\" $chk1 onchange=\"$onCh\"></td>
                   <td class=\"align-middle col-sm-1\"><input type=\"checkbox\" id=\"repr$apa\" name=\"repr$apa\" $chk2 onchange=\"$onCh $onCl\" onclick=\"\"></td>
                   <td class=\"align-middle col-sm-7\" id=\"selec$apa\">$sele</td>
                   <td class=\"align-middle col-sm-1 text-center\"><button class=\"btn btn-warning\" type=\"button\" onclick=\"xajax_setAsistente('$fecha','$apa')\" title=\"Deshacer\"><span class=\"oi oi-loop-circular\"></span></button></td>
                   <td class=\"align-middle col-sm-1 text-center\"><button id=\"boton$apa\" class=\"btn btn-success\" type=\"button\" onclick=\"xajax_grabarAsistenteMulti('$fecha', '$apa', $('#nombre$apa').val(), $('#repr$apa').prop('checked'), $('#voto$apa').prop('checked'), $('#multiples').prop('checked'))\" title=\"Guardar\" disabled=\"disabled\"><span class=\"oi oi-hard-drive\"></span></button></td></tr>";
   
        
        }
    return "$tabla</table><a name=\"final\"></a>";
}

function f_getPropietariosRepresentantes($apa, $fecha, $sel='', $repr='N', $onCh='') {
    global $oProps;
    //global $oPers;
    //$aPro = ($repr == 'S') ? $oPers->getRepresentantes($apa, $fecha) : $oPers->getPropietariosEnFecha($apa, $fecha, FALSE);
    //$aPro = ($repr == 'S') ? $oProps->getRepresentantes($apa, $fecha) : $oProps->getPropietariosEnFecha($apa, $fecha, FALSE);
    $aPro = ($repr == 'S') ? $oProps->getRepresentantes($apa, $fecha) : $oProps->getNombresPropietariosApartamentoFecha($apa, $fecha);
    $func = "$('#boton$apa').prop('disabled',false); if(!this.value){ $('#voto$apa').prop('checked',false); } else { $('#voto$apa').prop('checked',true); }; $onCh";

    //return f_getSelect($aPro, "nombre$apa", $sel, "form-control", $func, TRUE);
    return f_getSelect($aPro, "nombre$apa", $sel, "form-control", $func, TRUE);
}

//--- VOTACIONES -------------------------------------------------------------//

/**
 * Obtiene una lista con los años en los que ha habido votaciones.
 * 
 * @return string Lista con los años de las votaciones.
 */
function f_getVotacionesAnyos() {
    $oVots = new Votaciones();
    $aAnys = $oVots->getVotacionesAnyos();
    $sAnys = "";
    foreach ($aAnys as $any) {
        $sAnys .= "<a href=\"#any$any\">$any</a>&nbsp;";
    }
    return $sAnys;
}

/**
 * Obtiene un listado con las fechas de todas las botaciones que ha habido.
 * 
 * @global string $pagina Nombre de pagina.
 * @return string Codigo HTML con las fechas de la votaciones.
 */
function f_getVotacionesListado() {
    global $pagina;
    $oVots = new Votaciones();
    $aVots = $oVots->getVotaciones();
    $sVots = "<div><a name=\"inicio\"></a></div>";
    $sAny = "";
    $dFec = "";
    foreach ($aVots as $date => $aVota) {
        $any   = substr($date, 0, 4);
        $sVots .= ($any != $sAny) ? "<div class=\"listado-tit\"><a name=\"any$any\" href=\"#inicio\">$any</a></div>" : "";
        foreach ($aVota as $numvo => $fecha) {
            $sVots .= ($date != $dFec) ? "<div class=\"\">$fecha</div>" : "";
            $sVots .= "<div class=\"listado-elem\" onclick=\"xajax_reenviarFuncion('$pagina', '$fecha', '$numvo');\">Votaci&oacute;n $numvo</div>";
            $dFec = $date;
        }
        $sAny = $any;
    } 
    return $sVots;
}

/**
 * Obtiene una lista de propietarios para realizar una votacion.
 * 
 * @global \Apartamentos $oApars Instancia de Apartamentos.
 * @global \Personas $oPers Instancia de Personas.
 * @param date $fecha Fecha de la votacion.
 * @param int $num Numero de votacion.
 * @return string Codigo HTML de la votacion.
 */
function f_getAsistentesVotacion($fecha, $num=1) {
    global $oApars, $oProps; //$oPers;
    
    $aApars = $oApars->getApartamentos();
    $oVots = new Votaciones();
    $oVotx = new Votacion($fecha, $num);
    $oVota = (!$oVots->existeVotacion($fecha, $num)) ? f_cargarAsistentesJunta($oVotx) : $oVotx;
    
    $opc1 = ($oVota->getOpcion1()) ? $oVota->getOpcion1() : "Opc. 1";
    $opc2 = ($oVota->getOpcion2()) ? $oVota->getOpcion2() : "Opc. 2";
    $opc3 = ($oVota->getOpcion3()) ? $oVota->getOpcion3() : "Opc. 3";
    $opc4 = ($oVota->getOpcion4()) ? $oVota->getOpcion4() : "Opc. 4";
    
    $portal = "";
    $tabla  = "<a name=\"iniciolis\"></a><table id=\"tablavot\" class=\"table table-sm\">";
    
    // Recorre los apartamentos de la urbanizacion.
    foreach ($aApars as $apa => $aApartamento) {
        
        // Cabecera para un nuevo portal.
        if ($portal != $aApartamento[0]) {
            $portal = $aApartamento[0];
            $tabla .= f_cabeceraTablaAsistentes($portal, $opc1, $opc2, $opc3, $opc4);
        }
        
        $aProp = $oProps->getNombrePropietarioApartamentoFecha($apa, $fecha);   // array('codpers'=>array('persona','date','fecha','orden'))
        $iProp = $oProps->getCodigoPropietarioApartamentoFecha($apa, $fecha);   // codpers
        $aApPr = $oProps->getCodigosPropiedadesPersonaFecha($iProp, $fecha);    // array(apa1,apa2...)
        
        $sApPr = "[" . implode(",", $aApPr) . "]";                      // Codigos en formato [1,2,3]
        
        // Datos de la votacion.
        $aVoto = $oVota->getVoto($apa); // array('asis','vota','pres','res1','res2','res3','res4')
        
        $ch1 = ($aVoto[0] == 'S') ? "checked=\"checked\"" : ""; // Asiste.
        $ch2 = ($aVoto[1] == 'S') ? "checked=\"checked\"" : ""; // Tiene voto.
        $ch3 = ($aVoto[2] == 'S') ? "checked=\"checked\"" : ""; // Presente.
        
        $op1 = ($aVoto[3] == 'S') ? "checked=\"checked\"" : ""; // Resultado 1.
        $op2 = ($aVoto[4] == 'S') ? "checked=\"checked\"" : ""; // Resultado 2.
        $op3 = ($aVoto[5] == 'S') ? "checked=\"checked\"" : ""; // Resultado 3.
        $op4 = ($aVoto[6] == 'S') ? "checked=\"checked\"" : ""; // Resultado 4.
        
        $bch = ($aVoto[0] != 'S') ? "disabled=\"disabled\"" : "";
        $bop = ($aVoto[0] != 'S' || $aVoto[1] != 'S' || $aVoto[2] != 'S') ? "disabled=\"disabled\"" : "";
        
        $nom = $aProp[$iProp];
        $onc = "js_marcarOpc(this.id, $apa, $sApPr, this.checked);";
        $onk = f_nombresApartamentosMultiples($apa, $aApPr);
        
        // Fila con los datos de la votacion de un apartamento.
        $tabla .= f_datosTablaAsistentes($apa, $aApartamento, $nom, $onc, $onk, $ch1, $ch2, $ch3, $bch, $sApPr, $op1, $op2, $op3, $op4, $bop);
    }
    return "$tabla</table>";
}

/**
 * Crea una llamada a JavaScript para mostrar un tool-tip con los nombres de los apartamentos de un propietario.
 * Se muestran los nombres de sus apartamentos, menos el actual.
 * 
 * @global \Apartamentos $oApars Instancia de Apartamentos.
 * @param int $apa Codigo del apartamento actual.
 * @param array $aCods Codigos de apartamentos.
 * @return string Llamada a la funcion que muestra los tool-tip.
 */
function f_nombresApartamentosMultiples($apa, $aCods) {
    global $oApars;
    $aNombres = array();
    if(count($aCods) > 1) {
        // Tiene varios apartamentos.
        foreach ($aCods as $codigo) {
            if ($codigo != $apa) {
                // No incluye el apartamento actual.
                $aNombres[] = $oApars->getNombreCompleto($codigo);
            }
        }
        $sNombres = implode(", ", $aNombres);
        $onk = "if($('#sincro').prop('checked') == true) { js_existeTooltip($apa, 'Sincronizado con $sNombres'); }";
    } else {
        $onk = "";
    }
    return $onk;
}

/**
 * Obtiene la cabecera de portal en la lista de una votacion.
 * 
 * @param int $portal Numero de portal.
 * @param string $opc1 Texto de la opcion 1.
 * @param string $opc2 Texto de la opcion 2.
 * @param string $opc3 Texto de la opcion 3.
 * @param string $opc4 Texto de la opcion 4.
 * @return string Codigo HTML de la cabecera.
 */
function f_cabeceraTablaAsistentes($portal, $opc1, $opc2, $opc3, $opc4) {
    return "<tr id=\"portal$portal\" style=\"background-color:#F5F5F5\">
                <th class=\"align-middle col-sm-1\">&nbsp;<a name=\"ini$portal\" href=\"#ini1\">Portal $portal</a></th>
                <th class=\"align-middle col-sm-4\">Propietario</th>
                <th class=\"align-middle text-center col-sm-1\">Asi.</th>
                <th class=\"align-middle text-center col-sm-1\">Vot</th>
                <th class=\"align-middle text-center col-sm-1\">Pre</th>
                <th id=\"titop1$portal\" class=\"align-middle text-center col-sm-1\" title=\"$opc1\">" . substr($opc1, 0, 8) . "</th>
                <th id=\"titop2$portal\" class=\"align-middle text-center col-sm-1\" title=\"$opc2\">" . substr($opc2, 0, 8) . "</th>
                <th id=\"titop3$portal\" class=\"align-middle text-center col-sm-1\" title=\"$opc3\">" . substr($opc3, 0, 8) . "</th>
                <th id=\"titop4$portal\" class=\"align-middle text-center col-sm-1\" title=\"$opc4\">" . substr($opc4, 0, 8) . "</th></tr>";
}

/**
 * Obtiene el contenido de lo que ha votado un apartamento en la lista de una votacion.
 * 
 * @param int $apa Codigo del apartamento.
 * @param array $aApartamento Datos del apartamento.
 * @param string $nom Nombre del propietario.
 * @param string $onc Llamada a la funcion que se ejecuta al hacer clic.
 * @param string $onk Llamada a la funcion de sincronizacion.
 * @param string $ch1 Estado de la seleccion de la casilla de asistencia.
 * @param string $ch2 Estado de la seleccion de la casilla de puede votar.
 * @param string $ch3 Estado de la seleccion de la casilla de presencia.
 * @param string $bch Estado de activacion de las casillas.
 * @param string $sApPr Lista de los codigos de todos los apartamentos de los cuales es propietario.
 * @param string $op1 Estado de seleccion de la opcion 1.
 * @param string $op2 Estado de seleccion de la opcion 2.
 * @param string $op3 Estado de seleccion de la opcion 3.
 * @param string $op4 Estado de seleccion de la opcion 4.
 * @param boolean $bop Estado de activacion de las opciones.
 * @return string Codigo HTML de la informacion sobre la votacion del apartamento.
 */
function f_datosTablaAsistentes($apa, $aApartamento, $nom, $onc, $onk, $ch1, $ch2, $ch3, $bch, $sApPr, $op1, $op2, $op3, $op4, $bop) {
    return "<tr id=\"fila$apa\"><td id=\"apartamento$apa\" class=\"align-middle\">" . $aApartamento[0] . "-" . $aApartamento[1] . $aApartamento[2] . "</td>
                <td class=\"align-middle\">$nom</td>
                <td class=\"align-middle text-center\"><input type=\"checkbox\" id=\"asis$apa\" name=\"asis[$apa]\" onclick=\"$onc $onk\" $ch1></td>
                <td class=\"align-middle text-center\"><input type=\"checkbox\" id=\"vota$apa\" name=\"vota[$apa]\" onclick=\"$onc\" $ch2 $bch></td>    
                <td class=\"align-middle text-center\"><input type=\"checkbox\" id=\"pres$apa\" name=\"pres[$apa]\" onclick=\"$onc\" $ch3 $bch></td>
                <td class=\"align-middle text-center\"><input type=\"radio\" id=\"res1$apa\" name=\"opciones[$apa]\" onclick=\"js_sincronizar($sApPr,1)\" value=\"1\" $op1 $bop></td>
                <td class=\"align-middle text-center\"><input type=\"radio\" id=\"res2$apa\" name=\"opciones[$apa]\" onclick=\"js_sincronizar($sApPr,2)\" value=\"2\" $op2 $bop></td>
                <td class=\"align-middle text-center\"><input type=\"radio\" id=\"res3$apa\" name=\"opciones[$apa]\" onclick=\"js_sincronizar($sApPr,3)\" value=\"3\" $op3 $bop></td>
                <td class=\"align-middle text-center\"><input type=\"radio\" id=\"res4$apa\" name=\"opciones[$apa]\" onclick=\"js_sincronizar($sApPr,4)\" value=\"4\" $op4 $bop></td></tr>";
}

/**
 * Carga los votos de los asistentes a una votacion.
 * 
 * @param \Votacion $oVota Instancia de Votacion.
 * @return \Votacion Instancia con los votos cargados.
 */
function f_cargarAsistentesJunta($oVota) {
    $fecha = $oVota->getFecha();
    $oAsis = new Asistentes($fecha);
    $aAsis = $oAsis->getAsistentes();   // Asistentes: array('codapar' => array('apart','codpers','nombre','repre','voto')...)
    foreach ($aAsis as $apa => $aDat) {
        $vot = $aDat[4];    // Voto S/N.
        $oVota->setVoto($apa, 'S', $vot, 'S', 'N', 'N', 'N', 'N'); 
    } 
    return $oVota;
}

/**
 * Convierte el formato de un array de PHP en el formato para JavaScript.
 * 
 * @param array $aDatos Datos a transformar.
 * @return string Datos transformados como arrays de JavaScript.
 */
function f_convertirArrayJS($aDatos) {
    $sDatos = "var coeficienteJS = new Array();";
    foreach ($aDatos as $iApar => $aCoef) {
        $sCoef = implode(",", $aCoef);
        $sDatos .= "coeficienteJS[$iApar] = [$sCoef];";
    }
    return $sDatos;
}

/**
 * Obtiene el Select para elegir el numero de la votacion.
 * 
 * @param date $fecha Fecha de la votacion.
 * @param int $vot Numero de votacion seleccionada.
 * @return Codigo HTML para el Select del numero de votacion.
 */
function f_getSelectNumVotaciones($fecha, $vot=1) {
    $oVots = new Votaciones();
    $num   = $oVots->getUltimaVotacion($fecha) + 1;
    $aNum  = array();
    for($i=1; $i<=$num; $i++) {
        $aNum[$i] = $i;
    }
    return f_getSelectSimple($aNum, "votacion", $vot, "form-control form-control-sm", "js_cambiarNumeroVotacion($('#votacion').val(), $('#votacioninicial').val())");
}

/**
 * Graba los datos de una votacion.
 * 
 * @param array $frm Datos del formulario de la votacion.
 * @return boolean Devuelve TRUE si todo ha ido bien o FALSE en caso contrario.
 */
function f_grabarVotacion($frm) {
    $fec = $frm['fecha'];
    $num = $frm['votacion'];
    $txt = $frm['textvot'];
    $op1 = $frm['opcion1'];
    $op2 = $frm['opcion2'];
    $op3 = $frm['opcion3'];
    $op4 = $frm['opcion4'];
    $aAs = $frm['asis'];
    $aVo = $frm['vota'];
    $aPr = $frm['pres'];
    $aOp = $frm['opciones'];
    
    $oVot = new Votacion($fec, $num);
    $oVot->setTexto($txt);
    $oVot->setOpcion1($op1);
    $oVot->setOpcion2($op2);
    $oVot->setOpcion3($op3);
    $oVot->setOpcion4($op4);
    
    for($a=1; $a<=373; $a++) {
        $asi = ($aAs[$a]) ? 'S' : 'N';
        if ($asi == 'S') {
            $vot = ($aVo[$a]) ? 'S' : 'N';
            $pre = ($aPr[$a]) ? 'S' : 'N';
            $re1 = ($aOp[$a] == 1) ? 'S' : 'N';
            $re2 = ($aOp[$a] == 2) ? 'S' : 'N';
            $re3 = ($aOp[$a] == 3) ? 'S' : 'N';
            $re4 = ($aOp[$a] == 4) ? 'S' : 'N';
            $oVot->setVoto($a, $asi, $vot, $pre, $re1, $re2, $re3, $re4);
        }
    }
    return $oVot->grabar();
}

//--- ACTAS VISUALIZAR -------------------------------------------------------//

function f_getActasAnyos() {
    $oActas = new Actas();
    $aAnys = $oActas->getActasAnyos();
    $sAnys = "";
    foreach ($aAnys as $any) {
        $sAnys .= "<a href=\"#any$any\">$any</a>&nbsp;";
    }
    return $sAnys;
}

function f_getActasListado() {
    global $pagina;
    $oActas = new Actas();
    $aActas = $oActas->getActas();
    $sActas = "<div><a name=\"inicio\"></a></div>";
    $sAny = "";
    
    foreach ($aActas as $date => $aActa) {
        $any   = substr($date, 0, 4);
        $fecha = $aActa[0];
        $sActas .= ($any != $sAny) ? "<div class=\"listado-tit\"><a name=\"any$any\" href=\"#inicio\">$any</a></div>" : "";
        $sActas .= "<div class=\"listado-elem\" onclick=\"xajax_reenviarFuncion('$pagina', '$fecha');\">$fecha</div>";
        $sAny = $any;
    } 
    return $sActas;
}

function f_getDatosActa($fecha) {
    global $oJuntas;
    $oJunta = new Junta();
    $oActa = new Acta($fecha);
    $feISO = $oJuntas->convertirFechaBDaISO($fecha);
    // Titulo principal.
    $aTipo = $oJunta->getTipos();
    $sTipo = $aTipo[$oJuntas->getTipoJunta($fecha)];
    $html = "<a name=\"inicioacta\">&nbsp;</a><br /><h1>Acta de la Junta General $sTipo del $feISO</h1>";
    // Indice del acta.
    $html .= f_getDatosActaIndice($oActa->getTitulos()) . "<hr />";
    // Cuerpo del acta.
    $html .= "<div class=\"cuerpoacta\">" . f_getDatosActaCuerpo($oActa->getPuntos()) . "</div>";
    return $html;
}

function f_getDatosActaIndice($aIndi) {
    $html = "";
    if ($aIndi && count($aIndi) > 0) {
        foreach ($aIndi as $iPun => $sTit) {
            $html .= "<div class=\"indice\"><a href=\"#punto$iPun\">$sTit</a></div>";
        }
    }
    return $html;
}

/**
 * Obtiene el cuerpo del acta.
 * 
 * @param array $aPuntos array del tipo array('pun'=>array(punto, titulo, array('apa'=>array(apart, subtit, texto)...))...)
 * @return string Codigo HTML del cuerpo del acta.
 */
function f_getDatosActaCuerpo($aPuntos) {
    $html = "";
    foreach ($aPuntos as $iPun => $aPunto) {
        $sPun = $aPunto[0];
        $sTit = $aPunto[1];
        $aAps = $aPunto[2];
        $html .= "<h2><a name=\"punto$iPun\"></a>";
        $html .= ($sPun) ? "$sPun.- $sTit</h2>" : "$sTit</h2>";
        $html .= f_getDatosActaApartados($aAps);
    }
    return $html;
}

/**
 * Obtiene los apartados del acta.
 * 
 * @param array $aAps del tipo array('apa'=>array(apart, subtit, texto)...)
 * @return string Codigo HTML de los apartados.
 */
function f_getDatosActaApartados($aAps) {
    $html = "";
    foreach ($aAps as $aApa) {
        $sApa = $aApa[0];
        $sSub = $aApa[1];
        $sTxt = $aApa[2];
        $apar = ($sApa) ? "$sApa.- " : "";
        $subt = ($sSub) ? "<h3>$apar$sSub</h3>" : "";
        $html .= $subt . $sTxt;
    }
    return $html;
}

//--- ACTAS BUSCAR -----------------------------------------------------------//

/**
 * Obtiene un panel de resultados de la busqueda de palabras en las actas.
 * 
 * @param string $lista Lista de palabras a buscar.
 * @param int $tipo Tipo de busqueda: 0 - Natural, 1 - Booleana, 2 - Literal.
 * @return string Codigo HTML del resultado.
 */
function f_getActasDatosBusqueda($lista, $tipo) {
    $oActas = new Actas();
    switch ($tipo) {
        // array(0 - fecha, 1 - codpun, 2 - punto, 3 - titulo, 4 - codapa, 5 - apartado, 6 - subtit, 7 - texto, 8 - relevancia)
        case 1 : $aBus = $oActas->busquedaBooleana($lista); break;
        case 2 : $aBus = $oActas->busquedaLiteral($lista); break;
        default: $aBus = $oActas->busquedaNatural($lista); break;
    }
    $html = "<h3><a name=\"inicio\"></a>Encontradas " . count($aBus) . " actas para <em>$lista</em></h3><div id=\"accordion\" role=\"tablist\">"; 
    foreach ($aBus as $aPunto) {
        $fecha = $aPunto[0];
        $feISO = $oActas->convertirFechaBDaISO($fecha);
        $iPunt = $aPunto[1];
        $sPunt = $aPunto[2];
        $sTitu = ($aPunto[3]) ? $oActas->remarcar($lista, $aPunto[3]) : "";
        $iApar = $aPunto[4];
        $sApar = $aPunto[5];
        $sSubt = ($aPunto[6]) ? $oActas->remarcar($lista, $aPunto[6]) : "";
        $sText = ($aPunto[7]) ? $oActas->remarcar($lista, $aPunto[7]) : "";
        $iRele = number_format($aPunto[8],2);
        $apart = ($sApar) ? "$sApar.- " : "";
        $subti = ($sSubt) ? "<h5>$apart$sSubt</h5>" : "";
        
        $html .= "<div class=\"card\"><div class=\"card-header\" role=\"tab\" id=\"c$fecha$iPunt$iApar\" title=\"Relevancia $iRele\"><div class=\"row\">
                  <div class=\"col-sm-1\"><a data-toggle=\"collapse\" href=\"#p$fecha$iPunt$iApar\" role=\"button\" aria-expanded=\"true\" aria-controls=\"p$fecha$iPunt$iApar\">$feISO</a></div>
                  <div class=\"col-sm-10\"><a data-toggle=\"collapse\" href=\"#p$fecha$iPunt$iApar\" role=\"button\" aria-expanded=\"true\" aria-controls=\"p$fecha$iPunt$iApar\">$sPunt.- $sTitu</a></div>
                  <div class=\"col-sm-1 text-right\"><a href=\"actas.php?fecha=$fecha\" role=\"button\" class=\"btn btn-sm btn-outline-primary\" title=\"Ver acta del d&iacute;a $feISO\"><span class=\"oi oi-eye\"></span></a></div></div></div>
                  <div id=\"p$fecha$iPunt$iApar\" class=\"collapse\" role=\"tabpanel\" aria-labelledby=\"c$fecha$iPunt$iApar\" data-parent=\"#accordion\">
                  <div class=\"card-body\">$subti$sText</div></div></div>";
    }
    return "$html</div>";
}

//--- ACTAS EDITAR -----------------------------------------------------------//

function f_getActa($fecha='') {
    $html = "<a name=\"inicioacta\"></a><div id=\"accordion\" role=\"tablist\">";
    $oActa = new Acta($fecha);
    $aPunt = $oActa->getPuntos();
    $iNuev = 0;
    foreach ($aPunt as $iPun => $aPunto) {
        $sPun = $aPunto[0];
        $sTit = $aPunto[1];
        $aAps = $aPunto[2];
        $html .= "<div class=\"card\">" . f_getActaCabecera($iPun, $sPun, $sTit) . f_getActaApartados($iPun, $aAps) . "</div>";
        $iNuev = $iPun + 1;
    }
    // Punto vacio.
    $html .= "<div class=\"card\">" . f_getActaCabecera($iNuev, '', '') . f_getActaApartados($iNuev, array()) . "</div>";
    
    return "$html</div>";
}

function f_getActaApartados($iPun, $aAps) {
    $html .= "<div id=\"p$iPun\" class=\"collapse\" role=\"tabpanel\" aria-labelledby=\"c$iPun\" data-parent=\"#accordion\"><div class=\"card-body\">";
    $iNue = 0;
    foreach ($aAps as $iApa => $aApa) {
        $sApa = $aApa[0];
        $sSub = $aApa[1];
        $sTxt = $aApa[2];
        $html .= f_getActaCuerpo($iPun, $iApa, $sApa, $sSub, $sTxt);
        $iNue = $iApa + 1;
    }
    // Apartado vacio.
    $html .= f_getActaCuerpo($iPun, $iNue, '', '', '');
        
    return "$html</div></div>";
}

function f_getActaCabecera($iPun, $sPun, $sTit) {
    return "<div class=\"card-header\" role=\"tab\" id=\"c$iPun\">
                <div class=\"row\">
                    <div class=\"col-sm-1\"><input type=\"text\" id=\"pun$iPun\" name=\"punt[$iPun]\" class=\"form-control\" value=\"$sPun\" placeholder=\"Punto\"></div>
                    <div class=\"col-sm-10\"><input type=\"text\" id=\"tit$iPun\" name=\"tit[$iPun]\" class=\"form-control\" value=\"$sTit\" placeholder=\"T&iacute;tulo\"></div>
                    <div class=\"col-sm-1 text-right\"><a data-toggle=\"collapse\" href=\"#p$iPun\" class=\"btn btn-outline-primary\" role=\"button\" aria-expanded=\"true\" aria-controls=\"p$iPun\"><span class=\"oi oi-caret-bottom\"></span></a></div>
                </div>
            </div>";
}

function f_getActaCuerpo($iPun, $iApa, $sApa, $sSub, $sTxt) {
    return "<div class=\"row\">
                <div class=\"col-sm-1\"><input type=\"text\" id=\"apa$iPun-$iApa\" name=\"apa[$iPun][$iApa]\" class=\"form-control\" value=\"$sApa\" placeholder=\"Apartado\"></div>
                <div class=\"col-sm-11\"><input type=\"text\" id=\"sub$iPun-$iApa\" name=\"sub[$iPun][$iApa]\" class=\"form-control\" value=\"$sSub\" placeholder=\"Subt&iacute;tulo\"></div>
            </div>
            <div class=\"row\">
                <div class=\"col-sm-12\"><textarea id=\"txt$iPun-$iApa\" name=\"txt[$iPun][$iApa]\" class=\"editor form-control\">$sTxt</textarea></div>
            </div>";
}

function f_grabarActa($frm) {
    $fini = $frm['fechainicial'];   // Fecha inicial del acta.
    $fnew = $frm['fecha'];          // Fecha actual a guardar.
    
    $oActa = new Acta($fini);       // Crea el acta inicial.
    $oActa->setFecha($fnew);        // Guarda la nueva fecha.
    
    $aPun = $frm['punt'];           // Array con los puntos del acta.
    $aTit = $frm['tit'];            // Array con los titulos de los puntos.

    // Recorre los puntos.
    foreach ($aPun as $iPun => $sPun) {
        $sTit = $aTit[$iPun];
        if ($sTit) {
            // Si hay un titulo guardamos y seguimos con los apartados.
            $oActa->setPuntoTxt($iPun, $sPun);  // Guarda el numero o letra del punto.
            $oActa->setTitulo($iPun, $sTit);    // Guarda el titulo del punto.
            
            $aApa = $frm['apa'][$iPun];         // Numeros de apartado del punto actual.
            $aSub = $frm['sub'][$iPun];         // Subtitulos del punto actual.
            $aTxt = $frm['txt'][$iPun];         // Textos del punto actual.
            // Recorre los apartados.
            foreach ($aApa as $iApa => $sApa) {
                $sSub = $aSub[$iApa];   // Subtitulo del apartado actual.
                $sTxt = $aTxt[$iApa];   // Texto del apartado actual.
                
                $oActa->setApartadoTxt($iPun, $iApa, $sApa);    // Guarda el numero o letra del apartado.
                $oActa->setSubtitulo($iPun, $iApa, $sSub);      // Guarda el subtitulo del apartado.
                $oActa->setTexto($iPun, $iApa, $sTxt);          // Guarda el texto del apartado.
            }
        }
    }
    return $oActa->grabar();
}

//--- CALCULOS Y LISTADOS ----------------------------------------------------//

//--- LISTADO DE PERSONAS ---//

/**
 * Obtiene el listado de personas.
 * 
 * @global \Personas $oPers Instancia de Personas.
 * @param array $frm Datos del formulario.
 * @return string Codigo HTML del listado.
 */
function f_getListadoPersonas($frm) {
    global $oPers;
     
    $aEnv = array('S'=>'S&iacute;', 'N'=>'No');
    $aSexo = array('H'=>'Hombre', 'M'=>'Mujer');
    $fTit = f_getListadoPersonasTitulo($frm);
    // Asigna el orden.
    $ord = $frm['orden'];
    $oPers->setOrden($ord);
    $sOrd = strtolower($oPers->getOrden(TRUE));
    // Asigna los filtros.
    $filPr = (isset($frm['propietarios'])) ? 'S' : 'N';
    $filCo = (isset($frm['concorreo'])) ? 'S' : 'N';
    $filTe = (isset($frm['contelefono'])) ? 'S' : 'N';
    $filHo = (isset($frm['hombres'])) ? 'S' : 'N';
    $filMu = (isset($frm['mujeres'])) ? 'S' : 'N';
    $filOt = (isset($frm['otros'])) ? 'S' : 'N';
    $oPers->setFiltroPropietario($filPr);
    $oPers->setFiltroCorreo($filCo);
    $oPers->setFiltroTelefono($filTe);
    $oPers->setFiltroSexoHombre($filHo);
    $oPers->setFiltroSexoMujer($filMu);
    $oPers->setFiltroSexoOtro($filOt);
    $aPers = $oPers->getFiltradas();
    $fPer = "";
    $num = 0;
    foreach ($aPers as $per => $aPersona) {
        // array('cod'=>array(0 apellidos, 1 nombre, 2 sexo, 3 codusu, 4 correo, 5 envios, 6 telefono, 7 notas)...)
        $fPer .= "<tr>";
        $fPer .= (isset($frm['codigo'])) ? "<td>$per</td>" : "";
        $fPer .= "<td>" . $oPers->getNombreCompleto($per) . "</td>";
        $fPer .= (isset($frm['correo'])) ? "<td>" . $aPersona[4] . "</td>" : "";
        $fPer .= (isset($frm['enviar'])) ? "<td>" . $aEnv[$aPersona[5]] . "</td>" : "";
        $fPer .= (isset($frm['telefono'])) ? "<td>" . $aPersona[6] . "</td>" : "";
        $fPer .= (isset($frm['sexo'])) ? "<td>" . $aSexo[$aPersona[2]] . "</td>" : "";
        $fPer .= "</tr>";
        $fPer .= (isset($frm['notas']) && $aPersona[7]) ? f_getListadoPersonasNotas($frm, $aPersona[7]) : "";
        $num++;
    }
    return "<h4><a name=\"inicio\"></a>Listado de $num personas ordenado por $sOrd. <span style=\"font-size:0.8em\"><em>Filtro: " . f_getListadoPersonasFiltros($frm) . "</em>.</span></h4><table class=\"table table-condensed table-ultra\">$fTit$fPer</table>";
}

/**
 * Obtiene la fila con los titulos del listado.
 * 
 * @param array $frm Datos del formulario.
 * @return string Codigo HTML de la fila.
 */
function f_getListadoPersonasTitulo($frm) {
    $fila = "<tr>";
    $fila .= (isset($frm['codigo'])) ? "<th>C&oacute;digo</th>" : "";
    $fila .= "<th>Nombre</th>";
    $fila .= (isset($frm['correo'])) ? "<th>Correo</th>" : "";
    $fila .= (isset($frm['enviar'])) ? "<th>Enviar</th>" : "";
    $fila .= (isset($frm['telefono'])) ? "<th>Tel&eacute;fono</th>" : "";
    $fila .= (isset($frm['sexo'])) ? "<th>Sexo</th>" : "";
    $fila .= "</tr>";
    return $fila;
}

/**
 * Obtiene la fila de notas.
 * 
 * @param array $frm Datos del formulario.
 * @param string $nota Notas.
 * @return string Codigo HTML de la fila.
 */
function f_getListadoPersonasNotas($frm, $nota) {
    $col = 1;
    $col += (isset($frm['correo'])) ? 1 : 0;
    $col += (isset($frm['enviar'])) ? 1 : 0;
    $col += (isset($frm['telefono'])) ? 1 : 0;
    $col += (isset($frm['sexo'])) ? 1 : 0;
    if (isset($frm['codigo'])) {
        $fila = "<tr><td>&nbsp;</td><td colspan=\"$col\">$nota</td></tr>";
    } else {
        $fila = "<tr><td colspan=\"$col\" style=\"padding-left:50px;\">$nota</td></tr>";
    }
    return $fila;
}

/**
 * Obtiene una lista con los filtros que se estan usando en el listado.
 * 
 * @param array $frm Datos del formulario.
 * @return string Lista de filtros.
 */
function f_getListadoPersonasFiltros($frm) {
    $aF = array();
    $filtros = "";
    if (isset($frm['propietarios'])) {
        $aF[] = 'propietarios';
    }
    if (isset($frm['concorreo'])) {
        $aF[] = 'correo';
    }
    if (isset($frm['contelefono'])) {
        $aF[] = 'tel&eacute;fono';
    }
    if (isset($frm['hombres'])) {
        $aF[] = 'hombres';
    }
    if (isset($frm['mujeres'])) {
        $aF[] = 'mujeres';
    }
    if (isset($frm['otros'])) {
        $aF[] = 'otros';
    }
    if (count($aF)) {
        $filtros = implode(', ', $aF);
    } else {
        $filtros = "ninguno";
    }
    return $filtros;
}

//--- LISTADO DE APARTAMENTOS ---//

/**
 * Obtiene el listado de apartamentos con los datos indicados.
 * 
 * @global \Apartamentos $oApars Instancia de Apartamentos.
 * @param array $frm Datos del formulario.
 * @return string Codigo HTML del listado de apartamentos.
 */
function f_getListadoApartamentos($frm) {
    global $oApars;
    
    $fTit = f_getListadoApartamentosTitulo($frm);
    // Asignar filtros.
    $tipo = $frm['contipo'];
    $por1 = $frm['portal1'];
    $por2 = $frm['portal2'];
    $cong = (isset($frm['congaraje'])) ? 'S' : 'N';
    $cont = (isset($frm['conterraza'])) ? 'S' : 'N';
    // Asignar filtros
    $oApars->setFiltroTipo($tipo);
    $oApars->setFiltroPortalIni($por1);
    $oApars->setFiltroPortalFin($por2);
    $oApars->setFiltroGarajes($cong);
    $oApars->setFiltroTerrazas($cont);
    
    // Inicializa las sumas.
    $bloApa = 0; $bloMe2 = 0; $bloTer = 0; $bloCoe = 0; $bloCof = 0; $bloCor = 0; $bloCob = 0; $bloGar = 0;
    $fasApa = 0; $fasMe2 = 0; $fasTer = 0; $fasCoe = 0; $fasCof = 0; $fasCor = 0; $fasCob = 0; $fasGar = 0;
    $sumApa = 0; $sumMe2 = 0; $sumTer = 0; $sumCoe = 0; $sumCof = 0; $sumCor = 0; $sumCob = 0; $sumGar = 0;
    
    
    $aApars = $oApars->getFiltrados();
    $portal = "";
    $fApa = "";
    $fase = "";
    foreach ($aApars as $apa => $aApartamento) {
        // array('0 portal','1 piso','2 letra','3 fase','4 tipo','5 finca','6 metros','7 terraza','8 coef.urb','9 coef.fase','10 coef.blo')
        
        // Mira si hay cambio de portal.
        if($portal != $aApartamento[0] && $frm['sumas']) {
            // Pone las sumas.
            $fSumb = f_getListadoApartamentosSumas($frm, "Portal $portal: ", $bloApa, $bloMe2, $bloTer, $bloCoe, $bloCof, $bloCor, $bloCob, $bloGar);
            $bloApa = 0; $bloMe2 = 0; $bloTer = 0; $bloCoe = 0; $bloCof = 0; $bloCor = 0; $bloCob = 0; $bloGar = 0;
            $fApa .= ($portal) ? $fSumb : "";
            $portal = $aApartamento[0];
        }
        
        // Mira si hay cambio de fase. 
        if ($fase != $aApartamento[3] && $frm['sumas']) {
            // Pone las sumas.
            $fSumf = f_getListadoApartamentosSumas($frm, "Fase $fase: ", $fasApa, $fasMe2, $fasTer, $fasCoe, $fasCof, $fasCor, $fasCob, $fasGar);
            $fasApa = 0; $fasMe2 = 0; $fasTer = 0; $fasCoe = 0; $fasCof = 0; $fasCor = 0; $fasCob = 0; $fasGar = 0;
            $fApa .= ($fase) ? $fSumf : "";
            $fase = $aApartamento[3];
        } 
        
        $fApa .= "<tr>";
        $fApa .= (isset($frm['codigo'])) ? "<td>$apa</td>" : "";
        $fApa .= (isset($frm['finca'])) ? "<td>" . $aApartamento[5] ."</td>" : "";
        $fApa .= "<td>" . $aApartamento[0] . "-" .$aApartamento[1] . $aApartamento[2] . "</td>";
        $bloApa++; $fasApa++; $sumApa++;
        
        $fApa .= (isset($frm['tipo'])) ? "<td>" . $aApartamento[4] ."</td>" : "";
        $fApa .= (isset($frm['fase'])) ? "<td>" . $aApartamento[3] ."</td>" : "";
        if (isset($frm['metros'])) {
            $fApa .= "<td class=\"text-right\">" . number_format($aApartamento[6],2,',','.') ."</td>";
            $bloMe2 += $aApartamento[6]; $fasMe2 += $aApartamento[6]; $sumMe2 += $aApartamento[6];
        }
        if (isset($frm['terraza'])) {
            $fApa .= (intval($aApartamento[7])) ? "<td class=\"text-right\">" . number_format($aApartamento[7],2,',','.') ."</td>" : "<td>&nbsp;</td>";
            $bloTer += $aApartamento[7]; $fasTer += $aApartamento[7]; $sumTer += $aApartamento[7];
        }
        if (isset($frm['urban'])) {
            $fApa .= "<td class=\"text-right\">" . number_format($aApartamento[8],4,',','.') ."</td>";
            $bloCoe += $aApartamento[8]; $fasCoe += $aApartamento[8]; $sumCoe += $aApartamento[8];
        }
        if (isset($frm['fase200'])) {
            $fApa .= "<td class=\"text-right\">" . number_format($aApartamento[9],4,',','.') ."</td>";
            $bloCof += $aApartamento[9]; $fasCof += $aApartamento[9]; $sumCof += $aApartamento[9];
        }
        if (isset($frm['fase100'])) {
            $fApa .= "<td class=\"text-right\">" . number_format($aApartamento[9]/2,5,',','.') ."</td>";
            $bloCor += $aApartamento[9]/2; $fasCor += $aApartamento[9]/2; $sumCor += $aApartamento[9]/2;
        }
        if (isset($frm['bloque'])) {
            $fApa .= "<td class=\"text-right\">" . number_format($aApartamento[10],2,',','.') ."</td>";
            $bloCob += $aApartamento[10]; $fasCob += $aApartamento[10]; $sumCob += $aApartamento[10];
        }
        if (isset($frm['garajes'])) {
            $iGar = $oApars->getNumeroGarajes($apa);
            $fApa .= ($iGar) ? "<td class=\"text-right\">$iGar&nbsp;</td>" : "<td>&nbsp;</td>";
            $bloGar += $iGar; $fasGar += $iGar; $sumGar += $iGar;
        }
        $fApa .= "</tr>";
    }
    // Sumas del ultimo portal, ultima fase y totales.
    $fApa .= ($frm['sumas']) ? f_getListadoApartamentosSumas($frm, "Portal $portal: ", $bloApa, $bloMe2, $bloTer, $bloCoe, $bloCof, $bloCor, $bloCob, $bloGar) : "";
    $fApa .= ($frm['sumas']) ? f_getListadoApartamentosSumas($frm, "Fase $fase: ", $fasApa, $fasMe2, $fasTer, $fasCoe, $fasCof, $fasCor, $fasCob, $fasGar) : "";
    $fApa .= ($frm['sumas']) ? f_getListadoApartamentosSumas($frm, "Total: ", $sumApa, $sumMe2, $sumTer, $sumCoe, $sumCof, $sumCor, $sumCob, $sumGar) : "";
    return "<h4><a name=\"inicio\"></a>Listado de $sumApa apartamentos. <span style=\"font-size:0.8em\"><em>Filtros: " . f_getListadoApartamentosFiltros($frm) . "</em>.</span></h4><table class=\"table table-condensed table-ultra\">$fTit$fApa</table>";
}

/**
 * Obtiene las sumas de los apartamentos.
 * 
 * @param array $frm Datos del formulario.
 * @param string $txt Texto para el inicio de la fila.
 * @param int $apa Suma de apartamentos.
 * @param int $me2 Suma de metros cuadrados de los apartamento.
 * @param int $ter Suma de metros cuadrados de las terrazas.
 * @param int $coe Suma de los coeficientes de urbanizacion al 100%.
 * @param int $cof Suma de los coeficientes de la fase al 200%.
 * @param int $cor Suma de los coeficientes de la fase al 100%.
 * @param int $cob Suma de los coeficientes de los bloques.
 * @param int $gar Suma del numero de garajes.
 * @return string Codigo HTML de la fila de sumas.
 */
function f_getListadoApartamentosSumas($frm, $txt, $apa, $me2, $ter, $coe, $cof, $cor, $cob, $gar) {
    $fTit = "<tr>";
    if (isset($frm['codigo']) && isset($frm['finca'])) {
        $fTit .= "<th colspan=\"3\">$txt$apa</th>";
    } elseif (isset($frm['codigo']) || isset($frm['finca'])) {
        $fTit .= "<th colspan=\"2\">$txt$apa</th>";
    } else {
        $fTit .= "<th>$txt$apa</th>";
    }
    $fTit .= (isset($frm['tipo'])) ? "<th>&nbsp;</th>" : "";
    $fTit .= (isset($frm['fase'])) ? "<th>&nbsp;</th>" : "";
    $fTit .= (isset($frm['metros'])) ? "<th class=\"text-right\">" . number_format($me2,2,',','.') . "</th>" : "";
    $fTit .= (isset($frm['terraza'])) ? "<th class=\"text-right\">" . number_format($ter,2,',','.') . "</th>" : "";
    $fTit .= (isset($frm['urban'])) ? "<th class=\"text-right\">" . number_format($coe,4,',','.') . "</th>" : "";
    $fTit .= (isset($frm['fase200'])) ? "<th class=\"text-right\">" . number_format($cof,4,',','.') . "</th>" : "";
    $fTit .= (isset($frm['fase100'])) ? "<th class=\"text-right\">" . number_format($cor,5,',','.') . "</th>" : "";
    // La suma de coeficientes y cuotas de portales solo se pone en la suma de portales.
    if (substr($txt, 0, 1) == "P") {
        $fTit .= (isset($frm['bloque'])) ? "<th class=\"text-right\">" . number_format($cob,2,',','.') . "</th>" : "";
    } else {
        $fTit .= (isset($frm['bloque'])) ? "<th>&nbsp;</th>" : "";
    }
    $fTit .= (isset($frm['garajes'])) ? "<th class=\"text-right\">$gar&nbsp;</th>" : "";
    return "$fTit</tr>";
}

/**
 * Obtiene un fila con los titulos del listado de apartamentos.
 * 
 * @param array $frm Datos del formulario.
 * @return string Codigo HTML de la fila de titulos.
 */
function f_getListadoApartamentosTitulo($frm) {
    $fila = "<tr>";
    $fila .= (isset($frm['codigo'])) ? "<th>C&oacute;digo</th>" : "";
    $fila .= (isset($frm['finca'])) ? "<th>Finca</th>" : "";
    $fila .= "<th>Apart.</th>";
    $fila .= (isset($frm['tipo'])) ? "<th>Tipo</th>" : "";
    $fila .= (isset($frm['fase'])) ? "<th>Fase</th>" : "";
    $fila .= (isset($frm['metros'])) ? "<th class=\"text-right\">Metros</th>" : "";
    $fila .= (isset($frm['terraza'])) ? "<th class=\"text-right\">Terraza</th>" : "";
    $fila .= (isset($frm['urban'])) ? "<th class=\"text-right\">Urb. 100%</th>" : "";
    $fila .= (isset($frm['fase200'])) ? "<th class=\"text-right\">Fase 200%</th>" : "";
    $fila .= (isset($frm['fase100'])) ? "<th class=\"text-right\">Fase 100%</th>" : "";
    $fila .= (isset($frm['bloque'])) ? "<th class=\"text-right\">Bloque</th>" : "";
    $fila .= (isset($frm['garajes'])) ? "<th class=\"text-right\">Gara.</th>" : "";
    $fila .= "</tr>";
    return $fila;
}

/**
 * Obtiene una cadena con los filtros aplicados al listado de apartamentos.
 * 
 * @param string $frm Datos del formulario.
 * @return string Cadena con los filtros aplicados.
 */
function f_getListadoApartamentosFiltros($frm) {
    $aF = array();
    $filtros = "";
    
    $aF[] = "del portal " . $frm['portal1'] . " al " . $frm['portal2'];
    if ($frm['contipo']) {
        $aF[] = 'apartamento tipo ' . $frm['contipo'];
    }
    if (isset($frm['congaraje'])) {
        $aF[] = 'con garaje';
    }
    if (isset($frm['conterraza'])) {
        $aF[] = 'con terraza';
    }
    if (count($aF)) {
        $filtros = implode(', ', $aF);
    } else {
        $filtros = "ninguno";
    }
    return $filtros;
}

//--- LISTADOS DE PROPIETARIOS ---//

function f_getListadoPropietarios($frm) {
    
    $int = (isset($frm['intervalo']) && $frm['intervalo'] == 0) ? FALSE : TRUE; // Fechas puntuales : Intervalo completo.
    $din = ($frm['fechaini']) ? $frm['fechaini'] : '24-08-1984';
    $dfi = ($frm['fechafin']) ? $frm['fechafin'] : date('d-m-Y');
    $ver = ($frm['verpor'] == 1) ? TRUE : FALSE;    // Por personas : Por apartamentos.
    $que = ($frm['verque'] == 1) ? TRUE : FALSE;    // Tambien bajas : Solo actuales.
    $dis = ($frm['distintos']) ? TRUE : FALSE;      // Solo distintos : Todos.
    $may = ($frm['mayusculas']) ? TRUE : FALSE;     // Mayusculas : Minusculas.
    
    $lis = ($int) ? f_getListadoPropietariosCompleto($din, $dfi, $ver, $que, $may) : f_getListadoPropietariosFechas($din, $dfi, $ver, $dis, $may);
    
    return f_getListadoPropietariosTitulo($int, $din, $dfi, $ver, $que, $dis) . $lis;
}

function f_getListadoPropietariosTitulo($int, $din, $dfi, $ver, $que, $dis) {
    $ti0 = ($int) ? "Listado de propietarios entre el d&iacute;a $din y el $dfi " : "Listado de propietarios el d&iacute;a $din y el d&iacute;a $dfi ";
    $tit = ($que) ? $ti0 : "Listado de propietarios actuales ";
    $tit .= ($ver) ? "ordenado por personas. " : "ordenado por apartamentos. ";
    $tit .= ($dis) ? "Solo los distintos." : "";
    return "<h4><a name=\"inicio\"></a>$tit</h4>";
}

function f_getListadoPropietariosCompleto($din, $dfi, $ver, $que, $may) {
    global $oProps;
    //array('0 codapa','1 apartamento','2 codpers','3 persona','4 date','5 fecha','6 orden')
    $aPro = ($que) ? $oProps->getPropietariosEntreFechas($din, $dfi, $ver) : $oProps->getPropietariosAlta($ver);
    $html = "<table class=\"table table-hover table-condensed table-ultra\">";
    $dini = "";
    $cuer = "";
    $cini = "";
    foreach ($aPro as $aD) {
        $nomb = ($may) ? $aD[3] : f_primeraMayuscula($aD[3]);
        $dato = ($ver) ? $nomb : $aD[1];   // persona : apartamento.
        $inic = ($ver) ? "<th>Propietarios - " . substr($aD[3], 0, 1) . "</th><th>Apartamentos</th>" : "<th>Portal " . strstr($aD[1], '-', true) . "&nbsp;</th><th>Propietarios</th>";
        if ($dato != $dini) {
            $html .= ($dini) ? "<tr><td>$dini</td><td>$cuer</td></tr>" : "";
            $cuer  = ($dini) ? "" : $cuer;
            $dini = $dato;
        }
        if ($inic != $cini) {
            $html .= "<tr>$inic</tr>";
            $cini = $inic;
        }
        $nuevo = ($cuer) ? " &mdash; " : ""; 
        $nuevo .= ($ver) ? $aD[1] : $nomb;
        $nuevo .= ($aD[5]) ? " (" . $aD[5] . ")" : "";
        $clase = ($aD[5]) ? "class=\"baja\"" : "";
        $cuer .= "<span $clase>$nuevo</span>";
    }
    return "$html<tr><td>$dato</td><td>$cuer</td></tr></table>";
}

function f_getListadoPropietariosFechas($din, $dfi, $ver, $dis, $may) {
    global $oProps;
    $aPr1 = f_getListadoPropietariosAgrupar($oProps->getPropietariosFecha($din, $ver), $ver, $may);
    $aPr2 = f_getListadoPropietariosAgrupar($oProps->getPropietariosFecha($dfi, $ver), $ver, $may);
    $aPro = f_getListadoPropietariosUnir($aPr1, $aPr2, $ver);
    $html = "<table class=\"table table-hover table-condensed table-ultra\">";
    $cini = "";
    foreach ($aPro as $aP) {
        $inic = ($ver) ? "<tr><th>Propietarios - " . substr($aP[0], 0, 1) . "</th><th>Apartamentos el $din</th><th>Apartamentos el $dfi</th></tr>" : "<tr><th>Portal " . strstr($aP[0], '-', true) . "&nbsp;</th><th>Propietarios el $din</th><th>Propietarios el $dfi</th></tr>";
        if ($inic != $cini) {
            $html .= $inic;
            $cini = $inic;
        }
        $html .= ($dis && $aP[1] == $aP[2]) ? "" : "<tr><td>" . $aP[0] . "</td><td>" . $aP[1] . "</td><td>" . $aP[2] . "</td></tr>";
    }
    return "$html</table>";
}

function f_getListadoPropietariosAgrupar($aPro, $ver, $may) {
    //array('0 codapa','1 apartamento','2 codpers','3 persona','4 date','5 fecha','6 orden')
    $aDatos = array();
    $t = "";
    $i = "";
    $k = 0;
    foreach ($aPro as $aD) {
        $key = ($ver) ? $aD[2] : $aD[0];                        // Por Persona : Por Apartamento.
        $nom = ($may) ? $aD[3] : f_primeraMayuscula($aD[3]);    // NOMBRE : Nombre.
        $dat = ($ver) ? $aD[1] : $nom;                          // Nombre : Apartamento.
        
        if ($k != $key) {
            if ($k != 0) {
                $aDatos[$k] = array($i, $t);
                $t = "";
            }
            $k = $key;
        }
        $n  = ($t) ? " &mdash; $dat" : " $dat"; 
        $n .= ($aD[5]) ? " (" . $aD[5] . ")" : "";
        $c  = ($aD[5]) ? "class=\"baja\"" : "";
        $t .= "<span $c>$n</span>";
        $i  = ($ver) ? $nom : $aD[1];                          // Apartamento : Nombre.
    }
    $aDatos[$k] = array($i, $t);    // codigo => array(descrip, propietarios o apartamentos)
    return $aDatos;
}

function f_getListadoPropietariosUnir($aPr1, $aPr2, $ver) {
    $aNueva = array();
    foreach ($aPr1 as $key => $aPro) {
        $aNueva[$key][0] = $aPro[0];
        $aNueva[$key][1] = $aPro[1];
        $aAux[$key] = ($ver) ? $aPro[0] : $key; // Nombre : Codapar
    }
    foreach ($aPr2 as $key => $aPro) {
        $aNueva[$key][0] = $aPro[0];
        $aNueva[$key][2] = $aPro[1];
        $aAux[$key] = ($ver) ? $aPro[0] : $key; // Nombre : Codapar
    }
    array_multisort($aAux, SORT_ASC, $aNueva);
    return $aNueva;
}

//--- CALCULO DE CUOTA MENSUAL ---//

/**
 * Calcula la cuota mensual para pagar una cantidad determinada en los meses indicados.
 * 
 * @global \Apartamentos $oApars Instancia de Apartamentos.
 * @param array $frm Datos del formulario.
 * @return string Codigo HTML del resultado de los calculos.
 */
function f_getCalculos($frm) {
    global $oApars;
    $can = $frm['cantidad'];
    $mes = $frm['meses'];
    $meses = ($mes == 1) ? "$mes mes" : "$mes meses";
    $fTit = f_getCalculosTitulo($frm);
    
    // Inicializa las sumas.
    $bloApa = 0; $bloMe2 = 0; $bloCoe = 0; $bloEue = 0; $bloCof = 0; $bloCor = 0; $bloEuf = 0; $bloRes = 0; $bloCob = 0; $bloEub = 0; $bloCog = 0; $bloEug = 0;
    $fasApa = 0; $fasMe2 = 0; $fasCoe = 0; $fasEue = 0; $fasCof = 0; $fasCor = 0; $fasEuf = 0; $fasRes = 0; $fasCob = 0; $fasEub = 0; $fasCog = 0; $fasEug = 0;
    $sumApa = 0; $sumMe2 = 0; $sumCoe = 0; $sumEue = 0; $sumCof = 0; $sumCor = 0; $sumEuf = 0; $sumRes = 0; $sumCob = 0; $sumEub = 0; $sumCog = 0; $sumEug = 0;
    
    $portal = "";
    $fase = "";
    $fApa = "";
    $aAp = $oApars->getApartamentos();
    foreach ($aAp as $apa => $aApar) {
        //array('0 portal','1 piso','2 letra','3 fase','4 tipo','5 finca','6 metros','7 terraza','8 coef.urb','9 coef.fase','10 coef.blo')
        
        // Mira si hay cambio de portal.
        if($portal != $aApar[0] && $frm['sumas']) {
            // Pone las sumas.
            $fSumb = f_getCalculosSumas($frm, "Portal $portal: ", $bloApa, $bloMe2, $bloCoe, $bloEue, $bloCof, $bloCor, $bloEuf, $bloRes, $bloCob, $bloEub, $bloCog, $bloEug);
            $bloApa = 0; $bloMe2 = 0; $bloCoe = 0; $bloEue = 0; $bloCof = 0; $bloCor = 0; $bloEuf = 0; $bloRes = 0; $bloCob = 0; $bloEub = 0; $bloCog = 0; $bloEug = 0;
            $fApa .= ($portal) ? $fSumb : "";
            $portal = $aApar[0];
        }
        
        // Mira si hay cambio de fase.
        if ($fase != $aApar[3] && $frm['sumas']) {
            // Pone las sumas.
            $fSumf = f_getCalculosSumas($frm, "Fase $fase: ", $fasApa, $fasMe2, $fasCoe, $fasEue, $fasCof, $fasCor, $fasEuf, $fasRes, $fasCob, $fasEub, $fasCog, $fasEug);
            $fasApa = 0; $fasMe2 = 0; $fasCoe = 0; $fasEue = 0; $fasCof = 0; $fasCor = 0; $fasEuf = 0; $fasRes = 0; $fasCob = 0; $fasEub = 0; $fasCog = 0; $fasEug = 0;
            $fApa .= ($fase) ? $fSumf : "";
            $fase = $aApar[3];
        }
        
        // Inicia la fila del apartamento.
        $fApa .= "<tr>";
        
        // Codigo del apartamento.
        if (isset($frm['codigo'])) {
            $fApa .= "<td>$apa</td>";
        }
        
        // Nombre del apartamento. Se muestra siempre.
        $fApa .= "<td>" . $aApar[0] . "-" .$aApar[1] . $aApar[2] . "</td>";
        $bloApa++; $fasApa++; $sumApa++;
        
        // Fase.
        if(isset($frm['fase'])) {
            $fApa .= "<td>$aApar[3]</td>";
        }
        
        // Metros cuadrados.
        if(isset($frm['metros'])) {
            $fApa .= "<td class=\"text-right\">" . number_format($aApar[6],2,',','.') . "</td>";
            $bloMe2 += $aApar[6]; $fasMe2 += $aApar[6]; $sumMe2 += $aApar[6];
        }
        
        // Coeficiente urbanizacion 100% + Cuota urbanizacion.
        if(isset($frm['coeur'])) {
            $cuotau = ($aApar[8] * $can) / ($mes * 100);
            $fApa .= "<td class=\"text-right\">" . number_format($aApar[8],4,',','.') . "</td><td class=\"text-right successcolor\">" . number_format($cuotau,2,',','.') . "</td>";
            $bloCoe += $aApar[8]; $bloEue += $cuotau; $fasCoe += $aApar[8]; $fasEue += $cuotau; $sumCoe += $aApar[8]; $sumEue += $cuotau;
        }
        
        // Coeficiente fase 200%
        if(isset($frm['coef200'])) {
            $fApa .= "<td class=\"text-right\">" . number_format($aApar[9],4,',','.') . "</td>";
            $bloCof += $aApar[9]; $fasCof += $aApar[9]; $sumCof += $aApar[9];
        }
        
        // Coeficiente fase 100%
        if(isset($frm['coef100'])) {
            $fApa .= "<td class=\"text-right\">" . number_format($aApar[9]/2,5,',','.') . "</td>";
            $bloCor += $aApar[9] / 2; $fasCor += $aApar[9] / 2; $sumCor += $aApar[9] / 2;
        }
        
        // Cuota fase.
        if(isset($frm['coef200']) || isset($frm['coef100'])) {
            $cuotaf = ($aApar[9] * $can) / ($mes * 200);
            $fApa .= "<td class=\"text-right successcolor\">" . number_format($cuotaf,2,',','.') . "</td>";
            $bloEuf += $cuotaf; $fasEuf += $cuotaf; $sumEuf += $cuotaf;
        }
        
        // Diferencias.
        if (isset($frm['dife'])) {
            $resta = $cuotau - $cuotaf;
            $fApa .= "<td class=\"text-right dangercolor\">" . number_format($resta,2,',','.') . "</td>";
            $bloRes += $resta; $fasRes += $resta; $sumRes += $resta;
        }
        
        // Coeficiente escalera 100%
        if(isset($frm['coeblo'])) {
            $cuotab = ($aApar[10] * $can) / ($mes * 100);
            $fApa .= "<td class=\"text-right\">" . number_format($aApar[10],5,',','.') . "</td><td class=\"text-right successcolor\">" . number_format($cuotab,2,',','.') . "</td>";
            $bloCob += $aApar[10]; $bloEub += $cuotab; $fasCob += $aApar[10]; $fasEub += $cuotab; $sumCob += $aApar[10]; $sumEub += $cuotab;
        }
        
        // Coeficiente garajes 100%
        if(isset($frm['coegar'])) {
            $oApar = new Apartamento($apa);
            $coega = $oApar->getGarajesCoeficiente();
            $cuotag = ($coega) ? ($coega * $can) / ($mes * 100) : 0;
            $fApa .= ($cuotag) ? "<td class=\"text-right\">" . number_format($coega,4,',','.') . "</td><td class=\"text-right successcolor\">" . number_format($cuotag,2,',','.') . "</td>" : "<td>&nbsp;</td><td>&nbsp;</td>";
            $bloCog += $coega; $bloEug += $cuotag; $fasCog += $coega; $fasEug += $cuotag; $sumCog += $coega; $sumEug += $cuotag;
        }
        
        // Cierra las filas.
        $fApa .= "</tr>";
    }
    // Sumas del ultimo portal, ultima fase y totales.
    $fApa .= ($frm['sumas']) ? f_getCalculosSumas($frm, "Portal $portal: ", $bloApa, $bloMe2, $bloCoe, $bloEue, $bloCof, $bloCor, $bloEuf, $bloRes, $bloCob, $bloEub, $bloCog, $bloEug) : "";
    $fApa .= ($frm['sumas']) ? f_getCalculosSumas($frm, "Fase $fase: ", $fasApa, $fasMe2, $fasCoe, $fasEue, $fasCof, $fasCor, $fasEuf, $fasRes, $fasCob, $fasEub, $fasCog, $fasEug) : "";
    $fApa .= ($frm['sumas']) ? f_getCalculosSumas($frm, "Total: ", $sumApa, $sumMe2, $sumCoe, $sumEue, $sumCof, $sumCor, $sumEuf, $sumRes, $sumCob, $sumEub, $sumCog, $sumEug) : "";
    return "<h4><a name=\"inicio\"></a>Cuotas mensuales para pagar la cantidad de " . number_format($can,2,',','.') . " € en un plazo de $meses.</h4><table class=\"table table-condensed table-ultra\">$fTit$fApa</table>";
}

/**
 * Obtiene el titulo para los calculos seleccionados.
 * 
 * @param array $frm Datos del formulario.
 * @return string Codigo HTML del titulo.
 */
function f_getCalculosTitulo($frm) {
    $fTit = "<tr>";
    $fTit .= (isset($frm['codigo'])) ? "<th>C&oacute;digo</th>" : "";
    $fTit .= "<th>Apart.</th>";
    $fTit .= (isset($frm['fase'])) ? "<th>Fase</th>" : "";
    $fTit .= (isset($frm['metros'])) ? "<th class=\"text-right\">Metros</th>" : "";
    $fTit .= (isset($frm['coeur'])) ? "<th class=\"text-right\">% Urbanizaci&oacute;n</th><th class=\"text-right\">Cuota</th>" : "";
    $fTit .= (isset($frm['coef200'])) ? "<th class=\"text-right\">Fase 200%</th>" : "";
    $fTit .= (isset($frm['coef100'])) ? "<th class=\"text-right\">Fase 100%</th>" : "";
    $fTit .= (isset($frm['coef200']) || isset($frm['coef100'])) ? "<th class=\"text-right\">Cuota</th>" : "";
    $fTit .= (isset($frm['dife'])) ? "<th class=\"text-right\">Resta</th>" : "";
    $fTit .= (isset($frm['coeblo'])) ? "<th class=\"text-right\">% Bloque</th><th class=\"text-right\">Cuota</th>" : "";
    $fTit .= (isset($frm['coegar'])) ? "<th class=\"text-right\">% Garaje</th><th class=\"text-right\">Cuota</th>" : "";
    return "$fTit</tr>";    
}

/**
 * Realiza las sumas de los calculos seleccionados.
 * 
 * @param array $frm Datos del formulario.
 * @param string $txt Texto de las sumas.
 * @param int $apa Numero de apartamentos.
 * @param int $me2 Metros cuadrados.
 * @param int $coe Coeficiente urbanizacion.
 * @param int $eue Cuota urbanizacion.
 * @param int $cof Coeficiente fase.
 * @param int $cor Coeficiente fase regularizado.
 * @param int $euf Cuota fase.
 * @param int $res Diferencia.
 * @param int $cob Coeficiente bloque.
 * @param int $eub Cuota bloque.
 * @param int $cog Coeficiente garajes.
 * @param int $eug Cuota garajes.
 * @return string Codigo HTML de las sumas.
 */
function f_getCalculosSumas($frm, $txt, $apa, $me2, $coe, $eue, $cof, $cor, $euf, $res, $cob, $eub, $cog, $eug) {
    $fTit = "<tr>";
    $fTit .= (isset($frm['codigo'])) ? "<th colspan=\"2\">$txt$apa</th>" : "<th>$txt$apa</th>";
    $fTit .= (isset($frm['fase'])) ? "<th>&nbsp;</th>" : "";
    $fTit .= (isset($frm['metros'])) ? "<th class=\"text-right\">" . number_format($me2,2,',','.') . "</th>" : "";
    $fTit .= (isset($frm['coeur'])) ? "<th class=\"text-right\">" . number_format($coe,4,',','.') . "</th><th class=\"text-right successcolor\">" . number_format($eue,2,',','.') . "</th>" : "";
    $fTit .= (isset($frm['coef200'])) ? "<th class=\"text-right\">" . number_format($cof,4,',','.') . "</th>" : "";
    $fTit .= (isset($frm['coef100'])) ? "<th class=\"text-right\">" . number_format($cor,5,',','.') . "</th>" : "";
    $fTit .= (isset($frm['coef200']) || isset($frm['coef100'])) ? "<th class=\"text-right successcolor\">" . number_format($euf,2,',','.') . "</th>" : "";
    $fTit .= (isset($frm['dife'])) ? "<th class=\"text-right dangercolor\">" . number_format($res,2,',','.') . "</th>" : "";
    // La suma de coeficientes y cuotas de portales solo se pone en la suma de portales.
    if (substr($txt, 0, 1) == "P") {
        $fTit .= (isset($frm['coeblo'])) ? "<th class=\"text-right\">" . number_format($cob,4,',','.') . "</th><th class=\"text-right successcolor\">" . number_format($eub,2,',','.') . "</th>" : "";
    } else {
        $fTit .= (isset($frm['coeblo'])) ? "<th>&nbsp;</th><th>&nbsp;</th>" : "";
    }
    $fTit .= (isset($frm['coegar'])) ? "<th class=\"text-right\">" . number_format($cog,4,',','.') . "</th><th class=\"text-right successcolor\">" . number_format($eug,2,',','.') . "</th>" : "";
    return "$fTit</tr>";
}

//--- TRANSFORMAR TEXTOS -----------------------------------------------------//

function f_transformarLabel($tabla, $resul) {
    return ($resul) ? "<b style=\"color:green\">$tabla</b>&nbsp;<span class=\"oi oi-check\"></span>" : "<b style=\"color:red\">$tabla</b>&nbsp;<span class=\"oi oi-x\"></span>";
}

function f_transformarClaves($tabla) {
    $aCla = array();
    switch ($tabla) {
        case "ACTAS_PUNTOS": $aCla = array('FECHA','CODPUN'); break;
        case "ACTAS_TEXTOS": $aCla = array('FECHA','CODPUN','CODAPA'); break;
        case "ADMINISTRACIONES": $aCla = array('CODADM'); break;
        case "GARAJES": $aCla = array('CODGAR','CODAPAR'); break;
        case "JUNTAS": $aCla = array('FECHA'); break;
        case "PERSONAS": $aCla = array('CODPERS'); break;
        case "VOTACIONES": $aCla = array('FECHA','NUMVOT'); break;
    }
    return $aCla;
}