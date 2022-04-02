<?php 

session_start();
require("../conexao.php");
include_once '../conexao-oracle.php';

if(isset($_SESSION['idUsuario']) && empty($_SESSION['idUsuario'])==false){
    
    //$nomeUsuario = $_SESSION['nomeUsuario'];
    $tipoUsuario = $_SESSION['tipoUsuario'];
    $pagina = (isset($_GET['pagina']))? $_GET['pagina'] : 1;
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

}else{
    echo "<script>alert('Acesso não permitido');</script>";
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
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="apple-touch-icon" sizes="180x180" href="../assets/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../assets/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../assets/favicon/favicon-16x16.png">
        <link rel="manifest" href="../assets/favicon/site.webmanifest">
        <link rel="mask-icon" href="../assets/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

         <!-- arquivos para datatable -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.10.25/af-2.3.7/date-1.1.0/r-2.2.9/rg-1.1.3/sc-2.0.4/sp-1.3.0/datatables.min.css"/>
    </head>
    <body>
        <div class="container-fluid corpo">
            <?php require('../menu-lateral.php') ?>
            <!-- Tela com os dados -->
            <div class="tela-principal">
                <div class="menu-superior">
                   <div class="icone-menu-superior">
                        <img src="../assets/images/icones/trocas.png" alt="">
                   </div>
                   <div class="title">
                        <h2>Trocas Autorizada</h2>
                   </div>
                </div>
                <!-- dados exclusivo da página-->
                <div class="menu-principal">
                    
                    <div class="table-responsive">
                        <table id="trocas" class="table table-striped table-dark table-bordered"> 
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center text-nowrap"> Pedido</th>
                                    <th scope="col" class="text-center text-nowrap">Cód. Cliente</th>
                                    <th scope="col" class="text-center">Cliente</th>
                                    <th scope="col" class="text-center">Razão Social</th>
                                    <th scope="col" class="text-center text-nowrap">Data</th>
                                    <th scope="col" class="text-center text-nowrap"> RCA</th>
                                    <th scope="col" class="text-center text-nowrap"> Supervisor</th>
                                    <th scope="col" class="text-center text-nowrap">Valor Atendido</th>
                                    <th scope="col" class="text-center text-nowrap">Análise Apoio</th>
                                    <th scope="col" class="text-center text-nowrap">Posição</th>
                                    <th scope="col" class="text-center text-nowrap">Ações</th>
                                </tr>
                            </thead>
                            
                        </table>
                    </div>
                </div>
                <!-- finalizando dados exclusivo da página -->
                
               
                
            </div>
        </div>

        <script src="../assets/js/jquery.js"></script>
        <script src="../assets/js/bootstrap.bundle.min.js"></script>
        <script src="../assets/js/menu.js"></script> 

        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.10.25/af-2.3.7/date-1.1.0/r-2.2.9/rg-1.1.3/sc-2.0.4/sp-1.3.0/datatables.min.js"></script>

        <script >
            $(document).ready(function(){
                $('#trocas').DataTable({
                    'processing':true,
                    'serverSide':true,
                    'serverMethod':'post',
                    'ajax':{
                        'url':'pesq_autorizadas.php'
                    },
                    'columns':[
                        {data:'pedido'},
                        {data:'cod_cliente'},
                        {data:'fantasia'},
                        {data:'razao_social'},
                        {data:'data_troca'},
                        {data:'cod_rca'},
                        {data:'cod_sup'},
                        {data:'valor_atend'},
                        {data:'analise_apoio'},
                        {data:'posicao'},
                        {data:'acoes'},
                    ],
                    "language":{
                        "url":"//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
                    }
                });
            });
        </script>
    
    </body>
</html>