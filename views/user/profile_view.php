<?php
// views/user/profile_view.php
// Memuat variabel $data dari controller: 
// $user_data, $schools, $total_komentar

$user_data = $data['user_data'];
$schools = $data['schools'];
$total_komentar = $data['total_komentar'];

$content = '
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user me-2"></i>Profil Saya</h2>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-center mb-4">
                <div class="card-body">
                    <img src="uploads/' . (!empty($user_data['photo']) ? $user_data['photo'] : 'default-avatar.png') . '" 
                         alt="Profile" class="rounded-circle mb-3" width="150" height="150" 
                         style="object-fit: cover; border: 4px solid var(--primary-color);">
                    <h4>' . htmlspecialchars($user_data['nama']) . '</h4>
                    <p class="text-muted">' . htmlspecialchars($user_data['email']) . '</p>
                    <span class="badge bg-primary">' . ucfirst($user_data['role']) . '</span>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-school me-1"></i>' . htmlspecialchars($user_data['nama_sekolah'] ?? 'Unknown') . '
                        </small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="stat-item">
                                <h4 class="text-primary">' . $total_komentar . '</h4>
                                <small class="text-muted">Komentar</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <h4 class="text-success">' . $user_data['poin'] . '</h4>
                                <small class="text-muted">Poin</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>Bergabung: ' . date('d M Y', strtotime($user_data['tanggal_post'] ?? 'now')) . '
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Profil</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="profileForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" required 
                                       value="' . htmlspecialchars($user_data['nama']) . '">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       value="' . htmlspecialchars($user_data['email']) . '">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="id_sekolah" class="form-label fw-bold">Sekolah</label>
                            <select class="form-select" id="id_sekolah" name="id_sekolah" required>
                                <option value="">Pilih Sekolah</option>';
                                
foreach ($schools as $school) {
    $selected = ($user_data['id_sekolah'] == $school['id_sekolah']) ? 'selected' : '';
    $content .= '<option value="' . $school['id_sekolah'] . '" ' . $selected . '>' . htmlspecialchars($school['nama_sekolah']) . '</option>';
}

$content .= '
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label fw-bold">Foto Profil</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, GIF. Maksimal: 2MB</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="removePhoto" name="remove_photo">
                                <label class="form-check-label" for="removePhoto">
                                    Hapus foto profil (gunakan avatar default)
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Keamanan</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">Kelola keamanan akun Anda</p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-lock me-2"></i>Ubah Password
                        </button>
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="fas fa-trash me-2"></i>Hapus Akun
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="change-password.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" name="new_password" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="confirm_password" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Ubah Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Hapus Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                </div>
                <p>Apakah Anda yakin ingin menghapus akun Anda? Semua data Anda akan dihapus secara permanen.</p>
                <form method="POST" action="delete-account.php">
                    <div class="mb-3">
                        <label for="confirm_delete" class="form-label">Ketik "HAPUS" untuk konfirmasi</label>
                        <input type="text" class="form-control" name="confirm_delete" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("photo").addEventListener("change", function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.querySelector(".rounded-circle");
            preview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

document.getElementById("removePhoto").addEventListener("change", function(e) {
    if (this.checked) {
        document.getElementById("photo").disabled = true;
        document.querySelector(".rounded-circle").src = "uploads/default-avatar.png";
    } else {
        document.getElementById("photo").disabled = false;
        document.querySelector(".rounded-circle").src = "uploads/' . (!empty($user_data['photo']) ? $user_data['photo'] : 'default-avatar.png') . '";
    }
});

document.getElementById("profileForm").addEventListener("submit", function(e) {
    const email = document.getElementById("email").value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!emailRegex.test(email)) {
        e.preventDefault();
        alert("Format email tidak valid!");
        return;
    }
});
</script>';

echo $content;
?>