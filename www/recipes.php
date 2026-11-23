<?php
// Recipes resource, GET reads the list or the form, POST writes
require_once __DIR__ . '/app/session.php';
require_once __DIR__ . '/app/Recipe.php';
require_once __DIR__ . '/app/Ingredient.php';
require_once __DIR__ . '/app/Unit.php';

// Collect the ingredient lines of the form, empty rows are dropped
function linesFromRequest()
{
    $ingredientIds = isset($_POST['ingredient_id']) ? (array)$_POST['ingredient_id'] : [];
    $quantities = isset($_POST['quantity']) ? (array)$_POST['quantity'] : [];
    $unitIds = isset($_POST['unit_id']) ? (array)$_POST['unit_id'] : [];
    $lines = [];
    foreach ($ingredientIds as $index => $ingredientId) {
        if ($ingredientId === '' || !isset($unitIds[$index]) || $unitIds[$index] === '') {
            continue;
        }
        $lines[] = [
            'ingredient_id' => (int)$ingredientId,
            'quantity' => isset($quantities[$index]) && $quantities[$index] !== '' ? (float)$quantities[$index] : 0,
            'unit_id' => (int)$unitIds[$index]
        ];
    }
    return $lines;
}

// Save and go back to the list
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
    $data = [
        'name' => $name,
        'base_people' => isset($_POST['base_people']) && (int)$_POST['base_people'] > 0 ? (int)$_POST['base_people'] : 2,
        'instructions' => isset($_POST['instructions']) ? trim((string)$_POST['instructions']) : '',
        'ingredients' => linesFromRequest()
    ];
    if ($name === '' || count($data['ingredients']) === 0) {
        flash(t('recipes.error.empty'));
    } else {
        if ($id > 0) {
            Recipe::update($id, $data);
        } else {
            Recipe::create($data);
        }
        flash(t('recipes.saved'));
    }
    header('Location: recipes.php');
    exit;
}

$units = Unit::all();
$ingredients = Ingredient::all();
$recipes = Recipe::all();

// The form replaces the list when the edit parameter arrives
$view = 'list';
$editing = null;
$lines = [];
if (isset($_GET['edit'])) {
    $view = 'form';
    if ((string)$_GET['edit'] !== 'new') {
        $editing = Recipe::find((int)$_GET['edit']);
    }
    if ($editing) {
        foreach ($editing['ingredients'] as $line) {
            $lines[] = ['ingredient_id' => $line['ingredient_id'], 'quantity' => $line['quantity'], 'unit_id' => $line['unit_id']];
        }
    }
    for ($i = 0; $i < 3; $i++) {
        $lines[] = ['ingredient_id' => '', 'quantity' => '', 'unit_id' => ''];
    }
}

$pageTitle = t('recipes.title');
$currentPage = 'recipes';
require __DIR__ . '/app/header.php';
?>
<?php if ($view === 'form') { ?>
    <h1><?php echo $editing ? t('recipes.edit.heading') : t('nav.recipes.add'); ?></h1>
    <p><a class="button-link" href="recipes.php"><?php echo t('nav.recipes.list'); ?></a></p>

    <form method="post" action="recipes.php">
      <fieldset>
        <legend><?php echo t('recipes.form.legend'); ?></legend>
        <input type="hidden" name="id" value="<?php echo $editing ? (int)$editing['id'] : 0; ?>">
        <p>
          <label for="recipe-name"><?php echo t('recipes.name.label'); ?></label>
          <input type="text" id="recipe-name" name="name" required value="<?php echo $editing ? e($editing['name']) : ''; ?>">
        </p>
        <p>
          <label for="recipe-people"><?php echo t('recipes.people.label'); ?></label>
          <input type="number" id="recipe-people" name="base_people" min="1" max="12" value="<?php echo $editing ? (int)$editing['base_people'] : 2; ?>" required>
        </p>
        <p>
          <label for="recipe-instructions"><?php echo t('recipes.instructions.label'); ?></label>
          <textarea id="recipe-instructions" name="instructions"><?php echo $editing ? e($editing['instructions']) : ''; ?></textarea>
        </p>
      </fieldset>

      <fieldset>
        <legend><?php echo t('recipes.lines.heading'); ?></legend>
        <table>
          <caption class="visually-hidden"><?php echo t('recipes.lines.heading'); ?></caption>
          <thead>
            <tr>
              <th scope="col"><?php echo t('common.ingredient'); ?></th>
              <th scope="col"><?php echo t('common.quantity'); ?></th>
              <th scope="col"><?php echo t('common.unit'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lines as $index => $line) { ?>
            <tr>
              <td>
                <select name="ingredient_id[]" aria-label="<?php echo t('common.ingredient') . ' ' . ($index + 1); ?>">
                  <option value=""><?php echo t('recipes.choose.ingredient'); ?></option>
                  <?php foreach ($ingredients as $ingredient) { ?>
                  <option value="<?php echo $ingredient['id']; ?>"<?php echo $line['ingredient_id'] !== '' && (int)$line['ingredient_id'] === (int)$ingredient['id'] ? ' selected' : ''; ?>><?php echo e($ingredient['name']); ?></option>
                  <?php } ?>
                </select>
              </td>
              <td>
                <input type="number" name="quantity[]" min="0" step="0.01" value="<?php echo $line['quantity'] !== '' ? e(formatQuantity($line['quantity'])) : ''; ?>" aria-label="<?php echo t('common.quantity') . ' ' . ($index + 1); ?>">
              </td>
              <td>
                <select name="unit_id[]" aria-label="<?php echo t('common.unit') . ' ' . ($index + 1); ?>">
                  <?php foreach ($units as $unit) { ?>
                  <option value="<?php echo $unit['id']; ?>"<?php echo $line['unit_id'] !== '' && (int)$line['unit_id'] === (int)$unit['id'] ? ' selected' : ''; ?>><?php echo e($unit['name']); ?></option>
                  <?php } ?>
                </select>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
        <p><button type="submit"><?php echo t('common.save'); ?></button></p>
      </fieldset>
    </form>
<?php } else { ?>
    <h1><?php echo t('recipes.heading'); ?></h1>
    <p class="intro"><?php echo t('recipes.intro'); ?></p>

    <p><a class="button-link" href="recipes.php?edit=new"><?php echo t('nav.recipes.add'); ?></a></p>

    <table>
      <caption class="visually-hidden"><?php echo t('recipes.list.heading'); ?></caption>
      <thead>
        <tr>
          <th scope="col"><?php echo t('common.name'); ?></th>
          <th scope="col"><?php echo t('recipes.col.people'); ?></th>
          <th scope="col"><?php echo t('common.ingredients'); ?></th>
          <th scope="col"><?php echo t('common.actions'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($recipes) === 0) { ?>
        <tr><td colspan="4"><?php echo t('recipes.empty'); ?></td></tr>
        <?php } ?>
        <?php foreach ($recipes as $recipe) { ?>
        <tr>
          <td><?php echo e($recipe['name']); ?></td>
          <td><?php echo $recipe['base_people']; ?></td>
          <td><?php echo $recipe['ingredient_count']; ?></td>
          <td><a class="button-link" href="recipes.php?edit=<?php echo $recipe['id']; ?>" aria-label="<?php echo t('common.edit') . ' ' . e($recipe['name']); ?>"><?php echo t('common.edit'); ?></a></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
<?php } ?>
<?php
require __DIR__ . '/app/footer.php';
