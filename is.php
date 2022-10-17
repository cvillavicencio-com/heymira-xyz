<?php
    $contenido[] = 'Iniciar sesión';
	    $contenido[] = '
<div class="columns">
<div class="column">
<form action="./?f=si" method="post">
<div class="field">
  <label class="label">Nombre de usuario</label>
  <div class="control">
    <input name="nombre" class="input" type="text" placeholder="nombre de usuario o correo electrónico">
  </div>
</div>

<div class="field">
  <label class="label">Contraseña</label>
  <div class="control">
    <input name="clave" class="input" type="password" placeholder="****">
  </div>
</div>

<div class="field">
  <label class="checkbox">
    <input name="mantener" value="1" type="checkbox">
    Mantener sesión abierta
  </label>
</div>

<div class="field">
  <div class="control">
    <button class="button is-link">Iniciar sesión</button>
  </div>
</div>
</form>
</div>
<div class="column">
	    
<img src="css/logosub.gif" width="100%">
	    </div>
	    </div>	    ';
?>
