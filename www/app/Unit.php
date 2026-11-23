<?php
require_once __DIR__ . '/Database.php';

class Unit
{
    public static function all()
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT id, name, type, factor FROM units ORDER BY id';
        return $pdo->query($sql)->fetchAll();
    }
}
