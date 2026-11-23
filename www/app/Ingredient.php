<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Unit.php';

// Ingredients with their purchase habits, flags and usage details
class Ingredient
{
    public static function all()
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT i.id, i.name, i.family, i.purchase_quantity, i.purchase_unit_id,
                       i.is_infinite, i.always_buy, u.name AS unit_name, u.type AS unit_type, u.factor AS unit_factor
                FROM ingredients i
                JOIN units u ON u.id = i.purchase_unit_id
                ORDER BY i.name';
        return $pdo->query($sql)->fetchAll();
    }

    // The list plus the pantry stock and the recipes that use each ingredient
    public static function allWithDetails()
    {
        $pdo = Database::getConnection();
        $units = [];
        foreach (Unit::all() as $unit) {
            $units[$unit['id']] = $unit;
        }

        $ingredients = self::all();
        $byId = [];
        foreach ($ingredients as $index => $ingredient) {
            $ingredients[$index]['pantry_total'] = 0;
            $ingredients[$index]['recipes'] = [];
            $byId[$ingredient['id']] = $index;
        }

        // Pantry stock in base units, brougth back to the purchase unit
        $sql = 'SELECT p.ingredient_id, u.type AS unit_type, SUM(p.quantity * u.factor) AS base_total
                FROM pantry p
                JOIN units u ON u.id = p.unit_id
                GROUP BY p.ingredient_id, u.type';
        foreach ($pdo->query($sql)->fetchAll() as $row) {
            if (!isset($byId[$row['ingredient_id']])) {
                continue;
            }
            $index = $byId[$row['ingredient_id']];
            $purchaseUnit = $units[$ingredients[$index]['purchase_unit_id']];
            if ($purchaseUnit['type'] === $row['unit_type']) {
                $ingredients[$index]['pantry_total'] = round((float)$row['base_total'] / (float)$purchaseUnit['factor'], 2);
            }
        }

        // Recipes that use each ingredient
        $sql = 'SELECT ri.ingredient_id, r.name AS recipe_name, ri.quantity, u.name AS unit_name
                FROM recipe_ingredients ri
                JOIN recipes r ON r.id = ri.recipe_id
                JOIN units u ON u.id = ri.unit_id
                ORDER BY r.name';
        foreach ($pdo->query($sql)->fetchAll() as $row) {
            if (!isset($byId[$row['ingredient_id']])) {
                continue;
            }
            $index = $byId[$row['ingredient_id']];
            $ingredients[$index]['recipes'][] = [
                'name' => $row['recipe_name'],
                'quantity' => (float)$row['quantity'],
                'unit_name' => $row['unit_name']
            ];
        }

        return $ingredients;
    }

    public static function create($data)
    {
        $pdo = Database::getConnection();
        $sql = 'INSERT INTO ingredients (name, family, purchase_quantity, purchase_unit_id, is_infinite, always_buy)
                VALUES (?, ?, ?, ?, ?, ?)';
        $statement = $pdo->prepare($sql);
        $statement->execute([
            $data['name'],
            $data['family'],
            $data['purchase_quantity'],
            $data['purchase_unit_id'],
            !empty($data['is_infinite']) ? 1 : 0,
            !empty($data['always_buy']) ? 1 : 0
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update($id, $data)
    {
        $pdo = Database::getConnection();
        $sql = 'UPDATE ingredients
                SET name = ?, family = ?, purchase_quantity = ?, purchase_unit_id = ?, is_infinite = ?, always_buy = ?
                WHERE id = ?';
        $statement = $pdo->prepare($sql);
        $statement->execute([
            $data['name'],
            $data['family'],
            $data['purchase_quantity'],
            $data['purchase_unit_id'],
            !empty($data['is_infinite']) ? 1 : 0,
            !empty($data['always_buy']) ? 1 : 0,
            $id
        ]);
    }
}
