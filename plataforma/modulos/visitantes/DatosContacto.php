<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Datos de Contacto';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <h1>Datos de Contacto</h1>

    <div class="cuadro">
        
        <form>
            <label for="nombre">Nombre:</label><br>
            <input type="text" id="nombre" name="nombre" value="Iliana Janett Hernández Partida"><br>
            
            <label for="correo">Correo:</label><br>
            <input type="email" id="correo" name="correo" value="IlianaJanettHernandezPartida@chapala.tecmm.edu.mx"><br>
        </form>
    </div>

    <!-- Imagen en la esquina superior derecha como botón -->
    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a otra página" style="width: 80px; height: auto;"> <!-- Ajusta el tamaño según sea necesario -->
    </a>

    
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>