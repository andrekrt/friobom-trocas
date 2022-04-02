<?php

session_start();
require("../conexao.php");

if(isset($_SESSION['idUsuario']) && empty($_SESSION['idUsuario'])==false){

    $idUsuario = $_SESSION['idUsuario'];
    
    $autorizacao = filter_input(INPUT_POST, 'autorizacao')?filter_input(INPUT_POST, 'autorizacao'):null;
    $analiseApoio = filter_input(INPUT_POST, 'analiseApoio');
    $posicao = filter_input(INPUT_POST, 'posicao');
    $numPedido = filter_input(INPUT_POST, 'pedido');

    if(empty($analiseApoio)==false){
        $atualiza = $db->prepare("UPDATE troca SET analise_apoio = :analiseApoio, autorizado = :autorizacao WHERE pedido = :pedido");
        $atualiza->bindValue(':analiseApoio', $analiseApoio);
        $atualiza->bindValue(':autorizacao', $autorizacao);
        $atualiza->bindValue(':pedido', $numPedido);
        $atualiza->execute();
        if($atualiza){
            echo "<script>alert('Troca Autorizada!');</script>";
            echo "<script>window.location.href='../index.php'</script>";
        }else{
            print_r($db->errorInfo());
        }
    }elseif(empty($posicao)==false){

        $atualiza = $db->prepare("UPDATE troca SET posicao = :posicao WHERE pedido = :pedido");
        $atualiza->bindValue(':posicao',$posicao);
        $atualiza->bindValue(':pedido', $numPedido);
        $atualiza->execute();
        if($atualiza){
            echo "<script>alert('Troca Liberada!');</script>";
            echo "<script>window.location.href='trocas.php'</script>";
        }else{
            print_r($db->errorInfo());
        }

    }else{
        echo "erro";
    }

}

?>