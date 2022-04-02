<?php 

session_start();
require("conexao.php");
include_once 'conexao-oracle.php';

if(isset($_SESSION['idUsuario']) && empty($_SESSION['idUsuario'])==false){
    
    //$nomeUsuario = $_SESSION['nomeUsuario'];
    $tipoUsuario = $_SESSION['tipoUsuario'];
 
    $selecionar = $dbora->query("SELECT 
    pcpedc.numped, pcpedc.codcli, pcclient.fantasia, pcclient.cliente, pcpedc.DATA,pcpedc.codusur, pcusuari.nome as nomerca, pcpedc.codsupervisor, pcsuperv.nome AS nomeSup, pcpedc.codcob, pcpedc.vlatend, pcpedc.posicao FROM friobom.pcpedc left join friobom.pcclient on pcpedc.codcli = pcclient.codcli left join friobom.pcusuari on pcpedc.codusur = pcusuari.codusur left join friobom.pcsuperv on pcpedc.codsupervisor = pcsuperv.codsupervisor where pcpedc.condvenda = 11 and pcpedc.posicao = 'B'");
    $trocas = $selecionar->fetchAll();
    $qtdTrocas = count($trocas);


    foreach($trocas as $troca){

        $consulta = $db->query("SELECT * FROM troca WHERE pedido = '$troca[NUMPED]' ");        
        if($consulta->rowCount()===0){

            $valor =str_replace(",",".",$troca['VLATEND']);
            $dataTroca =  $troca['DATA'];

            /* consulta para calcular valor comprado */
            $ora = $dbora->prepare("SELECT CODPROD FROM friobom.pcpedi WHERE NUMPED = :pedido");
            $ora->bindValue(':pedido', $troca['NUMPED']);
            $ora->execute();
            $produto = $ora->fetch();
            $consultaFornec = $dbora->prepare("SELECT CODFORNEC FROM friobom.pcprodut WHERE CODPROD = :produto");
            $consultaFornec->bindValue(':produto', $produto['CODPROD']);
            $consultaFornec->execute();
            $fornecedor = $consultaFornec->fetch();            

           $sql = $db->query("INSERT INTO troca (pedido, cod_cliente, fantasia, razao_social, data_troca, cod_rca, nome_rca, cod_sup, nome_sup, valor_atend, fornecedor, analise_apoio, posicao) VALUES ('$troca[NUMPED]', '$troca[CODCLI]' , '$troca[FANTASIA]', '$troca[CLIENTE]', '$dataTroca', '$troca[CODUSUR]', '$troca[NOMERCA]', '$troca[CODSUPERVISOR]', '$troca[NOMESUP]', '$valor', '$fornecedor[CODFORNEC]', 'aguardando', '$troca[POSICAO]') ");  

        }

    }

    //qtd de trocas registradas
    $qtdPend = $db->prepare("SELECT * FROM troca WHERE analise_apoio = :analise");
    $qtdPend->bindValue(':analise', 'aguardando');
    $qtdPend->execute();
    $qtdPend = $qtdPend->rowCount();

    $qtdAut = $db->prepare("SELECT * FROM troca WHERE analise_apoio = :analise");
    $qtdAut->bindValue(':analise', 'Autorizado');
    $qtdAut->execute();
    $qtdAut = $qtdAut->rowCount();

    $qtdRec = $db->prepare("SELECT * FROM troca WHERE analise_apoio = :analise");
    $qtdRec->bindValue(':analise', 'Recusado');
    $qtdRec->execute();
    $qtdRec = $qtdRec->rowCount();

}else{
    echo "<script>window.location.href='login.php'</script>";
}

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--<meta http-equiv="refresh" content="60">-->
        <title>TROCAS</title>
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
        <link rel="manifest" href="assets/favicon/site.webmanifest">
        <link rel="mask-icon" href="assets/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </head>
    <body>
        <div class="container-fluid corpo">
            <div class="menu-lateral">
                <div class="logo">  
                    <img src="assets/images/logo.png" alt="">
                </div>
                <div class="opcoes">
                    <div class="item">
                        <a href="index.php">
                            <img src="assets/images/menu/inicio.png" alt="">
                        </a>
                    </div>  
                    <div class="item">
                        <a onclick="menuTrocas()">
                            <img src="assets/images/menu/trocas.png" >
                        </a>
                        <nav id="submenuTrocas">
                            <ul class="nav flex-column">
                                <?php if($tipoUsuario==3): ?>
                                <li class="nav-item"> <a class="nav-link" href="trocas/trocas.php"> Todas as Trocas </a> </li>
                                <li class="nav-item"> <a class="nav-link" href="trocas/pendentes.php"> Trocas Pendentes </a> </li>
                                <?php endif; ?>
                                <li class="nav-item"> <a class="nav-link" href="trocas/autorizadas.php"> Trocas Autorizadas </a> </li>
                                <li class="nav-item"> <a class="nav-link" href="trocas/recusadas.php"> Trocas Recusadas </a> </li>
                                
                            </ul>
                        </nav>
                    </div>
                    <div class="item">
                        <a href="sair.php">
                            <img src="assets/images/menu/sair.png" alt="">
                        </a>
                    </div>
                </div>                
            </div>
            <!-- Tela com os dados -->
            <div class="tela-principal">
                <div class="menu-superior">
                   <div class="icone-menu-superior">
                        <img src="assets/images/icones/home.png" alt="">
                   </div>
                   <div class="title">
                        <h2>Trocas</h2>
                   </div>
                </div>
                <!-- dados exclusivo da página-->
                <div class="menu-principal">
                    <div class="area-indice-val">
                        <div class="indice-ind">
                            <div class="indice-ind-tittle">
                                <p>Trocas Pendente</p>
                            </div>
                            <div class="indice-qtde">
                                <img src="assets/images/icones/troca-pendente.jpg" alt="">
                                <p class="qtde">  <?= $qtdPend?> </p>
                            </div>
                        </div>
                        <div class="indice-ind">
                            <div class="indice-ind-tittle">
                                <p>Trocas Autorizadas</p>
                            </div>
                            <div class="indice-qtde">
                                <img src="assets/images/icones/troca-autorizada.jpg" alt="">
                                <p class="qtde"> <?=$qtdAut?> </p>
                            </div>
                        </div>
                        <div class="indice-ind">
                            <div class="indice-ind-tittle">
                                <p>Trocas Recusadas</p>
                            </div>
                            <div class="indice-qtde">
                                <img src="assets/images/icones/troca-recusada.jpg" alt="">
                                <p class="qtde"> <?=$qtdRec?> </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="assets/js/jquery.js"></script>
        <script src="assets/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/menu.js"></script>
    </body>
</html>