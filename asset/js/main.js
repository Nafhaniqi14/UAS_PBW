document.addEventListener("DOMContentLoaded", function () {
  var toggle = document.getElementById("sidebarToggle");
  var sidebar = document.querySelector(".sidebar");
  var wrapper = document.querySelector(".main-wrapper");
  var brand = document.getElementById("navbarBrandText");

  function updateBrand() {
    if (!brand) return;
    if (sidebar && sidebar.classList.contains("collapsed")) {
      brand.textContent = "MATERIAL POINT";
    } else {
      brand.textContent = "";
    }
  }

  updateBrand();

  if (toggle && sidebar && wrapper) {
    toggle.addEventListener("click", function () {
      sidebar.classList.toggle("collapsed");
      wrapper.classList.toggle("collapsed");
      updateBrand();
    });
  }
});
