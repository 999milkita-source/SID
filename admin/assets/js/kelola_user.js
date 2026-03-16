const modal = document.getElementById("modalUser");
const form = document.getElementById("formUser");

function openTambah() {
  document.getElementById("modalTitle").innerText = "Tambah Penduduk";
  form.reset();
  document.getElementById("id").value = "";

  const wrap = document.getElementById("passwordLamaWrap");
  if (wrap) wrap.style.display = "none";

  modal.classList.add("active");
}

function openEdit(u) {
  document.getElementById("modalTitle").innerText = "Edit Penduduk";

  for (let k in u) {
    if (document.getElementById(k)) {
      document.getElementById(k).value = u[k];
    }
  }

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
