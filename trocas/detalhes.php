<?php

session_start();
require("../conexao.php");
include_once '../conexao-oracle.php';

if(isset($_SESSION['idUsuario']) && empty($_SESSION['idUsuario'])==false){

    $idtroca = filter_input(INPUT_GET, 'idtroca');
    $tipoUsuario = $_SESSION['tipoUsuario'];

    $trocas = $db->prepare("SELECT * FROM troca WHERE idtroca = :idtroca");
    $trocas->bindValue(':idtroca', $idtroca);
    $trocas->execute();

    $dado = $trocas->fetch();

}else{
    echo "<script>alert('Acesso não permitido');</script>";
    echo "<script>window.location.href='../index.php'</script>";
}

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    </head>
    <body>
        <div class="container-fluid corpo">
            <?php require('../menu-lateral.php') ?>
            <div class="tela-principal">
                <div class="menu-superior">
                   <div class="icone-menu-superior">
                        <img src="../assets/images/icones/trocas.png" alt="">
                   </div>
                   <div class="title">
                        <h2>Trocas</h2>
                   </div>
                </div>
                <!-- dados exclusivo da página-->
                <div class="menu-principal">
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
                        <div class="form-row" id="ultimaLinha">
                            <div class="form-group col-md-4"> 
                                <label for="analiseApoio" class="col-form-abel">Análise Apoio</label>
                                <select class="form-control" name="analiseApoio" id="analiseApoio">
                                    <option value="<?php echo $dado['analise_apoio']; ?>"><?php echo $dado['analise_apoio']; ?></option>
                                    <option value="Autorizado">Autorizado</option>
                                    <option value="Recusado">Recusado</option>
                                </select>
                            </div>
                            
                        
                            <?php
                            if($qtdTv1<1):
                            ?>
                            <div class="form-group col-md-4">
                                <label for="autorizacao" class="col-form-abel">Autorizado por:</label>
                                <input type="text" class="form-control" required name="autorizacao" id="autorizacao">
                            </div>
                            <?php endif; ?>  
                        </div>
                        <?php

                            }elseif($tipoUsuario==2 && ($dado['analise_apoio']=="Autorizado" || $dado['analise_apoio']=="Recusado")){

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
                            if(!empty($dado['recusa'])){
                            ?>
                            <div class="form-group col-md-4">
                                <label for="recusa" class="col-form-abel">Motivo de Recusa</label>
                                <input type="text" value="<?=$dado['recusa'];?>"  readonly class="form-control" required name="recusa" id="recusa">
                            </div> 
                            <?php
                            }
                            ?>
                            <div class="form-group col-md-4"> 
                                <label for="posicao" class="col-form-abel">Posição</label>
                                <select class="form-control" name="posicao" id="posicao">
                                    <option value="<?php echo $dado['posicao']; ?>"><?php echo $dado['posicao']; ?></option>
                                    <option value="L">Liberar</option>
                                    <option value="C">Cortar</option>
                                </select>
                            </div>
                        </div> 
                        <?php 
                        }
                        if(($tipoUsuario==3) && ($dado['analise_apoio']=='aguardando')):
                        ?>
                       
                        <button type="submit" name="" class="btn btn-primary" >Enviar</button>
                        
                        <?php 
                        elseif(($tipoUsuario==2) && ($dado['analise_apoio']!='aguardando')):
                        ?> 
                            <button type="submit" class="btn btn-primary" >Enviar</button>
                        <?php endif; ?> 
                        
                    </form>
                </div>
            </div>
        </div>

        <script src="../assets/js/jquery.js"></script>
        <script src="../assets/js/bootstrap.bundle.min.js"></script>
        <script src="../assets/js/menu.js"></script> 
        <script>
            $("select[name='analiseApoio']").change(function(){
                var analise = $(this).val();
                if(analise=='Recusado'){
                    $('#ultimaLinha').append('<div class="form-group col-md-4"> <label for="recusa" class="col-form-abel">Motivo de Recusa</label> <input required type="text" name="recusa" id="recusa" class="form-control"> </div>');
                    
                }
            });
        </script>
    </body>
</html>