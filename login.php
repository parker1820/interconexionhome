<?php
session_start();

if(isset($_SESSION['usuario'])){

    header("Location: menu.php");
    exit;

}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Login</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

    margin:0;
    height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    background:#0f172a;

    font-family:Arial;

}

.card-login{

    width:380px;

    background:#1e293b;

    border-radius:20px;

    padding:40px;

    box-shadow:
    0 0 30px rgba(0,0,0,0.35);

}

.logo{

    text-align:center;

    color:white;

    font-size:28px;
    font-weight:bold;

    margin-bottom:30px;

}

input{

    width:100%;

    padding:12px;

    border:none;

    border-radius:12px;

    margin-bottom:15px;

}

.btn-login{

    width:100%;

    border:none;

    background:#2563eb;

    color:white;

    padding:12px;

    border-radius:12px;

    font-weight:bold;

    transition:0.25s;

}

.btn-login:hover{

    background:#1d4ed8;

}

</style>

</head>

<body>
<?php if(isset($_GET['expirada'])){ ?>

<div class="alert alert-warning">

    Tu sesión expiró por inactividad.
    Vuelve a iniciar sesión.

</div>

<?php } ?>  
<div class="card-login">

    <div class="logo">

        📡 INTERCONEXION IPS

    </div>

    <form action="validar.php"
          method="POST">

        <input type="text"
               name="usuario"
               placeholder="Usuario"
               required>

        <input type="password"
               name="password"
               placeholder="Contraseña"
               required>

        <button type="submit"
                class="btn-login">

            Ingresar

        </button>

    </form>

</div>

</body>
</html>