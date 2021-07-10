<?php 

session_start();
require("conexao.php");
include_once 'conexao-oracle.php';

if(isset($_SESSION['idUsuario']) && empty($_SESSION['idUsuario'])==false){

    $idUsuario = $_SESSION['idUsuario'];

    $sql = $db->prepare("SELECT * FROM usuario WHERE idusuario = :idUsuario");
    $sql->bindValue(':idUsuario', $idUsuario);
    $sql->execute();
    
    if($sql->rowCount()>0){
        $dado = $sql->fetch();

        $nomeUsuario = $dado['nome_usuario'];
        $tipoUsuario = $dado['tipo_usuario_idtipo_usuario'];

        $_SESSION['tipoUsuario'] = $tipoUsuario;
        $_SESSION['nomeUsuario'] = $nomeUsuario;

        $qtdTrocas = $db->query("SELECT * FROM troca WHERE analise_apoio = 'aguardando' ")->rowCount();

    }else{
        header("Location:login.php");
    }
    
}else{
    header("Location:login.php");
}

if(isset($_SESSION['idUsuario']) && empty($_SESSION['idUsuario'])==false){
    
    $nomeUsuario = $_SESSION['nomeUsuario'];
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
    echo "<script>window.location.href='index.php'</script>";
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
                   <!-- <div class="item"> 
                        <a href="trocas/trocas.php">
                            Todas as Trocas
                        </a>
                    </div>
                    <div class="item"> 
                        <a href="form-nova-troca.php">
                            Solicitar Nova Troca
                        </a>
                    </div> -->           
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
                        <img src="assets/images/icones/trocas.png" alt="">
                   </div>
                   <div class="title">
                        <h2>Trocas</h2>
                   </div>
                </div>
                <!-- dados exclusivo da página-->
                <div class="menu-principal">
                    <div class="filtro">
                        <form action="" class="form-inline" method="post">
                            <div class="form-row">
                                <select name="cliente" id="cliente" class="form-control">
                                    <option value=""></option>
                                    <?php
                                    $filtro = $db->query("SELECT cod_cliente, fantasia FROM troca");
                                    if ($filtro->rowCount() > 0) {
                                        $dados = $filtro->fetchAll();
                                        foreach ($dados as $dado) {

                                    ?>
                                            <option value="<?php echo $dado['cod_cliente'] ?>"> <?php echo $dado['cod_cliente'] . " - " . utf8_encode($dado['fantasia'])  ?> </option>
                                    <?php

                                        }
                                    }

                                    ?>
                                </select>
                                <input type="submit" value="Filtrar" name="filtro" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-dark table-bordered"> 
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
                            <tbody>
                                <?php
                                if($tipoUsuario==3 || $tipoUsuario==99){

                                  $analiseApoio = 'aguardando';

                                }elseif($tipoUsuario==2){
                                    $analiseApoio = 'Autorizado';
                                }
                                //paginação
                                $totalProduto = $db->query("SELECT * FROM troca WHERE posicao = 'B' AND analise_apoio = '$analiseApoio'")->rowCount();
                                $qtdPorPagina = 6;
                                $numPaginas = ceil($totalProduto/$qtdPorPagina);
                                $paginaInicial = ($qtdPorPagina*$pagina)-$qtdPorPagina;
                                

                                if(isset($_POST['filtro']) && !empty($_POST['cliente'])){

                                    $cliente = filter_input(INPUT_POST, 'cliente');
                                    $limitado = $db->query("SELECT * FROM troca WHERE posicao = 'B' AND analise_apoio = '$analiseApoio' AND cod_cliente = '$cliente' LIMIT $paginaInicial, $qtdPorPagina ");
                                    

                                }else{
                                    $limitado = $db->query("SELECT * FROM troca WHERE posicao = 'B' AND analise_apoio = '$analiseApoio' LIMIT $paginaInicial, $qtdPorPagina ");
                                }
                                
                                    if($limitado){
                                        $dados = $limitado->fetchAll();
                                        foreach($dados as $dado){
                                        
                                    ?>
                                    <tr id="<?php echo $dado['pedido'] ?>">
                                        <td scope="col" class="text-center text-nowrap"> <?php echo $dado['pedido']; ?> </td>
                                        <td scope="col" class="text-center text-nowrap"> <?php echo $dado['cod_cliente'] ; ?> </td>
                                        <td scope="col" class="text-center"> <?php echo utf8_encode($dado['fantasia']) ; ?> </td>
                                        <td scope="col" class="text-center"> <?php echo $dado['razao_social'] ; ?> </td>
                                        <td scope="col" class="text-center text-nowrap"> <?php  echo $dado['data_troca']; ?> </td>
                                        <td scope="col" class="text-center"> <?php echo $dado['cod_rca']. " - " . utf8_encode($dado['nome_rca']) ; ?> </td>
                                        <td scope="col" class="text-center "> <?php echo $dado['cod_sup'] . " - " . utf8_encode($dado['nome_sup']); ?> </td>
                                        <td scope="col" class="text-center text-nowrap"> <?php echo "R$ " . number_format($dado['valor_atend'],2,",","."); ?> </td>
                                        <td scope="col" class="text-center text-nowrap"> <?php echo $dado['analise_apoio'] ?> </td>
                                        <td scope="col" class="text-center text-nowrap"> <?php echo $dado['posicao']; ?> </td>
                                        <td scope="col" class="text-center text-nowrap">
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal<?php echo $dado['pedido']; ?>" data-whatever="@mdo" value="<?php echo $dado['pedido']; ?>" name="pedido" >Visualisar</button>
                                        </td>
                                    </tr>
                                    <!-- INICIO MODAL visualisar troca-->
                                    <div class="modal fade" id="modal<?php echo $dado['pedido']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">TROCA</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="atualiza.php" method="post">
                                                        <div class="form-row">
                                                            <div class="form-group col-md-2">
                                                                <label for="pedido" class="col-form-label">Pedido</label>
                                                                <input type="text" name="pedido" class="form-control" readonly id="pedido" value="<?php echo $dado['pedido'] ?>">
                                                            </div>
                                                            <div class="form-group col-md-2">
                                                                <label for="codCliente" class="col-form-label"> Cód. Cliente</label>
                                                                <input type="text" readonly name="codCliente" class="form-control"  id="codCliente" value="<?php echo $dado['cod_cliente'];  ?>">
                                                            </div>
                                                            <div class="form-group col-md-3">
                                                                <label for="fantasia" class="col-form-label"> Cliente</label>
                                                                <input type="text" readonly name="fantasia" class="form-control"  id="fantasia" value="<?php echo utf8_encode($dado['fantasia']);  ?>">
                                                            </div>
                                                            <div class="form-group col-md-5">
                                                                <label for="razaoSocial" class="col-form-label"> Razão Social</label>
                                                                <input type="text" readonly name="razaoSocial" class="form-control"  id="RazaoSocial" value="<?php echo utf8_encode($dado['razao_social']);  ?>">
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-2">
                                                                <label for="data" class="col-form-label"> Data da Troca </label>
                                                                <input type="text" readonly name="data" class="form-control"  id="data" value="<?php echo $dado['data_troca'];  ?>">
                                                            </div>
                                                            <div class="form-group col-md-4">
                                                                <label for="rca" class="col-form-label"> RCA </label>
                                                                <input type="text" readonly name="rca" class="form-control"  id="rca" value="<?php echo $dado['cod_rca']. " - " . utf8_encode($dado['nome_rca']);  ?>">
                                                            </div>
                                                            <div class="form-group col-md-4">
                                                                <label for="supervisor" class="col-form-label"> Supervisor </label>
                                                                <input type="text" readonly name="supervisor" class="form-control"  id="supervisor" value="<?php echo $dado['cod_sup'] . " - " .utf8_encode($dado['nome_sup']);  ?>">
                                                            </div>
                                                            <div class="form-group col-md-2">
                                                                <label for="vlAtendido" class="col-form-label"> Valor Atendido </label>
                                                                <input type="text" readonly name="vlAtendido" class="form-control"  id="vlAtendido" value="<?php echo "R$ " . number_format($dado['valor_atend'],2,",",".")  ;  ?>">
                                                            </div>
                                                        </div>  
                                                        <?php
                                                            $consultaCompras = $dbora->prepare("SELECT pcpedi.QT, pcpedi.PVENDA,  pcprodut.codfornec from friobom.pcpedi left join friobom.pcprodut on pcpedi.codprod = pcprodut.codprod
                                                            where codcli = :cliente and posicao = 'F'  and codfornec = :fornecedor and (DATA BETWEEN  sysdate-365 and sysdate)");
                                                            $consultaCompras->bindValue('cliente',$dado['cod_cliente'] );
                                                            $consultaCompras->bindValue(':fornecedor', $dado['fornecedor']);
                                                            $consultaCompras->execute();
                                                            $comprado = $consultaCompras->fetchAll();
                                                            $total = 0;
                                                            foreach($comprado as $compra){
                                                            
                                                                $valor =  str_replace(".", "",$compra['PVENDA']);
                                                                $valor = str_replace(",",".",$valor);

                                                                $qtd = str_replace(",",".",$compra['QT']);

                                                            // echo $compra['QT']. "x" .$valor. "<BR>";

                                                                $totalInd = $qtd*$valor;
                                                                $total = $total + $totalInd;
                                                            }

                                                            $consultaTrocas = $dbora->prepare("SELECT pcpedi.numped,pcpedi.qt, pcpedi.data, pcpedi.pvenda, pcpedc.condvenda, pcprodut.codfornec from friobom.pcpedi 
                                                            left join friobom.pcpedc on pcpedi.numped = pcpedc.numped
                                                            left join friobom.pcprodut on pcpedi.codprod = pcprodut.codprod
                                                            where pcpedi.codcli = :cliente and pcpedc.condvenda = '11' and codfornec = :fornecedor 
                                                            and (pcpedi.DATA BETWEEN  sysdate-365 and sysdate)");
                                                            $consultaTrocas->bindValue(':cliente', $dado['cod_cliente']);
                                                            $consultaTrocas->bindValue(':fornecedor', $dado['fornecedor']);
                                                            $consultaTrocas->execute();
                                                            $trocasArray = $consultaTrocas->fetchAll();
                                                            $totalTrocado = 0;
                                                            foreach($trocasArray as $troca){

                                                                $valor =  str_replace(",", ".",$troca['PVENDA']);

                                                                $qtd = str_replace(",",".",$troca['QT']) ;

                                                                //echo $troca['QT']. " x " .$valor. "<BR>";

                                                                $totalInd = $qtd*$valor;
                                                                $totalTrocado = $totalTrocado + $totalInd;
                                                            }

                                                            $verificaTv1 = $dbora->prepare("SELECT posicao from friobom.pcpedc where codcli = :cliente and posicao = 'L'");
                                                            $verificaTv1->bindValue(':cliente', $dado['cod_cliente']);
                                                            $verificaTv1->execute();
                                                            $qtdTv1 = count($verificaTv1->fetchAll());
                                                            $tv1;
                                                             
                                                            if($qtdTv1>0){
                                                                $tv1 = "SIM";
                                                            }else{
                                                                $tv1 = "NÃO";
                                                            }


                                                        
                                                        ?>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-2">
                                                                <label class="col-form-label" for="fornecedor">Fornecedor</label>
                                                                <input type="text" readonly name="fornecedor" class="form-control" id="fornecedor" value="<?=$dado['fornecedor']; ?>">
                                                            </div>
                                                            <div class="form-group col-md-5">
                                                                <label class="col-form-label" for="vlComprado">Valor Comprado no último ano</label>
                                                                <input type="text" readonly name="vlComprado" class="form-control" id="vlComprado" value="<?php echo "R$ " .number_format($total, 2, ",", ".") ; ?>">
                                                            </div>
                                                            <div class="form-group col-md-5">
                                                                <label class="col-form-label" for="vlTrocado">Valor Trocado no último ano</label>
                                                                <input type="text" readonly name="vlTrocado" class="form-control" id="vlTrocado" value="<?php echo "R$ " .number_format($totalTrocado, 2, ",", ".") ; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-2">
                                                                <label class="col-form-label" for="fornecedor">% Troca</label>
                                                                <input type="text" readonly name="fornecedor" class="form-control" id="fornecedor" value="<?=($total==0)?0:number_format(($totalTrocado/$total)*100, 2,",", "."). "%" ; ?>">
                                                            </div>
                                                            <div class="form-group col-md-3">
                                                                <label class="col-form-label" for="trocaFutura">% Troca Futura</label>
                                                                <input type="text" readonly name="trocaFutura" class="form-control" id="trocaFutura" value="<?=($total==0)?0:number_format((($totalTrocado+$dado['valor_atend'])/$total)*100, 2,",", "."). "%" ; ?>">
                                                            </div>
                                                            <div class="form-group col-md-2">
                                                                <label class="col-form-label" for="fornecedor">TV1 Liberado</label>
                                                                <input type="text" readonly name="fornecedor" class="form-control" id="fornecedor" value="<?= $tv1; ?>">
                                                            </div>
                                                        </div>
                                                        <?php
                                                            $consultaItens = $dbora->query("SELECT pcpedi.numped, pcprodut.codprod, pcprodut.descricao, pcprodut.unidade, pcpedi.qt, pcpedi.ptabela, pcprodut.codfornec 
                                                            FROM friobom.pcpedi 
                                                            LEFT JOIN friobom.pcprodut ON pcpedi.codprod = pcprodut.codprod
                                                            WHERE numped = '$dado[pedido]'");
                                                            $produtos = $consultaItens->fetchAll();
                                                            foreach($produtos as $produto){
                                                        ?>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-1">
                                                                <label for="codProduto" class="col-form-label"> Produto </label>
                                                                <input type="text" readonly name="codProduto" class="form-control"  id="codProduto" value="<?php echo $produto['CODPROD'] ; ?>">
                                                            </div>
                                                            <div class="form-group col-md-4">
                                                                <label for="descricao" class="col-form-label"> Descrição </label>
                                                                <input type="text" readonly name="descricao" class="form-control"  id="descricao" value="<?php echo $produto['DESCRICAO'] ; ?>">
                                                            </div>
                                                            <div class="form-group col-md-1">
                                                                <label for="un" class="col-form-label"> Un. </label>
                                                                <input type="text" readonly name="un" class="form-control"  id="un" value="<?php echo $produto['UNIDADE'] ; ?>">
                                                            </div>
                                                            <div class="form-group col-md-1">
                                                                <label for="qtd" class="col-form-label"> Qtd. </label>
                                                                <input type="text" readonly name="qtd" class="form-control"  id="qtd" value="<?php echo $produto['QT'] ; ?>">
                                                            </div>
                                                            <div class="form-group col-md-2">
                                                                <label for="ptabela" class="col-form-label"> Preço Tabela </label>
                                                                <input type="text" readonly name="ptabela" class="form-control"  id="ptabela" value="<?php echo "R$ " . $produto['PTABELA'] ; ?>">
                                                            </div>
                                                        </div>
                                                        
                                                        <?php        
                                                            }
                                                            if($tipoUsuario==3 || $tipoUsuario==99) {
                                                        ?>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-4"> 
                                                                <label for="analiseApoio" class="col-form-abel">Análise Apoio</label>
                                                                <select class="form-control" name="analiseApoio" id="analiseApoio">
                                                                    <option value="<?php echo $dado['analise_apoio']; ?>"><?php echo $dado['analise_apoio']; ?></option>
                                                                    <option value="Autorizado">Autorizado</option>
                                                                    <option value="Recusado">Recusado</option>
                                                                </select>
                                                            </div>
                                                        
                                                            <?php
                                                            if($qtdTv1<1){
                                                            ?>
                                                            <div class="form-group col-md-4">
                                                                <label for="autorizacao" class="col-form-abel">Autorizado por:</label>
                                                                <input type="text" class="form-control" required name="autorizacao" id="autorizacao">
                                                            </div> 
                                                        </div>
                                                            <?php
                                                            }

                                                            }elseif($tipoUsuario==2 && $dado['analise_apoio']=="Autorizado"){

                                                            ?>
                                                        <div class="form-row">
                                                        <?php
                                                            if($qtdTv1<1){
                                                            ?>
                                                            <div class="form-group col-md-4">
                                                                <label for="autorizacao" class="col-form-abel">Autorizado por:</label>
                                                                <input type="text" value="<?=$dado['autorizado'];?>"  readonly class="form-control" required name="autorizacao" id="autorizacao">
                                                            </div> 
                                                            <?php
                                                            }
                                                            ?>
                                                            <div class="form-group col-md-4"> 
                                                                <label for="posicao" class="col-form-abel">Posição</label>
                                                                <select class="form-control" name="posicao" id="posicao">
                                                                    <option value="<?php echo $dado['posicao']; ?>"><?php echo $dado['posicao']; ?></option>
                                                                    <option value="L">Liberar</option>
                                                                </select>
                                                            </div>
                                                        </div> 
                                                            <?php 
                                                            }
                                                            ?>        
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                                    <button type="submit" class="btn btn-primary" >Enviar</button>
                                                    
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- FIM MODAL -->
                                    <?php 
                                    
                                        }
                                    }
                                
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- finalizando dados exclusivo da página -->
                <!-- Iniciando paginação -->
                <?php
            
               $paginaAnterior = $pagina-1;
                $paginaPosterior = $pagina+1;
                            
                ?>
                <nav aria-label="Navegação de página exemplo" class="paginacao">
                    <ul class="pagination">
                        <li class="page-item">
                        <?php
                            if($paginaAnterior!=0){
                                echo "<a class='page-link' href='index.php?pagina=$paginaAnterior' aria-label='Anterior'>
                                <span aria-hidden='true'>&laquo;</span>
                                <span class='sr-only'>Anterior</span>
                            </a>";
                            }else{
                                echo "<a class='page-link' aria-label='Anterior'> 
                                    <span aria-hidden='true'>&laquo;</span>
                                    <span class='sr-only'>Anterior</span>
                                </a>";
                            }
                        ?>
                        
                        </li>
                        <?php
                            for($i=1;$i < $numPaginas+1;$i++){
                                echo "<li class='page-item'><a class='page-link' href='index.php?pagina=$i'>$i</a></li>";
                            }
                        ?>
                        <li class="page-item">
                        <?php
                            if($paginaPosterior <= $numPaginas){
                                echo " <a class='page-link' href='index.php?pagina=$paginaPosterior' aria-label='Próximo'>
                                <span aria-hidden='true'>&raquo;</span>
                                <span class='sr-only'>Próximo</span>
                            </a>";
                            }else{
                                echo " <a class='page-link' aria-label='Próximo'>
                                        <span aria-hidden='true'>&raquo;</span>
                                        <span class='sr-only'>Próximo</span>
                                </a> ";
                            }
                        ?>
                    
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <script src="assets/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/menu.js"></script>
        <script>
            $(document).ready(function() {
                $('#cliente').select2();
            });
        </script>
    </body>
</html>