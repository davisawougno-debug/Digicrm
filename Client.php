<?php

require_once CONFIG_PATH . '/database.php';

class Client
{
    public static function getAll(): array
    {
        $pdo = getPDO();
        $stmt = $pdo->query('SELECT * FROM clients ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public static function findById(int $id): array|false
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM clients WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function create(array $data): int|false
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('
            INSERT INTO clients (nom, prenom, email, telephone, entreprise, adresse, secteur_activite, created_from_prospect_id, statut)
            VALUES (:nom, :prenom, :email, :telephone, :entreprise, :adresse, :secteur_activite, :created_from_prospect_id, :statut)
        ');
        $stmt->execute([
            ':nom'                      => trim($data['nom']),
            ':prenom'                   => trim($data['prenom']),
            ':email'                    => trim($data['email']),
            ':telephone'                => trim($data['telephone'] ?? ''),
            ':entreprise'               => trim($data['entreprise'] ?? ''),
            ':adresse'                  => trim($data['adresse'] ?? ''),
            ':secteur_activite'         => $data['secteur_activite'] ?? null,
            ':created_from_prospect_id' => $data['created_from_prospect_id'] ?? null,
            ':statut'                   => $data['statut'] ?? 'actif',
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = getPDO();
        $fields = [];
        $params = [':id' => $id];

        $allowed = ['nom', 'prenom', 'email', 'telephone', 'entreprise', 'adresse', 'secteur_activite', 'created_from_prospect_id', 'statut'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = 'UPDATE clients SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete(int $id): bool
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('DELETE FROM clients WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public static function getByProspect(int $prospectId): array|false
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM clients WHERE created_from_prospect_id = :created_from_prospect_id LIMIT 1');
        $stmt->execute([':created_from_prospect_id' => $prospectId]);
        return $stmt->fetch();
    }

    public static function count(): int
    {
        $pdo = getPDO();
        return (int) $pdo->query('SELECT COUNT(*) FROM clients')->fetchColumn();
    }

    public static function getRecent(int $limit): array
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM clients ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
