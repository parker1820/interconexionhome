<?php

include("../includes/session.php");
include("../config/conexion.php");

$redSeleccionada = "";
$orden = "";
$modoSeleccionado = "";
$buscarCliente = "";
$buscarIP = "";

if(isset($_GET['modo'])){
    $modoSeleccionado = $_GET['modo'];
}

if(isset($_GET['red'])){
    $redSeleccionada = $_GET['red'];
}

if(isset($_GET['buscarCliente'])){
    $buscarCliente = trim($_GET['buscarCliente']);
}

if(isset($_GET['buscarIP'])){
    $buscarIP = trim($_GET['buscarIP']);
}

if(isset($_GET['orden'])){
    switch($_GET['orden']){
        case "id_asc":
            $orden = "ORDER BY clientes.id ASC";
        break;

        case "id_desc":
            $orden = "ORDER BY clientes.id DESC";
        break;

        case "nombre_asc":
            $orden = "ORDER BY clientes.nombre ASC";
        break;

        case "nombre_desc":
            $orden = "ORDER BY clientes.nombre DESC";
        break;

        case "ip_asc":
            $orden = "ORDER BY clientes.ip ASC";
        break;

        case "ip_desc":
            $orden = "ORDER BY clientes.ip DESC";
        break;
    }
}

/*
====================================
PAGINADOR
====================================
*/

$porPagina = 10;

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

if($pagina < 1){
    $pagina = 1;
}

$inicio = ($pagina - 1) * $porPagina;

/*
====================================
CONSULTA PRINCIPAL CON PAGINADOR
====================================
*/

$sql = "SELECT clientes.*
        FROM clientes";

$sqlTotal = "SELECT COUNT(*) AS total
             FROM clientes";

$where = [];

if($redSeleccionada != ""){
    $redEscapada = $conn->real_escape_string($redSeleccionada);
    $where[] = "clientes.red='$redEscapada'";
}

if($modoSeleccionado != ""){
    $modoEscapado = $conn->real_escape_string($modoSeleccionado);
    $where[] = "clientes.modo='$modoEscapado'";
}

if($buscarCliente != ""){
    $clienteEscapado = $conn->real_escape_string($buscarCliente);
    $where[] = "clientes.nombre LIKE '%$clienteEscapado%'";
}

if($buscarIP != ""){
    $ipEscapada = $conn->real_escape_string($buscarIP);
    $where[] = "clientes.ip LIKE '%$ipEscapada%'";
}

if(count($where) > 0){

    $condicion = implode(" AND ", $where);

    $sql .= " WHERE $condicion";
    $sqlTotal .= " WHERE $condicion";
}

$totalResultado = $conn->query($sqlTotal);

if(!$totalResultado){
    die($conn->error);
}

$totalFila = $totalResultado->fetch_assoc();
$totalRegistros = $totalFila['total'];

$totalPaginas = ceil($totalRegistros / $porPagina);

$sql .= " $orden LIMIT $inicio, $porPagina";

$resultado = $conn->query($sql);

if(!$resultado){
    die($conn->error);
}

/*
====================================
CONSULTA REDES
====================================
*/

$redes = $conn->query(
    "SELECT nombre
     FROM redes
     ORDER BY nombre ASC"
);

/*
====================================
BOTON SWITCH AP / STA / TODOS
====================================
*/

$nuevoModo = "AP";
$textoModo = "AP";

if($modoSeleccionado == "AP"){
    $nuevoModo = "STA";
    $textoModo = "STA";
}
elseif($modoSeleccionado == "STA"){
    $nuevoModo = "";
    $textoModo = "TODOS";
}

/*
====================================
PARAMETROS PARA LINKS
====================================
*/

$ordenActual = $_GET['orden'] ?? "";

$parametrosBase =
    "red=" . urlencode($redSeleccionada) .
    "&orden=" . urlencode($ordenActual) .
    "&modo=" . urlencode($modoSeleccionado) .
    "&buscarCliente=" . urlencode($buscarCliente) .
    "&buscarIP=" . urlencode($buscarIP);

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Sistema IPs</title>

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

