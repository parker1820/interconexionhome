<?php

include("../includes/solo_admin.php");
include("../config/conex.php");

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

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Inventario</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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

.stock-verde{
    color:#16a34a;
    font-weight:bold;
}

.stock-rojo{
    color:#dc2626;
    font-weight:bold;
}

.btn-redondo{
    border-radius:12px;
}

.tabla-productos td{
    vertical-align:middle;
}

</style>

</head>

<body>

<div class="sidebar">

    <div class="logo">
        📡 INTERCONEXION
    </div>

    <div class="menu">
        <a href="inventario.php">📦 Inventario</a>
        <a href="../IPS/ips_instalador.php">🌐 IPS</a>
        <a href="../Instalaciones/instalaciones.php">🛠 Instalaciones</a>
        <a href="reportes.php">📋 Reportes</a>
        <a href="../logout.php">🚪 Salir</a>
    </div>

    <div class="mt-5">
        <small>
            Sesión:
            <b><?= $_SESSION['nombre'] ?></b>
        </small>
    </div>

</div>

<div class="main">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="m-0">Inventario</h2>
            <small class="text-muted">Administración de materiales</small>
        </div>

        <button class="btn btn-primary btn-redondo"
                data-bs-toggle="modal"
                data-bs-target="#modalAgregarProducto">
            + Agregar Producto
        </button>

    </div>

    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card card-dashboard shadow-sm p-4">
                <h5>Productos</h5>
                <h2>
                    <?php
                    $total = $conn->query(
                        "SELECT COUNT(*) total FROM productos WHERE activo = 1"
                    )->fetch_assoc();

                    echo $total['total'];
                    ?>
                </h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-dashboard shadow-sm p-4">
                <h5>Stock total</h5>
                <h2>
                    <?php
                    $stock = $conn->query(
                        "SELECT SUM(stock) total FROM productos WHERE activo = 1"
                    )->fetch_assoc();

                    echo $stock['total'] ?? 0;
                    ?>
                </h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-dashboard shadow-sm p-4">
                <h5>Stock bajo</h5>
                <h2 class="text-danger">
                    <?php
                    $bajo = $conn->query(
                        "SELECT COUNT(*) total
                         FROM productos
                         WHERE activo = 1
                         AND stock <= stock_minimo"
                    )->fetch_assoc();

                    echo $bajo['total'];
                    ?>
                </h2>
            </div>
        </div>

    </div>

    <div class="card card-dashboard shadow-sm p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h4 class="m-0">Inventario actual</h4>

            <input type="text"
                   id="buscarProducto"
                   class="form-control w-25"
                   placeholder="Buscar producto">

        </div>

        <div class="table-responsive">

            <table class="table table-hover tabla-productos">

                <thead class="table-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Unidad</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaProductos">

                <?php while($p = $productos->fetch_assoc()) { ?>

                    <tr>

                        <td class="producto">
                            <?= $p['nombre'] ?>
                        </td>

                        <td>
                            <?= $p['categoria'] ?>
                        </td>

                        <td>
                            <?= $p['unidad'] ?>
                        </td>

                        <td>
                            <?php if($p['stock'] <= $p['stock_minimo']){ ?>
                                <span class="stock-rojo"><?= $p['stock'] ?></span>
                            <?php }else{ ?>
                                <span class="stock-verde"><?= $p['stock'] ?></span>
                            <?php } ?>
                        </td>

                        <td>

    <div class="d-flex gap-2 align-items-center">

        <a href="eliminar_producto.php?id=<?= $p['id'] ?>"
           class="btn btn-dark btn-sm btn-eliminar"
           onclick="return confirm('¿Eliminar producto?')">

            🗑️

        </a>

        <a href="sumar_stock.php?id=<?= $p['id'] ?>"
           class="btn btn-success btn-sm">

            +

        </a>

        <a href="restar_stock.php?id=<?= $p['id'] ?>"
           class="btn btn-danger btn-sm">

            -

        </a>

    </div>

</td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<div class="modal fade" id="modalAgregarProducto" tabindex="-1">

    <div class="modal-dialog">

        <form class="modal-content" method="POST" action="agregar_producto.php">

            <div class="modal-header">

                <h5 class="modal-title">Agregar producto</h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <label>Nombre del producto</label>
                <input type="text"
                       name="nombre"
                       class="form-control mb-3"
                       required>

                <label>Categoría</label>
                <select name="categoria_id"
                        class="form-control mb-3"
                        required>

                    <option value="">Seleccionar categoría</option>

                    <?php
                    $categorias = $conn->query(
                        "SELECT id, nombre FROM categorias ORDER BY nombre ASC"
                    );

                    while($cat = $categorias->fetch_assoc()){
                        echo "<option value='".$cat['id']."'>".$cat['nombre']."</option>";
                    }
                    ?>

                </select>

                <label>Unidad</label>
                <input type="text"
                       name="unidad"
                       class="form-control mb-3"
                       placeholder="Unidades, metros, bobinas..."
                       required>

                <label>Stock inicial</label>
                <input type="number"
                       name="stock"
                       class="form-control mb-3"
                       required>

                <label>Stock mínimo</label>
                <input type="number"
                       name="stock_minimo"
                       class="form-control mb-3"
                       value="1"
                       required>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit"
                        class="btn btn-primary">
                    Guardar producto
                </button>

            </div>

        </form>

    </div>

</div>

<script>

document
.getElementById("buscarProducto")
.addEventListener("keyup", function(){

    let texto = this.value.toLowerCase();

    let filas = document.querySelectorAll("#tablaProductos tr");

    filas.forEach(fila => {

        let producto = fila
        .querySelector(".producto")
        .innerText
        .toLowerCase();

        if(producto.includes(texto)){
            fila.style.display = "";
        }else{
            fila.style.display = "none";
        }

    });

});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>