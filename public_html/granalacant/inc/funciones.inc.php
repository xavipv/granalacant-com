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
    return ucwords(strtolower($nom)," \t\r\n\f\v-.");
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


/**
 * Obtiene los enlaces a las página CSS para la página web.
 * 
 * @return string Código HTML de los enlaces.
 *
function f_getEstilos() {
    $css = array(
        'bootstrap.min.css', 
        'open-iconic-bootstrap.min.css', 
        'bootstrap-datepicker.min.css', 
        'jquery-te-1.4.0.css', 
        'personal.css'
    );
    $txt = "";
    foreach ($css as $f) {
        $fich = _ULIB_ . "/css/$f";
        f_comprobarFichero($fich);
        $txt .= "<link href=\"" . _ULIB_ . "/css/$f\" rel=\"stylesheet\">";
    }
    return $txt;
} */

/**
 * Obtiene las llamadas a los ficheros JavaScript.
 * 
 * @global /xajax $xajax Instancia de XAJAX.
 * @return string Código HTML para los scripts.
 *
function f_getScripts() {
    global $xajax;
    $js = array(
        'jquery-3.2.1.min.js', 
        'tether.min.js', 
        'bootstrap.min.js', 
        'bootstrap-datepicker.min.js', 
        'bootstrap-datepicker.es.min.js', 
        'jquery-te-1.4.0.min.js', 
        'personal.js'
    );
    $txt = "";
    foreach ($js as $f) {
        $fich = _ULIB_ . "/js/$f";
        f_comprobarFichero($fich);
        $txt .= "<script src=\"$fich\"></script>";
    }
    return $txt . $xajax->printJavascript();
} */


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
        case "personas.php":
            $activo[1] = "active";
            $busqueda = TRUE;
            break;
        case "listpers.php":
            $activo[1] = "active";
            break;
        // Apartamentos.
        case "apartamentos.php":
            $activo[2] = "active";
            $busqueda = TRUE;
            break;
        case "coeficientes.php":
            $activo[2] = "active";
            break;
        case "listapar.php":
            $activo[2] = "active";
            break;
        // Propietarios.
        case "propietarios.php":
            $activo[3] = "active";
            $busqueda = TRUE;
            break;
        case "propper.php":
            $activo[3] = "active";
            $busqueda = TRUE;
            break;
        case "listprop.php":
            $activo[3] = "active";
            break;
        // Juntas.
        case "juntas.php":
            $activo[4] = "active";
            break;
        case "asistentes.php":
            $activo[4] = "active";
            break;
        case "votaciones.php":
            $activo[4] = "active";
            break;
        case "listjunt.php":
            $activo[4] = "active";
            break;
        // Actas.
        case "actas.php":
            $activo[5] = "active";
            break;
        case "actasbuscar.php":
            $activo[5] = "active";
            break;
        case "actasedit.php":
            $activo[5] = "active";
            break;
        // Otros.
        case "listcalc.php":
            $activo[6] = "active";
            break;
        // Inicio.
        default:
            $activo[0] = "active";
            break;
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
    return ($oPer->grabar()) ? "Los datos se han guardado correctamente." : "Error al guardar los datos.";
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

function f_getSelectPortales($sel='') {
    global $oApars;
    $aDat = $oApars->getPortalesDistintos();
    return f_getSelectSimple($aDat, 'portal', $sel, 'form-control');
} 

function f_getSelectPisos($sel='') {
    global $oApars;
    $aDat = $oApars->getPisosDistintos();
    return f_getSelectSimple($aDat, 'piso', $sel, 'form-control');
}

function f_getSelectLetras($sel='') {
    global $oApars;
    $aDat = $oApars->getLetrasDistintas();
    return f_getSelectSimple($aDat, 'letra', $sel, 'form-control');
}

function f_getSelectFases($sel='') {
    global $oApars;
    $aDat = $oApars->getFasesDistintas();
    return f_getSelectSimple($aDat, 'fase', $sel, 'form-control');
}

