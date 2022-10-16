<?php
// cc : CREAR CUENTA

$contenido[] = 'Crear cuenta';
$contenido[] = '
<div class="columns">
  <div class="column">
<center>
<img src="css/cc.png">
</center>
  </div>
  <div class="column">
<form action="./?f=nc" method="post">
<div class="field">
  <label class="label">Nombre de usuario</label>
  <div class="control">
    <input name="nombre" class="input" type="text" placeholder="Para identificarte dentro del sistema" required>
  </div>
</div>
<div class="field">
  <label class="label">Correo electrónico (opcional)</label>
  <div class="control">
    <input name="mail" class="input" type="email" placeholder="Para que recuperes tu cuenta, si olvidas tu contraseña">
  </div>
</div>
<div class="field">
  <label class="label">Código de invitación</label>
  <div class="control">
    <input name="cinv" class="input" placeholder="Para que recuperes tu cuenta, si olvidas tu contraseña" value="'.@$_GET['cod'].'">
  </div>
</div>
<div class="field">
  <label class="label">Contraseña</label>
  <div class="control">
    <input name="clave" class="input" type="password" placeholder="Para saber que eres tu">
  </div>
</div>
<div class="field">
  <div class="control">
    <button class="button is-link">Crear cuenta</button>
  </div>
</div>

</form>
</div>


</div>
';
?>
