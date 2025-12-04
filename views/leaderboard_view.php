<?php
$user_leaderboard_global = $data['user_leaderboard_global'];
$user_leaderboard_school = $data['user_leaderboard_school'];
$nama_sekolah_user = $data['nama_sekolah_user'];
$is_in_school = !empty($nama_sekolah_user);

$content = '
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-trophy me-2"></i>Leaderboard</h2>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary active" onclick="showTab(\'global\')">
                <i class="fas fa-globe me-2"></i>Top Global Users
            </button>
            <button type="button" class="btn btn-outline-primary ' . (!$is_in_school ? 'disabled' : '') . '" onclick="showTab(\'school\')">
                <i class="fas fa-school me-2"></i>Sekolah Saya (' . htmlspecialchars($nama_sekolah_user ?? 'Tidak Terdaftar') . ')
            </button>
        </div>
    </div>

    <div id="globalTab" class="tab-content">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-medal me-2"></i>Top 10 Kontributor Global</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>User</th>
                                <th>Sekolah</th>
                                <th class="text-center">Total Komentar</th>
                                <th class="text-center">Poin</th>
                            </tr>
                        </thead>
                        <tbody>';

if (empty($user_leaderboard_global)) {
    $content .= '
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>Belum ada data leaderboard global</p>
                                </td>
                            </tr>';
} else {
    foreach ($user_leaderboard_global as $index => $user) {
        $rank = $index + 1;
        $rank_class = '';
        $rank_icon = '';
        
        if ($rank == 1) {
            $rank_class = 'table-warning';
            $rank_icon = '<i class="fas fa-crown text-warning me-2"></i>';
        } elseif ($rank == 2) {
            $rank_class = 'table-secondary';
            $rank_icon = '<i class="fas fa-medal text-secondary me-2"></i>';
        } elseif ($rank == 3) {
            $rank_icon = '<i class="fas fa-award text-danger me-2"></i>';
        }
        
        $points = $user['poin'] ?? 0;
        $is_current_user = $user['id_user'] == getUserId();
        
        $content .= '
                            <tr class="' . $rank_class . '">
                                <td>
                                    <strong>' . $rank_icon . $rank . '</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="user.php?id=' . $user['id_user'] . '" class="text-decoration-none">
                                            <img src="uploads/' . (!empty($user['photo']) ? $user['photo'] : 'default-avatar.png') . '" 
                                                 alt="User" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                        </a>
                                        <div>
                                            <a href="user.php?id=' . $user['id_user'] . '" class="fw-bold text-dark text-decoration-none">
                                                <strong>' . htmlspecialchars($user['nama']) . '</strong>
                                            </a>';
        
        if ($is_current_user) {
            $content .= '<br><small class="text-primary">(Anda)</small>';
        }
        
        $content .= '
                                        </div>
                                    </div>
                                </td>
                                <td>' . htmlspecialchars($user['nama_sekolah'] ?? 'Unknown') . '</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">' . $user['total_komentar'] . '</span>
                                </td>
                                <td class="text-center">
                                    <strong>' . number_format($points) . '</strong>
                                </td>
                            </tr>';
    }
}

$content .= '
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="schoolTab" class="tab-content" style="display: none;">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-school me-2"></i>Top 10 User di ' . htmlspecialchars($nama_sekolah_user ?? 'Sekolah Anda') . '</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>User</th>
                                <th class="text-center">Poin</th>
                            </tr>
                        </thead>
                        <tbody>';

if (empty($user_leaderboard_school)) {
    $content .= '
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                    <p>Belum ada user yang terdaftar atau aktif di sekolah Anda.</p>
                                </td>
                            </tr>';
} else {
    foreach ($user_leaderboard_school as $index => $user) {
        $rank = $index + 1;
        $rank_class = '';
        $rank_icon = '';
        
        if ($rank == 1) {
            $rank_class = 'table-warning';
            $rank_icon = '<i class="fas fa-crown text-warning me-2"></i>';
        } elseif ($rank == 2) {
            $rank_class = 'table-secondary';
            $rank_icon = '<i class="fas fa-medal text-secondary me-2"></i>';
        } elseif ($rank == 3) {
            $rank_icon = '<i class="fas fa-award text-danger me-2"></i>';
        }
        
        $points = $user['poin'] ?? 0;
        $is_current_user = $user['id_user'] == getUserId();
        
        $content .= '
                            <tr class="' . $rank_class . '">
                                <td>
                                    <strong>' . $rank_icon . $rank . '</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="user.php?id=' . $user['id_user'] . '" class="text-decoration-none">
                                            <img src="uploads/' . (!empty($user['photo']) ? $user['photo'] : 'default-avatar.png') . '" 
                                                 alt="User" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                        </a>
                                        <div>
                                            <a href="user.php?id=' . $user['id_user'] . '" class="fw-bold text-dark text-decoration-none">
                                                <strong>' . htmlspecialchars($user['nama']) . '</strong>
                                            </a>';
        
        if ($is_current_user) {
            $content .= '<br><small class="text-primary">(Anda)</small>';
        }
        
        $content .= '
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <strong>' . number_format($points) . '</strong>
                                </td>
                            </tr>';
    }
}

$content .= '
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Cara Perhitungan Poin</h5>
            </div>
            <div class="card-body">
                <p>Poin Reputasi dihitung berdasarkan aktivitas kontribusi Anda:</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-plus text-success me-2"></i>Post Jawaban Baru = <strong>+20 poin</strong></li>
                    <li><i class="fas fa-plus text-success me-2"></i>Post Komentar Baru = <strong>+10 poin</strong></li>
                    <li><i class="fas fa-plus text-success me-2"></i>Jawaban di-Upvote = <strong>+5 poin</strong></li>
                    <li><i class="fas fa-minus text-danger me-2"></i>Jawaban di-Downvote = <strong>-2 poin</strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    const globalTab = document.getElementById("globalTab");
    const schoolTab = document.getElementById("schoolTab");
    const buttons = document.querySelectorAll(".btn-group .btn");
    
    buttons.forEach(btn => btn.classList.remove("active"));
    
    if (tab === "global") {
        globalTab.style.display = "block";
        schoolTab.style.display = "none";
        buttons[0].classList.add("active");
    } else {
        globalTab.style.display = "none";
        schoolTab.style.display = "block";
        buttons[1].classList.add("active");
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get("tab");
    if (tab === "school" && ' . ($is_in_school ? 'true' : 'false') . ') {
        showTab("school");
    } else {
        showTab("global");
    }
});
</script>';

echo $content;
?>