function f_getSelectTipos($sel='') {
    global $oApars;
    $aDat = $oApars->getTiposDistintos();
    return f_getSelectSimple($aDat, 'tipo', $sel, 'form-control');
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
    
    $sTabla  = "<a name=\"inicio\"></a><table class=\"table table-sm\" style=\"width:100%\">";
    $sTabla .= "<tr><th class=\"text-center\" colspan=\"10\">Fase $sFase</th></tr>";
    $sTabla .= "<tr><th class=\"text-center\" colspan=\"10\"><a name=\"ini$iPortal\" href=\"#inicio\">Portal $iPortal</a></th></tr><tr><th colspan=\"2\" class=\"text-center\">Apartamento</th><th class=\"text-center\">Superficie</th><th class=\"text-center\">Terraza</th><th class=\"text-center\">Urbanizaci&oacute;n</th><th class=\"text-center\">Fase 200%</th><th class=\"text-center\">Fase 100%</th><th class=\"text-center\">Escalera</th><th>&nbsp;</th><th>&nbsp;</th></tr>";
    
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
            $sTabla .= "<tr class=\"active\"><th class=\"text-center\" colspan=\"10\"><a name=\"ini$iPortal\" href=\"#inicio\">Portal $iPortal</a></th></tr><tr><th colspan=\"2\" class=\"text-center\">Apartamento</th><th class=\"text-center\">Superficie</th><th class=\"text-center\">Terraza</th><th class=\"text-center\">Urbanizaci&oacute;n</th><th class=\"text-center\">Fase 200%</th><th class=\"text-center\">Fase 100%</th><th class=\"text-center\">Escalera</th><th>&nbsp;</th><th>&nbsp;</th></tr>";
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
    global $pagina, $oPers;
    $aPer = $oPers->getPropietarosNumPropiedadesAltaBaja();
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
    global $oPers;
    $aPro = $oPers->getPropiedades($per);
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
    global $oPers;
    
    $aPro = ($repr == 'S') ? $oPers->getRepresentantes($apa, $fecha) : $oPers->getPropietariosEnFecha($apa, $fecha, FALSE);
    $func = "$('#boton$apa').prop('disabled',false); if(!this.value){ $('#voto$apa').prop('checked',false); } else { $('#voto$apa').prop('checked',true); }; $onCh";
    
    return f_getSelect($aPro, "nombre$apa", $sel, "form-control", $func, TRUE);
}

//--- VOTACIONES -------------------------------------------------------------//

function f_getVotacionesAnyos() {
    $oVots = new Votaciones();
    $aAnys = $oVots->getVotacionesAnyos();
    $sAnys = "";
    foreach ($aAnys as $any) {
        $sAnys .= "<a href=\"#any$any\">$any</a>&nbsp;";
    }
    return $sAnys;
}

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

