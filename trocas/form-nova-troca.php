<?php 

session_start();
require("../conexao.php");

if(isset($_SESSION['idUsuario']) && empty($_SESSION['idUsuario'])==false && $_SESSION['tipoUsuario']==1){

    $idUsuario = $_SESSION['idUsuario'];
    $nomeUsuario = $_SESSION['nomeUsuario'];
    
}else{
    echo "<script>alert('Acesso não permitido');</script>";
    echo "<script>window.location.href='../index.php'</script>";
}

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Solicitação de Troca</title>
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
            <div class="menu-lateral">
                <div class="logo">  
                    <img src="../assets/images/logo.png" alt="">
                </div>
                <div class="opcoes">
                    <div class="item">
                        <a href="../index.php">
                            <img src="../assets/images/menu/inicio.png" alt="">
                        </a>
                    </div>
                    <div class="item"> 
                        <a href="../trocas/trocas.php">
                            Todas as Trocas
                        </a>
                    </div>
                    <div class="item"> 
                        <a href=";;/form-nova-troca.php">
                            Solicitar Nova Troca
                        </a>
                    </div>            
                    <div class="item">
                        <a href="../sair.php">
                            <img src="../assets/images/menu/sair.png" alt="">
                        </a>
                    </div>
                </div>                
            </div>
            <!-- finalizando menu lateral -->
            <!-- Tela com os dados -->
            <div class="tela-principal">
                <div class="menu-superior">
                   <div class="icone-menu-superior">
                        <img src="../assets/images/icones/trocas.png" alt="">
                   </div>
                   <div class="title">
                        <h2>Solicitar Troca</h2>
                   </div>
                </div>
                <!-- dados exclusivo da página-->
                <div class="menu-principal">
                    <form action="add-troca.php" enctype="multipart/form-data" method="post">
                        <div class="form-row">
                            <div class="form-group col-md-2  espaco">
                                <label for="cliente"> Cliente </label>
                                <input type="text" name="cliente" for="cliente" class="form-control">
                            </div>
                            <div class="form-group col-md-3 espaco">
                                <label for="pedido">Pedido</label>
                                <input type="text" name="pedido" for="pedido" class="form-control">                                
                            </div>
                            <div class="form-group col-md-5 espaco">
                                <label for="anexo">Inserir Anexos</label>
                                <input type="file" class="form-control-file" multiple="multiple" name="anexo[]" id="anexo">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-10 espaco">
                                <label for="obs">Observações</label>
                                <textarea name="obs" id="obs" class="form-control" rows="5"></textarea>
                            </div>
                        </div>
                       <button type="submit" class="btn btn-primary">Solicitar</button>
                    </form>
                </div>
            </div>
        </div>

        <script src="../assets/js/jquery.js"></script>
        <script src="../assets/js/bootstrap.bundle.min.js"></script>
        <script src="../assets/js/menu.js"></script>
    </body>
</html>