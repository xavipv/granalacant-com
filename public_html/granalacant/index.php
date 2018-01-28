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
        <?php echo f_getCabeceraHTML("Inicio"); ?>
    </head>
    <body>
        <!-- Barra de navegacion -->
        <?php include 'menu.php'; ?>
        
        <!-- Contenido de la pagina -->
        <div class="container">
            <div class="row">
                <div class="text-center col-sm-12" style="padding: 50px">
                    <h1>Gran Alacant</h1>
                    <h3>Gestión de datos</h3>
                </div>
            </div>    
        </div>
        
        <!-- JavaScript -->
        <?php echo f_getScripts(); ?>
  </body>
</html>
