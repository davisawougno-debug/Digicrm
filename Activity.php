<?php

require_once CONFIG_PATH . '/database.php';

class Activity
{
    public static function log(
        ?int   $userId,
        string $action,
        string $description = null,
        string $module = null
    ): bool {
        $pdo = getPDO();
        $stmt = $pdo->prepare('
            INSERT INTO activities (user_id, action, module, description)
            VALUES (:user_id, :action, :module, :description)
        ');
        return $stmt->execute([
            ':user_id'     => $userId,
            ':action'      => $action,
            ':module'      => $module,
            ':description' => $description,
        ]);
    }

    public static function getByUser(int $userId, int $limit = 50): array
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('
            SELECT * FROM activities
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            LIMIT :lim
        ');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getAll(int $limit = 100): array
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('
            SELECT a.*, u.nom, u.prenom
            FROM activities a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT :lim
        ');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getByAction(string $action, int $limit = 50): array
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('
            SELECT a.*, u.nom, u.prenom
            FROM activities a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE a.action = :action
            ORDER BY a.created_at DESC
            LIMIT :lim
        ');
        $stmt->bindValue(':action', $action, PDO::PARAM_STR);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
