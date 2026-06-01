<?php

include("../includes/session.php");
include("../config/conexion.php");

/*
====================================
VALIDAR ROL INSTALADOR
====================================
*/

if($_SESSION['rol'] != "INSTALADOR"){
    header("Location: inventario.php");
    exit;
}

/*
====================================
PRODUCTOS
====================================
*/

$productos = $conn->query("

    SELECT productos.*,
           categorias.nombre AS categoria

    FROM productos

    LEFT JOIN categorias
    ON categorias.id = productos.categoria_id

    WHERE activo = 1

    ORDER BY productos.nombre ASC

");

/*
====================================
MIS ÚLTIMOS REPORTES
====================================
*/

$misReportes = $conn->query("

    SELECT movimientos.*,
           productos.nombre AS producto

    FROM movimientos

    INNER JOIN productos
    ON productos.id = movimientos.producto_id

    WHERE movimientos.usuario_id = ".$_SESSION['id_usuario']."

    ORDER BY movimientos.fecha DESC

    LIMIT 5

");

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Mi Material</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#f4f6f9;
    font-family:Arial;
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
    transition:0.25s;
}

.menu a:hover{
    background:#1e293b;
}

.main{
    margin-left:270px;
    padding:30px;
}

.card-dashboard{
    border:none;
    border-radius:18px;
}

.tabla-productos td{
    vertical-align:middle;
}

.stock-verde{
    color:#16a34a;
    font-weight:bold;
}

.stock-rojo{
    color:#dc2626;
    font-weight:bold;
}

.btn-usar{
    border-radius:10px;
}

.cantidad-box{
    width:80px;
    text-align:center;
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        📡 INTERCONEXION IPS
    </div>
<div class="menu">

    <a href="material.php">
        📦 Mi Material
    </a>

    <a href="mis_reportes.php">
        📋 Mis Reportes
    </a>

    <a href="../IPS/ips_instalador.php">
    🌐 IPS
</a>
<a href="../Instalaciones/instalaciones.php">🛠 Instalaciones</a>

    <a href="../logout.php">
        🚪 Salir
    </a>

</div>

    <div class="mt-5">

        <small>
            Sesión:
            <b><?= $_SESSION['nombre'] ?></b>
        </small>

        <br>

        <small>
            Rol:
            <b><?= $_SESSION['rol'] ?></b>
        </small>

    </div>

</div>

<!-- MAIN -->

<div class="main">

    <div class="mb-4">

        <h2 class="m-0">
            Mi Material
        </h2>

        <small class="text-muted">
            Reporta el material que utilizaste en tus instalaciones
        </small>

    </div>

    <!-- CARDS -->

    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card card-dashboard shadow-sm p-4">

                <h5>Material usado hoy</h5>

                <h2>

                    <?php

                    $hoy = $conn->query("
                        SELECT SUM(cantidad) total
                        FROM movimientos
                        WHERE usuario_id = ".$_SESSION['id_usuario']."
                        AND tipo = 'SALIDA'
                        AND DATE(fecha) = CURDATE()
                    ")->fetch_assoc();

                    echo $hoy['total'] ?? 0;

                    ?>

                </h2>

                <small class="text-muted">
                    unidades reportadas
                </small>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card card-dashboard shadow-sm p-4">

                <h5>Reportes esta semana</h5>

                <h2>

                    <?php

                    $semana = $conn->query("
                        SELECT COUNT(*) total
                        FROM movimientos
                        WHERE usuario_id = ".$_SESSION['id_usuario']."
                        AND tipo = 'SALIDA'
                        AND YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)
                    ")->fetch_assoc();

                    echo $semana['total'];

                    ?>

                </h2>

                <small class="text-muted">
                    movimientos
                </small>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card card-dashboard shadow-sm p-4">

                <h5>Total este mes</h5>

                <h2>

                    <?php

                    $mes = $conn->query("
                        SELECT SUM(cantidad) total
                        FROM movimientos
                        WHERE usuario_id = ".$_SESSION['id_usuario']."
                        AND tipo = 'SALIDA'
                        AND MONTH(fecha) = MONTH(CURDATE())
                        AND YEAR(fecha) = YEAR(CURDATE())
                    ")->fetch_assoc();

                    echo $mes['total'] ?? 0;

                    ?>

                </h2>

                <small class="text-muted">
                    unidades usadas
                </small>

            </div>

        </div>

    </div>

    <div class="row">

        <!-- REPORTAR MATERIAL -->

        <div class="col-md-8">

            <div class="card card-dashboard shadow-sm p-4">

                <h4 class="mb-4">
                    Reportar material utilizado
                </h4>

                <div class="alert alert-primary">

                    Selecciona la cantidad utilizada y presiona
                    <b>Usar</b>. El inventario se descontará automáticamente.

                </div>

                <div class="table-responsive">

                    <table class="table table-hover tabla-productos">

                        <thead class="table-dark">

                            <tr>

                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Cantidad</th>
                                <th>Acción</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php while($p = $productos->fetch_assoc()) { ?>

                            <tr>

                                <td>
                                    <?= $p['nombre'] ?>
                                </td>

                                <td>
                                    <?= $p['categoria'] ?>
                                </td>

                                <td>

                                    <?php if($p['stock'] <= $p['stock_minimo']){ ?>

                                        <span class="stock-rojo">
                                            <?= $p['stock'] ?>
                                            <?= $p['unidad'] ?>
                                        </span>

                                    <?php } else { ?>

                                        <span class="stock-verde">
                                            <?= $p['stock'] ?>
                                            <?= $p['unidad'] ?>
                                        </span>

                                    <?php } ?>

                                </td>

                                <td>

                                    <form action="usar_material.php"
                                          method="POST"
                                          class="d-flex gap-2">

                                        <input type="hidden"
                                               name="producto_id"
                                               value="<?= $p['id'] ?>">

                                        <input type="number"
                                               name="cantidad"
                                               class="form-control cantidad-box"
                                               min="1"
                                               max="<?= $p['stock'] ?>"
                                               value="1"
                                               required>

                                </td>

                                <td>

                                        <button type="submit"
                                                class="btn btn-success btn-sm btn-usar">

                                            Usar

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <!-- ULTIMOS REPORTES -->

        <div class="col-md-4">

            <div class="card card-dashboard shadow-sm p-4">

                <h4 class="mb-4">
                    Mis últimos reportes
                </h4>

                <?php if($misReportes->num_rows == 0){ ?>

                    <p class="text-muted">
                        Aún no tienes reportes.
                    </p>

                <?php } ?>

                <?php while($m = $misReportes->fetch_assoc()) { ?>

                    <div class="border-bottom pb-3 mb-3">

                        <b>
                            <?= $m['producto'] ?>
                        </b>

                        <br>

                        <span class="text-danger">
                            -<?= $m['cantidad'] ?> unidades
                        </span>

                        <br>

                        <small class="text-muted">
                            <?= $m['fecha'] ?>
                        </small>

                        <?php if($m['observacion'] != ""){ ?>

                            <br>

                            <small>
                                <?= $m['observacion'] ?>
                            </small>

                        <?php } ?>

                    </div>

                <?php } ?>

            </div>

        </div>

    </div>

</div>

</body>
</html>