<?php

require_once CONFIG_PATH . '/database.php';

class Contract
{
    public static function getAll(): array
    {
        $pdo = getPDO();
        $stmt = $pdo->query('SELECT * FROM contracts ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public static function findById(int $id): array|false
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM contracts WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function create(array $data): int|false
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('
            INSERT INTO contracts (numero, client_id, devis_id, date_debut, date_fin, montant, statut, description)
            VALUES (:numero, :client_id, :devis_id, :date_debut, :date_fin, :montant, :statut, :description)
        ');
        $stmt->execute([
            ':numero'      => $data['numero'],
            ':client_id'   => $data['client_id'],
            ':devis_id'    => $data['devis_id'] ?? null,
            ':date_debut'  => $data['date_debut'] ?? null,
            ':date_fin'    => $data['date_fin'] ?? null,
            ':montant'     => $data['montant'] ?? 0,
            ':statut'      => $data['statut'] ?? 'actif',
            ':description' => trim($data['description'] ?? ''),
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = getPDO();
        $fields = [];
        $params = [':id' => $id];

        $allowed = ['numero', 'client_id', 'devis_id', 'date_debut', 'date_fin', 'montant', 'statut', 'description'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = 'UPDATE contracts SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete(int $id): bool
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('DELETE FROM contracts WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public static function getByClient(int $clientId): array
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM contracts WHERE client_id = :client_id ORDER BY created_at DESC');
        $stmt->execute([':client_id' => $clientId]);
        return $stmt->fetchAll();
    }

    public static function getByStatut(string $statut): array
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM contracts WHERE statut = :statut ORDER BY created_at DESC');
        $stmt->execute([':statut' => $statut]);
        return $stmt->fetchAll();
    }

    public static function count(): int
    {
        $pdo = getPDO();
        return (int) $pdo->query('SELECT COUNT(*) FROM contracts')->fetchColumn();
    }

    public static function countByStatut(): array
    {
        $pdo = getPDO();
        $stmt = $pdo->query('SELECT statut, COUNT(*) as total FROM contracts GROUP BY statut');
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['statut']] = (int) $row['total'];
        }
        return $result;
    }

    public static function generateNumero(): string
    {
        $pdo = getPDO();
        $year = date('Y');
        $stmt = $pdo->query("SELECT COUNT(*) FROM contracts WHERE YEAR(created_at) = {$year}");
        $count = (int) $stmt->fetchColumn() + 1;
        return 'CTR-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
