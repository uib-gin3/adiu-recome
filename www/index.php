<?php
// Home page with the weekly menu and the summary squares
require_once __DIR__ . '/app/session.php';
require_once __DIR__ . '/app/Dashboard.php';
require_once __DIR__ . '/app/Recipe.php';

$summary = Dashboard::summary();
$recipes = Recipe::all();

// The texts of the grid for the JavaScript
$texts = [
    'addMeal' => t('menu.add.meal'),
    'peopleWord' => t('menu.people.word'),
    'saved' => t('menu.saved'),
    'cleared' => t('menu.cleared')
];
for ($day = 1; $day <= 7; $day++) {
    $texts['day' . $day] = t('menu.day' . $day);
}
for ($slot = 1; $slot <= 5; $slot++) {
    $texts['slot' . $slot] = t('menu.slot' . $slot);
}

$pageTitle = t('menu.title');
$currentPage = 'menu';
$scripts = ['js/common.js', 'js/menu.js'];
require __DIR__ . '/app/header.php';
?>
    <h1><?php echo t('home.heading'); ?></h1>
    <p class="intro"><?php echo t('menu.intro'); ?></p>

    <noscript><p class="intro"><?php echo t('menu.noscript'); ?></p></noscript>

    <div class="menu-scroll">
      <table class="menu-grid">
        <caption class="visually-hidden"><?php echo t('menu.heading'); ?></caption>
        <thead>
          <tr>
            <th scope="col"><span class="visually-hidden"><?php echo t('menu.slot.heading'); ?></span></th>
            <?php for ($day = 1; $day <= 7; $day++) { ?>
            <th scope="col"><?php echo t('menu.day' . $day); ?></th>
            <?php } ?>
          </tr>
        </thead>
        <tbody id="menu-body"></tbody>
      </table>
    </div>

    <dialog id="meal-dialog" aria-labelledby="meal-dialog-heading">
      <form id="meal-form">
        <h2 id="meal-dialog-heading"><?php echo t('menu.dialog.heading'); ?></h2>
        <p class="meal-people" id="dialog-slot"></p>
        <p>
          <label for="meal-recipe"><?php echo t('menu.recipe.label'); ?></label>
          <select id="meal-recipe" required>
            <option value=""><?php echo t('menu.choose.recipe'); ?></option>
            <?php foreach ($recipes as $recipe) { ?>
            <option value="<?php echo $recipe['id']; ?>"><?php echo e($recipe['name']); ?></option>
            <?php } ?>
          </select>
        </p>
        <p>
          <label for="meal-people"><?php echo t('common.people'); ?></label>
          <input type="number" id="meal-people" min="1" max="12" value="2" required>
        </p>
        <p>
          <button type="submit"><?php echo t('menu.assign'); ?></button>
          <button type="button" id="meal-clear"><?php echo t('menu.clear'); ?></button>
          <button type="button" id="meal-cancel"><?php echo t('common.cancel'); ?></button>
        </p>
      </form>
    </dialog>

    <script id="menu-texts" type="application/json"><?php echo json_encode($texts, JSON_UNESCAPED_UNICODE); ?></script>

    <section aria-labelledby="stats-heading">
      <h2 id="stats-heading" class="visually-hidden"><?php echo t('home.stats.heading'); ?></h2>
      <ul class="stats">
        <li><a href="recipes.php"><span class="stat-number"><?php echo $summary['recipes']; ?></span><span><?php echo t('home.stats.recipes'); ?></span></a></li>
        <li><a href="ingredients.php"><span class="stat-number"><?php echo $summary['ingredients']; ?></span><span><?php echo t('home.stats.ingredients'); ?></span></a></li>
        <li><a href="pantry.php"><span class="stat-number"><?php echo $summary['pantry']; ?></span><span><?php echo t('home.stats.pantry'); ?></span></a></li>
        <li><span class="stat-plain"><span class="stat-number"><?php echo $summary['meals']; ?></span><span><?php echo t('home.stats.meals'); ?></span></span></li>
      </ul>
    </section>
<?php
require __DIR__ . '/app/footer.php';
