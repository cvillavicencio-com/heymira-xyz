<?php
$buscador= '
<form action="." method="GET">
<div class="field">
  <label class="label">Término de búsqueda</label>
  <div class="control">
    <input name="buscar" class="input is-info" type="text" placeholder="Text input" required>
  </div>
</div>

<div class="field is-grouped">
  <div class="control">
    <input  type="submit" value="Buscar">
  </div>
  <div class="control">
    <input value="Procesar código" type="button" disabled>
  </div>
</div>
</form>
';
$contenido = array('Buscador',$buscador);

// pendiente: poner opciones para buscar en titulo, info o url
// ahora está funcional
?>
