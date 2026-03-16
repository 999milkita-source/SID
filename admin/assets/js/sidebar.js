document.addEventListener("DOMContentLoaded", function () {
  const toggle = document.getElementById("menu-toggle");
  const sidebar = document.getElementById("sidebar");

  toggle.addEventListener("click", function () {
    sidebar.classList.toggle("active");
  });

  // klik di luar sidebar menutup sidebar
  document.addEventListener("click", function (e) {
    if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
      sidebar.classList.remove("active");
    }
  });
});
