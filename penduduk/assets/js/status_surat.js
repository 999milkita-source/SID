document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("previewModal");
  const closeButton = document.getElementById("closePreview");
  const previewBody = document.getElementById("previewBody");
  const previewTitle = document.getElementById("previewTitle");
  const downloadLink = document.getElementById("previewDownloadLink");
  const imageExtensions = ["jpg", "jpeg", "png", "gif", "webp"];

  if (!modal || !closeButton || !previewBody || !previewTitle || !downloadLink) {
    return;
  }

  if (window.feather) {
    window.feather.replace();
  }

  const openPreview = (button) => {
    const previewUrl = button.dataset.previewUrl || "";
    const downloadUrl = button.dataset.downloadUrl || previewUrl;
    const fileType = (button.dataset.filetype || "").toLowerCase();
    const fileName = button.dataset.filename || "dokumen";

    previewBody.innerHTML = "";
    previewTitle.textContent = `Lihat File: ${fileName}`;
    downloadLink.href = downloadUrl;
    downloadLink.setAttribute("download", fileName);

    if (imageExtensions.includes(fileType)) {
      const image = document.createElement("img");
      image.src = previewUrl;
      image.alt = fileName;
      image.className = "preview-media";
      previewBody.appendChild(image);
    } else if (fileType === "pdf") {
      const frame = document.createElement("iframe");
      frame.src = previewUrl;
      frame.className = "preview-frame";
      frame.title = fileName;
      previewBody.appendChild(frame);
    } else {
      const message = document.createElement("p");
      message.className = "preview-fallback";
      message.textContent = "Preview tidak tersedia untuk tipe file ini.";
      previewBody.appendChild(message);
    }

    modal.hidden = false;
  };

  const closePreview = () => {
    modal.hidden = true;
    previewBody.innerHTML = "";
    previewTitle.textContent = "Lihat File";
    downloadLink.href = "#";
  };

  document.addEventListener("click", function (event) {
    const trigger = event.target.closest(".preview-trigger");
    if (trigger) {
      openPreview(trigger);
      return;
    }

    if (event.target === modal) {
      closePreview();
    }
  });

  closeButton.addEventListener("click", closePreview);

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && !modal.hidden) {
      closePreview();
    }
  });
});
