<?php
require_once __DIR__ . '/Database.php';

// What is at home right now
class Pantry
{
    public static function all()
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT p.id, p.ingredient_id, p.quantity, p.unit_id,
                       i.name AS ingredient_name, u.name AS unit_name
                FROM pantry p
                JOIN ingredients i ON i.id = p.ingredient_id
                JOIN units u ON u.id = p.unit_id
                ORDER BY i.name';
        return $pdo->query($sql)->fetchAll();
    }

    public static function create($data)
    {
        $pdo = Database::getConnection();
        $sql = 'INSERT INTO pantry (ingredient_id, quantity, unit_id) VALUES (?, ?, ?)';
        $statement = $pdo->prepare($sql);
        $statement->execute([$data['ingredient_id'], $data['quantity'], $data['unit_id']]);
        return (int)$pdo->lastInsertId();
    }

    public static function update($id, $data)
    {
        $pdo = Database::getConnection();
        $sql = 'UPDATE pantry SET quantity = ?, unit_id = ? WHERE id = ?';
        $statement = $pdo->prepare($sql);
        $statement->execute([$data['quantity'], $data['unit_id'], $id]);
    }

    public static function delete($id)
    {
        $pdo = Database::getConnection();
        $statement = $pdo->prepare('DELETE FROM pantry WHERE id = ?');
        $statement->execute([$id]);
    }
}
