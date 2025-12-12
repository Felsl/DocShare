<?php
// site/Controller/HomeController.php

class HomeController
{
    protected $docDAO = null;
    protected $catRepo = null;
    protected $newsRepo = null;

    public function __construct()
    {
        // DocumentDAO
        if (!class_exists('DocumentDAO')) {
            $f = __DIR__ . '/../../model/Document/DocumentDAO.php';
            if (file_exists($f))
                require_once $f;
        }
        if (class_exists('DocumentDAO')) {
            try {
                $this->docDAO = new DocumentDAO();
            } catch (Throwable $e) {
                error_log("HomeController: cannot instantiate DocumentDAO: " . $e->getMessage());
            }
        }

        // CategoryDAO (fixed names/paths)
        if (!class_exists('CategoryDAO')) {
            $f = __DIR__ . '/../../model/Category/CategoryDAO.php';
            if (file_exists($f))
                require_once $f;
        }
        if (class_exists('CategoryDAO')) {
            try {
                $this->catRepo = new CategoryDAO();
            } catch (Throwable $e) {
                error_log("HomeController: cannot instantiate CategoryDAO: " . $e->getMessage());
            }
        }

        // NewsDAO (fixed names/paths)
        if (!class_exists('NewsDAO')) {
            $f = __DIR__ . '/../../model/News/NewsDAO.php';
            if (file_exists($f))
                require_once $f;
        }
        if (class_exists('NewsDAO')) {
            try {
                $this->newsRepo = new NewsDAO();
            } catch (Throwable $e) {
                error_log("HomeController: cannot instantiate NewsDAO: " . $e->getMessage());
            }
        }
    }

    /**
     * index - build data for home view
     */
    public function index()
    {
        error_log("SESSION TRONG HOME = " . print_r($_SESSION, true));

        // Latest documents (approved)
        $latest = [];
        try {
            if ($this->docDAO && method_exists($this->docDAO, 'listApproved')) {
                $latest = $this->docDAO->listApproved(); // returns array of Document
            } elseif ($this->docDAO && method_exists($this->docDAO, 'listAll')) {
                $latest = $this->docDAO->listAll();
            }
        } catch (Throwable $e) {
            error_log("HomeController.index: error fetching latest docs: " . $e->getMessage());
            $latest = [];
        }

        // Featured: try DAO if has method, otherwise filter latest by isFeatured
        $featured = [];
        try {
            if ($this->docDAO && method_exists($this->docDAO, 'listFeatured')) {
                $featured = $this->docDAO->listFeatured(4);
            } elseif (!empty($latest)) {
                foreach ($latest as $d) {
                    if (is_object($d) && method_exists($d, 'isFeatured') && $d->isFeatured()) {
                        $featured[] = $d;
                    }
                    if (count($featured) >= 4)
                        break;
                }
            }
        } catch (Throwable $e) {
            error_log("HomeController.index: error fetching featured docs: " . $e->getMessage());
            $featured = [];
        }

        // Categories
        $categories = [];
        try {
            if ($this->catRepo && method_exists($this->catRepo, 'all')) {
                $categories = $this->catRepo->all();
            }
        } catch (Throwable $e) {
            error_log("HomeController.index: error fetching categories: " . $e->getMessage());
            $categories = [];
        }

        // Hot news / announcements
        $hotNews = [];
        try {
            if ($this->newsRepo && method_exists($this->newsRepo, 'listHot')) {
                $hotNews = $this->newsRepo->listHot(3);
            } elseif ($this->newsRepo && method_exists($this->newsRepo, 'listAll')) {
                $hotNews = array_slice($this->newsRepo->listAll(), 0, 3);
            }
        } catch (Throwable $e) {
            error_log("HomeController.index: error fetching news: " . $e->getMessage());
            $hotNews = [];
        }

        // Fallback sample data so view never breaks
        if (empty($latest))
            $latest = $this->sampleDocs();
        if (empty($featured))
            $featured = array_slice($latest, 0, 4);
        if (empty($categories))
            $categories = [];
        if (empty($hotNews))
            $hotNews = [];

        // Render view (view path relative to this controller)
        $view = __DIR__ . '/../view/home/index.php';
        if (!file_exists($view)) {
            $alt = __DIR__ . '/../../view/home/index.php';
            if (file_exists($alt))
                $view = $alt;
        }

        if (!file_exists($view)) {
            http_response_code(500);
            die("Home view missing. Expected at: {$view} or {$alt}");
        }

        // $latest, $featured, $categories, $hotNews are available to view
        require_once $view;
    }

    /**
     * sampleDocs - lightweight fallback sample data (objects with methods)
     */
    protected function sampleDocs(int $count = 6): array
    {
        $out = [];
        for ($i = 1; $i <= $count; $i++) {
            $out[] = new class ($i) {
                private int $id;
                private int $is_featured;
                public string $title;
                public string $description;
                public string $filename;
                public string $thumbnail;
                public int $downloads;
                public int $uploader_id;
                public string $uploader;

                public function __construct(int $i)
                {
                    $this->id = $i;
                    $this->title = "Tài liệu mẫu #{$i}";
                    $this->description = "Mô tả ngắn cho tài liệu mẫu số {$i}.";
                    $this->filename = '/uploads/placeholder.pdf';
                    $this->thumbnail = '/assets/img/placeholder.png';
                    $this->downloads = rand(0, 500);
                    $this->uploader_id = 1;
                    $this->uploader = 'Admin';
                    $this->is_featured = ($i % 3 === 0) ? 1 : 0;
                }

                // Methods expected by views
                public function getId(): int
                {
                    return $this->id;
                }
                public function getTitle(): string
                {
                    return $this->title;
                }
                public function getDescription(): string
                {
                    return $this->description;
                }
                public function getFilename(): string
                {
                    return $this->filename;
                }
                public function getThumbnail(): string
                {
                    return $this->thumbnail;
                }
                public function getDownloads(): int
                {
                    return $this->downloads;
                }
                public function getUploaderId(): int
                {
                    return $this->uploader_id;
                }
                public function getUploader(): string
                {
                    return $this->uploader;
                }
                public function isFeatured(): bool
                {
                    return (bool) $this->is_featured;
                }
            };
        }
        return $out;
    }
}
