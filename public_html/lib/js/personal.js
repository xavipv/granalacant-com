//--- EDITOR Y CALENDARIO ----------------------------------------------------//

/**
 * Crea un editor de texto.
 *
function js_editor() {
    $(".editor").jqte({
        color: false,
        format: false,
        fsize: false,
        link: false,
        unlink: false,
        titletext:[
            {title:"Formato"},
            {title:"Tamaño de fuente"},
            {title:"Color"},
            {title:"Negrita",hotkey:"B"},
            {title:"Cursiva",hotkey:"I"},
            {title:"Subrayado",hotkey:"U"},
            {title:"Lista ordenada",hotkey:"."},
            {title:"Lista desordenada",hotkey:","},
            {title:"Subíndice",hotkey:"flecha abajo"},
            {title:"Superíndice",hotkey:"flecha arriba"},
            {title:"Aumentar sangrado",hotkey:"flecha izquierda"},
            {title:"Disminuir sangrado",hotkey:"flecha derecha"},
            {title:"Justificado izquierda"},
            {title:"Justificado centro"},
            {title:"Justificado derecha"},
            {title:"Tachado",hotkey:"K"},
            {title:"Añadir enlace",hotkey:"L"},
            {title:"Quitar enlace",hotkey:""},
            {title:"Eliminar estilos",hotkey:"Suprimir"},
            {title:"Línea horizontal",hotkey:"H"},
            {title:"Código",hotkey:""}
        ]
    });
} */

function js_editor() {
    $('.editor').trumbowyg({
        lang: 'es',
        svgPath: '/lib/css/icons.svg',
        autogrow: true,
        autogrowOnEnter: true,
        removeformatPasted: true,
        btns: [
        ['viewHTML'],
        ['undo', 'redo'], // Only supported in Blink browsers
        ['strong', 'em', 'del'],
        ['superscript', 'subscript'],
        // ['insertImage'],
        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
        ['unorderedList', 'orderedList'],
        ['preformatted'],
        ['horizontalRule'],
        // ['formatting'],
        // ['link'],
        ['removeformat'],
        ['fullscreen']
    ]
    });
}
/**
 * Se llama al cambiar la fecha del calendario.
 * 
 * @param {string} pag Pagina desde la que se llama.
 * @param {string} id Identificador del elemento.
 * @returns {undefined}
 */
function js_onCalendario(pag, id) {
    // Obtiene la parte numerica del identificador.
    var num = id.replace(/[^0-9.]/g, "");
    
    switch(pag) {
        case "propietarios.php" : 
        case "propper.php" : 
            $("#boton"+num).prop("disabled",false);
            if(num === "0" && $('#nombre0').val() === "") {
                $("#boton"+num).prop("disabled",true);
            }
            break;
        case "votaciones.php" : 
            js_cambiarFechaVotacion($('#fecha').val(), $('#fechainicial').val()); 
            break;
        case "juntas.php" : 
            if(!$('#boasistentes').is(':visible')) { 
                xajax_setJuntaDatosForm($('#fecha').val()); 
            } 
            break;    
    }
}

//--- AJUSTE DE TAMAÑO AUTOMATICO --------------------------------------------// 

/**
 * Lanza el ajuste de tamaño cuando el documento se carga.
 */
$(document).ready(js_bodyResize);

/**
 * Lanza el ajuste de tamaño cuando se cambia el tamaño de la ventana.
 */
$(window).resize(js_bodyResize);

/**
 * Ajusta el tamaño del cuerpo de la pagina.
 */
function js_bodyResize() {
    $("#contenedor").css("height",0);
    var altoPag = $(document).innerHeight();
    var altoCab = $("#cabecera").outerHeight(true);
    var altura = (altoPag - altoCab);
    
    $("#contenedor").css("height", altura + "px");      // Contenedor principal.
    $("#divlistado").css("height", altura + "px");      // Listados.
    $("#divcontenidoacta").css("height", (altura - 10) + "px");
    setTimeout(js_busquedaResize,1000);
    setTimeout(js_asistentesResize,1000);
}

/**
 * Ajusta el tamaño del DIV de busqueda.
 */
function js_busquedaResize() {
    $("#divbusqueda").css("height",0);
    var altoPag = $(document).innerHeight();
    var altoCab = $("#cabecera").outerHeight(true);
    var altura = (altoPag - altoCab - 10);
    var altoFor = $("#divformulario").outerHeight(true);
    var altura1 = (altoFor>10) ? (altura - altoFor) : (altura / 2);
    $("#divbusqueda").css("height", altura1 + "px");    // Busquedas.
}

