<?php
require_once __DIR__ . "/../../model/Document/DocumentDAO.php";
require_once __DIR__ . "/../../model/Category/CategoryDAO.php";
require_once __DIR__ . "/../../model/User/UserDAO.php";
require_once __DIR__ . "/../../model/Comment/CommentDAO.php";

class DocumentController
{
    private DocumentDAO $docDAO;
    private CategoryDAO $catDAO;
    private UserDAO $userDAO;
    private CommentDAO $commentDAO;

    public function __construct()
    {
        $this->docDAO = new DocumentDAO();
        $this->catDAO = new CategoryDAO();
        $this->userDAO = new UserDAO();
        $this->commentDAO = new CommentDAO();
    }

    /* -------------------- HOME LIST -------------------- */
    public function index()
    {
        $docs = $this->docDAO->listApproved();
        require __DIR__ . "/../../view/document/index.php";
    }

    /* -------------------- DOCUMENT DETAIL -------------------- */
    public function detail($id)
    {
        $doc = $this->docDAO->find((int) $id);
        if (!$doc) {
            http_response_code(404);
            echo "Tài liệu không tồn tại.";
            exit;
        }

        $category = $this->catDAO->find($doc->getCategoryId());
        $uploader = $this->userDAO->find($doc->getUploaderId());
        $comments = $this->commentDAO->getByDocument((int) $id);

        require __DIR__ . "/../../view/document/detail.php";
    }

    /* -------------------- UPLOAD FORM -------------------- */
    public function upload()
    {
        $categories = $this->catDAO->all();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleUpload();
        }

        require __DIR__ . "/../../view/document/upload.php";
    }

    private function handleUpload()
    {
        if (!isset($_SESSION["user_id"])) {
            echo "Bạn phải đăng nhập để upload.";
            exit;
        }

        $title = $_POST["title"];
        $desc = $_POST["description"];
        $catId = (int) $_POST["category_id"];

        $file = $_FILES["file"];
        $filename = "uploads/" . time() . "_" . basename($file["name"]);
        move_uploaded_file($file["tmp_name"], $filename);

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

        $doc = new Document(
            null,
            $title,
            $slug,
            $desc,
            $filename,
            pathinfo($filename, PATHINFO_EXTENSION),
            filesize($filename),
            $catId,
            $_SESSION["user_id"], // uploader
            0,
            "pending",
            0,
            null
        );

        $this->docDAO->create($doc);

        header("Location: index.php?controller=document&action=index");
        exit;
    }

    /* -------------------- ADMIN: PENDING LIST -------------------- */
    public function pending()
    {
        $docs = $this->docDAO->listPending();
        require __DIR__ . "/../../view/admin/pending_documents.php";
    }

    /* -------------------- ADMIN: APPROVE -------------------- */
    public function approve($id)
    {
        $this->docDAO->approve((int) $id);
        header("Location: index.php?controller=document&action=pending");
    }

    /* -------------------- ADMIN: REJECT -------------------- */
    public function reject($id)
    {
        $this->docDAO->reject((int) $id);
        header("Location: index.php?controller=document&action=pending");
    }
}
