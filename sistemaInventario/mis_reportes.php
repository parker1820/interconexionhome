<?php

include("../includes/session.php");
include("../config/conex.php");

$usuario_id = $_SESSION['id_usuario'];

if($usuario_id == 0){
    die("Error: no se encontró el ID del usuario en la sesión.");
}

$movimientos = $conn->query("
    SELECT movimientos.*,
           productos.nombre AS producto,
           usuarios.nombre AS usuario,
           usuarios.rol AS rol
    FROM movimientos
    INNER JOIN productos
        ON productos.id = movimientos.producto_id
    INNER JOIN usuarios
        ON usuarios.id = movimientos.usuario_id
    WHERE movimientos.usuario_id = $usuario_id
    ORDER BY movimientos.fecha DESC
");

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>Mis Reportes</title>

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
}

.menu a:hover{
    background:#1e293b;
}

.main{
    margin-left:270px;
    padding:30px;
}

.card{
    border:none;
    border-radius:18px;
}

td{
    vertical-align:middle;
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        📡 INTERCONEXION
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
            <b>
                <?= $_SESSION['nombre'] ?>
            </b>

        </small>

        <br><br>

        <small>

            Rol:
            <b>
                <?= strtoupper($_SESSION['rol']) ?>
            </b>

        </small>

    </div>

</div>

<div class="main">

    <div class="mb-4">

        <h2 class="m-0">
            Mis Reportes
        </h2>

        <small class="text-muted">
            Historial de material utilizado
        </small>

    </div>

    <div class="card shadow-sm p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h4 class="m-0">
                Historial
            </h4>

            <input type="text"
                   id="buscarReporte"
                   class="form-control w-25"
                   placeholder="Buscar...">

        </div>

        <div class="table-responsive">

            <table class="table table-hover">

                <thead class="table-dark">

                    <tr>

                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Observación</th>

                    </tr>

                </thead>

                <tbody id="tablaReportes">

                <?php while($m = $movimientos->fetch_assoc()){ ?>

                    <tr>

                        <td>
                            <?= $m['fecha'] ?>
                        </td>

                        <td>
                            <?= $m['producto'] ?>
                        </td>

                        <td>

                            <?php if($m['tipo'] == "ENTRADA"){ ?>

                                <span class="badge bg-success">
                                    ENTRADA
                                </span>

                            <?php } else { ?>

                                <span class="badge bg-danger">
                                    SALIDA
                                </span>

                            <?php } ?>

                        </td>

                        <td>
                            <?= $m['cantidad'] ?>
                        </td>

                        <td>
                            <?= $m['observacion'] ?>
                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>

document
.getElementById("buscarReporte")
.addEventListener("keyup", function(){

    let texto = this.value.toLowerCase();

    let filas = document.querySelectorAll("#tablaReportes tr");

    filas.forEach(fila => {

        let contenido = fila.innerText.toLowerCase();

        if(contenido.includes(texto)){
            fila.style.display = "";
        }else{
            fila.style.display = "none";
        }

    });

});

</script>

</body>
</html>