/**
 * Ajusta el tamaño del DIV de asistentes.
 */
function js_asistentesResize() {
    $("#divformularioasis").css("height",0);
    var altoPag = $(document).innerHeight();
    var altoCab = $("#cabecera").outerHeight(true);
    var altura = (altoPag - altoCab - 16);
    var altoFor = $("#divcabeceraasis").outerHeight(true);
    var altura1 = (altoFor > 10) ? (altura - altoFor) : (altura / 2);
    $("#divformularioasis").css("height", altura1 + "px");
}

function js_sumarTodos(por) {
    js_sumar(por, "me");
    js_sumar(por, "te");
    js_sumar(por, "cu");
    js_sumar(por, "cf");
    js_sumar(por, "cb");
}

function js_sumar(por, id) {
    var col = id.substr(0, 2);
    var fas = (por > 15) ? "II" : "I";
    var spo = "p" + col + por;
    
    var sto = "t" + col;
    var apa = js_apartPortal(por);
    var ain = apa[0];
    var afi = apa[1];
    var dec = 4;
    var sup = 0;
    var sur = 0;
    var nom;
    
    if (col === "me" || col === "te" || col === "cb") {
        dec = 2;
    }
    
    for(i=ain; i<=afi; i++) {
        nom = col + i;
        sup += parseFloat($('#'+nom).val());
        if(col === "cf") {
            sur += parseFloat($('#cr'+i).val());
        }
    }
    
    $('#'+spo).val(sup.toFixed(dec));
    
    if (col === "cf") {
        $('#pcr'+por).val(sur.toFixed(5));
    }
    js_sumarFase(col, fas);
}

function js_sumarFase(col, fas) {
    var sfa = "f" + col + fas;
    var a = (fas === "I") ? [1, 15] : [16, 26];
    var nom;
    var suf = 0;
    var sur = 0;
    var dec = (col === "me" || col === "te" || col === "cb") ? 2 : 4;
    
    for(i=a[0]; i<=a[1]; i++) {
        nom = "p" + col + i;
        suf += parseFloat($('#'+nom).val());
        if(col === "cf") {
            sur += parseFloat($('#pcr'+i).val());
        }
    }
    
    $('#'+sfa).val(suf.toFixed(dec));
    
    if (col === "cf") {
        $('#fcr'+fas).val(sur.toFixed(5));
    }
    js_sumarTotal(col);
}

function js_sumarTotal(col) {
    var f1 = "f" + col + "I";
    var f2 = "f" + col + "II";
    var dec = (col === "me" || col === "te" || col === "cb") ? 2 : 4;
    var sut = parseFloat($('#'+f1).val()) + parseFloat($('#'+f2).val());
    
    $('#t'+col).val(sut.toFixed(dec));
    
    if(col === "cf") {
        var sur = parseFloat($('#fcrI').val()) + parseFloat($('#fcrII').val());
        $('#tcr').val(sur.toFixed(5));
    }
}

function js_apartPortal(por) {
    var a = [0, 0];
    switch(por) {
        case 1: a = [1, 15]; break;
        case 2: a = [16, 29]; break;
        case 3: a = [30, 41]; break;
        case 4: a = [42, 57]; break;
        case 5: a = [58, 70]; break;
        case 6: a = [71, 90]; break;
        case 7: a = [91, 106]; break;
        case 8: a = [107, 122]; break;
        case 9: a = [123, 135]; break;
        case 10: a = [136, 149]; break;
        case 11: a = [150, 164]; break;
        case 12: a = [165, 173]; break;
        case 13: a = [174, 181]; break;
        case 14: a = [182, 193]; break;
        case 15: a = [194, 209]; break;
        
        case 16: a = [210, 224]; break;
        case 17: a = [225, 238]; break;
        case 18: a = [239, 250]; break;
        case 19: a = [251, 266]; break;
        case 20: a = [267, 281]; break;
        case 21: a = [282, 301]; break;
        case 22: a = [302, 316]; break;
        case 23: a = [317, 332]; break;
        case 24: a = [333, 344]; break;
        case 25: a = [345, 358]; break;
        case 26: a = [359, 373]; break;
    }
    return a;
}

//--- UTILIDADES -------------------------------------------------------------//

