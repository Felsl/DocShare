<?php
// site/Controller/RatingController.php
// Yêu cầu: model/Rating/RatingDAO.php, model/Rating/Rating.php phải tồn tại
require_once __DIR__ . '/../../model/Rating/RatingDAO.php';
require_once __DIR__ . '/../../model/Rating/Rating.php';

class RatingController
{
    protected RatingDAO $repo;

    public function __construct()
    {
        // DAO tự lấy $GLOBALS['pdo'] trong __construct() (theo chuẩn project)
        $this->repo = new RatingDAO();
    }

    /**
     * Thêm rating mới (trả về id mới) — không upsert
     */
    public function create(int $userId, int $documentId, int $stars): int
    {
        $r = new Rating(null, $documentId, $userId, (int) $stars, null);
        return $this->repo->create($r); // giả sử trả về lastInsertId()
    }

    /**
     * Cập nhật rating theo id
     */
    public function update(int $ratingId, int $stars): bool
    {
        $rating = $this->repo->find($ratingId);
        if (!$rating) {
            return false;
        }
        $rating->setStars((int) $stars);
        return $this->repo->update($rating);
    }

    /**
     * Xóa rating theo id
     */
    public function delete(int $ratingId): bool
    {
        return $this->repo->delete($ratingId);
    }

    /**
     * Lấy rating của một user cho một document (nếu có)
     * Trả về Rating|null
     */
    public function getUserRatingForDocument(int $userId, int $documentId)
    {
        return $this->repo->findByUserAndDocument($userId, $documentId);
    }

    /**
     * Lấy tất cả ratings cho một document
     * Trả về array of Rating
     */
    public function getByDocument(int $documentId): array
    {
        return $this->repo->getByDocument($documentId);
    }

    /**
     * Lấy tất cả ratings do user tạo (nếu cần)
     */
    public function getByUser(int $userId): array
    {
        return $this->repo->getByUser($userId);
    }

    /**
     * Tính điểm trung bình (average stars) và số lượng cho document
     * Trả về ['avg' => float, 'count' => int]
     */
    public function getAverageForDocument(int $documentId): array
    {
        // tốt nhất DAO có method để tính aggregate, fallback query nếu không có
        $agg = $this->repo->getAggregateForDocument($documentId);
        if ($agg !== null) {
            // $agg dự kiến = ['avg' => 4.5, 'count' => 12]
            return $agg;
        }

        // fallback: compute in PHP
        $rows = $this->getByDocument($documentId);
        $count = count($rows);
        if ($count === 0)
            return ['avg' => 0.0, 'count' => 0];

        $sum = 0;
        foreach ($rows as $r) {
            $sum += $r->getStars();
        }
        return ['avg' => round($sum / $count, 2), 'count' => $count];
    }

    /**
     * Upsert: nếu user đã rate document thì update, còn không thì tạo mới.
     * Trả về ['ok' => bool, 'action' => 'created'|'updated', 'rating_id' => int|null]
     */
    public function upsertRating(int $userId, int $documentId, int $stars): array
    {
        $existing = $this->getUserRatingForDocument($userId, $documentId);
        if ($existing) {
            $existing->setStars((int) $stars);
            $ok = $this->repo->update($existing);
            return ['ok' => (bool) $ok, 'action' => 'updated', 'rating_id' => $existing->getId()];
        } else {
            $id = $this->create($userId, $documentId, $stars);
            return ['ok' => $id > 0, 'action' => 'created', 'rating_id' => $id];
        }
    }

    /**
     * API helper: xử lý request POST để rate (AJAX)
     * Input: POST['document_id'], POST['stars'], (userId từ session)
     * Trả về JSON (echo)
     */
    public function handleRateRequest(): void
    {
        session_start();
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
            return;
        }

        $userId = (int) $_SESSION['user_id'];
        $documentId = isset($_POST['document_id']) ? (int) $_POST['document_id'] : 0;
        $stars = isset($_POST['stars']) ? (int) $_POST['stars'] : 0;

        if ($documentId <= 0 || $stars < 1 || $stars > 5) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Invalid parameters']);
            return;
        }

        try {
            $res = $this->upsertRating($userId, $documentId, $stars);
            $agg = $this->getAverageForDocument($documentId);
            echo json_encode(['ok' => $res['ok'], 'action' => $res['action'], 'rating_id' => $res['rating_id'], 'aggregate' => $agg]);
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => $ex->getMessage()]);
        }
    }

    /**
     * API helper: trả về ratings + aggregate cho document (AJAX GET)
     * Query: ?document_id=123
     */
    public function handleGetDocumentRatings(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $documentId = isset($_GET['document_id']) ? (int) $_GET['document_id'] : 0;
        if ($documentId <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Missing document_id']);
            return;
        }

        $ratings = $this->getByDocument($documentId);
        $out = [];
        foreach ($ratings as $r) {
            $out[] = [
                'id' => $r->getId(),
                'user_id' => $r->getUserId(),
                'stars' => $r->getStars(),
                'created_at' => $r->getCreatedAt(),
            ];
        }
        $agg = $this->getAverageForDocument($documentId);
        echo json_encode(['ok' => true, 'ratings' => $out, 'aggregate' => $agg]);
    }
}
