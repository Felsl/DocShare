<?php
// controller/category/CategoryController.php

require_once __DIR__ . "/../../model/Category/CategoryDAO.php";

class CategoryController
{
    protected CategoryDAO $repo;

    public function __construct()
    {
        $this->repo = new CategoryDAO();
    }

    public function index()
    {
        $categories = $this->repo->All();
        require "./view/category/index.php";
    }
     public function admin()
    {
        $categories = $this->repo->all();
        require __DIR__ . "/../view/category/admin.php";
    }

    

    public function create()
    {
        require __DIR__ . "/../view/category/create.php";
    }

     public function store()
    {
         $code = trim($_POST['code']);
    $name = trim($_POST['name']);
    $desc = $_POST['description'] ?? null;

    // 🔴 kiểm tra trùng code
    if ($this->repo->findByCode($code)) {
        $error = "❌ Mã danh mục đã tồn tại.";
        $categories = $this->repo->all();
        require __DIR__ . "/../view/category/admin.php";
        return;
    }
        $c = new Category(
            null,
            $code,
            $name,
           $desc ?? null
        );

        $this->repo->create($c);
        header("Location: index.php?c=category&a=admin");
        exit;
    }
	public function show()
{
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        die("Thiếu ID danh mục");
    }

    $category = $this->repo->find($id);
    if (!$category) {
        die("Danh mục không tồn tại");
    }

    require dirname(__DIR__) . "/view/category/show.php";
}
public function edit()
{
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        die("Thiếu ID danh mục");
    }

    $category = $this->repo->find($id);
    if (!$category) {
        die("Danh mục không tồn tại");
    }

    require dirname(__DIR__) . "/view/category/update.php";
}
public function update()
{
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        die("Thiếu ID danh mục");
    }

    $cat = $this->repo->find($id);
    if (!$cat) {
        die("Category not found");
    }

    $cat->setCode($_POST['code'] ?? '')
        ->setName($_POST['name'] ?? '')
        ->setDescription($_POST['description'] ?? '');

    $this->repo->update($cat);

    header("Location: index.php?c=category&a=admin");
    exit;
}
public function delete()
{
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        die("Thiếu ID danh mục");
    }

    $this->repo->delete($id);

    header("Location: index.php?c=category&a=admin");
    exit;
}


    
}
