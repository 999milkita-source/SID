<?php /** modal menggunakan $users dari kelola_user.php */ ?>
<div id="modalUser">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3 id="modalTitle">Tambah Penduduk</h3>

    <form method="post" id="formUser">
      <input type="hidden" name="id" id="id">
      <input type="hidden" name="mode" id="mode" value="penduduk">

      <div id="pendudukFields">
        <label>NIK</label>
        <input type="text" name="nik" id="nik">

        <div class="autocomplete-group">
          <label>Nama Lengkap</label>
          <input type="text" name="nama_lengkap" id="nama_lengkap" class="nama-autocomplete" autocomplete="off" required>
          <ul id="namaPendudukSuggestions" class="nama-suggestions"></ul>
        </div>

        <label>Tempat Lahir</label>
        <input type="text" name="tempat_lahir" id="tempat_lahir">

        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" id="tanggal_lahir">

        <label>Jenis Kelamin</label>
        <select name="jenis_kelamin" id="jenis_kelamin">
          <option value="L">Laki-laki</option>
          <option value="P">Perempuan</option>
        </select>

        <label>Alamat</label>
        <textarea name="alamat" id="alamat"></textarea>

        <label>RT</label>
        <input type="number" name="rt" id="rt">

        <label>RW</label>
        <input type="number" name="rw" id="rw">

        <label>No HP</label>
        <input type="text" name="no_hp" id="no_hp">
      </div>

      <div id="userFields" style="grid-column: span 2; display:none;">
        <div class="autocomplete-group" style="grid-column: span 2;">
          <label>Nama Penduduk</label>
          <input type="text" name="nama_lengkap_user" id="nama_lengkap_user" class="nama-autocomplete" autocomplete="off">
          <ul id="namaUserSuggestions" class="nama-suggestions"></ul>
        </div>

        <label>Email / Username</label>
        <input type="email" name="email" id="email">

        <label>Role</label>
        <select name="role" id="role">
          <option value="penduduk">Penduduk</option>
          <option value="rt">RT</option>
          <option value="rw">RW</option>
          <option value="admin">Admin</option>
        </select>

        <!-- PASSWORD BARU -->
        <div style="grid-column: span 2;">
          <label>Password Baru</label>
          <div style="position:relative;">
            <input type="password" name="password" id="password">
            <span class="toggle-eye" onclick="togglePassword('password', this)">👁</span>
          </div>
        </div>
      </div>

      <button type="submit" name="simpan" class="btn" style="grid-column: span 2;">
        Simpan
      </button>
    </form>
  </div>
</div>