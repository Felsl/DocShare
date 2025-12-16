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
        if (isset($_GET['category_id'])) {
            $catId = (int) $_GET['category_id'];

            $categoryDAO = new CategoryDAO();
            $category = $categoryDAO->find($catId);
            if (!$category) {
                die("Danh mục không tồn tại");
            }

            $documents = $this->docDAO->listByCategory($catId);
        }
        // SEARCH
        elseif (isset($_GET['q']) && trim($_GET['q']) !== '') {
            $documents = $this->docDAO->search(trim($_GET['q']));
        }
        // MẶC ĐỊNH
        else {
            $documents = $this->docDAO->listApproved();
        }

        require __DIR__ . "/../view/document/index.php";
    }

    /* -------------------- DOCUMENT DETAIL -------------------- */
    public function detail()
    {
        // debug: log router gọi hàm với id
        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo "Thiếu hoặc sai ID tài liệu.";
            exit;
        }


        // normalize id
        $id = (int) $id;
        if ($id <= 0) {
            http_response_code(400);
            echo "ID không hợp lệ.";
            error_log("DEBUG: invalid id passed to detail(): " . var_export($id, true));
            return;
        }

        // fetch document
        $doc = null;
        try {
            $doc = $this->docDAO->find($id);
        } catch (Throwable $e) {
            error_log("ERROR: docDAO->find threw: " . $e->getMessage());
            http_response_code(500);
            echo "Lỗi server khi tìm tài liệu.";
            return;
        }

        // log result
        error_log("DEBUG: docDAO->find returned: " . var_export($doc, true));

        if (!$doc) {
            http_response_code(404);
            echo "Tài liệu không tồn tại.";
            return; // dùng return thay exit để test dễ hơn
        }

        // load related data safely (with null-checks)
        $category = null;
        $uploader = null;
        $comments = [];

        if ($this->catDAO && method_exists($this->catDAO, 'find')) {
            try {
                $category = $this->catDAO->find($doc->getCategoryId());
            } catch (Throwable $e) {
                error_log("ERROR: catDAO->find: " . $e->getMessage());
            }
        }

        if ($this->userDAO && method_exists($this->userDAO, 'find')) {
            try {
                $uploader = $this->userDAO->find($doc->getUploaderId());
            } catch (Throwable $e) {
                error_log("ERROR: userDAO->find: " . $e->getMessage());
            }
        }

        if ($this->commentDAO && method_exists($this->commentDAO, 'getByDocument')) {
            try {
                $comments = $this->commentDAO->getByDocument($id);
            } catch (Throwable $e) {
                error_log("ERROR: commentDAO->getByDocument: " . $e->getMessage());
            }
        }

        // determine view path robustly
        $candidates = [
            __DIR__ . "/../view/document/detail.php",
            dirname(__DIR__, 2) . "/view/document/detail.php",
            (isset($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . '/site/view/document/detail.php' : null),
        ];

        $view = null;
        foreach ($candidates as $p) {
            if (!$p)
                continue;
            if (file_exists($p)) {
                $view = $p;
                break;
            }
        }

        if (!$view) {
            http_response_code(500);
            $msg = "View detail.php not found. Tried:\n" . implode("\n", array_filter($candidates));
            error_log($msg);
            echo nl2br(htmlspecialchars($msg));
            return;
        }

        // finally render view
        require $view;
    }


    /* -------------------- UPLOAD FORM -------------------- */
    public function upload()
    {
        $categories = $this->catDAO->all();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleUpload();
        }

        require __DIR__ . "/../view/document/upload.php";
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

        $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
$slug = $baseSlug . '-' . time();


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

        header("Location: index.php?c=document&a=index");
        exit;
    }

    /* -------------------- ADMIN: PENDING LIST -------------------- */
    public function pending()
    {
        $docs = $this->docDAO->listPending();
        require __DIR__ . "/../../view/admin/pending_documents.php";
    }

    /* -------------------- ADMIN: APPROVE -------------------- */
    public function approve()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "Thiếu ID";
            return;
        }

        $this->docDAO->approve((int) $id);
        header("Location: ../site/index.php?c=admin&a=dashboard");

        exit;
    }

    /* -------------------- ADMIN: REJECT -------------------- */
    public function reject()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "Thiếu ID";
            return;
        }

        $this->docDAO->reject((int) $id);
        header("Location: ../site/index.php?c=admin&a=dashboard");

        exit;
    }
    private function docValue($doc, $prop, $default = '')
    {
        $getter = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $prop)));

        // Nếu là object Document
        if (is_object($doc)) {

            // Có getter → dùng getter
            if (method_exists($doc, $getter)) {
                return $doc->$getter();
            }

            // Property public
            if (property_exists($doc, $prop)) {
                try {
                    return $doc->$prop;
                } catch (Throwable $e) {
                }
            }

            // Magic __get
            if (method_exists($doc, '__get')) {
                try {
                    return $doc->$prop;
                } catch (Throwable $e) {
                }
            }

            // Method cùng tên
            if (method_exists($doc, $prop)) {
                return $doc->$prop();
            }

            return $default;
        }

        // Nếu là array
        if (is_array($doc)) {
            return $doc[$prop] ?? $default;
        }

        return $default;
    }

    public function search()
{
    $keyword = trim($_GET['q'] ?? '');

    if ($keyword === '') {
        $documents = [];
    } else {
        $documents = $this->docDAO->search($keyword);
    }

    require __DIR__ . "/../view/document/index.php";
}

}
