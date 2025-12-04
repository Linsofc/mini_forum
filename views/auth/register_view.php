<?php 
$schools = $data['schools'];

$nama_prev = isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '';
$email_prev = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$sekolah_prev = isset($_POST['id_sekolah']) ? intval($_POST['id_sekolah']) : 0;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Forum - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #7c3aed;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: flex;
            min-height: 600px;
        }

        .register-left {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            flex: 1;
        }

        .register-right {
            padding: 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-height: 90vh;
            overflow-y: auto;
        }

        .logo {
            font-size: 3rem;
            margin-bottom: 20px;
            background: white;
            color: var(--success-color);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--success-color);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #059669);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }

        .input-group-text {
            background: var(--light-color);
            border: 2px solid #e2e8f0;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

        .form-control.with-icon {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }

        .strength-weak { background: var(--danger-color); width: 33%; }
        .strength-medium { background: var(--warning-color); width: 66%; }
        .strength-strong { background: var(--success-color); width: 100%; }

        @media (max-width: 768px) {
            .register-card {
                flex-direction: column;
                max-width: 400px;
            }
            
            .register-left {
                padding: 40px 20px;
            }
            
            .register-right {
                padding: 40px 20px;
            }
        }

        .benefit-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }

        .benefit-list li {
            margin: 15px 0;
            display: flex;
            align-items: center;
            text-align: left;
        }

        .benefit-list i {
            margin-right: 15px;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-left">
                <div class="logo">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2 class="mb-4">Bergabung dengan Mini Forum</h2>
                <p class="mb-4">Raih pengetahuan baru dan bagikan pengalaman Anda</p>
                <ul class="benefit-list">
                    <li><i class="fas fa-check-circle"></i> <span>Akses ke ribuan pertanyaan dan jawaban</span></li>
                    <li><i class="fas fa-check-circle"></i> <span>Bangun reputasi profesional</span></li>
                    <li><i class="fas fa-check-circle"></i> <span>Terhubung dengan komunitas developer</span></li>
                    <li><i class="fas fa-check-circle"></i> <span>Dapatkan bantuan untuk masalah teknis</span></li>
                </ul>
            </div>
            <div class="register-right">
                <h3 class="text-center mb-4">Daftar Akun Baru</h3>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="auth.php" id="registerForm">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control with-icon" id="nama" name="nama" required 
                                   placeholder="John Doe" value="<?php echo $nama_prev; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control with-icon" id="email" name="email" required 
                                   placeholder="nama@email.com" value="<?php echo $email_prev; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="id_sekolah" class="form-label">Sekolah</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-school"></i>
                            </span>
                            <select class="form-select with-icon" id="id_sekolah" name="id_sekolah" required>
                                <option value="">Pilih Sekolah</option>
                                <?php
                                foreach ($schools as $school) {
                                    $selected = ($sekolah_prev == $school['id_sekolah']) ? 'selected' : '';
                                    echo "<option value='{$school['id_sekolah']}' $selected>" . htmlspecialchars($school['nama_sekolah']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control with-icon" id="password" name="password" required 
                                   placeholder="Minimal 6 karakter" minlength="6">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control with-icon" id="confirm_password" name="confirm_password" required 
                                   placeholder="Ulangi password">
                        </div>
                        <div id="passwordMatch" class="form-text"></div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            Saya setuju dengan <a href="#" class="text-decoration-none">syarat dan ketentuan</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-success w-100 mb-3">
                        <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                    </button>

                    <div class="text-center">
                        <p class="mb-0">Sudah punya akun? <a href="login.php" class="text-decoration-none">Masuk di sini</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });

        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchText = document.getElementById('passwordMatch');
            
            if (confirmPassword === '') {
                matchText.textContent = '';
                matchText.className = 'form-text';
            } else if (password === confirmPassword) {
                matchText.textContent = '✓ Password cocok';
                matchText.className = 'form-text text-success';
            } else {
                matchText.textContent = '✗ Password tidak cocok';
                matchText.className = 'form-text text-danger';
            }
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Password dan konfirmasi password harus sama!');
            }
        });
    </script>
</body>
</html>