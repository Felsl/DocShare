<?php
// controller/comment/CommentController.php

require_once __DIR__ . "/../../model/comment/CommentDAO.php";

class CommentController
{
    protected CommentDAO $repo;

    public function __construct()
    {
        $this->repo = new CommentDAO();
    }

    public function store()
    {
        $c = new Comment(
            null,
            $_POST['document_id'],
            $_SESSION['user_id'],
            $_POST['content']
        );

        $this->repo->create($c);
        header("Location: document.php?action=show&id=" . $_POST['document_id']);
        exit;
    }

    public function delete(int $id)
    {
        $this->repo->delete($id);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
