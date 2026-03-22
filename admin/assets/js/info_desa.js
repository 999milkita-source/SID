const tabMap = {
  'beranda': 'Beranda',
  'profil': 'Profil',
  'tentang': 'Tentang',
  'kontak': 'Kontak'
};

// ================= TAB =================
document.querySelectorAll(".tab-btn").forEach((btn) => {
  btn.addEventListener("click", function () {
    const tab = this.dataset.tab;

    // simpan ke URL
    const url = new URL(window.location);
    url.searchParams.set("tab", tab);
    window.history.pushState({}, "", url);

    document
      .querySelectorAll(".tab-btn")
      .forEach((b) => b.classList.remove("active"));
    document
      .querySelectorAll(".tab-panel")
      .forEach((p) => p.classList.remove("active"));

    this.classList.add("active");
    document.getElementById("tab-" + tab).classList.add("active");
  });
});

// 🔥 LOAD TAB DARI URL
window.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const tab = params.get("tab");

  if (tab) {
    document
      .querySelectorAll(".tab-btn")
      .forEach((b) => b.classList.remove("active"));
    document
      .querySelectorAll(".tab-panel")
      .forEach((p) => p.classList.remove("active"));

    const btn = document.querySelector(`.tab-btn[data-tab="${tab}"]`);
    const panel = document.getElementById("tab-" + tab);

    if (btn && panel) {
      btn.classList.add("active");
      panel.classList.add("active");
    }
  }
});

// ================= MODAL =================
function openModal(type = "text", tab = "beranda") {
  const modal = document.getElementById("modal");

  document.getElementById("text-form").reset();
  document.getElementById("galeri-form").reset();

  if (type === "text") {
    document.getElementById("text-form").style.display = "block";
    document.getElementById("galeri-form").style.display = "none";
    document.getElementById("text-type").value = tab;
    document.getElementById("modal-title").textContent = `Tambah Konten - ${tabMap[tab] || tab.charAt(0).toUpperCase() + tab.slice(1)}`;
    document.getElementById("tab-label").textContent = tabMap[tab] || tab;
  } else {
    document.getElementById("text-form").style.display = "none";
    document.getElementById("galeri-form").style.display = "block";
    document.getElementById("modal-title").textContent = "Tambah Galeri";
  }

  modal.style.display = "flex";
}

function closeModal() {
  document.getElementById("modal").style.display = "none";
}

// ================= EDIT =================
function editRow(data, type) {
  openModal(type, data.tipe || "beranda");

  setTimeout(() => {
    if (type === "text") {
      document.getElementById("text-id").value = data.id;
      document.getElementById("text-type").value = data.tipe;
      document.getElementById("text-judul").value = data.judul;
      document.getElementById("text-urutan").value = data.urutan || 0;
      document.getElementById("text-konten").value = data.konten || "";
      const tabLabel = tabMap[data.tipe] || data.tipe;
      document.getElementById("modal-title").textContent = `Edit Konten - ${tabLabel}`;
      document.getElementById("tab-label").textContent = tabLabel;
    } else {
      document.getElementById("galeri-id").value = data.id;
      document.getElementById("galeri-judul").value = data.judul;
      document.getElementById("modal-title").textContent = "Edit Foto";
    }
  }, 100);
}

// ================= DELETE =================
function deleteRow(id, type) {
  if (confirm("Yakin hapus data?")) {
    const tab =
      new URLSearchParams(window.location.search).get("tab") || "beranda";

    window.location.href =
      (type === "text"
        ? "hapus_info.php?id=" + id
        : "hapus_galeri.php?id=" + id) +
      "&tab=" +
      tab;
  }
}

// ================= TOAST =================
const params = new URLSearchParams(window.location.search);

if (params.get("status")) {
  const toast = document.createElement("div");
  toast.className = "toast show";

  if (params.get("status").includes("sukses")) {
    toast.textContent = "✅ Berhasil!";
  } else {
    toast.classList.add("error");
    toast.textContent = "❌ Terjadi kesalahan!";
  }

  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}