function f_getAsistentesVotacion($fecha, $num=1) {
    global $oApars, $oPers;
    
    $aApars = $oApars->getApartamentos();
    $oVots = new Votaciones();
    $oVotx = new Votacion($fecha, $num);
    $oVota = (!$oVots->existeVotacion($fecha, $num)) ? f_cargarAsistentesJunta($oVotx) : $oVotx;
    
    $opc1 = ($oVota->getOpcion1()) ? $oVota->getOpcion1() : "Opc. 1";
    $opc2 = ($oVota->getOpcion2()) ? $oVota->getOpcion2() : "Opc. 2";
    $opc3 = ($oVota->getOpcion3()) ? $oVota->getOpcion3() : "Opc. 3";
    $opc4 = ($oVota->getOpcion4()) ? $oVota->getOpcion4() : "Opc. 4";
    
    $portal = "";
    $tabla  = "<table id=\"tablavot\" class=\"table table-sm\">";
    
    foreach ($aApars as $apa => $aApartamento) {
        
        if ($portal != $aApartamento[0]) {
            // Cambio de portal.
            $portal = $aApartamento[0];
            $tabla .= "<tr id=\"portal$portal\" style=\"background-color:#F5F5F5\">
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
        
        // Datos del propietario.
        $aProp = $oPers->getPropietarioEnFecha($apa, $fecha, FALSE);    // array('codpers'=>'nombre')
        $iProp = $oPers->getPropietarioEnFechaCodigo($apa, $fecha);     // codpers
        $aApPr = array_keys($oPers->getMisPropiedades($apa, $fecha));   // array(apa1,apa2...)
        $sApPr = "[" . implode(",", $aApPr) . "]";                      // Codigos en formato [1,2,3]
        
        // Datos del propietario.
        //$oProp = new Propiedad($apa);
        //$aProp = $oProp->getPrimerPropietarioAlta();        //array('codpers'=>array('nombre','date','fecha','orden))
        //$iProp = $oProp->getPrimerPropietarioAltaCodigo();  // codpers
        //$aApPr = $oPers->getPropiedadesAltaCodigos($iProp); // Propiedades de la persona.
        //$sApPr = "[" . implode(",", $aApPr) . "]";          // Codigos en formato [1,2,3]
        
        // Datos de la votacion.
        $aVoto = $oVota->getVoto($apa);         // array('asis','vota','pres','res1','res2','res3','res4')
        
        $ch1 = ($aVoto[0] == 'S') ? "checked=\"checked\"" : "";
        $ch2 = ($aVoto[1] == 'S') ? "checked=\"checked\"" : "";
        $ch3 = ($aVoto[2] == 'S') ? "checked=\"checked\"" : "";
        
        $op1 = ($aVoto[3] == 'S') ? "checked=\"checked\"" : "";
        $op2 = ($aVoto[4] == 'S') ? "checked=\"checked\"" : "";
        $op3 = ($aVoto[5] == 'S') ? "checked=\"checked\"" : "";
        $op4 = ($aVoto[6] == 'S') ? "checked=\"checked\"" : "";
        
        $bch = ($aVoto[0] != 'S') ? "disabled=\"disabled\"" : "";
        $bop = ($aVoto[0] != 'S' || $aVoto[1] != 'S' || $aVoto[2] != 'S') ? "disabled=\"disabled\"" : "";
        
        $onc = "js_marcarOpc(this.id, $apa, $sApPr, this.checked);";

        $tabla .= "<tr id=\"fila$apa\"><td class=\"align-middle\">" . $aApartamento[0] . "-" . $aApartamento[1] . $aApartamento[2] . "</td>
                   <td class=\"align-middle\">" . $aProp[$iProp] . "</td>
                   <td class=\"align-middle text-center\"><input type=\"checkbox\" id=\"asis$apa\" name=\"asis[$apa]\" onclick=\"$onc\" $ch1></td>
                   <td class=\"align-middle text-center\"><input type=\"checkbox\" id=\"vota$apa\" name=\"vota[$apa]\" onclick=\"$onc\" $ch2 $bch></td>    
                   <td class=\"align-middle text-center\"><input type=\"checkbox\" id=\"pres$apa\" name=\"pres[$apa]\" onclick=\"$onc\" $ch3 $bch></td>
                   <td class=\"align-middle text-center\"><input type=\"radio\" id=\"res1$apa\" name=\"opciones[$apa]\" onclick=\"js_sincronizar($sApPr,1)\" value=\"1\" $op1 $bop></td>
                   <td class=\"align-middle text-center\"><input type=\"radio\" id=\"res2$apa\" name=\"opciones[$apa]\" onclick=\"js_sincronizar($sApPr,2)\" value=\"2\" $op2 $bop></td>
                   <td class=\"align-middle text-center\"><input type=\"radio\" id=\"res3$apa\" name=\"opciones[$apa]\" onclick=\"js_sincronizar($sApPr,3)\" value=\"3\" $op3 $bop></td>
                   <td class=\"align-middle text-center\"><input type=\"radio\" id=\"res4$apa\" name=\"opciones[$apa]\" onclick=\"js_sincronizar($sApPr,4)\" value=\"4\" $op4 $bop></td></tr>";
    }
    return "$tabla</table>";
}

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

function f_getSelectNumVotaciones($fecha, $vot=1) {
    $oVots = new Votaciones();
    $num   = $oVots->getUltimaVotacion($fecha) + 1;
    $aNum  = array();
    for($i=1; $i<=$num; $i++) {
        $aNum[$i] = $i;
    }
    return f_getSelectSimple($aNum, "votacion", $vot, "form-control form-control-sm", "js_cambiarNumeroVotacion($('#votacion').val(), $('#votacioninicial').val())");
}

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
    $html = "<h3>Encontradas " . count($aBus) . " actas para <em>$lista</em></h3><div id=\"accordion\" role=\"tablist\">"; 
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
    $html = "<div id=\"accordion\" role=\"tablist\">";
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