<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Unit.php';
require_once __DIR__ . '/Ingredient.php';

// The shopping list built from the weekly menu and the pantry stock
class ShoppingList
{
    public static function items()
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT s.id, s.ingredient_id, s.custom_name, s.quantity, s.unit_id, s.status, s.is_manual,
                       COALESCE(i.name, s.custom_name) AS name, u.name AS unit_name
                FROM shopping_list s
                LEFT JOIN ingredients i ON i.id = s.ingredient_id
                LEFT JOIN units u ON u.id = s.unit_id
                ORDER BY FIELD(s.status, \'pending\', \'bought\', \'discarded\'), name';
        return $pdo->query($sql)->fetchAll();
    }

    // Rebuild the pending lines from the menu of the week
    public static function generate()
    {
        $pdo = Database::getConnection();
        $units = [];
        $baseUnitIds = [];
        foreach (Unit::all() as $unit) {
            $units[$unit['id']] = $unit;
            if ((float)$unit['factor'] == 1.0) {
                $baseUnitIds[$unit['type']] = $unit['id'];
            }
        }
        $ingredients = [];
        foreach (Ingredient::all() as $ingredient) {
            $ingredients[$ingredient['id']] = $ingredient;
        }

        // What the whole week needs, scaled to the people of each meal
        $sql = 'SELECT ri.ingredient_id, u.type AS unit_type,
                       SUM(ri.quantity * ms.people / r.base_people * u.factor) AS base_total
                FROM menu_slots ms
                JOIN recipes r ON r.id = ms.recipe_id
                JOIN recipe_ingredients ri ON ri.recipe_id = r.id
                JOIN units u ON u.id = ri.unit_id
                GROUP BY ri.ingredient_id, u.type';
        $needed = [];
        foreach ($pdo->query($sql)->fetchAll() as $row) {
            $needed[$row['ingredient_id']][$row['unit_type']] = (float)$row['base_total'];
        }

        // What is already at home
        $sql = 'SELECT p.ingredient_id, u.type AS unit_type, SUM(p.quantity * u.factor) AS base_total
                FROM pantry p
                JOIN units u ON u.id = p.unit_id
                GROUP BY p.ingredient_id, u.type';
        $stock = [];
        foreach ($pdo->query($sql)->fetchAll() as $row) {
            $stock[$row['ingredient_id']][$row['unit_type']] = (float)$row['base_total'];
        }

        $pdo->beginTransaction();
        // Recipe lines that are still pending are rebuilt, the rest survive
        $pdo->exec("DELETE FROM shopping_list WHERE status = 'pending' AND is_manual = 0");
        $added = [];

        foreach ($needed as $ingredientId => $amountsByType) {
            if (!isset($ingredients[$ingredientId])) {
                continue;
            }
            $ingredient = $ingredients[$ingredientId];
            if (!empty($ingredient['is_infinite'])) {
                // Things like salt or tap water are never bought
                continue;
            }
            $purchaseUnit = $units[$ingredient['purchase_unit_id']];
            $purchaseType = $purchaseUnit['type'];
            $base = isset($amountsByType[$purchaseType]) ? $amountsByType[$purchaseType] : 0;
            if (empty($ingredient['always_buy']) && isset($stock[$ingredientId][$purchaseType])) {
                $base = max(0, $base - $stock[$ingredientId][$purchaseType]);
            }
            if ($base > 0) {
                $quantity = round($base / (float)$purchaseUnit['factor'], 2);
                self::insertLine($pdo, $ingredientId, $quantity, $purchaseUnit['id']);
                $added[$ingredientId] = true;
            }
            // Amounts measured in another kind of unit become their own line
            foreach ($amountsByType as $type => $amount) {
                if ($type === $purchaseType || $amount <= 0) {
                    continue;
                }
                self::insertLine($pdo, $ingredientId, round($amount, 2), $baseUnitIds[$type]);
                $added[$ingredientId] = true;
            }
        }

        // Ingredients marked as always bought join the list every week
        foreach ($ingredients as $ingredientId => $ingredient) {
            if (empty($ingredient['always_buy']) || isset($added[$ingredientId])) {
                continue;
            }
            self::insertLine($pdo, $ingredientId, (float)$ingredient['purchase_quantity'], $ingredient['purchase_unit_id']);
        }

        $pdo->commit();
        return self::items();
    }

    public static function createManual($data)
    {
        $pdo = Database::getConnection();
        $sql = 'INSERT INTO shopping_list (ingredient_id, custom_name, quantity, unit_id, status, is_manual)
                VALUES (NULL, ?, ?, ?, \'pending\', 1)';
        $statement = $pdo->prepare($sql);
        $statement->execute([$data['custom_name'], $data['quantity'], $data['unit_id']]);
        return (int)$pdo->lastInsertId();
    }

    public static function setStatus($id, $status)
    {
        $pdo = Database::getConnection();
        $statement = $pdo->prepare('UPDATE shopping_list SET status = ? WHERE id = ?');
        $statement->execute([$status, $id]);
    }

    public static function delete($id)
    {
        $pdo = Database::getConnection();
        $statement = $pdo->prepare('DELETE FROM shopping_list WHERE id = ?');
        $statement->execute([$id]);
    }

    private static function insertLine($pdo, $ingredientId, $quantity, $unitId)
    {
        $sql = 'INSERT INTO shopping_list (ingredient_id, custom_name, quantity, unit_id, status, is_manual)
                VALUES (?, NULL, ?, ?, \'pending\', 0)';
        $statement = $pdo->prepare($sql);
        $statement->execute([$ingredientId, $quantity, $unitId]);
    }
}
