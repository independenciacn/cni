<?php require_once 'inc/configuracion.php'; ?>
<?php echo $cabezeraHtml; ?>
<body>
<div id='cuerpo' class='container showgrid'>
<div id='registro' class='span-12 append-6 prepend-6 last'>
    <div class='span-12 last'>
        <img src='<?echo $conf['root']; ?>/imagenes/logotipo2.png' width='470px' alt='The Perfect Place' />
        <h2 class='alert'>
        <img src='<?echo $conf['root']; ?>/estilo/iconos/404.png' alt='Pagina no encontrada' />
        La pagina solicitada no existe<br/>
        Haga clic <a href='<?php echo $conf['root']; ?>' target='_self'>aqui</a> 
        para ir a la pagina principal</h2>
    </div>
</div>
</div>
<?php echo $firmaAplicacion; ?>
</body>
</html>