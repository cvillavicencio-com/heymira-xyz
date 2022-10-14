<?php
$es="SELECT * FROM Users WHERE id='$id'";
$eq=$conn->query($es);
$el=$eq->fetch_row();
//id nombre clave token info mail tema setfav utypeId
//0  1      2     3     4    5    6    7      +++


$avatar = (file_exists('avatars/'.$id.'-'.strlen($el[1]).'.png')) ? $id.'-'.strlen($el[1]) : 'default';

$setcats = '';
$scS = "SELECT id, nombre FROM Catsets;";
$scQ = $conn->query($scS);

if ($scQ->num_rows > 0) {
    while($scL = $scQ->fetch_assoc()) {
	$sel = $scL['id'] == intval($el[7]) ? ' selected':false;
        $setcats .= '<option value="'.$scL['id'].'" '.$sel.'>'.$scL['nombre'].'</option>';
    }
}

$sel ='';
$apariec ='';
$temas = array('del futuro','normalFome()','oscuro');
for ($i = 0; $i <= 2; $i++){
    $sel = $i == intval($el[6]) ? ' selected':false;
    $apariec .= '<option value="'.$i.'"'.$sel.'>'.$temas[$i].'</option>';
}

$prefs= '
<b>Preferencias</b><br>
<div class="columns">
<div class="column">
<p>Apariencia</p>
<div class="control">
  <div class="select">
    <select name="tema">
'.$apariec.'
    </select>
  </div>
</div>
</div>
<div class="column">
<p>Set de categorías por defecto</p>
<div class="control">
  <div class="select">
    <select name="setfav">
'.$setcats.'
    </select>
  </div>
</div>
</div>
<div class="column">
<p>Visibilidad de la cuenta</p>
<div class="control">
  <div class="select">
    <select>
      <option>Pública</option>
    </select>
  </div>
</div>
</div>
</div>

';

$form= '

<form action="?f=ap" method="POST" enctype="multipart/form-data"/>

<div class="columns">
<div class="column is-one-quarter">
  <label class="label">Avatar</label>
 <div id="file-with-js"
             class="file has-name is-small is-centered is-boxed is-link">
            <label class="file-label">
                <input class="file-input"
                       type="file" name="avatar">
                <span class="file-cta">
                  <img id="output" src="avatars/'.$avatar.'.png" height="100px" width="100px" />

                    <span class="file-label">
                        Subir
                    </span>
                </span>
                <span class="file-name">
                </span>
            </label>
        </div>
    <script>
        // Select the input element using
        // document.querySelector
        var input = document.querySelector(
          "#file-with-js>.file-label>.file-input"
        );
 
        // Bind an listener to onChange event of the input
        input.onchange = function () {
            if(input.files.length > 0){
                var fileNameContainer =
                    document.querySelector(
                      "#file-with-js>.file-label>.file-name"
                    );
                // set the inner text of fileNameContainer
                // to the name of the file
                fileNameContainer.textContent =
                  input.files[0].name;
                  var image = document.getElementById(\'output\');
	          image.src = URL.createObjectURL(event.target.files[0]);
            }
        }
    </script>
</div>
<div class="column">
<div class="field">
  <label class="label">Información</label>
  <div class="control">
    <textarea class="textarea" name="info" maxlength="300" placeholder="Trescientos caracteres para describirte.">'.$el['4'].'</textarea>
  </div>
</div>
</div>
</div>


    <hr>
    '.$prefs.'


<hr>
<div class="columns">
<div class="column">
<div class="field">
  <label class="label">Nueva contraseña</label>
  <div class="control">
    <input class="input" name="clave" type="text" placeholder="Dejar en blanco si quieres conservar contraseña actual">
  </div>
</div>
</div>
<div class="column">
<div class="field">
  <label class="label">Confirmar</label>
  <div class="control">
    <label class="checkbox">
      <input name="conf1" value="1" type="checkbox">
      Quiero cambiar mi contraseña.</label><br>
    <label class="checkbox">
      <input name="conf2" value="2" type="checkbox">
      En serio, quiero cambiar mi contraseña. 
    </label>
  </div>
</div>
</div>
</div>
<div class="field">
  <div class="control is-centered">
    <button class="button is-link">Actualizar</button>
  </div>
</div>
</form>
    ';
$contenido[] = 'Editar Perfil';
$contenido[] = $form;
?>
