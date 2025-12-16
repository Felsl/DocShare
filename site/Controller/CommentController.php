<?php
// controller/comment/CommentController.php

require_once __DIR__ . "/../../model/Comment/CommentDAO.php";

class CommentController
{
    protected CommentDAO $repo;

    public function __construct()
    {
        $this->repo = new CommentDAO();
    }

  public function store()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?c=user&a=login");
        exit;
    }

    if (empty($_POST['content']) || empty($_POST['document_id'])) {
        header("Location: index.php");
        exit;
    }

    $c = new Comment(
        null,
        (int)$_POST['document_id'],
        (int)$_SESSION['user_id'],
        trim($_POST['content'])
    );

    $this->repo->create($c);

    header("Location: index.php?c=document&a=detail&id=" . (int)$_POST['document_id']);
    exit;
}


    public function delete(int $id)
    {
        $this->repo->delete($id);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
