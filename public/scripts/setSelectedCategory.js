function setSelectedCategory(category) {
  let categoriaSeleccionada = category;

  document.addEventListener("DOMContentLoaded", function () {
    let selectCategoria = document.getElementById("categoria");

    for (let i = 0; i < selectCategoria.options.length; i++) {
      if (selectCategoria.options[i].value == categoriaSeleccionada) {
        selectCategoria.options[i].selected = true;
        break;
      }
    }
  });
}
