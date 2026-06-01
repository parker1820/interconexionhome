<?php

session_start();
include("../config/conex.php");

if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit;
}

$idUsuario = $_SESSION['id_usuario'];
$rol = $_SESSION['rol'] ?? '';
$nombreUsuario = $_SESSION['nombre'] ?? '';

$editando = false;
$instalacionEditar = null;

/* ELIMINAR */
if(isset($_GET['eliminar']) && $rol == 'ADMIN'){

    $idEliminar = (int)$_GET['eliminar'];

    $stmt = $conn->prepare("DELETE FROM instalaciones WHERE id = ?");
    $stmt->bind_param("i", $idEliminar);
    $stmt->execute();

    header("Location: instalaciones.php");
    exit;
}

/* CARGAR PARA EDITAR */
if(isset($_GET['editar']) && $rol == 'ADMIN'){

    $idEditar = (int)$_GET['editar'];

    $stmt = $conn->prepare("SELECT * FROM instalaciones WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $idEditar);
    $stmt->execute();

    $resultadoEditar = $stmt->get_result();

    if($resultadoEditar->num_rows > 0){
        $editando = true;
        $instalacionEditar = $resultadoEditar->fetch_assoc();
    }
}

/* GUARDAR */
if(isset($_POST['guardar_instalacion']) && $rol == 'ADMIN'){

    $cliente = trim($_POST['cliente']);
    $telefono = trim($_POST['telefono']);
    $ubicacion = trim($_POST['ubicacion']);
    $prioridad = (int)$_POST['prioridad'];
    $instalador_id = (int)$_POST['instalador_id'];
    $observaciones = trim($_POST['observaciones']);

    $stmt = $conn->prepare("
        INSERT INTO instalaciones
        (cliente, telefono, ubicacion, prioridad, instalador_id, observaciones)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssiis",
        $cliente,
        $telefono,
        $ubicacion,
        $prioridad,
        $instalador_id,
        $observaciones
    );

    $stmt->execute();

    header("Location: instalaciones.php");
    exit;
}

/* ACTUALIZAR */
if(isset($_POST['actualizar_instalacion']) && $rol == 'ADMIN'){

    $id = (int)$_POST['id'];
    $cliente = trim($_POST['cliente']);
    $telefono = trim($_POST['telefono']);
    $ubicacion = trim($_POST['ubicacion']);
    $prioridad = (int)$_POST['prioridad'];
    $instalador_id = (int)$_POST['instalador_id'];
    $estado = trim($_POST['estado']);
    $observaciones = trim($_POST['observaciones']);

    if($estado == 'TERMINADA'){

        $stmt = $conn->prepare("
            UPDATE instalaciones
            SET cliente = ?,
                telefono = ?,
                ubicacion = ?,
                prioridad = ?,
                instalador_id = ?,
                estado = ?,
                observaciones = ?,
                fecha_terminada = NOW()
            WHERE id = ?
        ");

    }else{

        $stmt = $conn->prepare("
            UPDATE instalaciones
            SET cliente = ?,
                telefono = ?,
                ubicacion = ?,
                prioridad = ?,
                instalador_id = ?,
                estado = ?,
                observaciones = ?,
                fecha_terminada = NULL
            WHERE id = ?
        ");
    }

    $stmt->bind_param(
        "sssiissi",
        $cliente,
        $telefono,
        $ubicacion,
        $prioridad,
        $instalador_id,
        $estado,
        $observaciones,
        $id
    );

    $stmt->execute();

    header("Location: instalaciones.php");
    exit;
}

/* CAMBIAR ESTADO INSTALADOR */
if(isset($_POST['cambiar_estado'])){

    $idInstalacion = (int)$_POST['id_instalacion'];
    $nuevoEstado = $_POST['estado'];

    if($nuevoEstado == 'TERMINADA'){

        $stmt = $conn->prepare("
            UPDATE instalaciones
            SET estado = ?, fecha_terminada = NOW()
            WHERE id = ?
            AND instalador_id = ?
        ");

    }else{

        $stmt = $conn->prepare("
            UPDATE instalaciones
            SET estado = ?
            WHERE id = ?
            AND instalador_id = ?
        ");
    }

    $stmt->bind_param("sii", $nuevoEstado, $idInstalacion, $idUsuario);
    $stmt->execute();

    header("Location: instalaciones.php");
    exit;
}

/* INSTALADORES */
$instaladores = $conn->query("
    SELECT id, nombre, usuario
    FROM usuarios
    WHERE rol = 'INSTALADOR'
    ORDER BY nombre ASC
");

/* CONSULTA PRINCIPAL */
if($rol == 'ADMIN'){

    $instalaciones = $conn->query("
        SELECT instalaciones.*, usuarios.nombre AS instalador
        FROM instalaciones
        LEFT JOIN usuarios ON instalaciones.instalador_id = usuarios.id
        ORDER BY
            FIELD(instalaciones.estado, 'PENDIENTE', 'EN_PROCESO', 'TERMINADA'),
            instalaciones.prioridad ASC,
            instalaciones.fecha_creacion DESC
    ");

}else{

    $stmt = $conn->prepare("
        SELECT *
        FROM instalaciones
        WHERE instalador_id = ?
        AND estado != 'TERMINADA'
        ORDER BY prioridad ASC, fecha_creacion ASC
    ");

    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();

    $instalaciones = $stmt->get_result();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>Instalaciones</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
}

.sidebar{
    width:250px;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    background:#0f172a;
    padding:20px;
    color:white;
}

.logo{
    font-size:24px;
    font-weight:bold;
    margin-bottom:40px;
}

.menu a{
    display:block;
    color:white;
    text-decoration:none;
    padding:12px 15px;
    border-radius:10px;
    margin-bottom:10px;
}

.menu a:hover{
    background:#1e293b;
}

.main-content{
    margin-left:250px;
    padding:30px;
}

.card{
    border:none;
    border-radius:15px;
}

.prioridad-1{
    background:#dc3545;
    color:white;
}

.prioridad-2{
    background:#fd7e14;
    color:white;
}

.prioridad-3{
    background:#ffc107;
    color:black;
}

.prioridad-4{
    background:#0dcaf0;
    color:black;
}

.prioridad-5{
    background:#6c757d;
    color:white;
}

@media(max-width:768px){

    .sidebar{
        position:relative;
        width:100%;
        height:auto;
    }

    .main-content{
        margin-left:0;
        padding:15px;
    }
}
</style>

</head>

<body>

<div class="sidebar">

    <div class="logo">
        📡 INTERCONEXION
    </div>

      <div class="menu">
        <a href="../sistemaInventario/inventario.php">📦 Inventario</a>
        <a href="../IPS/ips_instalador.php">🌐 IPS</a>
        <a href="instalaciones.php">🛠 Instalaciones</a>
        <a href="../sistemaInventario/reportes.php">📋 Reportes</a>
        <a href="../logout.php">🚪 Salir</a>
    </div>

    <div class="mt-5">
        <small>
            Sesión:
            <b><?= htmlspecialchars($nombreUsuario) ?></b>
        </small>
    </div>

</div>

<div class="main-content">

    <div class="container-fluid">

        <h2 class="mb-4">🛠 Instalaciones</h2>

        <?php if($rol == 'ADMIN'){ ?>

        <div class="card shadow p-4 mb-4">

            <h4>
                <?php if($editando){ ?>
                    Editar instalación
                <?php } else { ?>
                    Agregar instalación
                <?php } ?>
            </h4>

            <form method="POST" class="row g-3 mt-2">

                <?php if($editando){ ?>
                    <input type="hidden"
                           name="id"
                           value="<?= $instalacionEditar['id'] ?>">
                <?php } ?>

                <div class="col-md-4">
                    <label>Cliente</label>
                    <input type="text"
                           name="cliente"
                           class="form-control"
                           value="<?= $editando ? htmlspecialchars($instalacionEditar['cliente']) : '' ?>"
                           required>
                </div>

                <div class="col-md-4">
                    <label>Teléfono</label>
                    
                    <input type="text"
                           name="telefono"
                           class="form-control"
                           value="<?= $editando ? htmlspecialchars($instalacionEditar['telefono']) : '' ?>"
                           required>
                </div>

                <div class="col-md-4">
                    <label>Instalador</label>

                    <select name="instalador_id"
                            class="form-select"
                            required>

                        <option value="">Seleccionar instalador</option>

                        <?php while($ins = $instaladores->fetch_assoc()){ ?>

                            <option value="<?= $ins['id'] ?>"
                                <?= ($editando && $instalacionEditar['instalador_id'] == $ins['id']) ? 'selected' : '' ?>>

                                <?= htmlspecialchars($ins['nombre']) ?>

                            </option>

                        <?php } ?>

                    </select>
                </div>

                <div class="col-md-3">
                    <label>Prioridad</label>

                    <?php
                    $prioridadActual = $editando ? $instalacionEditar['prioridad'] : 5;
                    ?>

                    <select name="prioridad"
                            class="form-select"
                            required>

                        <option value="1" <?= $prioridadActual == 1 ? 'selected' : '' ?>>
                            1 - Urgente
                        </option>

                        <option value="2" <?= $prioridadActual == 2 ? 'selected' : '' ?>>
                            2 - Alta
                        </option>

                        <option value="3" <?= $prioridadActual == 3 ? 'selected' : '' ?>>
                            3 - Media
                        </option>

                        <option value="4" <?= $prioridadActual == 4 ? 'selected' : '' ?>>
                            4 - Baja
                        </option>

                        <option value="5" <?= $prioridadActual == 5 ? 'selected' : '' ?>>
                            5 - Normal
                        </option>

                    </select>
                </div>

                <?php if($editando){ ?>

                <div class="col-md-3">
                    <label>Estado</label>

                    <select name="estado"
                            class="form-select"
                            required>

                        <option value="PENDIENTE" <?= $instalacionEditar['estado'] == 'PENDIENTE' ? 'selected' : '' ?>>
                            PENDIENTE
                        </option>

                        <option value="EN_PROCESO" <?= $instalacionEditar['estado'] == 'EN_PROCESO' ? 'selected' : '' ?>>
                            EN PROCESO
                        </option>

                        <option value="TERMINADA" <?= $instalacionEditar['estado'] == 'TERMINADA' ? 'selected' : '' ?>>
                            TERMINADA
                        </option>

                    </select>
                </div>

                <?php } else { ?>

                    <input type="hidden" name="estado" value="PENDIENTE">

                <?php } ?>

                <div class="<?= $editando ? 'col-md-6' : 'col-md-9' ?>">
                    <label>Ubicación</label>
                    <input type="text"
                           name="ubicacion"
                           class="form-control"
                           value="<?= $editando ? htmlspecialchars($instalacionEditar['ubicacion']) : '' ?>"
                           required>
                </div>

                <div class="col-md-12">
                    <label>Observaciones</label>
                    <textarea name="observaciones"
                              class="form-control"
                              rows="2"><?= $editando ? htmlspecialchars($instalacionEditar['observaciones']) : '' ?></textarea>
                </div>

                <div class="col-md-12">

                    <?php if($editando){ ?>

                        <button type="submit"
                                name="actualizar_instalacion"
                                class="btn btn-warning">
                            Actualizar instalación
                        </button>

                        <a href="instalaciones.php"
                           class="btn btn-secondary">
                            Cancelar
                        </a>

                    <?php } else { ?>

                        <button type="submit"
                                name="guardar_instalacion"
                                class="btn btn-success">
                            Guardar instalación
                        </button>

                    <?php } ?>

                </div>

            </form>

        </div>

        <?php } ?>

        <div class="card shadow p-4">

            <h4>
                <?php if($rol == 'ADMIN'){ ?>
                    Todas las instalaciones
                <?php } else { ?>
                    Mis instalaciones pendientes
                <?php } ?>
            </h4>

            <div class="table-responsive mt-3">

                <table class="table table-bordered table-hover">

                    <thead class="table-dark">
                        <tr>
                            <th>Prioridad</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Ubicación</th>

                            <?php if($rol == 'ADMIN'){ ?>
                                <th>Instalador</th>
                            <?php } ?>

                            <th>Estado</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if($instalaciones->num_rows > 0){ ?>

                        <?php while($fila = $instalaciones->fetch_assoc()){ ?>

                            <tr>

                                <td>
                                    <span class="badge prioridad-<?= $fila['prioridad'] ?>">
                                        <?= $fila['prioridad'] ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($fila['cliente']) ?></td>

                                <td>
                                    <a href="tel:<?= htmlspecialchars($fila['telefono']) ?>">
                                        <?= htmlspecialchars($fila['telefono']) ?>
                                    </a>
                                </td>

                                <td>
                                    <?= htmlspecialchars($fila['ubicacion']) ?>
                                </td>

                                <?php if($rol == 'ADMIN'){ ?>
                                    <td><?= htmlspecialchars($fila['instalador'] ?? 'Sin asignar') ?></td>
                                <?php } ?>

                                <td>
                                    <?php if($fila['estado'] == 'PENDIENTE'){ ?>
                                        <span class="badge bg-warning text-dark">PENDIENTE</span>
                                    <?php } elseif($fila['estado'] == 'EN_PROCESO'){ ?>
                                        <span class="badge bg-primary">EN PROCESO</span>
                                    <?php } else { ?>
                                        <span class="badge bg-success">TERMINADA</span>
                                    <?php } ?>
                                </td>

                                <td><?= htmlspecialchars($fila['observaciones']) ?></td>

                                <td>

                                    <?php if($rol == 'ADMIN'){ ?>

                                        <a href="instalaciones.php?editar=<?= $fila['id'] ?>"
                                           class="btn btn-warning btn-sm">
                                            Editar
                                        </a>

                                        <a href="instalaciones.php?eliminar=<?= $fila['id'] ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('¿Eliminar esta instalación?')">
                                            Eliminar
                                        </a>

                                    <?php } else { ?>

                                        <?php if($fila['estado'] == 'PENDIENTE'){ ?>

                                            <form method="POST" class="d-inline">
                                                <input type="hidden"
                                                       name="id_instalacion"
                                                       value="<?= $fila['id'] ?>">

                                                <input type="hidden"
                                                       name="estado"
                                                       value="EN_PROCESO">

                                                <button type="submit"
                                                        name="cambiar_estado"
                                                        class="btn btn-primary btn-sm">
                                                    Iniciar
                                                </button>
                                            </form>

                                        <?php } ?>

                                        <form method="POST" class="d-inline">
                                            <input type="hidden"
                                                   name="id_instalacion"
                                                   value="<?= $fila['id'] ?>">

                                            <input type="hidden"
                                                   name="estado"
                                                   value="TERMINADA">

                                            <button type="submit"
                                                    name="cambiar_estado"
                                                    class="btn btn-success btn-sm">
                                                Terminar
                                            </button>
                                        </form>

                                    <?php } ?>

                                </td>

                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="8"
                                class="text-center text-muted py-4">
                                No hay instalaciones pendientes
                            </td>
                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>