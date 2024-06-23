<?php

class CommentManager
{
    private static ?CommentManager $instance = null;
    private DB $db;

    private function __construct()
    {
        require_once(ROOT . '/utils/DB.php');
        require_once(ROOT . '/class/Comment.php');

        $this->db = DB::getInstance();
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param int $newsId
     * @return array
     * @throws Exception
     */
    public function getCommentsByNewsId(int $newsId): array
    {
        $sql = 'SELECT * FROM `comment` WHERE news_id = :news_id';
        $params = [':news_id' => $newsId];
        $rows = $this->db->select($sql, $params);

        $comments = [];
        foreach ($rows as $row) {
            $comments[] = new Comment(
                $row['id'],
                $row['body'],
                new \DateTime($row['created_at']),
                $row['news_id']
            );
        }

        return $comments;
    }

    /**
     * @param string $body
     * @param int $newsId
     * @return int
     * @throws Exception
     */
    public function addCommentForNews(string $body, int $newsId): int
    {
        $sql = "INSERT INTO `comment` (body, created_at, news_id) VALUES(:body, :created_at, :news_id)";
        $params = [
            ':body' => $body,
            ':created_at' => date('Y-m-d'),
            ':news_id' => $newsId
        ];
        $this->db->exec($sql, $params);

        return $this->db->lastInsertId();
    }

    /**
     * @param int $newsId
     * @return bool
     * @throws Exception
     */
    public function deleteNewsComments(int $newsId): bool
    {
        $sql = "DELETE FROM `comment` WHERE news_id = :newsId";
        $params = [':newsId' => $newsId];

        return $this->db->exec($sql, $params);
    }

    /**
     * @param int $id
     * @return array
     * @throws Exception
     */
    private function selectCommentById(int $id): array
    {
        $sql = "SELECT * FROM `comment` WHERE id = :id";
        $params = [':id' => $id];
        return $this->db->select($sql, $params);
    }
}
