<?php
// 1. Cargar el modelo de Configuración de forma segura
require_once __DIR__ . '/../../models/Configuracion.php';

// 2. Obtener los datos de la empresa
$config = Configuracion::getInfo();
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        
        <a class="navbar-brand d-flex align-items-center" href="/home/index">
            <?php if(!empty($config['logo'])): ?>
                <img src="/img/<?= $config['logo'] ?>?v=<?= time() ?>" 
                     alt="Logo" width="35" height="35" 
                     class="d-inline-block align-text-top rounded-circle me-2 bg-white p-1">
            <?php endif; ?>
            <?= $config['nombre_sistema'] ?>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                
                <li class="nav-item">
                    <a class="nav-link text-warning fw-bold" href="/asistencia/index"><i class="fas fa-clock"></i> Asistencia</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/socios/index"><i class="fas fa-users"></i> Socios</a>
                </li>

                <?php if(isset($_SESSION['user_rol']) && $_SESSION['user_rol'] != 'entrenador'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/caja/index"><i class="fas fa-cash-register"></i> Caja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/suscripciones/index"><i class="fas fa-file-invoice-dollar"></i> Suscripciones</a>
                    </li>
                <?php endif; ?>

                <?php if(isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/planes/index"><i class="fas fa-dumbbell"></i> Planes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gastos/index"><i class="fas fa-money-bill-wave"></i> Gastos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/usuarios/index"><i class="fas fa-user-shield"></i> Usuarios</a>
                    </li>
                <?php endif; ?>

            </ul>

            <ul class="navbar-nav ms-auto border-start ps-3">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?= $_SESSION['user_name'] ?? 'Usuario' ?>
                        <small class="text-muted">(<?= ucfirst($_SESSION['user_rol'] ?? '') ?>)</small>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        
                        <?php if(isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 'admin'): ?>
                            <li><a class="dropdown-item" href="/configuracion/index"><i class="fas fa-cogs"></i> Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        
                        <li><a class="dropdown-item text-danger" href="/auth/logout"><i class="fas fa-sign-out-alt"></i> Salir del Sistema</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>