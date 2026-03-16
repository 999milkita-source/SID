// status_surat.js

document.addEventListener("DOMContentLoaded", function () {
  const lightbox = document.getElementById("lightbox");
  const lightboxImg = document.getElementById("lightbox-img");
  const lightboxDownload = document.getElementById("lightbox-download");
  const closeBtn = document.querySelector(".lightbox .close");

  // buka lightbox ketika gambar diklik
  document.querySelectorAll(".lightbox-trigger").forEach((img) => {
    img.addEventListener("click", function () {
      const fileUrl = this.getAttribute("data-file");
      const filename = this.getAttribute("data-filename");

      lightboxImg.src = fileUrl;

      // download link diarahkan ke download.php
      lightboxDownload.href =
        "../public/inc/download.php?file=" + encodeURIComponent(filename);
      lightboxDownload.setAttribute("download", filename);

      lightbox.style.display = "block";
    });
  });

  // tutup lightbox
  closeBtn.addEventListener("click", function () {
    lightbox.style.display = "none";
    lightboxImg.src = "";
  });

  // klik di luar gambar juga tutup lightbox
  lightbox.addEventListener("click", function (e) {
    if (e.target === lightbox) {
      lightbox.style.display = "none";
      lightboxImg.src = "";
    }
  });
});
