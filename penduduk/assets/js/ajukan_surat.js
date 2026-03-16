document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("form_surat");
  const fileInput = document.getElementById("file_upload");
  const previewContainer = document.getElementById("file_preview");

  const allowedTypes = ["pdf", "jpg", "jpeg", "png"];
  const maxSize = 5 * 1024 * 1024; // 5MB

  // Preview file
  fileInput.addEventListener("change", function () {
    previewContainer.innerHTML = ""; // reset preview
    const files = Array.from(fileInput.files);
    if (files.length === 0) return;

    files.forEach((file) => {
      const ext = file.name.split(".").pop().toLowerCase();

      if (!allowedTypes.includes(ext)) {
        alert(`File "${file.name}" tidak diperbolehkan. Hanya PDF/JPG/PNG.`);
        fileInput.value = "";
        previewContainer.innerHTML = "";
        return;
      }
      if (file.size > maxSize) {
        alert(`File "${file.name}" terlalu besar. Maksimal 5MB.`);
        fileInput.value = "";
        previewContainer.innerHTML = "";
        return;
      }

      if (["jpg", "jpeg", "png"].includes(ext)) {
        const img = document.createElement("img");
        img.src = URL.createObjectURL(file);
        img.alt = file.name;
        img.onload = () => URL.revokeObjectURL(img.src);
        previewContainer.appendChild(img);
      } else if (ext === "pdf") {
        const p = document.createElement("p");
        p.textContent = `PDF: ${file.name}`;
        previewContainer.appendChild(p);
      }
    });
  });

  // Validasi sebelum submit
  form.addEventListener("submit", function (e) {
    const files = Array.from(fileInput.files);
    for (let file of files) {
      const ext = file.name.split(".").pop().toLowerCase();
      if (!allowedTypes.includes(ext)) {
        alert(`File "${file.name}" tidak diperbolehkan.`);
        e.preventDefault();
        return;
      }
      if (file.size > maxSize) {
        alert(`File "${file.name}" terlalu besar. Maksimal 5MB.`);
        e.preventDefault();
        return;
      }
    }
  });
});
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