/**
 * Hace que todos los INPUT que sean de clase 'solonumeros' solo permitan el uso
 * de numeros y el punto decimal (una vez), los otros caracteres seran ignorados.
 */
$(function () {
    $(".solonumeros").keydown(function (event) {
        if (event.shiftKey === true) {
            event.preventDefault();
        }

        if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode === 8 || event.keyCode === 9 || event.keyCode === 37 || event.keyCode === 39 || event.keyCode === 46 || event.keyCode === 190) {

        } else {
            event.preventDefault();
        }

        if($(this).val().indexOf('.') !== -1 && event.keyCode === 190) {
            event.preventDefault();
        }
    });
});

/**
 * Crea los tooltips.
 */
$(function () {
  $('[data-toggle="tooltip"]').tooltip();
});

function js_existeTooltip(apa, txt) {
    var id = "#apartamento" + apa;
    if( $(id).attr('data-original-title') !== "" ) {
        $(id).tooltip({
            title: txt, 
            placement: 'left', 
            trigger: 'manual', 
            animation: false 
        }); 
        $(id).css('cursor','pointer'); 
        $(id).css('color','green');
        $(id).click(function() { $(id).tooltip('toggle'); }); 
        $(id).tooltip('show');
    } 
}

function js_eliminarTooltips(ch) {
    for (var i=0; i<=26; i++) {
        if ( ch === true ) {
            $('#apartamento' + i).tooltip('show');
        } else { 
            $('#apartamento' + i).tooltip('hide');
        }
    }
}

function js_comprobarBotones() {
    for (var i=0; i<=26; i++) {
        if ( $('#boton' + i) && $('#boton' + i).prop('disabled') === false ) {
            if (confirm("El asistente " + i + " no se ha guardado. ¿Quieres grabarlo?") === true) {
                $('#boton' + i).focus();
                return false;
            }
        } 
    }
    return true;
}


//--- VOTACIONES -------------------------------------------------------------//

function js_ponerEtiquetas(op) {
    // Lee los valores de la opcion.
    var o = $('#opcion' + op).val();
    
    // Si etá vacío pone Opc. X
    o = (o === "") ? "Opc. " + op : o;
    
    // Pone los datos en las cabeceras.
    for(var i=1; i<=26; i++) {
        $('#titop' + op + i).attr('title',o);
        $('#titop' + op + i).html(o.substr(0,8));
    }
}

/**
 * Marca las opciones de voto y presente cuando se marca o desmarca la de asistencia.
 * 
 * @param {string} id Identificador de la casilla.
 * @param {int} a Número de apartamento actual.
 * @param {array} ap Apartamentos del propietario.
 * @param {boolean} ch Chequear o no chequear.
 */
function js_marcarOpc(id, a, ap, ch) {
    var x, i;
    
    a  = (!Array.isArray(a)) ? [a] : a;
    ap = (!Array.isArray(ap)) ? [ap] : ap;
    i  = ($("#sincro").prop("checked") === true) ? ap : a;
    
    if(id.substr(0,4) === "asis") {
        for(x in i) {
            $("#asis"+i[x]).prop("checked",ch);
            $("#vota"+i[x]).prop("checked",ch);
            $("#pres"+i[x]).prop("checked",ch);
        }
    } else if(id.substr(0,4) === "vota") {
        for(x in i) {
            $("#vota"+i[x]).prop("checked",ch);
        }
    } else if(id.substr(0,4) === "pres") {
        for(x in i) {
            $("#pres"+i[x]).prop("checked",ch);
        }
    }
    js_comprOpc(i);
}

/**
 * Activa o desactiva las casillas de las votaciones según la asistencia.
 * 
 * @param {array} i Números de apartamentos.
 *
 */
function js_comprOpc(i) {
    var x;
    if(!Array.isArray(i)) {
        i = [i];
    }
    for(x in i) {
        if($("#asis"+i[x]).prop("checked") === false) {
            // No asiste. Desactiva todo.
            $("#vota"+i[x]).prop("disabled",true);
            $("#pres"+i[x]).prop("disabled",true);
            $("#res1"+i[x]).prop("disabled",true);
            $("#res2"+i[x]).prop("disabled",true);
            $("#res3"+i[x]).prop("disabled",true);
            $("#res4"+i[x]).prop("disabled",true);
        } else {
            // Sí asiste. Activa el voto y presente.
            $("#vota"+i[x]).prop("disabled",false);
            $("#pres"+i[x]).prop("disabled",false);

            // Comprueba si la casilla voto o presente están elegidas.
            if($("#vota"+i[x]).prop("checked") === false || $("#pres"+i[x]).prop("checked") === false) {
                // Alguna de las dos no está elegida. Desactiva.
                $("#res1"+i[x]).prop("disabled",true);
                $("#res2"+i[x]).prop("disabled",true);
                $("#res3"+i[x]).prop("disabled",true);
                $("#res4"+i[x]).prop("disabled",true);
            } else {
                // Ambas están elegidas. Activa.
                $("#res1"+i[x]).prop("disabled",false);
                $("#res2"+i[x]).prop("disabled",false);
                $("#res3"+i[x]).prop("disabled",false);
                $("#res4"+i[x]).prop("disabled",false);
            }
        }
    }
    js_sumarAsistentes();
}

