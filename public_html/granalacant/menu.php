<?php
$busqueda = FALSE;
$activo = f_getMenuAcciones($pagina);
?>
<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#menuprincipal" aria-controls="menuprincipal" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand <?php echo $activo[0]; ?>" href="index.php">Gran Alacant</a>
    <div class="collapse navbar-collapse" id="menuprincipal">
        <ul class="nav nav-pills mr-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo $activo[1]; ?>" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Personas</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="personas.php">Editar</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="personaslis.php">Listado</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo $activo[2]; ?>" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Apartamentos</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="apartamentos.php">Individuales</a>
                    <a class="dropdown-item" href="coeficientes.php">Coeficientes</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Listado</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo $activo[3]; ?>" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Propietarios</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="propietarios.php">Por apartamentos</a>
                    <a class="dropdown-item" href="propper.php">Por personas</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Listado</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo $activo[4]; ?>" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Juntas</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="juntas.php">Datos</a>
                    <a class="dropdown-item" href="asistentes.php">Asistentes</a>
                    <a class="dropdown-item" href="votaciones.php">Votaciones</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Listado juntas</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo $activo[5]; ?>" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Actas</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="actas.php">Visualizar</a>
                    <a class="dropdown-item" href="actasbuscar.php">Buscar</a>
                    <a class="dropdown-item" href="actasedit.php">Editar</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo $activo[6]; ?>" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Otros</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="calculos.php">C&aacute;lculos</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="transformar.php">Trasformar textos</a>
                </div>
            </li>
        </ul>
        <?php if($busqueda) { ?>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="text" placeholder="Buscar" onfocus="xajax_buscar('<?php echo $pagina; ?>', this.value);"  onkeyup="xajax_buscar('<?php echo $pagina; ?>', this.value);">
        </form>
        <?php } ?>
  </div>
</nav>

