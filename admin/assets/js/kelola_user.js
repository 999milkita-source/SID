const modal = document.getElementById("modalUser");
const form = document.getElementById("formUser");

function setModePenduduk() {
  document.getElementById("mode").value = "penduduk";
  document.getElementById("userFields").style.display = "none";
  const pendudukBlock = document.getElementById("pendudukFields");
  if (pendudukBlock) pendudukBlock.style.display = "grid";
  // aktifkan field penduduk
  ["nik","nama_lengkap","tempat_lahir","tanggal_lahir","jenis_kelamin","alamat","rt","rw","no_hp"].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.removeAttribute("readonly");
      el.removeAttribute("disabled");
    }
  });
}

function setModeUser() {
  document.getElementById("mode").value = "user";
  document.getElementById("userFields").style.display = "block";
  const pendudukBlock = document.getElementById("pendudukFields");
  if (pendudukBlock) pendudukBlock.style.display = "none";
  // field penduduk tidak diubah saat mode user
  ["nik","nama_lengkap","tempat_lahir","tanggal_lahir","jenis_kelamin","alamat","rt","rw","no_hp"].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.setAttribute("readonly","readonly");
    }
  });
}

function openTambahPenduduk() {
  document.getElementById("modalTitle").innerText = "Tambah Penduduk";
  form.reset();
  document.getElementById("id").value = "";
  setModePenduduk();
  modal.classList.add("active");
}

function openEditPenduduk(u) {
  document.getElementById("modalTitle").innerText = "Edit Penduduk";
  form.reset();
  for (let k in u) {
    if (Object.prototype.hasOwnProperty.call(u, k) && document.getElementById(k)) {
      document.getElementById(k).value = u[k];
    }
  }
  setModePenduduk();
  modal.classList.add("active");
}

function openTambahUser() {
  document.getElementById("modalTitle").innerText = "Tambah User";
  form.reset();
  document.getElementById("id").value = "";
  setModeUser();
  modal.classList.add("active");
}

function openEditUser(u) {
  document.getElementById("modalTitle").innerText = "Edit User";
  form.reset();
  for (let k in u) {
    if (Object.prototype.hasOwnProperty.call(u, k) && document.getElementById(k)) {
      document.getElementById(k).value = u[k];
    }
  }
  setModeUser();
  modal.classList.add("active");
}

function closeModal() {
  modal.classList.remove("active");
}

window.onclick = (e) => {
  if (e.target === modal) closeModal();
};

/* TOGGLE PASSWORD */
function togglePassword(id, el) {
  const input = document.getElementById(id);
  if (input.type === "password") {
    input.type = "text";
    el.innerHTML = "👁‍🗨";
  } else {
    input.type = "password";
    el.innerHTML = "👁";
  }
}