/**
 * Sincroniza los votos de un propietario con varios apartamentos.
 * 
 * @param {array} i Códigos de apartamentos.
 * @param {int} p Numero de opcion.
 */
function js_sincronizar(i,p) {
    var x;
    
    // Comprueba si la casilla de sincronizar se ha marcado.
    if($("#sincro").prop("checked") === true) {
        // Sincroniza los votos
        if(!Array.isArray(i)) {
            i = [i];
        }
        // Marca los apartamentos del propietario.
        for(x in i) {
            $("#res"+p+i[x]).prop("checked",true); 
        }
    }
    // Recalcula los datos.
    js_sumarVotos();
}

/**
 * Pone en la cabecera del formulario de votaciones la suma de datos de asistencia.
 */
function js_sumarAsistentes() {
    var v1=0, v2=0, v3=0, v4=0;
    
    // Recorre los apartamentos.
    for(var i=1; i<=373; i++) {
        asi = document.getElementById("asis"+i);
        vot = document.getElementById("vota"+i);
        pre = document.getElementById("pres"+i);
        
        // Suma los asistentes, votos y presentes.
        v1 += (asi.disabled === false && asi.checked === true) ? 1 : 0;
        v2 += (vot.disabled === false && vot.checked === true) ? 1 : 0;
        v4 += (pre.disabled === false && pre.checked === true) ? 1 : 0;
    }
    // Sin voto.
    v3 = v1 - v2;
    
    // Pone los valores.
    $("#sumasis").val(v1);
    $("#sumvota").val(v2);
    $("#sumnovoto").val(v3);
    $("#sumpres").val(v4);
    
    // Recalcula los datos.
    js_sumarVotos();
}

/**
 * Recalcula los datos de los votos y coeficientes, después los pone en la web.
 * 
 * @global array coeficienteJS
 */
function js_sumarVotos() {
    var v1=0, v2=0, v3=0, v4=0;
    var c, c1e=0, c2e=0, c3e=0, c4e=0, c1f=0, c2f=0, c3f=0, c4f=0, c1r=0, c2r=0, c3r=0, c4r=0;
    var svot, scoe, scof, scor;
    var s, opc, coe, cof, cor;
    
    // Recorre los apartamentos.
    for(var i=1; i<=373; i++) {
        // Obtiene las opciones disponibles de cada apartamento.
        opc = document.getElementsByName("opciones["+i+"]");
        
        // Recorre las opciones del apartamento.
        for(var j=0; j<opc.length; j++) { 
            // Comprueba que la opción esté activada y chequeada.
            if(opc[j].disabled === false && opc[j].checked === true) {
                // La opción está chequeada.
                s = opc[j].value;       // Número de opción: 0, 1, 2, 3.
                c = coeficienteJS[i];   // Coeficientes del apartamento.
                coe = c[0];     // Coeficiente urbanizacion.
                cof = c[1];     // Coeficiente fase al 200%.
                cor = c[1]/2;   // Coeficiente fase al 100%.
                switch(j) {
                    case 0 : v1++; c1e += coe; c1f += cof; c1r += cor; break;
                    case 1 : v2++; c2e += coe; c2f += cof; c2r += cor; break;
                    case 2 : v3++; c3e += coe; c3f += cof; c3r += cor; break;
                    case 3 : v4++; c4e += coe; c4f += cof; c4r += cor; break;
                }
            }
        }
    }
    svot = v1+v2+v3+v4;
    scoe = c1e+c2e+c3e+c4e;
    scof = c1f+c2f+c3f+c4f;
    scor = c1r+c2r+c3r+c4r;
    
    $("#votos1").val(v1);
    $("#votos2").val(v2);
    $("#votos3").val(v3);
    $("#votos4").val(v4);
    $("#sumvotos").val(svot);
    
    // Coeficiente calculado.
    $("#coe1").val(c1e.toFixed(4));
    $("#coe2").val(c2e.toFixed(4));
    $("#coe3").val(c3e.toFixed(4));
    $("#coe4").val(c4e.toFixed(4));
    $("#sumcoe").val(scoe.toFixed(4));
    
    // Coeficiente fase.
    $("#cof1").val(c1f.toFixed(4));
    $("#cof2").val(c2f.toFixed(4));
    $("#cof3").val(c3f.toFixed(4));
    $("#cof4").val(c4f.toFixed(4));
    $("#sumcof").val(scof.toFixed(4));
    
    // Coeficiente fase al 100%.
    $("#cor1").val(c1r.toFixed(5));
    $("#cor2").val(c2r.toFixed(5));
    $("#cor3").val(c3r.toFixed(5));
    $("#cor4").val(c4r.toFixed(5));
    $("#sumcor").val(scor.toFixed(5));
}

