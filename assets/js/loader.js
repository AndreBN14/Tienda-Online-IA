window.addEventListener("load", (event) => {
  const loader = document.querySelector(".page-loading");
  // Aumentar el tiempo del setTimeout a 1000 milisegundos (1 segundo)
  setTimeout(function () {
    loader.classList.remove("active");
    loader.remove();
  }, 10);
});
