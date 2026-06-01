<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "sistemacompleto"
);

if($conn->connect_error){
    die("Error conexión");
}

?>