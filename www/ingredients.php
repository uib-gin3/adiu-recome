<?php
// Ingredients resource, GET reads the list or the form, POST writes
require_once __DIR__ . '/app/session.php';
require_once __DIR__ . '/app/Ingredient.php';
require_once __DIR__ . '/app/Unit.php';

// Save and go back to the list
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
    $family = isset($_POST['family']) ? trim((string)$_POST['family']) : '';
    if ($name === '' || $family === '') {
        flash(t('ingredients.error.required'));
    } else {
        $data = [
            'name' => $name,
            'family' => $family,
            'purchase_quantity' => isset($_POST['purchase_quantity']) && $_POST['purchase_quantity'] !== '' ? (float)$_POST['purchase_quantity'] : 1,
            'purchase_unit_id' => isset($_POST['purchase_unit_id']) ? (int)$_POST['purchase_unit_id'] : 0,
            'is_infinite' => isset($_POST['is_infinite']),
            'always_buy' => isset($_POST['always_buy'])
        ];
        if ($id > 0) {
            Ingredient::update($id, $data);
        } else {
            Ingredient::create($data);
        }
        flash(t('ingredients.saved'));
    }
    header('Location: ingredients.php');
    exit;
}

$units = Unit::all();
$ingredients = Ingredient::allWithDetails();

// The families already used suggest the datalist of the form
$families = [];
foreach ($ingredients as $ingredient) {
    if (!in_array($ingredient['family'], $families, true)) {
        $families[] = $ingredient['family'];
    }
}

// The form replaces the list when the edit parameter arrives
$view = 'list';
$editing = null;
if (isset($_GET['edit'])) {
    $view = 'form';
    if ((string)$_GET['edit'] !== 'new') {
        $editId = (int)$_GET['edit'];
        foreach ($ingredients as $ingredient) {
            if ((int)$ingredient['id'] === $editId) {
                $editing = $ingredient;
                break;
            }
        }
    }
}

$pageTitle = t('ingredients.title');
$currentPage = 'ingredients';
require __DIR__ . '/app/header.php';
?>
<?php if ($view === 'form') { ?>
    <h1><?php echo $editing ? t('ingredients.edit.heading') : t('nav.ingredients.add'); ?></h1>
    <p><a class="button-link" href="ingredients.php"><?php echo t('nav.ingredients.list'); ?></a></p>

    <form method="post" action="ingredients.php">
      <fieldset>
        <legend><?php echo t('ingredients.form.legend'); ?></legend>
        <input type="hidden" name="id" value="<?php echo $editing ? (int)$editing['id'] : 0; ?>">
        <p>
          <label for="ingredient-name"><?php echo t('ingredients.name.label'); ?></label>
          <input type="text" id="ingredient-name" name="name" required value="<?php echo $editing ? e($editing['name']) : ''; ?>">
        </p>
        <p>
          <label for="ingredient-family"><?php echo t('common.family'); ?></label>
          <input type="text" id="ingredient-family" name="family" list="family-list" required value="<?php echo $editing ? e($editing['family']) : ''; ?>">
          <datalist id="family-list">
            <?php foreach ($families as $family) { ?>
            <option value="<?php echo e($family); ?>"></option>
            <?php } ?>
          </datalist>
        </p>
        <p>
          <label for="ingredient-quantity"><?php echo t('ingredients.purchase.label'); ?></label>
          <input type="number" id="ingredient-quantity" name="purchase_quantity" min="0" step="0.01" value="<?php echo $editing ? e(formatQuantity($editing['purchase_quantity'])) : '1'; ?>" required>
        </p>
        <p>
          <label for="ingredient-unit"><?php echo t('ingredients.unit.label'); ?></label>
          <select id="ingredient-unit" name="purchase_unit_id" required>
            <?php foreach ($units as $unit) { ?>
            <option value="<?php echo $unit['id']; ?>"<?php echo $editing && (int)$editing['purchase_unit_id'] === (int)$unit['id'] ? ' selected' : ''; ?>><?php echo e($unit['name']); ?></option>
            <?php } ?>
          </select>
        </p>
        <p>
          <label><input type="checkbox" name="is_infinite"<?php echo $editing && (int)$editing['is_infinite'] === 1 ? ' checked' : ''; ?>> <?php echo t('ingredients.infinite.label'); ?></label>
        </p>
        <p>
          <label><input type="checkbox" name="always_buy"<?php echo $editing && (int)$editing['always_buy'] === 1 ? ' checked' : ''; ?>> <?php echo t('ingredients.always.label'); ?></label>
        </p>
        <p><button type="submit"><?php echo $editing ? t('ingredients.submit.edit') : t('ingredients.submit.add'); ?></button></p>
      </fieldset>
    </form>
<?php } else { ?>
    <h1><?php echo t('ingredients.heading'); ?></h1>
    <p class="intro"><?php echo t('ingredients.intro'); ?></p>

    <p><a class="button-link" href="ingredients.php?edit=new"><?php echo t('nav.ingredients.add'); ?></a></p>

    <table>
      <caption class="visually-hidden"><?php echo t('ingredients.heading'); ?></caption>
      <thead>
        <tr>
          <th scope="col"><?php echo t('common.name'); ?></th>
          <th scope="col"><?php echo t('common.family'); ?></th>
          <th scope="col"><?php echo t('ingredients.col.purchase'); ?></th>
          <th scope="col"><?php echo t('ingredients.col.stock'); ?></th>
          <th scope="col"><?php echo t('ingredients.col.recipes'); ?></th>
          <th scope="col"><?php echo t('common.actions'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ingredients as $ingredient) { ?>
        <tr>
          <td>
            <?php echo e($ingredient['name']); ?>
            <?php if ((int)$ingredient['is_infinite'] === 1) { ?>
            <br><span class="tag"><?php echo t('ingredients.tag.infinite'); ?></span>
            <?php } ?>
            <?php if ((int)$ingredient['always_buy'] === 1) { ?>
            <br><span class="tag"><?php echo t('ingredients.tag.always'); ?></span>
            <?php } ?>
          </td>
          <td><?php echo e($ingredient['family']); ?></td>
          <td><?php echo formatQuantity($ingredient['purchase_quantity']) . ' ' . e($ingredient['unit_name']); ?></td>
          <td>
            <?php if ((float)$ingredient['pantry_total'] > 0) { ?>
            <?php echo formatQuantity($ingredient['pantry_total']) . ' ' . e($ingredient['unit_name']); ?>
            <?php } else { ?>
            <?php echo t('ingredients.stock.none'); ?>
            <?php } ?>
          </td>
          <td>
            <?php if (count($ingredient['recipes']) === 0) { ?>
            <?php echo t('ingredients.recipes.none'); ?>
            <?php } else { ?>
            <?php foreach ($ingredient['recipes'] as $use) { ?>
            <p><?php echo e($use['name']); ?> (<?php echo formatQuantity($use['quantity']) . ' ' . e($use['unit_name']); ?>)</p>
            <?php } ?>
            <?php } ?>
          </td>
          <td><a class="button-link" href="ingredients.php?edit=<?php echo $ingredient['id']; ?>" aria-label="<?php echo t('common.edit') . ' ' . e($ingredient['name']); ?>"><?php echo t('common.edit'); ?></a></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
<?php } ?>
<?php
require __DIR__ . '/app/footer.php';
