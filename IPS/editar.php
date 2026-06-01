<?php
include("../includes/session.php");
include("../config/conexion.php");

$id = $_GET['id'];

/*
====================================
OBTENER DATOS
====================================
*/

$sql = "SELECT * FROM clientes
        WHERE id='$id'";

$resultado = $conn->query($sql);

$cliente = $resultado->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Editar IP</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.card{
    border:none;
    border-radius:15px;
}

</style>

</head>

<body>

<div class="container mt-5">

    <div class="card shadow p-4">

        <div class="d-flex
                    justify-content-between
                    align-items-center
                    mb-4">

            <h2>
                Editar IP
            </h2>

            <a href="../IPS/ips_instalador.php"
               class="btn btn-dark">

                ← Volver

            </a>

        </div>

        <form action="actualizar.php"
            autocomplete="off"
              method="POST"
              class="row g-3">

            <!-- ID OCULTO -->

            <input type="hidden"
                   name="id"
                   value="<?= $cliente['id'] ?>">

            <!-- CLIENTE -->

            <div class="col-md-3">

                <label class="form-label">
                    Cliente
                </label>

                <input type="text"
                       name="nombre"
                       class="form-control"
                       value="<?= $cliente['nombre'] ?>"
                       required>

            </div>

            <!-- IP -->

            <div class="col-md-3">

                <label class="form-label">
                    IP
                </label>

                <input type="text"
                       name="ip"
                       class="form-control"
                       value="<?= $cliente['ip'] ?>"
                       required>

            </div>

            <!-- RED -->

            <div class="col-md-3">

                <label class="form-label">
                    Red
                </label>

                <select name="red"
                        class="form-select"
                        required>

                    <?php

                    $redes = $conn->query(
                        "SELECT * FROM redes
                         ORDER BY nombre ASC"
                    );

                    while($red = $redes->fetch_assoc()){

                    ?>

                    <option value="<?= $red['nombre'] ?>"

                    <?php

                    if($cliente['red'] == $red['nombre']){
                        echo "selected";
                    }

                    ?>

                    >

                        <?= $red['nombre'] ?>

                    </option>

                    <?php } ?>

                </select>

            </div>

            <!-- MODO -->

            <div class="col-md-3">

                <label class="form-label">
                    Tipo
                </label>

                <select name="modo"
                        class="form-select"
                        required>

                    <option value="AP"

                    <?php
                    if($cliente['modo'] == "AP"){
                        echo "selected";
                    }
                    ?>

                    >

                        AP

                    </option>

                    <option value="STA"

                    <?php
                    if($cliente['modo'] == "STA"){
                        echo "selected";
                    }
                    ?>

                    >

                        STA

                    </option>

                </select>

            </div>

            <!-- BOTON -->

            <div class="col-md-4 mx-auto">

                <button type="submit"
                        class="btn btn-success w-100">

                    Guardar Cambios

                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>