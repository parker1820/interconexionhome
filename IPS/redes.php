<?php
include("../includes/session.php");
include("../config/conex.php");

/*
====================================
GUARDAR RED
====================================
*/

if(isset($_POST['guardar'])){

    $nombre = strtoupper(trim($_POST['nombre']));
    $ubicacion = strtoupper(trim($_POST['ubicacion'] ?? ''));
    $segmento = trim($_POST['segmento'] ?? '');

    /*
    ====================================
    VALIDAR NOMBRE
    ====================================
    */

    if($nombre == ""){

        echo "

        <div class='alert alert-danger mt-3'>

            Debe ingresar un nombre de red

        </div>

        ";

    }

    /*
    ====================================
    VALIDAR SEGMENTO
    ====================================
    */

    elseif($segmento == ""){

        echo "

        <div class='alert alert-danger mt-3'>

            Debe ingresar el segmento IP

        </div>

        ";

    }

    /*
    ====================================
    VALIDAR FORMATO SEGMENTO
    ====================================
    */

    elseif(!preg_match('/^([0-9]{1,3}\.){2}[0-9]{1,3}$/', $segmento)){

        echo "

        <div class='alert alert-danger mt-3'>

            Formato inválido.
            Ejemplo correcto:
            192.168.10

        </div>

        ";

    }

    else{

        /*
        ====================================
        VALIDAR DUPLICADOS
        ====================================
        */

        $verificar = $conn->query(
            "SELECT *
             FROM redes
             WHERE nombre='$nombre'"
        );

        if($verificar->num_rows > 0){

            echo "

            <div class='alert alert-warning mt-3'>

                La red ya existe

            </div>

            ";

        }
        else{

            /*
            ====================================
            INSERTAR
            ====================================
            */

            $sql = "INSERT INTO redes(nombre, ubicacion, segmento)
                    VALUES('$nombre','$ubicacion','$segmento')";

            if($conn->query($sql)){

                echo "

                <div class='alert alert-success mt-3'>

                    Red agregada correctamente

                </div>

                ";

            }
            else{

                echo "

                <div class='alert alert-danger mt-3'>

                    Error al guardar:
                    ".$conn->error."

                </div>

                ";

            }

        }

    }

}

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Agregar Redes</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.card{
    border:none;
    border-radius:18px;
}

.titulo{
    font-weight:bold;
    color:#1e293b;
}

.form-control{
    border-radius:10px;
    padding:12px;
}

.btn-guardar{
    background:#198754;
    border:none;
    border-radius:10px;
    padding:12px;
    font-weight:bold;
    transition:0.3s;
}

.btn-guardar:hover{
    background:#157347;
}

.mayus{
    text-transform:uppercase;
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

            <h2 class="titulo">

                Agregar Nueva Red

            </h2>

            <a href="ips_instalador.php"
               class="btn btn-dark">

               ← Volver

            </a>

        </div>

        <!-- FORMULARIO -->

        <form method="POST"
              autocomplete="off"
              class="row g-3">

            <!-- NOMBRE -->

            <div class="col-md-4">

                <label class="form-label">

                    Nombre Red

                </label>

                <input type="text"
                       name="nombre"
                       class="form-control mayus"
                       placeholder="Ej: RED NORTE"
                       required>

            </div>

            <!-- UBICACION -->

            <div class="col-md-4">

                <label class="form-label">

                    Ubicación

                </label>

                <input type="text"
                       name="ubicacion"
                       class="form-control mayus"
                       placeholder="Ej: OFICINA CENTRAL">

            </div>

            <!-- SEGMENTO -->

            <div class="col-md-4">

                <label class="form-label">

                    Segmento IP

                </label>

                <input type="text"
                       name="segmento"
                       class="form-control"
                       placeholder="Ej: 192.168.10"
                       required>

            </div>

            <!-- BOTON -->

            <div class="col-md-6 mx-auto">

                <button type="submit"
                        name="guardar"
                        class="btn btn-success btn-guardar w-100">

                    Guardar Red

                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>