<?php
// Read and edit the meal slots of the week
require __DIR__ . '/../app/Menu.php';
require __DIR__ . '/common.php';

// Seven days and five meal slot per day
function slotFromRequest()
{
    $data = readBody();
    $day = isset($data['day']) ? (int)$data['day'] : 0;
    $slot = isset($data['slot']) ? (int)$data['slot'] : 0;
    $recipeId = isset($data['recipe_id']) ? (int)$data['recipe_id'] : 0;
    $people = isset($data['people']) ? (int)$data['people'] : 2;
    if ($day < 1 || $day > 7 || $slot < 1 || $slot > 5 || $recipeId <= 0 || $people < 1 || $people > 12) {
        sendJson(['error' => 'Wrong day, slot, recipe or people'], 400);
    }
    return [$day, $slot, $recipeId, $people];
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        sendJson(Menu::slots());
        break;
    case 'POST':
        list($day, $slot, $recipeId, $people) = slotFromRequest();
        Menu::setSlot($day, $slot, $recipeId, $people);
        sendJson(['ok' => true]);
        break;
    case 'DELETE':
        $day = isset($_GET['day']) ? (int)$_GET['day'] : 0;
        $slot = isset($_GET['slot']) ? (int)$_GET['slot'] : 0;
        if ($day < 1 || $day > 7 || $slot < 1 || $slot > 5) {
            sendJson(['error' => 'Wrong day or slot'], 400);
        }
        Menu::clearSlot($day, $slot);
        sendJson(['ok' => true]);
        break;
    default:
        methodNotAllowed();
}
