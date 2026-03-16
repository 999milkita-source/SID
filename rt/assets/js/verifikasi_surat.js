const modal = document.getElementById("previewModal");
const container = document.getElementById("fileContainer");
const closeBtn = document.getElementById("closePreview");

document.querySelectorAll(".preview-file").forEach((link) => {
  link.addEventListener("click", (e) => {
    e.preventDefault();
    const file = link.dataset.file;
    const ext = file.split(".").pop().toLowerCase();
    let html = "";

    if (["jpg", "jpeg", "png", "gif"].includes(ext)) {
      html = '<img src="../storage/' + file + '">';
    } else if (ext === "pdf") {
      html = '<iframe src="../storage/' + file + '" frameborder="0"></iframe>';
    } else {
      html = "<p>Preview tidak tersedia untuk tipe file ini.</p>";
    }

    container.innerHTML = html;
    modal.style.display = "flex";
  });
});

closeBtn.addEventListener("click", () => {
  modal.style.display = "none";
  container.innerHTML = "";
});

modal.addEventListener("click", (e) => {
  if (e.target === modal) modal.style.display = "none";
});
