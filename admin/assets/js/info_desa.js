document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("modal");
  const form = document.querySelector("#modal form");

  window.openAdd = function () {
    form.reset();
    document.getElementById("id").value = "";
    if (typeof window.syncBackgroundForm === "function") {
      window.syncBackgroundForm();
    }
    modal.style.display = "flex";
  };

  window.openEdit = function (data) {
    document.getElementById("id").value = data.id;
    document.getElementById("judul").value = data.judul;
    document.getElementById("tipe").value = data.tipe;
    document.getElementById("urutan").value = data.urutan;
    document.getElementById("konten").value = data.konten;
    if (typeof window.syncBackgroundForm === "function") {
      window.syncBackgroundForm();
    }
    modal.style.display = "flex";
  };

  window.closeModal = function () {
    modal.style.display = "none";
  };

  // Custom Delete Confirm
  document.querySelectorAll(".btn-delete").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      showConfirm("Yakin ingin menghapus data ini?", () => {
        window.location.href = this.href;
      });
    });
  });

  function showConfirm(message, callback) {
    const overlay = document.createElement("div");
    overlay.className = "custom-alert";

    overlay.innerHTML = `
      <div class="alert-box">
        <p>${message}</p>
        <div class="alert-action">
          <button class="btn-confirm">Ya</button>
          <button class="btn-cancel">Batal</button>
        </div>
      </div>
    `;

    document.body.appendChild(overlay);

    overlay.querySelector(".btn-confirm").onclick = () => {
      callback();
      overlay.remove();
    };

    overlay.querySelector(".btn-cancel").onclick = () => {
      overlay.remove();
    };
  }
});
// ===============================
// SUCCESS ALERT
// ===============================
const params = new URLSearchParams(window.location.search);
const status = params.get("status");

if (status === "sukses_simpan") {
  showAlert("Data berhasil disimpan!");
}

if (status === "sukses_hapus") {
  showAlert("Data berhasil dihapus!");
}

function showAlert(message) {
  const overlay = document.createElement("div");
  overlay.className = "custom-alert";

  overlay.innerHTML = `
    <div class="alert-box">
      <p>${message}</p>
      <div class="alert-action">
        <button class="btn-confirm">OK</button>
      </div>
    </div>
  `;

  document.body.appendChild(overlay);

  overlay.querySelector(".btn-confirm").onclick = () => {
    overlay.remove();
    window.history.replaceState({}, document.title, "info_desa.php");
  };
}
