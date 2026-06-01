<?php

include("conex.php");

$adminPass = password_hash("Dinguis1", PASSWORD_DEFAULT);
$instPass = password_hash("Instalador1", PASSWORD_DEFAULT);

$conn->query("INSERT INTO usuarios(usuario, password, nombre, rol)
VALUES('INTER.CV', '$adminPass', 'Administrador', 'ADMIN')");

$conn->query("INSERT INTO usuarios(usuario, password, nombre, rol)
VALUES('INSTALADOR1', '$instPass', 'Instalador', 'INSTALADOR')");

echo "Usuarios creados correctamente";

?>