document.addEventListener("DOMContentLoaded", function () {
  const imagePreviewInput = document.getElementById("image_preview_input");
  const preview = document.getElementById("image_preview");
  const imagePreviewSubmit = document.getElementById("image_preview_submit");

  if (!imagePreviewInput) {
    console.error("Input de imagem 'image_preview_input' não encontrado.");
    return; 
  }
  if (!preview) {
    console.error("Área de preview 'image_preview' não encontrada.");
    return;
  }

  imagePreviewInput.style.display = "none";

  imagePreviewInput.addEventListener("change", function (event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result; 
      
        if (imagePreviewSubmit) {
          imagePreviewSubmit.style.display = "block";
        }
      };
      reader.readAsDataURL(file);
    }
  });

  

  if (imagePreviewSubmit) {
    imagePreviewSubmit.style.display = "none"; 

    imagePreviewSubmit.addEventListener('click', function() {
      console.log("Botão 'Confirmar Nova Imagem' clicado.");
      this.style.display = 'none';
    });
  } else {
    console.warn("Elemento com ID 'image_preview_submit' não encontrado. Funcionalidade de botão de submit do preview pode não estar ativa.");
  }

  preview.addEventListener("click", function () {
    imagePreviewInput.click();
  });

});