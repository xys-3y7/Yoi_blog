<?php
// api.php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'root', '', 'yoi_blog'); // แก้ชื่อฐานข้อมูลให้ตรงของยอย

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connect fail']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    $search = $conn->real_escape_string($_GET['search'] ?? '');
    $category = $conn->real_escape_string($_GET['category'] ?? '');
    $where = 'WHERE 1';
    if ($category && $category !== 'All') $where .= " AND category='$category'";
    if ($search) $where .= " AND title LIKE '%$search%'";
    $sql = "SELECT * FROM posts $where ORDER BY id DESC";
    $result = $conn->query($sql);
    $posts = [];
    while ($row = $result->fetch_assoc()) $posts[] = $row;

    // นับจำนวนแต่ละ category
    $countData = ['Technology'=>0,'Lifestyle'=>0,'Travel'=>0,'Food'=>0];
    $res2 = $conn->query("SELECT category, COUNT(*) c FROM posts GROUP BY category");
    while ($r = $res2->fetch_assoc()) $countData[$r['category']] = (int)$r['c'];

    echo json_encode(['success'=>true,'posts'=>$posts,'counts'=>$countData,'total'=>count($posts)]);
    exit;
}

if ($action === 'create' || $action === 'update') {
    $data = json_decode(file_get_contents('php://input'), true);
    $title = $conn->real_escape_string($data['title']);
    $category = $conn->real_escape_string($data['category']);
    $content = $conn->real_escape_string($data['content']);
    if ($action === 'create') {
        $conn->query("INSERT INTO posts(title, category, content, created_at) VALUES('$title','$category','$content',NOW())");
    } else {
        $id = (int)$data['id'];
        $conn->query("UPDATE posts SET title='$title', category='$category', content='$content' WHERE id=$id");
    }
    echo json_encode(['success'=>true]);
    exit;
}

if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    $conn->query("DELETE FROM posts WHERE id=$id");
    echo json_encode(['success'=>true]);
    exit;
}

echo json_encode(['success'=>false,'message'=>'Invalid action']);
