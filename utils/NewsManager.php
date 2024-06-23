<?php

class NewsManager
{
    private static ?NewsManager $instance = null;
    private DB $db;

    private function __construct()
    {
        require_once ROOT . '/utils/DB.php';
        require_once ROOT . '/class/News.php';
        require_once ROOT . '/utils/CommentManager.php';

        $this->db = DB::getInstance();
    }

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function listNews(): array
    {
        $rows = $this->db->select('SELECT * FROM `news`');
        $newsList = [];

        foreach($rows as $row) {
            $news = new News(
                $row['id'],
                $row['title'],
                $row['body'],
                new DateTime($row['created_at'])
            );
            $newsList[] = $news;

            $comments = CommentManager::getInstance()->getCommentsByNewsId($news->getId());
            $news->setComments($comments);
        }

        return $newsList;
    }

    /**
     * @param string $title
     * @param string $body
     * @return int
     * @throws Exception
     */
    public function addNews(string $title, string $body): int
    {
        $sql = "INSERT INTO `news` (`title`, `body`, `created_at`) VALUES(?, ?, ?)";
        $params = [$title, $body, date('Y-m-d H:i:s')];
        $this->db->exec($sql, $params);

        return $this->db->lastInsertId();
    }

    /**
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function deleteNews(int $id): bool
    {
        $rows = $this->selectNewsById($id);

        if (count($rows) === 0) {
            throw new Exception('News not found', 404);
        }

        try {
            /**
             * TODO
             * This transaction could be removed if we add a FK on news_id with ON DELETE CASCADE
             *
             * ALTER TABLE comment
             * ADD CONSTRAINT fk_news_id
             * FOREIGN KEY (news_id) REFERENCES news(id)
             * ON DELETE CASCADE;
             */
            $this->db->beginTransaction();

            CommentManager::getInstance()->deleteNewsComments($id);

            $sql = "DELETE FROM news WHERE id = :id";
            $params = [':id' => $id];
            $result = $this->db->exec($sql, $params);

            $this->db->commit();

            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * @param int $id
     * @return array
     * @throws Exception
     */
    private function selectNewsById(int $id): array
    {
        $sql = "SELECT * FROM `news` WHERE id = :id";
        $params = [':id' => $id];

        return $this->db->select($sql, $params);
    }
}
