const modal = document.getElementById("modalUser");
const form = document.getElementById("formUser");

function openTambah() {
  document.getElementById("modalTitle").innerText = "Tambah Penduduk";
  form.reset();
  document.getElementById("id").value = "";

  modal.classList.add("active");
}

function openEdit(u) {
  document.getElementById("modalTitle").innerText = "Edit Penduduk";

  document.getElementById("id").value = u.id || "";
  document.getElementById("nik").value = u.nik || "";
  document.getElementById("nama_lengkap").value = u.nama_lengkap || "";
  document.getElementById("tempat_lahir").value = u.tempat_lahir || "";
  document.getElementById("tanggal_lahir").value = u.tanggal_lahir || "";
  document.getElementById("alamat").value = u.alamat || "";
  document.getElementById("rt").value = u.rt || "";
  document.getElementById("rw").value = u.rw || "";
  document.getElementById("no_hp").value = u.no_hp || "";
  document.getElementById("email").value = u.email || "";

  // FIX jenis kelamin
  if (u.jenis_kelamin === "L" || u.jenis_kelamin === "P") {
    document.getElementById("jenis_kelamin").value = u.jenis_kelamin;
  }

  // FIX role
  document.getElementById("role").value = u.role || "penduduk";

  modal.classList.add("active");
}

function closeModal() {
  modal.classList.remove("active");
}

window.onclick = (e) => {
  if (e.target === modal) closeModal();
};

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