/**
 * Oculta o muestra las filas de los no asistentes.
 * 
 * @param {Boolean} oc Indica si se oculta o se muestra.
 * @returns {Boolean} false.
 */
function js_ocultarFilas(oc) {
    var fila, opci;

    for(var i=1; i<=373; i++) {
        fila = $("#fila"+i);
        opci = $("#asis"+i).prop('checked');
        if(opci === false && oc === true) {
            fila.hide();
        } else {
            fila.show();
        } 
    } 
    js_ocultarTitulos(oc);
    return false;
}

function js_ocultarTitulos(oc) {
    var fila, filas, numfi, id, por='';
    var v=0, o=0;
    filas = document.getElementById("tablavot").rows;
    numfi = filas.length;
    
    for(var i=0; i<numfi; i++) {
        fila = filas[i];
        if (oc === true) {
            if ( $(fila).is(":visible") ) {
                id = $(fila).prop("id");
                if(id.substr(0,6) === "portal") {
                    if(por !== "") {
                        $('#'+por).hide();
                    }
                    por = id;
                } else {
                    por = "";
                }
            } 
        } else {
            $(fila).show();
        }
    }
}

function js_cambiarFechaVotacion(nueva, vieja) {
    if (nueva !== vieja && nueva) {
        if (confirm("Si cambias la fecha se perderán todos los datos que no hayan sigo grabados.\n¿Seguro que quieres cambiar la fecha " + vieja + " por la nueva fecha " + nueva + "?")) {
            xajax_setVotacionDatosForm(nueva, 1);
            $('#fechainicial').val(nueva);
        } else {
            $('.calendario').datepicker('destroy');
            $('#fecha').val($('#fechainicial').val());
            xajax_setCalendario(false, true);
        }
    }
}

function js_cambiarNumeroVotacion(nuevo, viejo) {
    if (nuevo !== viejo) {
        if (confirm("Si cambias el número de votación se perderán todos los datos que no hayan sigo grabados.\n¿Seguro que quieres cambiar la votación " + viejo + " por la nueva votación " + nuevo + "?")) {
            xajax_setVotacionDatosForm($('#fecha').val(), nuevo);
        } else {
            $('#votacion').val($('#votacioninicial').val());
        }
    }
}

function js_deshacerActa(fecha) {
    if (confirm("Si deshaces todos los cambios no guardados se perderán.\n¿Seguro que quieres continuar?")) {
        xajax_getActaDatosForm(fecha);
    }
}

function js_eliminarActa(fecha) {
    if (confirm("Se va a eliminar el acta actual, los datos no se podrán recuperar.\n¿Seguro que quieres continuar?")) {
        xajax_eliminarActa(fecha);
    }
}

/**
 * Marca o desmarca las casillas para transformar segun la eleccion de la tabla.
 * 
 * @param {string} tab Identificador de tabla (t1).
 * @param {boolean} val Valor para el chequeo.
 */
function js_transformar(tab, val) {
    var num = document.getElementsByName(tab+'[]').length - 1;
    for(var i=1; i<=num; i++) {
        $('#'+tab+'-'+i).prop('checked',val); 
        $('#'+tab+'-'+i).prop('disabled',!val); 
    }
}

function js_transformarCheck(val) {
    for(var i=1; i<=7; i++) {
        var n = 't' + i;
        $('#'+n).prop('checked', val); js_transformar(n, val);
    }
} 