<?php
// Shared opening of every page, expects pageTitle and currentPage

if (!isset($pageTitle)) {
    $pageTitle = 'Recome';
}
if (!isset($currentPage)) {
    $currentPage = '';
}

// The edit and add parameters decide which child of a group is the current one
$editParam = isset($_GET['edit']) ? (string)$_GET['edit'] : '';
$adding = isset($_GET['add']);

$navGroups = [
    ['href' => 'index.php', 'key' => 'nav.menu', 'page' => 'menu'],
    [
        'key' => 'nav.ingredients', 'page' => 'ingredients',
        'children' => [
            ['href' => 'ingredients.php', 'key' => 'nav.ingredients.list', 'current' => $currentPage === 'ingredients' && $editParam !== 'new'],
            ['href' => 'ingredients.php?edit=new', 'key' => 'nav.ingredients.add', 'current' => $currentPage === 'ingredients' && $editParam === 'new']
        ]
    ],
    [
        'key' => 'nav.recipes', 'page' => 'recipes',
        'children' => [
            ['href' => 'recipes.php', 'key' => 'nav.recipes.list', 'current' => $currentPage === 'recipes' && $editParam !== 'new'],
            ['href' => 'recipes.php?edit=new', 'key' => 'nav.recipes.add', 'current' => $currentPage === 'recipes' && $editParam === 'new']
        ]
    ],
    [
        'key' => 'nav.pantry', 'page' => 'pantry',
        'children' => [
            ['href' => 'pantry.php', 'key' => 'nav.pantry.list', 'current' => $currentPage === 'pantry' && !$adding],
            ['href' => 'pantry.php?add', 'key' => 'nav.pantry.add', 'current' => $currentPage === 'pantry' && $adding]
        ]
    ],
    [
        'key' => 'nav.shopping', 'page' => 'shopping',
        'children' => [
            ['href' => 'shopping-list.php', 'key' => 'nav.shopping.list', 'current' => $currentPage === 'shopping' && !$adding],
            ['href' => 'shopping-list.php?add', 'key' => 'nav.shopping.add', 'current' => $currentPage === 'shopping' && $adding]
        ]
    ],
    ['href' => 'dashboard.php', 'key' => 'nav.dashboard', 'page' => 'dashboard']
];
?>
<!DOCTYPE html>
<html lang="<?php echo currentLanguage(); ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="data:,">
  <title><?php echo e($pageTitle); ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <a class="skip-link" href="#main"><?php echo t('a11y.skip'); ?></a>

  <header class="site-header">
    <div class="brand">
      <p class="site-name">Recome</p>
      <p class="site-tagline"><?php echo t('site.tagline'); ?></p>
    </div>
    <nav class="site-nav" aria-label="<?php echo t('nav.label'); ?>">
      <ul>
        <?php foreach ($navGroups as $group) { ?>
        <?php if (isset($group['children'])) { ?>
        <li>
          <details class="nav-group"<?php echo $currentPage === $group['page'] ? ' open' : ''; ?>>
            <summary<?php echo $currentPage === $group['page'] ? ' aria-current="true"' : ''; ?>><?php echo t($group['key']); ?></summary>
            <ul class="nav-sub">
              <?php foreach ($group['children'] as $child) { ?>
              <li><a href="<?php echo $child['href']; ?>"<?php echo $child['current'] ? ' aria-current="page"' : ''; ?>><?php echo t($child['key']); ?></a></li>
              <?php } ?>
            </ul>
          </details>
        </li>
        <?php } else { ?>
        <li><a href="<?php echo $group['href']; ?>"<?php echo $currentPage === $group['page'] ? ' aria-current="page"' : ''; ?>><?php echo t($group['key']); ?></a></li>
        <?php } ?>
        <?php } ?>
      </ul>
    </nav>
    <div class="lang-buttons" role="group" aria-label="<?php echo t('lang.label'); ?>">
      <?php foreach (['ca', 'es', 'en'] as $code) { ?>
      <a class="lang-link<?php echo currentLanguage() === $code ? ' is-active' : ''; ?>" hreflang="<?php echo $code; ?>" href="<?php echo e(languageUrl($code)); ?>"><?php echo strtoupper($code); ?></a>
      <?php } ?>
    </div>
  </header>

  <main id="main">
    <p id="status" class="status" role="status" aria-live="polite"><?php echo e(takeFlash()); ?></p>
