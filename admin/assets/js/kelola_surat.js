// Lightbox untuk semua gambar
document.querySelectorAll(".lightbox-trigger").forEach((img) => {
  img.addEventListener("click", () => {
    const lightbox = document.getElementById("lightbox");
    const lightboxImg = document.getElementById("lightbox-img");
    const downloadBtn = document.getElementById("lightbox-download");
    lightbox.style.display = "block";
    lightboxImg.src = img.dataset.file;
    downloadBtn.href = img.dataset.file;
    downloadBtn.download = img.alt;
  });
});

document.querySelector(".close").addEventListener("click", () => {
  document.getElementById("lightbox").style.display = "none";
});

window.addEventListener("click", (e) => {
  const lightbox = document.getElementById("lightbox");
  if (e.target == lightbox) {
    lightbox.style.display = "none";
  }
});
