<?php
require_once __DIR__ . '/Database.php';

// Everything the dashboard charts need
class Dashboard
{
    public static function summary()
    {
        $pdo = Database::getConnection();
        return [
            'recipes' => (int)$pdo->query('SELECT COUNT(*) FROM recipes')->fetchColumn(),
            'ingredients' => (int)$pdo->query('SELECT COUNT(*) FROM ingredients')->fetchColumn(),
            'pantry' => (int)$pdo->query('SELECT COUNT(*) FROM pantry')->fetchColumn(),
            'meals' => (int)$pdo->query('SELECT COUNT(*) FROM menu_slots')->fetchColumn()
        ];
    }

    public static function data($family)
    {
        return [
            'families' => self::families(),
            'week' => self::weekPortions($family),
            'criteria' => self::criteria(),
            'recipes' => self::recipeIngredientCounts($family),
            'pantry' => self::pantryByFamily()
        ];
    }

    public static function families()
    {
        $pdo = Database::getConnection();
        $rows = $pdo->query('SELECT DISTINCT family FROM ingredients ORDER BY family')->fetchAll();
        $names = [];
        foreach ($rows as $row) {
            $names[] = $row['family'];
        }
        return $names;
    }

    // How many meals of the week use each ingredient family
    private static function weekPortions($family)
    {
        $pdo = Database::getConnection();
        $where = ['i.is_infinite = 0'];
        $params = [];
        if ($family !== '') {
            $where[] = 'i.family = ?';
            $params[] = $family;
        }
        $sql = 'SELECT i.family, ms.day, ms.slot
                FROM menu_slots ms
                JOIN recipe_ingredients ri ON ri.recipe_id = ms.recipe_id
                JOIN ingredients i ON i.id = ri.ingredient_id
                WHERE ' . implode(' AND ', $where);
        $statement = $pdo->prepare($sql);
        $statement->execute($params);

        // One meal count once per family even if two ingredients share it
        $seen = [];
        $meals = [];
        foreach ($statement->fetchAll() as $row) {
            $key = $row['day'] . ' ' . $row['slot'] . ' ' . $row['family'];
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            if (!isset($meals[$row['family']])) {
                $meals[$row['family']] = 0;
            }
            $meals[$row['family']]++;
        }
        return $meals;
    }

    private static function criteria()
    {
        $pdo = Database::getConnection();
        $portions = [];
        foreach ($pdo->query('SELECT family, portions FROM criteria')->fetchAll() as $row) {
            $portions[$row['family']] = (int)$row['portions'];
        }
        return $portions;
    }

    // How many ingredients of each recipe match the chosen family
    private static function recipeIngredientCounts($family)
    {
        $pdo = Database::getConnection();
        $counts = [];
        if ($family === '') {
            $rows = $pdo->query('SELECT recipe_id, COUNT(*) AS total FROM recipe_ingredients GROUP BY recipe_id')->fetchAll();
            foreach ($rows as $row) {
                $counts[$row['recipe_id']] = (int)$row['total'];
            }
        } else {
            $sql = 'SELECT ri.recipe_id, COUNT(*) AS total
                    FROM recipe_ingredients ri
                    JOIN ingredients i ON i.id = ri.ingredient_id
                    WHERE i.family = ?
                    GROUP BY ri.recipe_id';
            $statement = $pdo->prepare($sql);
            $statement->execute([$family]);
            foreach ($statement->fetchAll() as $row) {
                $counts[$row['recipe_id']] = (int)$row['total'];
            }
        }

        $sql = 'SELECT id, name FROM recipes ORDER BY name';
        $result = [];
        foreach ($pdo->query($sql)->fetchAll() as $row) {
            $result[] = [
                'name' => $row['name'],
                'total' => isset($counts[$row['id']]) ? $counts[$row['id']] : 0
            ];
        }
        return $result;
    }

    private static function pantryByFamily()
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT i.family, COUNT(*) AS items
                FROM pantry p
                JOIN ingredients i ON i.id = p.ingredient_id
                GROUP BY i.family
                ORDER BY items DESC, i.family';
        return $pdo->query($sql)->fetchAll();
    }
}
