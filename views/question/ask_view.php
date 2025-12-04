<?php
$judul_prev = $data['judul_prev'] ?? '';
$isi_prev = $data['isi_prev'] ?? '';
$tags_input_prev = $data['tags_input_prev'] ?? '';

require_once __DIR__ . '/../../config/db.php'; 
require_once __DIR__ . '/../../models/tag_model.php';

$conn = get_db_connection();
$existing_tags = get_all_tags($conn);
mysqli_close($conn);

$tag_names_array = array_map(fn($t) => htmlspecialchars($t['nama_tag']), $existing_tags);

$tag_names_json = json_encode($tag_names_array); 

$content = '
<style>
    /* Styling untuk Tag Chips */
    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 8px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        min-height: 50px; 
    }
    .tag-chip {
        display: inline-flex;
        align-items: center;
        background-color: var(--primary-color);
        color: white;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 0.875rem;
        user-select: none;
    }
    .tag-chip-remove {
        cursor: pointer;
        margin-left: 8px;
        font-weight: bold;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    .tag-chip-remove:hover {
        opacity: 1;
    }
    #tagInputContainer {
        border: none;
        outline: none;
        flex-grow: 1;
        min-width: 120px;
        padding: 0;
    }
</style>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-plus-circle me-2"></i>Ajukan Pertanyaan</h2>
    </div>

    <form method="POST" id="askForm">
        <input type="hidden" name="tags_input" id="finalTagsInput" value="' . $tags_input_prev . '">

        <div class="mb-4">
            <label for="judul" class="form-label fw-bold">Judul Pertanyaan</label>
            <input type="text" class="form-control" id="judul" name="judul" required 
                   placeholder="Apa yang ingin Anda tanyakan?" 
                   value="' . $judul_prev . '">
            <small class="text-muted">Buat judul yang jelas dan spesifik</small>
        </div>

        <div class="mb-4">
            <label for="isi" class="form-label fw-bold">Detail Pertanyaan</label>
            <textarea class="form-control" id="isi" name="isi" rows="10" required 
                      placeholder="Jelaskan pertanyaan Anda secara detail...">' . $isi_prev . '</textarea>
            <small class="text-muted">Berikan informasi yang cukup agar orang lain bisa membantu Anda</small>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold" for="tags_input">Tags</label>
            <div class="tags-container" id="tagsContainer">
                <input type="text" class="form-control" id="tagInputContainer" 
                       list="existingTagsList" placeholder="Ketik tag dan tekan Spasi/Koma/Enter">
                
                <datalist id="existingTagsList">';
                    foreach ($tag_names_array as $name) {
                        $content .= '<option value="' . $name . '">';
                    }
$content .= '
                </datalist>
            </div>
            <small class="text-muted">Tag harus unik. Tekan Spasi atau Koma untuk memisahkan tag.</small>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Posting Pertanyaan
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Batal
            </a>
        </div>
    </form>
</div>

<script>
const tagsContainer = document.getElementById("tagsContainer");
const tagInput = document.getElementById("tagInputContainer");
const finalTagsInput = document.getElementById("finalTagsInput");
const existingTags = ' . $tag_names_json . '; 

let tags = [];

function updateFinalInput() {
    finalTagsInput.value = tags.join(",");
}

function createTagChip(text) {
    const chip = document.createElement("span");
    chip.className = "tag-chip";
    chip.innerHTML = `
        ${text}
        <span class="tag-chip-remove" data-tag="${text}">&times;</span>
    `;
    
    // Tambahkan tag sebelum input
    tagsContainer.insertBefore(chip, tagInput);

    // Tambahkan ke array tags
    if (!tags.includes(text)) {
        tags.push(text);
        updateFinalInput();
    }
}

tagsContainer.addEventListener("click", function(e) {
    if (e.target.classList.contains("tag-chip-remove")) {
        const tagText = e.target.dataset.tag;
        e.target.closest(".tag-chip").remove();
        
        // Hapus dari array
        tags = tags.filter(t => t !== tagText);
        updateFinalInput();
    }
});

tagInput.addEventListener("keydown", function(e) {
    const isSeparator = e.key === "Enter" || e.key === "," || e.key === " ";
    
    if (isSeparator) {
        e.preventDefault();
        
        const tagText = tagInput.value.trim().toLowerCase();
        
        if (tagText.length > 0 && tagText.length < 50 && !tags.includes(tagText)) {
            createTagChip(tagText);
            tagInput.value = "";
        }
        
        if (tagText.length === 0) return;
    }
    
    if (e.key === "Backspace" && tagInput.value.length === 0 && tags.length > 0) {
        e.preventDefault();
        
        const lastChip = tagsContainer.querySelector(".tag-chip:last-of-type");
        if (lastChip) {
            const tagText = lastChip.querySelector(".tag-chip-remove").dataset.tag;
            
            tagInput.value = tagText; 
            
            tags = tags.filter(t => t !== tagText);
            lastChip.remove();
            updateFinalInput();
        }
    }
});

document.addEventListener("DOMContentLoaded", function() {
    const existingTagsString = finalTagsInput.value;
    if (existingTagsString) {
        const loadedTags = existingTagsString.split(",").map(t => t.trim()).filter(t => t.length > 0);
        loadedTags.forEach(tag => createTagChip(tag));
        // Reset tags array and update final input in case of error redirect
        tags = loadedTags;
        updateFinalInput(); 
    }
});


document.getElementById("judul").addEventListener("input", function() {
    const charCount = this.value.length;
    const remaining = 200 - charCount;
    
    let counter = document.getElementById("titleCounter");
    if (!counter) {
        counter = document.createElement("small");
        counter.id = "titleCounter";
        counter.className = "text-muted";
        this.parentNode.appendChild(counter);
    }
    
    counter.textContent = remaining + " karakter tersisa";
    
    if (remaining < 20) {
        counter.className = "text-danger";
    } else {
        counter.className = "text-muted";
    }
});

document.getElementById("isi").addEventListener("input", function() {
    const charCount = this.value.length;
    const remaining = 5000 - charCount;
    
    let counter = document.getElementById("contentCounter");
    if (!counter) {
        counter = document.createElement("small");
        counter.id = "contentCounter";
        counter.className = "text-muted d-block mt-1";
        this.parentNode.appendChild(counter);
    }
    
    counter.textContent = remaining + " karakter tersisa";
    
    if (remaining < 100) {
        counter.className = "text-danger d-block mt-1";
    } else {
        counter.className = "text-muted d-block mt-1";
    }
});

document.getElementById("askForm").addEventListener("submit", function(e) {
    const judul = document.getElementById("judul").value.trim();
    const isi = document.getElementById("isi").value.trim();
    
    const finalTagText = tagInput.value.trim().toLowerCase();
    if (finalTagText.length > 0 && !tags.includes(finalTagText)) {
         createTagChip(finalTagText);
         tagInput.value = "";
    }
    
    updateFinalInput();

    if (judul.length < 10) {
        e.preventDefault();
        alert("Judul pertanyaan minimal 10 karakter!");
        return;
    }
    
    if (isi.length < 20) {
        e.preventDefault();
        alert("Detail pertanyaan minimal 20 karakter!");
        return;
    }
    
    if (tags.length === 0) {
        e.preventDefault();
        alert("Masukkan minimal satu tag!");
        return;
    }
});
</script>';

echo $content;
?>