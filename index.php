<?php
// index.php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Yoi blog</title>
  <style>
    :root {
      --purple: #6f35ff;
      --muted: #f2f4f6;
      --card: #fff;
      --danger: #ff5b68;
      --shadow: 0 10px 20px rgba(20,20,20,0.08);
    }
    body {
      font-family: "Helvetica Neue", Arial, sans-serif;
      margin: 0;
      background: #eef1f3;
      color: #222;
    }

    /* ===== TOP BAR (บรรทัดที่ 1) ===== */
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #fff;
      padding: 20px 60px;
      border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .brand {
      font-size: 28px;
      font-weight: 700;
    }
    .create-btn {
      background: var(--purple);
      color: #fff;
      padding: 10px 18px;
      border-radius: 12px;
      border: none;
      cursor: pointer;
      font-weight: 600;
    }

    /* ===== SEARCH + CATEGORY (บรรทัดที่ 2) ===== */
    .filter-bar {
      background: #fff;
      display: flex;
      gap: 16px;
      padding: 20px 60px;
      border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .search, .category {
      flex: 1;
      padding: 12px;
      border-radius: 10px;
      border: 1px solid #ddd;
      background: var(--muted);
      font-size: 15px;
    }

    /* ===== COUNTS (บรรทัดที่ 3) ===== */
    .counts {
      padding: 14px 60px;
      background: #fff;
      font-size: 15px;
      color: #555;
      border-bottom: 1px solid rgba(0,0,0,0.06);
    }

    /* ===== POSTS GRID ===== */
    .grid {
      display: flex;
      justify-content: center; /* ✅ จัดให้อยู่ตรงกลางแนวนอน */
      align-items: center;     /* ✅ จัดให้อยู่ตรงกลางแนวตั้ง (ถ้าต้องการ) */
      gap: 24px;               /* ระยะห่างระหว่างการ์ด */
      padding: 40px 0;
      flex-wrap: wrap;       /* ✅ ให้เรียงในแถวเดียว */
    }

    .card {
      width: 360px;
      background: var(--card);
      border-radius: 12px;
      padding: 26px;
      box-shadow: var(--shadow);
      position: relative;
    }
    .badge {
      position: absolute;
      right: 20px;
      top: 20px;
      background: var(--purple);
      color: #fff;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 13px;
    }
    .card h3 { margin: 0 0 14px 0; font-size: 20px; }
    .meta { color: #777; margin-bottom: 14px; font-size: 14px; }
    .excerpt { color: #555; line-height: 1.6; margin-bottom: 16px; }
    .btn { padding: 8px 14px; border-radius: 10px; border: none; cursor: pointer; }
    .btn-edit { background: #eef1f3; }
    .btn-del { background: var(--danger); color: #fff; margin-left: 6px; }

    /* ===== MODAL ===== */
    .overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,0.45);
      display: none; align-items: center; justify-content: center; z-index: 999;
    }
    .modal {
      width: 640px; background: #fff; border-radius: 12px; padding: 26px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.3); position: relative;
    }
    .modal h2 { margin: 0 0 18px 0; }
    .field { margin-bottom: 14px; }
    .field label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; }
    .field input, .field select, .field textarea {
      width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e2e2;
    }
    .field textarea { min-height: 160px; resize: vertical; }
    .modal-footer { display: flex; justify-content: flex-end; margin-top: 12px; }
    .close-x { position: absolute; right: 12px; top: 8px; font-size: 20px; cursor: pointer; }

    @media(max-width: 800px) {
      .filter-bar, .header { padding: 16px 20px; flex-direction: column; align-items: stretch; }
      .grid { padding: 20px; flex-direction: column; align-items: center; }
      .card { width: 100%; }
    }
  </style>
</head>
<body>
  <!-- Line 1 -->
  <div class="header">
    <div class="brand">Yoi blog</div>
    <button id="createBtn" class="create-btn">Create New Post</button>
  </div>

  <!-- Line 2 -->
  <div class="filter-bar">
    <input id="searchInput" class="search" placeholder="Search posts..." />
    <select id="categorySelect" class="category">
      <option value="All">All Categories</option>
      <option>Technology</option>
      <option>Lifestyle</option>
      <option>Travel</option>
      <option>Food</option>
    </select>
  </div>

  <!-- Line 3 -->
  <div class="counts" id="countsBar">Loading counts...</div>

  <!-- Posts -->
  <div class="grid" id="postsGrid"></div>

  <!-- Modal -->
  <div class="overlay" id="overlay">
    <div class="modal">
      <span class="close-x" id="closeModal">&times;</span>
      <h2 id="modalTitle">Create New Post</h2>

      <div class="field">
        <label>Title:</label>
        <input type="text" id="postTitle" />
      </div>
      <div class="field">
        <label>Category:</label>
        <select id="postCategory">
          <option>Technology</option>
          <option>Lifestyle</option>
          <option>Travel</option>
          <option>Food</option>
        </select>
      </div>
      <div class="field">
        <label>Content:</label>
        <textarea id="postContent"></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn" id="saveBtn" style="background:var(--purple);color:#fff">Save Post</button>
      </div>
    </div>
  </div>

<script>
const apiBase = 'api.php';
let editingId = null;

const postsGrid = document.getElementById('postsGrid');
const countsBar = document.getElementById('countsBar');
const searchInput = document.getElementById('searchInput');
const categorySelect = document.getElementById('categorySelect');
const overlay = document.getElementById('overlay');
const createBtn = document.getElementById('createBtn');
const closeModal = document.getElementById('closeModal');
const modalTitle = document.getElementById('modalTitle');
const postTitle = document.getElementById('postTitle');
const postCategory = document.getElementById('postCategory');
const postContent = document.getElementById('postContent');
const saveBtn = document.getElementById('saveBtn');

function showModal(isEdit=false){
  overlay.style.display = 'flex';
  modalTitle.textContent = isEdit ? 'Edit Post' : 'Create New Post';
}
function hideModal(){
  overlay.style.display = 'none';
  editingId = null;
  postTitle.value = '';
  postContent.value = '';
  postCategory.value = 'Technology';
}

createBtn.addEventListener('click', ()=>{ showModal(false); });
closeModal.addEventListener('click', hideModal);
overlay.addEventListener('click', (e)=>{ if (e.target === overlay) hideModal(); });

async function loadPosts(){
  const search = searchInput.value.trim();
  const category = categorySelect.value;
  const url = `${apiBase}?action=list&search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}`;
  const res = await fetch(url);
  const data = await res.json();
  if (!data.success) { postsGrid.innerHTML = '<p>Error loading posts</p>'; return; }
  renderCounts(data.counts, data.total);
  renderPosts(data.posts);
}

function renderCounts(counts, total){
  countsBar.innerHTML = `
    All: ${total} | Technology: ${counts.Technology} | Lifestyle: ${counts.Lifestyle} |
    Travel: ${counts.Travel} | Food: ${counts.Food}
  `;
}

function renderPosts(posts){
  if (!posts.length) {
    postsGrid.innerHTML = '<div style="padding:30px;color:#666">No posts found.</div>';
    return;
  }
  postsGrid.innerHTML = '';
  posts.forEach(p=>{
    const card = document.createElement('div');
    card.className = 'card';
    card.innerHTML = `
      <span class="badge">${escapeHtml(p.category)}</span>
      <h3>${escapeHtml(p.title)}</h3>
      <div class="meta">${new Date(p.created_at).toLocaleDateString()}</div>
      <div class="excerpt">${escapeHtml(p.content.slice(0,120))}...</div>
      <div>
        <button class="btn btn-edit" data-id="${p.id}" data-title="${p.title}" data-cat="${p.category}" data-content="${p.content}">Edit</button>
        <button class="btn btn-del" data-id="${p.id}">Delete</button>
      </div>
    `;
    postsGrid.appendChild(card);
  });

  document.querySelectorAll('.btn-edit').forEach(btn=>{
    btn.onclick = ()=>{
      editingId = btn.dataset.id;
      postTitle.value = btn.dataset.title;
      postCategory.value = btn.dataset.cat;
      postContent.value = btn.dataset.content;
      showModal(true);
    }
  });

  document.querySelectorAll('.btn-del').forEach(btn=>{
    btn.onclick = async ()=>{
      if (!confirm('Delete this post?')) return;
      await fetch(`${apiBase}?action=delete&id=${btn.dataset.id}`);
      loadPosts();
    }
  });
}

function escapeHtml(s){ return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

saveBtn.addEventListener('click', async ()=>{
  const title = postTitle.value.trim();
  const category = postCategory.value;
  const content = postContent.value.trim();
  if (!title || !content) return alert('Please fill all fields');
  const method = editingId ? 'update' : 'create';
  const body = JSON.stringify({id: editingId, title, category, content});
  await fetch(`${apiBase}?action=${method}`, {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body
  });
  hideModal();
  loadPosts();
});

searchInput.addEventListener('input', debounce(loadPosts, 400));
categorySelect.addEventListener('change', loadPosts);
function debounce(fn, t){ let to; return function(){ clearTimeout(to); to = setTimeout(()=>fn.apply(this, arguments), t); }; }

loadPosts();
</script>
</body>
</html>