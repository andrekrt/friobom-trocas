<?php 

session_start();
require("conexao.php");

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

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FRIOBOM - TROCAS</title>
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
        <link rel="manifest" href="assets/favicon/site.webmanifest">
        <link rel="mask-icon" href="assets/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">
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
                        <a href="trocas/trocas.php">
                            Todas as Trocas
                        </a>
                    </div>
                    <div class="item"> 
                        <a >
                            Solicitar Nova Troca
                        </a>
                    </div>            
                    <div class="item">
                        <a href="sair.php">
                            <img src="assets/images/menu/sair.png" alt="">
                        </a>
                    </div>
                </div>                
            </div>
            <!-- finalizando menu lateral -->
            <!-- Tela com os dados -->
            <div class="tela-principal">
                <div class="menu-superior">
                   <div class="icone-menu-superior">
                        <img src="assets/images/icones/home.png" alt="">
                   </div>
                   <div class="title">
                        <h2>Bem-Vindo <?php echo utf8_encode($nomeUsuario); ?></h2>
                   </div>
                </div>
                <!-- dados exclusivo da página-->
                <div class="menu-principal">
                    <div class="indices">
                        <div class="indice-area-title">
                            <div class="icone-indice">
                                <img src="assets/images/dados.png" alt="">
                            </div>
                            <div class="title-indice">
                                <p>INFOR GERAIS</p>
                            </div>
                        </div>
                    </div>
                    <div class="area-indice-val">
                        <div class="indice-ind">
                            <div class="indice-ind-tittle">
                                <p>TROCAS</p>
                            </div>
                            <div class="indice-qtde">
                                <img src="assets/images/icones/departamento.png" alt="">
                                <p class="qtde"> <?php echo $qtdTrocas; ?> </p>
                            </div>
                        </div>
                        <div class="indice-ind">
                            <div class="indice-ind-tittle">
                                <p>Categorias</p>
                            </div>
                            <div class="indice-qtde">
                                <img src="assets/images/icones/categoria.png" alt="">
                                <p class="qtde"> <?php echo 0; ?> </p>
                            </div>
                        </div>
                        <div class="indice-ind">
                            <div class="indice-ind-tittle">
                                <p>Produto / Serviços</p>
                            </div>
                            <div class="indice-qtde">
                                <img src="assets/images/icones/produtos.png" alt="">
                                <p class="qtde"> <?php echo 8; ?> </p>
                            </div>
                        </div>
                        <div class="indice-ind">
                            <div class="indice-ind-tittle">
                                <p>Lançamentos</p>
                            </div>
                            <div class="indice-qtde">
                                <img src="assets/images/icones/despesa.png" alt="">
                                <p class="qtde"> <?php echo 6; ?> </p>
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