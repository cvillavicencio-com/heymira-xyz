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

    if (window.confirm("¿Cambiar set de categorías?\nEl contenido de los campos será reseteado.")) {
	window.location.replace(aa);
    }
}
