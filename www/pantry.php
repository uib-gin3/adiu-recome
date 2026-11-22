<?php
// Pantry resource, GET reads the list or the add form, POST writes
require_once __DIR__ . '/app/session.php';
require_once __DIR__ . '/app/Pantry.php';
require_once __DIR__ . '/app/Ingredient.php';
require_once __DIR__ . '/app/Unit.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? (string)$_POST['action'] : '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $ingredientId = isset($_POST['ingredient_id']) ? (int)$_POST['ingredient_id'] : 0;
    $quantity = isset($_POST['quantity']) && $_POST['quantity'] !== '' ? (float)$_POST['quantity'] : 0;
    $unitId = isset($_POST['unit_id']) ? (int)$_POST['unit_id'] : 0;

    if ($action === 'add') {
        if ($ingredientId > 0 && $unitId > 0) {
            Pantry::create(['ingredient_id' => $ingredientId, 'quantity' => $quantity, 'unit_id' => $unitId]);
            flash(t('pantry.saved'));
        } else {
            flash(t('pantry.error.ingredient'));
        }
    }
    if ($action === 'update' && $id > 0 && $unitId > 0) {
        Pantry::update($id, ['quantity' => $quantity, 'unit_id' => $unitId]);
        flash(t('pantry.saved'));
    }
    if ($action === 'delete' && $id > 0) {
        Pantry::delete($id);
        flash(t('pantry.deleted'));
    }
    header('Location: pantry.php');
    exit;
}

$units = Unit::all();
$ingredients = Ingredient::all();
$rows = Pantry::all();

// The add form replaces the list when the add parameter arrives
$view = isset($_GET['add']) ? 'form' : 'list';

$pageTitle = t('pantry.title');
$currentPage = 'pantry';
require __DIR__ . '/views/header.php';
?>
<?php if ($view === 'form') { ?>
    <h1><?php echo t('nav.pantry.add'); ?></h1>
    <p><a class="button-link" href="pantry.php"><?php echo t('nav.pantry.list'); ?></a></p>

    <form method="post" action="pantry.php">
      <fieldset>
        <legend><?php echo t('pantry.form.legend'); ?></legend>
        <input type="hidden" name="action" value="add">
        <p>
          <label for="pantry-ingredient"><?php echo t('common.ingredient'); ?></label>
          <select id="pantry-ingredient" name="ingredient_id" required>
            <option value=""><?php echo t('pantry.choose.ingredient'); ?></option>
            <?php foreach ($ingredients as $ingredient) { ?>
            <option value="<?php echo $ingredient['id']; ?>"><?php echo e($ingredient['name']); ?></option>
            <?php } ?>
          </select>
        </p>
        <p>
          <label for="pantry-quantity"><?php echo t('common.quantity'); ?></label>
          <input type="number" id="pantry-quantity" name="quantity" min="0" step="0.01" value="1" required>
        </p>
        <p>
          <label for="pantry-unit"><?php echo t('common.unit'); ?></label>
          <select id="pantry-unit" name="unit_id" required>
            <?php foreach ($units as $unit) { ?>
            <option value="<?php echo $unit['id']; ?>"><?php echo e($unit['name']); ?></option>
            <?php } ?>
          </select>
        </p>
        <p><button type="submit"><?php echo t('pantry.add'); ?></button></p>
      </fieldset>
    </form>
<?php } else { ?>
    <h1><?php echo t('pantry.heading'); ?></h1>
    <p class="intro"><?php echo t('pantry.intro'); ?></p>

    <p><a class="button-link" href="pantry.php?add"><?php echo t('nav.pantry.add'); ?></a></p>

    <table>
      <caption class="visually-hidden"><?php echo t('pantry.heading'); ?></caption>
      <thead>
        <tr>
          <th scope="col"><?php echo t('common.ingredient'); ?></th>
          <th scope="col"><?php echo t('common.quantity'); ?></th>
          <th scope="col"><?php echo t('common.actions'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($rows) === 0) { ?>
        <tr><td colspan="3"><?php echo t('pantry.empty'); ?></td></tr>
        <?php } ?>
        <?php foreach ($rows as $row) { ?>
        <tr>
          <td><?php echo e($row['ingredient_name']); ?></td>
          <td>
            <form method="post" action="pantry.php" class="row-form">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
              <input type="number" name="quantity" min="0" step="0.01" value="<?php echo e(formatQuantity($row['quantity'])); ?>" aria-label="<?php echo e($row['ingredient_name'] . ', ' . t('common.quantity')); ?>" required>
              <select name="unit_id" aria-label="<?php echo e($row['ingredient_name'] . ', ' . t('common.unit')); ?>">
                <?php foreach ($units as $unit) { ?>
                <option value="<?php echo $unit['id']; ?>"<?php echo (int)$row['unit_id'] === (int)$unit['id'] ? ' selected' : ''; ?>><?php echo e($unit['name']); ?></option>
                <?php } ?>
              </select>
              <button type="submit" aria-label="<?php echo t('common.save') . ' ' . e($row['ingredient_name']); ?>"><?php echo t('common.save'); ?></button>
            </form>
          </td>
          <td>
            <form method="post" action="pantry.php" class="row-form">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
              <button type="submit" aria-label="<?php echo t('common.remove') . ' ' . e($row['ingredient_name']); ?>"><?php echo t('common.remove'); ?></button>
            </form>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
<?php } ?>
<?php
require __DIR__ . '/views/footer.php';
