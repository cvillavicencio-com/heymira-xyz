function jump() {
    var lacat = document.getElementById('combocats')
    location.replace("#cat-" + lacat.value)
}


function replaceQueryParam(param, newval, search) {
    var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?");
    var query = search.replace(regex, "$1").replace(/&$/, '');

    return (query.length > 2 ? query + "&" : "?") + (newval ? param + "=" + newval : '');
}


function cargasetcat(){
    var selset = document.getElementById('catset');
    aa=replaceQueryParam('catset', selset.value, window.location.search);

    if (window.confirm("¿Cambiar set de categorías?\nSe perderán todos los cambios que no hayan sido guardados.")) {
	window.location.replace(aa);
    }
}

function buscarap(){
  var br      = document.getElementById('br');

  let cscats = document.getElementsByClassName('cscat');
  for (let cscat of cscats) {
    cscat.style.display='none';
  }

  for (let cscat of cscats) {
    let scats = cscat.getElementsByClassName('scat')
    for (let scat of scats) {
      scat.style.display='none';
    }

    for (let scat of scats) {
      let tops = scat.getElementsByClassName('top');
      for (let top of tops) {
        top.style.display='none';
      }

      for (let top of tops) {
        var tt = top.innerHTML.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        var bb = br.value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        if (tt.includes(bb)) {
          top.style.display='block';
          scat.style.display='block';
          cscat.style.display='block';

        } else {
          top.style.display='none';
        }
      }
    }
  }
}

function checkURL (abc) {
  var string = abc.value;
  if (!~string.indexOf("http")) {
    string = "http://" + string;
  }
  abc.value = string;
  return abc
}
