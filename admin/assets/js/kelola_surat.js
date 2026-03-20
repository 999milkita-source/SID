document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("previewModal");
  const closeButton = document.getElementById("closePreview");
  const previewBody = document.getElementById("previewBody");
  const previewTitle = document.getElementById("previewTitle");
  const downloadLink = document.getElementById("previewDownloadLink");

  if (!modal || !closeButton || !previewBody || !previewTitle || !downloadLink) {
    return;
  }

  if (window.feather) {
    window.feather.replace();
  }

  const imageExtensions = ["jpg", "jpeg", "png", "gif", "webp"];

  const createPreviewCard = ({ previewUrl, downloadUrl, fileType, fileName }) => {
    const card = document.createElement("button");
    const normalizedType = (fileType || "").toLowerCase();
    const isImage = imageExtensions.includes(normalizedType);
    const metaLabel = isImage ? "Gambar siap dilihat" : `Dokumen ${(normalizedType || "file").toUpperCase()}`;

    card.type = "button";
    card.className = `file-preview-card preview-trigger ${isImage ? "is-image" : "is-document"}`;
    card.dataset.previewUrl = previewUrl;
    card.dataset.downloadUrl = downloadUrl || previewUrl;
    card.dataset.filename = fileName || "dokumen";
    card.dataset.filetype = normalizedType;

    const media = document.createElement("span");
    media.className = "preview-card-media";

    if (isImage) {
      const image = document.createElement("img");
      image.src = previewUrl;
      image.alt = fileName || "Preview file";
      image.loading = "lazy";
      media.appendChild(image);
    } else {
      const extensionBadge = document.createElement("span");
      extensionBadge.className = "document-chip";
      extensionBadge.textContent = (normalizedType || "file").toUpperCase();

      const hint = document.createElement("span");
      hint.className = "document-hint";
      hint.textContent = "Klik untuk melihat dokumen";

      media.appendChild(extensionBadge);
      media.appendChild(hint);
    }

    const tag = document.createElement("span");
    tag.className = "preview-card-tag";
    tag.innerHTML = '<i data-feather="eye"></i><span>Lihat File</span>';
    media.appendChild(tag);

    const body = document.createElement("span");
    body.className = "preview-card-body";

    const name = document.createElement("span");
    name.className = "preview-card-name";
    name.textContent = fileName || "dokumen";

    const meta = document.createElement("span");
    meta.className = "preview-card-meta";
    meta.textContent = metaLabel;

    body.appendChild(name);
    body.appendChild(meta);

    card.appendChild(media);
    card.appendChild(body);

    return card;
  };

  const renderPreview = (previewUrl, fileType, fileName, downloadUrl) => {
    previewBody.innerHTML = "";
    previewTitle.textContent = fileName ? `Lihat File: ${fileName}` : "Lihat File";
    downloadLink.href = downloadUrl || previewUrl;
    downloadLink.setAttribute("download", fileName || "dokumen");

    const normalizedType = (fileType || "").toLowerCase();

    if (imageExtensions.includes(normalizedType)) {
      const image = document.createElement("img");
      image.src = previewUrl;
      image.alt = fileName || "Preview dokumen";
      image.className = "preview-media";
      previewBody.appendChild(image);
    } else if (normalizedType === "pdf") {
      const frame = document.createElement("iframe");
      frame.src = previewUrl;
      frame.className = "preview-frame";
      frame.title = fileName || "Preview PDF";
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

  document.addEventListener("click", (event) => {
    const trigger = event.target.closest(".preview-trigger");
    if (trigger) {
      renderPreview(
        trigger.dataset.previewUrl || "",
        trigger.dataset.filetype || "",
        trigger.dataset.filename || "dokumen",
        trigger.dataset.downloadUrl || ""
      );
      return;
    }

    if (event.target === modal) {
      closePreview();
    }
  });

  document.querySelectorAll(".upload-input").forEach((input) => {
    const form = input.closest("form");
    const previewArea = form?.querySelector(".upload-preview-area");
    let objectUrl = "";

    const resetUploadPreview = () => {
      if (objectUrl) {
        URL.revokeObjectURL(objectUrl);
        objectUrl = "";
      }

      if (previewArea) {
        previewArea.classList.add("is-empty");
        previewArea.innerHTML = '<span class="upload-preview-placeholder">Preview file upload akan tampil di sini</span>';
      }
    };

    input.addEventListener("change", () => {
      const selectedFile = input.files && input.files[0];

      resetUploadPreview();

      if (!selectedFile || !previewArea) {
        return;
      }

      objectUrl = URL.createObjectURL(selectedFile);

      const previewCard = createPreviewCard({
        previewUrl: objectUrl,
        downloadUrl: objectUrl,
        fileType: selectedFile.name.split(".").pop() || "",
        fileName: selectedFile.name,
      });

      previewArea.classList.remove("is-empty");
      previewArea.innerHTML = "";
      previewArea.appendChild(previewCard);

      if (window.feather) {
        window.feather.replace();
      }
    });

    window.addEventListener("beforeunload", resetUploadPreview);
  });

  closeButton.addEventListener("click", closePreview);

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && !modal.hidden) {
      closePreview();
    }
  });
});
