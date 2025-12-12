<?php
// controller/category/CategoryController.php

require_once __DIR__ . "/../../model/category/CategoryDAO.php";

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

    public function show(int $id)
    {
        $category = $this->repo->find($id);
        require __DIR__ . "/../../view/category/show.php";
    }

    public function create()
    {
        require __DIR__ . "/../../view/category/create.php";
    }

    public function store()
    {
        $c = new Category(null, $_POST['code'], $_POST['name'], $_POST['description']);
        $this->repo->create($c);

        header("Location: category.php?action=index");
        exit;
    }

    public function edit(int $id)
    {
        $category = $this->repo->find($id);
        require __DIR__ . "/../../view/category/edit.php";
    }

    public function update(int $id)
    {
        $cat = $this->repo->find($id);
        if (!$cat)
            die("Category not found");

        $cat->setCode($_POST['code'])
            ->setName($_POST['name'])
            ->setDescription($_POST['description']);

        $this->repo->update($cat);

        header("Location: category.php?action=index");
        exit;
    }

    public function delete(int $id)
    {
        $this->repo->delete($id);
        header("Location: category.php?action=index");
        exit;
    }
}
