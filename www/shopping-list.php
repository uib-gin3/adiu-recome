<?php
// Shopping list resource, GET reads the list or the add form, POST writes
require_once __DIR__ . '/app/session.php';
require_once __DIR__ . '/app/ShoppingList.php';
require_once __DIR__ . '/app/Unit.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? (string)$_POST['action'] : '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($action === 'generate') {
        ShoppingList::generate();
        flash(t('shopping.generated'));
    }
    if ($action === 'add') {
        $name = isset($_POST['custom_name']) ? trim((string)$_POST['custom_name']) : '';
        if ($name === '') {
            flash(t('shopping.error.name'));
        } else {
            ShoppingList::createManual([
                'custom_name' => $name,
                'quantity' => isset($_POST['quantity']) && $_POST['quantity'] !== '' ? (float)$_POST['quantity'] : 1,
                'unit_id' => isset($_POST['unit_id']) && $_POST['unit_id'] !== '' ? (int)$_POST['unit_id'] : null
            ]);
            flash(t('common.saved'));
        }
    }
    if ($action === 'status' && $id > 0) {
        $status = isset($_POST['status']) ? (string)$_POST['status'] : '';
        if (in_array($status, ['pending', 'bought', 'discarded'], true)) {
            ShoppingList::setStatus($id, $status);
            flash(t('common.saved'));
        }
    }
    if ($action === 'delete' && $id > 0) {
        ShoppingList::delete($id);
        flash(t('shopping.deleted'));
    }
    header('Location: shopping-list.php');
    exit;
}

$units = Unit::all();
$items = ShoppingList::items();
$statuses = ['pending', 'bought', 'discarded'];

// The add form replaces the list when the add parameter arrives
$view = isset($_GET['add']) ? 'form' : 'list';

$pageTitle = t('shopping.title');
$currentPage = 'shopping';
require __DIR__ . '/app/header.php';
?>
<?php if ($view === 'form') { ?>
    <h1><?php echo t('nav.shopping.add'); ?></h1>
    <p><a class="button-link" href="shopping-list.php"><?php echo t('nav.shopping.list'); ?></a></p>

    <form method="post" action="shopping-list.php">
      <fieldset>
        <legend><?php echo t('shopping.form.legend'); ?></legend>
        <input type="hidden" name="action" value="add">
        <p>
          <label for="shopping-custom-name"><?php echo t('shopping.custom.name.label'); ?></label>
          <input type="text" id="shopping-custom-name" name="custom_name" required>
        </p>
        <p>
          <label for="shopping-custom-quantity"><?php echo t('common.quantity'); ?></label>
          <input type="number" id="shopping-custom-quantity" name="quantity" min="0" step="0.01" value="1" required>
        </p>
        <p>
          <label for="shopping-custom-unit"><?php echo t('common.unit'); ?></label>
          <select id="shopping-custom-unit" name="unit_id">
            <option value=""><?php echo t('shopping.no.unit'); ?></option>
            <?php foreach ($units as $unit) { ?>
            <option value="<?php echo $unit['id']; ?>"><?php echo e($unit['name']); ?></option>
            <?php } ?>
          </select>
        </p>
        <p><button type="submit"><?php echo t('shopping.add.submit'); ?></button></p>
      </fieldset>
    </form>
<?php } else { ?>
    <h1><?php echo t('shopping.heading'); ?></h1>
    <p class="intro"><?php echo t('shopping.intro'); ?></p>

    <p><a class="button-link" href="shopping-list.php?add"><?php echo t('nav.shopping.add'); ?></a></p>

    <form method="post" action="shopping-list.php">
      <input type="hidden" name="action" value="generate">
      <p><button type="submit"><?php echo t('shopping.generate'); ?></button></p>
    </form>

    <table>
      <caption class="visually-hidden"><?php echo t('shopping.heading'); ?></caption>
      <thead>
        <tr>
          <th scope="col"><?php echo t('shopping.col.item'); ?></th>
          <th scope="col"><?php echo t('common.quantity'); ?></th>
          <th scope="col"><?php echo t('shopping.col.status'); ?></th>
          <th scope="col"><?php echo t('common.actions'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($items) === 0) { ?>
        <tr><td colspan="4"><?php echo t('shopping.empty'); ?></td></tr>
        <?php } ?>
        <?php foreach ($items as $item) { ?>
        <tr>
          <td>
            <?php echo e($item['name']); ?>
            <?php if ((int)$item['is_manual'] === 1) { ?>
            <br><span class="tag"><?php echo t('shopping.tag.manual'); ?></span>
            <?php } ?>
          </td>
          <td><?php echo formatQuantity($item['quantity']) . ' ' . e($item['unit_name'] ? $item['unit_name'] : t('shopping.no.unit')); ?></td>
          <td>
            <form method="post" action="shopping-list.php" class="row-form">
              <input type="hidden" name="action" value="status">
              <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
              <select name="status" aria-label="<?php echo e($item['name'] . ', ' . t('shopping.col.status')); ?>">
                <?php foreach ($statuses as $status) { ?>
                <option value="<?php echo $status; ?>"<?php echo $item['status'] === $status ? ' selected' : ''; ?>><?php echo t('shopping.status.' . $status); ?></option>
                <?php } ?>
              </select>
              <button type="submit"><?php echo t('common.save'); ?></button>
            </form>
          </td>
          <td>
            <form method="post" action="shopping-list.php" class="row-form">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
              <button type="submit" aria-label="<?php echo t('common.remove') . ' ' . e($item['name']); ?>"><?php echo t('common.remove'); ?></button>
            </form>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
<?php } ?>
<?php
require __DIR__ . '/app/footer.php';
