<?php 

$tipoUsuario = $_SESSION['tipoUsuario'];

?>
<div class="menu-lateral">
    <div class="logo">  
        <img src="../assets/images/logo.png" alt="">
    </div>
    <div class="opcoes">
        <div class="item">
            <a href="../index.php">
                <img src="../assets/images/menu/inicio.png" >
            </a>
        </div>  
        <div class="item">
            <a onclick="menuTrocas()">
                <img src="../assets/images/menu/trocas.png">
            </a>
            <nav id="submenuTrocas">
                <ul class="nav flex-column">
                    <?php if($tipoUsuario==3): ?>
                    <li class="nav-item"> <a class="nav-link" href="../trocas/trocas.php"> Todas as Trocas </a> </li>
                    <li class="nav-item"> <a class="nav-link" href="../trocas/pendentes.php"> Trocas Pendentes </a> </li>
                    <?php endif; ?>
                    <li class="nav-item"> <a class="nav-link" href="../trocas/autorizadas.php"> Trocas Autorizadas </a> </li>
                    <li class="nav-item"> <a class="nav-link" href="../trocas/recusadas.php"> Trocas Recusadas </a> </li>
                </ul>
            </nav>
        </div>
        <div class="item">
            <a href="../sair.php">
                <img src="../assets/images/menu/sair.png" alt="">
            </a>
        </div>
    </div>                
</div>