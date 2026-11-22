<?php
require_once __DIR__ . '/Database.php';

// Recipes and their ingredient lines
class Recipe
{
    public static function all()
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT r.id, r.name, r.base_people, COUNT(ri.id) AS ingredient_count
                FROM recipes r
                LEFT JOIN recipe_ingredients ri ON ri.recipe_id = r.id
                GROUP BY r.id, r.name, r.base_people
                ORDER BY r.name';
        return $pdo->query($sql)->fetchAll();
    }

    // One recipe with all its lines
    public static function find($id)
    {
        $pdo = Database::getConnection();
        $statement = $pdo->prepare('SELECT id, name, base_people, instructions FROM recipes WHERE id = ?');
        $statement->execute([$id]);
        $recipe = $statement->fetch();
        if ($recipe === false) {
            return null;
        }
        $sql = 'SELECT ri.ingredient_id, ri.quantity, ri.unit_id, i.name AS ingredient_name, u.name AS unit_name
                FROM recipe_ingredients ri
                JOIN ingredients i ON i.id = ri.ingredient_id
                JOIN units u ON u.id = ri.unit_id
                WHERE ri.recipe_id = ?
                ORDER BY ri.id';
        $statement = $pdo->prepare($sql);
        $statement->execute([$id]);
        $recipe['ingredients'] = $statement->fetchAll();
        return $recipe;
    }

    public static function create($data)
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();
        $statement = $pdo->prepare('INSERT INTO recipes (name, base_people, instructions) VALUES (?, ?, ?)');
        $statement->execute([$data['name'], (int)$data['base_people'], $data['instructions']]);
        $recipeId = (int)$pdo->lastInsertId();
        self::insertLines($pdo, $recipeId, isset($data['ingredients']) ? $data['ingredients'] : []);
        $pdo->commit();
        return $recipeId;
    }

    // The whole recipe is replaced, lines included
    public static function update($id, $data)
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();
        $statement = $pdo->prepare('UPDATE recipes SET name = ?, base_people = ?, instructions = ? WHERE id = ?');
        $statement->execute([$data['name'], (int)$data['base_people'], $data['instructions'], $id]);
        $statement = $pdo->prepare('DELETE FROM recipe_ingredients WHERE recipe_id = ?');
        $statement->execute([$id]);
        self::insertLines($pdo, $id, isset($data['ingredients']) ? $data['ingredients'] : []);
        $pdo->commit();
    }

    private static function insertLines($pdo, $recipeId, $lines)
    {
        $statement = $pdo->prepare('INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity, unit_id) VALUES (?, ?, ?, ?)');
        foreach ($lines as $line) {
            if (!isset($line['ingredient_id']) || $line['ingredient_id'] === '' || $line['ingredient_id'] === null) {
                continue;
            }
            $statement->execute([$recipeId, (int)$line['ingredient_id'], (float)$line['quantity'], (int)$line['unit_id']]);
        }
    }
}
