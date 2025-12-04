<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Forum - Platform Q&A</title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            margin: 0 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 15px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .user-dropdown:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }

        .sidebar {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .sidebar-sticky-wrapper {
            position: sticky;
            top: 100px;
            z-index: 1020;
        }

        .main-content {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .question-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .question-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .vote-section {
            text-align: center;
            min-width: 60px;
        }

        .vote-count {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .vote-btn {
            background: none;
            border: none;
            color: #6b7280;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            padding: 5px;
        }

        .vote-btn:hover {
            color: var(--primary-color);
            transform: scale(1.2);
        }

        .vote-btn.voted-up {
            color: var(--success-color);
        }

        .vote-btn.voted-down {
            color: var(--danger-color);
        }

        .question-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .question-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .tag {
            background: var(--light-color);
            color: var(--primary-color);
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .tag:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            border-radius: 25px;
            padding-left: 45px;
            border: 2px solid #e2e8f0;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }

        .filter-tabs {
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 20px;
        }

        .filter-tabs .nav-link {
            border: none;
            color: #6b7280;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 10px 10px 0 0;
            transition: all 0.3s ease;
        }

        .filter-tabs .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        .leaderboard-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 10px;
            background: var(--light-color);
            transition: all 0.3s ease;
        }

        .leaderboard-item:hover {
            background: #e2e8f0;
        }

        .rank-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 15px;
        }

        .rank-1 {
            background: gold;
            color: white;
        }

        .rank-2 {
            background: silver;
            color: white;
        }

        .rank-3 {
            background: #cd7f32;
            color: white;
        }

        .rank-default {
            background: #6b7280;
            color: white;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 15px;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
        }

        .pagination {
            justify-content: center;
            margin-top: 30px;
        }

        .pagination .page-link {
            border: none;
            color: white;
            margin: 0 5px;
            border-radius: 10px;
            background: var(--primary-color);
            padding: 8px 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .pagination .page-item.active .page-link {
            background: var(--secondary-color);
            color: white;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                top: 0;
            }

            .question-card {
                padding: 15px;
            }

            .vote-section {
                min-width: 50px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-comments me-2"></i>Mini Forum
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Beranda
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="ask.php">
                                <i class="fas fa-plus-circle me-1"></i>Tanya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="leaderboard.php">
                                <i class="fas fa-trophy me-1"></i>Leaderboard
                            </a>
                        </li>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin/">
                                    <i class="fas fa-cog me-1"></i>Admin
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <div class="search-box me-3">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control" placeholder="Cari pertanyaan..." id="searchInput">
                </div>

                <?php if (isLoggedIn()): ?>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle user-dropdown" type="button" data-bs-toggle="dropdown">
                            <img src="uploads/<?php echo getUserPhoto(); ?>" alt="Avatar" class="user-avatar">
                            <span><?php echo getUserName(); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user me-2"></i>Profil Saya
                                </a></li>
                            <li><a class="dropdown-item" href="my-questions.php">
                                    <i class="fas fa-question-circle me-2"></i>Pertanyaan Saya
                                </a></li>
                            <li><a class="dropdown-item" href="my-answers.php">
                                    <i class="fas fa-comment me-2"></i>Jawaban Saya
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="auth.php?action=logout">
                                    <i class="fas fa-sign-out-alt me-2"></i>Keluar
                                </a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-light">
                        <i class="fas fa-sign-in-alt me-2"></i>Masuk
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-3">
                <div class="sidebar-sticky-wrapper">
                    <?php if (isLoggedIn()): ?>
                        <div class="sidebar">
                            <h5 class="mb-3">Quick Actions</h5>
                            <a href="ask.php" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-plus-circle me-2"></i>Ajukan Pertanyaan
                            </a>
                            <div class="stats-card">
                                <div class="stats-number"><?php echo getUserStats(); ?></div>
                                <div>Poin Reputasi</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="sidebar">
                        <h5 class="mb-3">Popular Tags</h5>
                        <?php
                        $conn = get_db_connection();
                        require_once __DIR__ . '/../models/tag_model.php';
                        $popular_tags = get_popular_tags($conn, 10);
                        mysqli_close($conn);

                        foreach ($popular_tags as $t) {
                            echo "<a href='index.php?tag={$t['id_tag']}' class='tag d-inline-block me-2 mb-2'>{$t['nama_tag']} ({$t['total_pertanyaan']})</a>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-times-circle me-2"></i>
                        <?php
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php echo $content; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    window.location.href = 'search.php?q=' + encodeURIComponent(query);
                }
            }
        });

        function updateAllTimestamps() {
            const now = new Date();
            
            document.querySelectorAll('time.time-ago').forEach(el => {
                let dbTimestamp = el.getAttribute('datetime');

                if (dbTimestamp.includes(' ')) {
                    dbTimestamp = dbTimestamp.replace(' ', 'T');
                }
                
                const time = new Date(dbTimestamp);
                const diff = (now.getTime() - time.getTime()) / 1000;
                
                if (diff < 0 || isNaN(diff)) {
                    el.textContent = 'baru saja';
                    return; 
                }

                if (diff < 60) {
                    el.textContent = 'baru saja';
                } else if (diff < 3600) {
                    el.textContent = Math.floor(diff / 60) + ' menit yang lalu';
                } else if (diff < 86400) {
                    el.textContent = Math.floor(diff / 3600) + ' jam yang lalu';
                } else if (diff < 604800) {
                    el.textContent = Math.floor(diff / 86400) + ' hari yang lalu';
                } else {
                    el.classList.remove('time-ago');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', updateAllTimestamps);
        setInterval(updateAllTimestamps, 30000);

        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('mobile-page-toggle');

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const container = document.getElementById('mobile-page-list-container');
                    if (!container) return;

                    if (container.style.display === 'block') {
                        container.style.display = 'none';
                        toggleBtn.textContent = 'Halaman ' + this.dataset.currentPage + ' / ' + this.dataset.totalPages + ' (Tampilkan Semua)';
                    } else {
                        const totalPages = parseInt(this.dataset.totalPages, 10);
                        const baseUrl = this.dataset.baseUrl;
                        const currentPage = parseInt(this.dataset.currentPage, 10);

                        let pageHtml = '<div class="d-flex flex-wrap gap-2 justify-content-center">';

                        for (let i = 1; i <= totalPages; i++) {
                            if (i === currentPage) {
                                pageHtml += `<a href="#" class="btn btn-primary disabled" aria-current="page">${i}</a>`;
                            } else {
                                pageHtml += `<a href="${baseUrl}&page=${i}" class="btn btn-outline-primary">${i}</a>`;
                            }
                        }

                        pageHtml += '</div>';

                        container.innerHTML = pageHtml;
                        container.style.display = 'block';
                        toggleBtn.textContent = 'Sembunyikan Halaman';
                    }
                });
            }
        });

        document.addEventListener('click', async function(e) {

            if (e.target.classList.contains('btn-load-replies')) {
                e.preventDefault();

                const link = e.target;
                const rootId = link.dataset.rootId;
                const limit = link.dataset.limit;
                const offset = link.dataset.offset;
                const total = link.dataset.total;
                const formId = link.dataset.formId;
                const targetContainerId = link.dataset.targetContainer;

                const container = document.querySelector(targetContainerId);
                if (!container) return;

                link.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';

                const formData = new FormData();
                formData.append('id_root', rootId);
                formData.append('limit', limit);
                formData.append('offset', offset);
                formData.append('form_id', formId);

                try {
                    const response = await fetch('load_replies.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        container.insertAdjacentHTML('beforeend', data.html);

                        const newOffset = data.new_offset;
                        link.dataset.offset = newOffset;
                        const remaining = total - newOffset;

                        if (remaining > 0) {
                            link.innerHTML = `<i class="fas fa-comment-dots"></i> Lihat (${remaining}) balasan lagi`;
                        } else {
                            link.style.display = 'none';
                        }

                        const hideLinkQuery = `[data-hide-target="${targetContainerId}"]`;
                        let hideLink = document.querySelector(hideLinkQuery);

                        if (!hideLink) {
                            hideLink = document.createElement('a');
                            hideLink.href = '#';
                            hideLink.className = 'btn-hide-replies small fw-bold text-danger';
                            hideLink.dataset.hideTarget = targetContainerId;
                            hideLink.dataset.loadLinkId = `#${link.id}`;
                            hideLink.dataset.total = total;
                            hideLink.style.marginLeft = '40px';
                            hideLink.style.marginBottom = '10px';

                            container.after(hideLink);
                        }

                        hideLink.innerHTML = '<i class="fas fa-eye-slash"></i> Sembunyikan balasan';
                        hideLink.style.display = 'inline-block';

                    } else {
                        link.innerHTML = 'Gagal memuat balasan';
                    }
                } catch (error) {
                    link.innerHTML = 'Terjadi kesalahan';
                }
            }

            if (e.target.classList.contains('btn-hide-replies')) {
                e.preventDefault();

                const hideLink = e.target;
                const targetContainerId = hideLink.dataset.hideTarget;
                const loadLinkId = hideLink.dataset.loadLinkId;
                const total = hideLink.dataset.total;

                const container = document.querySelector(targetContainerId);
                const loadLink = document.querySelector(loadLinkId);

                if (container) {
                    container.innerHTML = '';
                }

                if (loadLink) {
                    loadLink.style.display = 'inline-block';
                    loadLink.dataset.offset = '0';
                    loadLink.innerHTML = `<i class="fas fa-comment-dots"></i> Lihat (${total}) balasan`;
                }

                hideLink.style.display = 'none';
            }

            if (e.target.classList.contains('btn-reply')) {
                e.preventDefault();

                const commentId = e.target.dataset.commentId;
                const userName = e.target.dataset.commentUser;
                const formId = e.target.dataset.formId;

                const form = document.getElementById(formId);
                if (!form) return;

                const statusEl = form.querySelector('.reply-status');
                const parentInputEl = form.querySelector('.parent-input-container');
                const labelEl = form.querySelector('label.form-label');
                const textarea = form.querySelector('textarea[name="comment"]');
                
                if (!statusEl || !parentInputEl || !textarea) return;

                const originalLabelText = labelEl ? labelEl.innerText : 'Tambah Komentar';

                statusEl.innerHTML = `
            <div class="alert alert-info py-2 px-3 small">
                Membalas <strong>@${userName}</strong>
                <button type="button" class="btn-close btn-cancel-reply" 
                        data-form-id="${formId}" 
                        data-label-text="${originalLabelText}" 
                        aria-label="Close" 
                        style="font-size: 0.75rem; float: right;">
                </button>
            </div>`;

                parentInputEl.innerHTML = `<input type="hidden" name="id_komentar_parent" value="${commentId}">`;

                if (labelEl) {
                    labelEl.innerText = `Balas ke @${userName}`;
                }

                textarea.focus();
            }

            if (e.target.classList.contains('btn-cancel-reply')) {
                e.preventDefault();

                const formId = e.target.dataset.formId;
                const originalLabelText = e.target.dataset.labelText;

                const form = document.getElementById(formId);
                if (!form) return;

                const statusEl = form.querySelector('.reply-status');
                const parentInputEl = form.querySelector('.parent-input-container');
                const labelEl = form.querySelector('label.form-label');

                if (statusEl) statusEl.innerHTML = '';
                if (parentInputEl) parentInputEl.innerHTML = '';

                if (labelEl) {
                    labelEl.innerText = originalLabelText;
                }
            }
        });
        
        function vote(type, id, isQuestion = true) {
            if (!<?php echo isLoggedIn() ? 'true' : 'false'; ?>) {
                alert('Silakan login terlebih dahulu!');
                return;
            }

            fetch('vote.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `type=${type}&id=${id}&is_question=${isQuestion ? '1' : '0'}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Terjadi kesalahan!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan!');
                });
        }
    </script>
</body>

</html>