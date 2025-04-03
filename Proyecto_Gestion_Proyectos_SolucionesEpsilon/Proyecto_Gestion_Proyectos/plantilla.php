<?php

function MostrarNavbar()
{
   
    ob_start();
    require_once BASE_PATH . 'db_config.php';

    $userRole = isset($_SESSION["role_id"]) ? $_SESSION["role_id"] : '';

    ?>
    <head>
        <meta charset="UTF-8" />
        <link rel="stylesheet" href="../CSS/estilos.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://kit.fontawesome.com/0a39c8afa7.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>

    <nav class="navbar navbar-expand-lg navbar-dark py-2">
        <div class="container-fluid">
            <a href="<?= BASE_URL ?>index.php" class="navbar-brand">
            <img src="<?= IMG_URL ?>Logo_SE.png" alt="Logo" width="30" height="30">
                Soluciones Epsilon
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                <ul class="navbar-nav">
                    <?php if ($userRole == 1) : ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>contabilidad.php">Contabilidad</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>RPA.php">RPA</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>listar_usuarios.php">Empleados</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>listar_evaluaciones.php">Evaluaciones</a></li>
                    <?php endif; ?>     
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>listar_proyectos.php">Proyectos</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>plantillas/main.php">Plantilla</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>reportes.php">Reportes</a></li>
                </ul> 

                <ul class="navbar-nav d-flex flex-row">
                    <li class="nav-item px-2">
                        <a class="nav-link" href="#"><i class="fa-solid fa-bell"></i></a>
                    </li>
                    <li class="nav-item dropdown px-2">
                        <a class="nav-link" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-user"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#">Perfil</a></li>
                            <li><a class="dropdown-item" href="#">Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>logout.php">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php
    $output = ob_get_clean();
    echo $output;
}
?>
