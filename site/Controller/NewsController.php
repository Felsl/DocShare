<?php

require_once __DIR__ . "/../../model/news/NewsDAO.php";

class NewsController
{
    protected NewsDAO $repo;

    public function __construct()
    {
        $this->repo = new NewsDAO();
    }

    public function index()
    {
        $news = $this->repo->listAll();
        require __DIR__ . "/../view/news/index.php";
    }

    public function show(int $id)
    {
        $n = $this->repo->find($id);
        require __DIR__ . "/../../view/news/show.php";
    }

    public function create()
    {
        require __DIR__ . "/../../view/news/create.php";
    }

    public function store()
    {
        $n = new News(
            null,
            $_POST['title'],
            $_POST['img'],
            $_POST['short_content'],
            $_POST['content'],
            $_POST['is_hot']
        );

        $this->repo->create($n);
        header("Location: news.php?action=index");
        exit;
    }
}
