<?php

session_start();
require("../conexao.php");

if(isset($_SESSION['idUsuario']) && empty($_SESSION['idUsuario'])==false && $_SESSION['tipoUsuario']==1){

    $idUsuario = $_SESSION['idUsuario'];
    $cliente = filter_input(INPUT_POST, 'cliente');
    $pedido = filter_input(INPUT_POST, 'pedido');
    $obsTroca = filter_input(INPUT_POST, 'obs');

}

?>