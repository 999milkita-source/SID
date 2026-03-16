document.addEventListener("DOMContentLoaded", () => {
  feather.replace(); //

  const toggle = document.getElementById("menuToggle");
  const menu = document.getElementById("navMenu");
  let open = false;

  toggle.addEventListener("click", () => {
    open = !open;

    if (open) {
      menu.classList.add("show");
      toggle.innerHTML = feather.icons.x.toSvg();
      document.body.style.overflow = "hidden";
    } else {
      menu.classList.remove("show");
      toggle.innerHTML = feather.icons.menu.toSvg();
      document.body.style.overflow = "";
    }
  });

  // auto close saat klik menu
  menu.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      menu.classList.remove("show");
      toggle.innerHTML = feather.icons.menu.toSvg();
      document.body.style.overflow = "";
      open = false;
    });
  });
});