.red-btn{
    margin-right:10px;
    margin-bottom:10px;
}

.titulo{
    font-weight:bold;
}

.table td{
    vertical-align:middle;
}

.btn-eliminar-red{

    width:38px;
    height:38px;

    border:none;
    border-radius:12px;

    background:transparent;

    color:#ef4444;

    display:inline-flex;
    align-items:center;
    justify-content:center;

    text-decoration:none;

    transition:all 0.25s ease;

    margin-left:6px;

    position:relative;

    overflow:hidden;

}

.btn-eliminar-red:hover{

    background:rgba(239,68,68,0.10);

    transform:
    translateY(-2px)
    scale(1.08);

}

.btn-eliminar-red:active{
    transform:scale(0.95);
}

.btn-eliminar-red .icono{
    font-size:16px;
    transition:0.25s;
}

.btn-eliminar-red:hover .icono{
    transform:
    rotate(-8deg)
    scale(1.1);
}

.btn-logout{
    transition:0.25s ease;
}

.btn-logout:hover{

    transform:
    translateY(-2px)
    scale(1.05);

    box-shadow:
    0 6px 15px rgba(0,0,0,0.25);

}

.form-filtro{

    height:32px;

    padding:4px 10px;

    font-size:14px;

    border-radius:8px;

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
    min-width:250px;
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

html, body{
    overflow-x:hidden;
}

.main-content{
    margin-left:250px;
    width:calc(100% - 250px);
    padding:30px;
    overflow-x:hidden;
}

.table-responsive{
    overflow-x:auto;
}

@media(max-width:768px){

    .sidebar{
        position:relative;
        width:100%;
        height:auto;
    }

    .main-content{
        margin-left:0;
        width:100%;
        padding:15px;
    }

    .pagination{
        font-size:14px;
    }

    .page-link{
        padding:6px 10px;
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
        <a href="ips_instalador.php">🌐 IPS</a>
        <a href="../Instalaciones/instalaciones.php">🛠 Instalaciones</a>
        <a href="../sistemaInventario/reportes.php">📋 Reportes</a>
        <a href="../logout.php">🚪 Salir</a>
    </div>

    <div class="mt-5">

        <small>
            Sesión:
            <b><?= $_SESSION['nombre'] ?></b>
        </small>

    </div>

</div>

<div class="main-content">

    <div class="container-fluid mt-4">

        <div class="card shadow p-4">

            <!-- HEADER -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h2 class="titulo m-0">
                    Panel de Redes
                </h2>

                <a href="redes.php"
                   class="btn btn-success">

                    + Agregar Red

                </a>

            </div>

            <!-- BOTONES REDES -->

            <div class="mb-4">

                <a href="ips_instalador.php"
                   class="btn btn-dark me-3">

                   Todas

                </a>

                <?php while($r = $redes->fetch_assoc()) { ?>

                    <div class="d-inline-block red-btn">

                        <a href="?red=<?= urlencode($r['nombre']) ?>"
                           class="btn btn-primary">

                            <?= htmlspecialchars($r['nombre']) ?>

                        </a>

                        <a href="eliminar_red.php?nombre=<?= urlencode($r['nombre']) ?>"
                           class="btn-eliminar-red"
                           onclick="return confirm('¿Eliminar esta red?')">

                            <span class="icono">
                                🗑️
                            </span>

                        </a>

                    </div>

                <?php } ?>

            </div>

            <!-- FORMULARIO AGREGAR -->

            <form action="agregar.php"
                  method="POST"
                  autocomplete="off"
                  class="row g-3">

                <div class="col-md-2">

                    <input type="text"
                           name="nombre"
                           class="form-control mayus"
                           placeholder="Cliente">

                </div>

                <div class="col-md-2">

                    <input type="text"
                           name="ip"
                           class="form-control"
                           placeholder="IP"
                           required>

                </div>

                <div class="col-md-3">

                    <select name="red"
                            class="form-select"
                            required>

                        <option value="">
                            Seleccionar red
                        </option>

                        <?php

                        $redesSelect = $conn->query(
                            "SELECT nombre, segmento
                            FROM redes
                            ORDER BY nombre ASC"
                        );
                        while($red = $redesSelect->fetch_assoc()){

                        ?>

                        <option value="<?= htmlspecialchars($red['nombre']) ?>">

                            <?= htmlspecialchars($red['nombre']) ?>
                            →
                            <?= htmlspecialchars($red['segmento']) ?>.X

                        </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="col-md-2">

                    <select name="modo"
                            class="form-select"
                            required>

                        <option value="">
                            Tipo
                        </option>

                        <option value="AP">
                            AP
                        </option>

                        <option value="STA">
                            STA
                        </option>

                    </select>

                </div>

                <div class="col-md-3">

                    <button type="submit"
                            class="btn btn-success w-100">

                        Guardar

                    </button>

                </div>

            </form>

            <!-- TABLA -->

            <div class="card shadow mt-4 p-4">

                <h4 class="mb-3">

                    <?php

                    if($redSeleccionada != ""){
                        echo "IPs de ".htmlspecialchars($redSeleccionada);
                    }
                    else{
                        echo "Todas las IPs";
                    }

                    ?>

                </h4>

                <!-- BOTONES ORDEN -->

                <div class="mb-4">

                    <a href="?red=<?= urlencode($redSeleccionada) ?>&orden=id_asc&modo=<?= urlencode($modoSeleccionado) ?>&buscarCliente=<?= urlencode($buscarCliente) ?>&buscarIP=<?= urlencode($buscarIP) ?>"
                       class="btn btn-secondary btn-sm">

                       ID ↑

                    </a>

                    <a href="?red=<?= urlencode($redSeleccionada) ?>&orden=id_desc&modo=<?= urlencode($modoSeleccionado) ?>&buscarCliente=<?= urlencode($buscarCliente) ?>&buscarIP=<?= urlencode($buscarIP) ?>"
                       class="btn btn-secondary btn-sm">

                       ID ↓

                    </a>

                    <a href="?red=<?= urlencode($redSeleccionada) ?>&orden=nombre_asc&modo=<?= urlencode($modoSeleccionado) ?>&buscarCliente=<?= urlencode($buscarCliente) ?>&buscarIP=<?= urlencode($buscarIP) ?>"
                       class="btn btn-info btn-sm">

                       Nombre A-Z

                    </a>

                    <a href="?red=<?= urlencode($redSeleccionada) ?>&orden=nombre_desc&modo=<?= urlencode($modoSeleccionado) ?>&buscarCliente=<?= urlencode($buscarCliente) ?>&buscarIP=<?= urlencode($buscarIP) ?>"
                       class="btn btn-info btn-sm">

                       Nombre Z-A

                    </a>

                    <a href="?red=<?= urlencode($redSeleccionada) ?>&orden=ip_asc&modo=<?= urlencode($modoSeleccionado) ?>&buscarCliente=<?= urlencode($buscarCliente) ?>&buscarIP=<?= urlencode($buscarIP) ?>"
                       class="btn btn-dark btn-sm">

                       IP ↑

                    </a>

                    <a href="?red=<?= urlencode($redSeleccionada) ?>&orden=ip_desc&modo=<?= urlencode($modoSeleccionado) ?>&buscarCliente=<?= urlencode($buscarCliente) ?>&buscarIP=<?= urlencode($buscarIP) ?>"
                       class="btn btn-dark btn-sm">

                       IP ↓

                    </a>

                    <a href="?red=<?= urlencode($redSeleccionada) ?>&orden=<?= urlencode($ordenActual) ?>&modo=<?= urlencode($nuevoModo) ?>&buscarCliente=<?= urlencode($buscarCliente) ?>&buscarIP=<?= urlencode($buscarIP) ?>"
                       class="btn btn-success btn-sm">

                       <?= $textoModo ?>

                    </a>

                </div>

                <!-- BUSCADOR GLOBAL -->

                <form method="GET"
                      class="row g-2 mb-3"
                      autocomplete="off">

                    <input type="hidden"
                           name="red"
                           value="<?= htmlspecialchars($redSeleccionada) ?>">

                    <input type="hidden"
                           name="modo"
                           value="<?= htmlspecialchars($modoSeleccionado) ?>">

                    <input type="hidden"
                           name="orden"
                           value="<?= htmlspecialchars($ordenActual) ?>">

                    <div class="col-md-4">
                        <input type="text"
                               name="buscarCliente"
                               class="form-control"
                               placeholder="Buscar cliente"
                               value="<?= htmlspecialchars($buscarCliente) ?>">
                    </div>

                    <div class="col-md-4">
                        <input type="text"
                               name="buscarIP"
                               class="form-control"
                               placeholder="Buscar IP"
                               value="<?= htmlspecialchars($buscarIP) ?>">
                    </div>

                    <div class="col-md-2">
                        <button type="submit"
                                class="btn btn-primary w-100">
                            Buscar
                        </button>
                    </div>

                    <div class="col-md-2">
                        <a href="ips_instalador.php"
                           class="btn btn-secondary w-100">
                            Limpiar
                        </a>
                    </div>

                </form>

                <?php if($buscarCliente != "" || $buscarIP != ""){ ?>

                    <div class="alert alert-info py-2">
                        Resultados encontrados: <b><?= $totalRegistros ?></b>
                    </div>

                <?php } ?>

                <div class="table-responsive">

                    <table class="table table-hover table-bordered">

                        <thead class="table-dark">

                            <tr>

                                <th>ID</th>

                                <th>Cliente</th>

                                <th>IP</th>

                                <th class="text-center">
                                    Red
                                </th>

                                <th class="text-center">
                                    Modo
                                </th>

                                <th class="text-center">Acciones</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if($resultado->num_rows > 0){ ?>

                            <?php while($fila = $resultado->fetch_assoc()) { ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($fila['id']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($fila['nombre']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($fila['ip']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($fila['red']) ?>
                                    </td>

                                    <td class="text-center">

                                        <?php if($fila['modo'] == "AP"){ ?>

                                            <span class="badge bg-success">
                                                AP
                                            </span>

                                        <?php } else { ?>

                                            <span class="badge bg-primary">
                                                STA
                                            </span>

                                        <?php } ?>

                                    </td>

                                    <td class="text-center align-middle">

                                        <div class="d-flex justify-content-center gap-2">

                                            <a href="editar.php?id=<?= htmlspecialchars($fila['id']) ?>"
                                               class="btn btn-warning btn-sm">

                                                Editar

                                            </a>

                                            <a href="eliminar.php?id=<?= htmlspecialchars($fila['id']) ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('¿Eliminar esta IP?')">

                                                Eliminar

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>
                                <td colspan="6"
                                    class="text-center text-muted py-4">
                                    No se encontraron resultados
                                </td>
                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                </div>

                <!-- PAGINADOR -->

                <?php if($totalPaginas > 1){ ?>

                    <?php

                    $rango = 2;

                    $inicioPag = max(1, $pagina - $rango);
                    $finPag = min($totalPaginas, $pagina + $rango);

                    ?>

                    <nav class="mt-4">

                        <ul class="pagination justify-content-center flex-wrap">

                            <?php if($pagina > 1){ ?>

                                <li class="page-item">
                                    <a class="page-link"
                                       href="?<?= $parametrosBase ?>&pagina=<?= $pagina - 1 ?>">
                                        Anterior
                                    </a>
                                </li>

                            <?php } ?>

                            <?php for($i = $inicioPag; $i <= $finPag; $i++){ ?>

                                <li class="page-item <?= ($i == $pagina) ? 'active' : '' ?>">
                                    <a class="page-link"
                                       href="?<?= $parametrosBase ?>&pagina=<?= $i ?>">
                                        <?= $i ?>
                                    </a>
                                </li>

                            <?php } ?>

                            <?php if($pagina < $totalPaginas){ ?>

                                <li class="page-item">
                                    <a class="page-link"
                                       href="?<?= $parametrosBase ?>&pagina=<?= $pagina + 1 ?>">
                                        Siguiente
                                    </a>
                                </li>

                            <?php } ?>

                        </ul>

                    </nav>

                <?php } ?>

            </div>

        </div>

    </div>

</div>

</body>
</html>
