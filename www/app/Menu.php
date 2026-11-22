<?php
require_once __DIR__ . '/Database.php';

// The menu of the current week, one recipe per meal slot
class Menu
{
    public static function slots()
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT ms.day, ms.slot, ms.recipe_id, ms.people, r.name AS recipe_name
                FROM menu_slots ms
                JOIN recipes r ON r.id = ms.recipe_id
                ORDER BY ms.day, ms.slot';
        $slots = $pdo->query($sql)->fetchAll();

        // Families of the ingredients of each meal, used by the balance check
        $sql = 'SELECT ms.day, ms.slot, GROUP_CONCAT(DISTINCT i.family ORDER BY i.family SEPARATOR \', \') AS families
                FROM menu_slots ms
                JOIN recipe_ingredients ri ON ri.recipe_id = ms.recipe_id
                JOIN ingredients i ON i.id = ri.ingredient_id
                WHERE i.is_infinite = 0
                GROUP BY ms.day, ms.slot';
        $families = [];
        foreach ($pdo->query($sql)->fetchAll() as $row) {
            $families[$row['day'] . ' ' . $row['slot']] = $row['families'];
        }
        foreach ($slots as &$slot) {
            $key = $slot['day'] . ' ' . $slot['slot'];
            $slot['families'] = isset($families[$key]) ? $families[$key] : '';
        }
        unset($slot);

        return $slots;
    }

    public static function setSlot($day, $slot, $recipeId, $people)
    {
        $pdo = Database::getConnection();
        $sql = 'INSERT INTO menu_slots (day, slot, recipe_id, people) VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE recipe_id = VALUES(recipe_id), people = VALUES(people)';
        $statement = $pdo->prepare($sql);
        $statement->execute([$day, $slot, $recipeId, $people]);
    }

    public static function clearSlot($day, $slot)
    {
        $pdo = Database::getConnection();
        $statement = $pdo->prepare('DELETE FROM menu_slots WHERE day = ? AND slot = ?');
        $statement->execute([$day, $slot]);
    }
}
