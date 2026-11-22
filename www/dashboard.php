<?php
// Dashboard page, the charts are drawn by JavaScript with the Fetch API
require_once __DIR__ . '/app/session.php';
require_once __DIR__ . '/app/Dashboard.php';

$families = Dashboard::families();

$pageTitle = t('dashboard.title');
$currentPage = 'dashboard';
$scripts = ['https://code.highcharts.com/highcharts.js', 'js/common.js', 'js/dashboard.js'];
require __DIR__ . '/views/header.php';
?>
    <h1><?php echo t('dashboard.heading'); ?></h1>
    <p class="intro"><?php echo t('dashboard.intro'); ?></p>

    <noscript><p class="intro"><?php echo t('dashboard.noscript'); ?></p></noscript>

    <p>
      <label for="family-filter"><?php echo t('dashboard.filter.label'); ?></label>
      <select id="family-filter">
        <option value=""><?php echo t('dashboard.filter.all'); ?></option>
        <?php foreach ($families as $family) { ?>
        <option value="<?php echo e($family); ?>"><?php echo e($family); ?></option>
        <?php } ?>
      </select>
    </p>

    <section aria-labelledby="week-chart-heading">
      <h2 id="week-chart-heading" class="visually-hidden"><?php echo t('dashboard.chart1.heading'); ?></h2>
      <div class="chart" id="week-chart"
           data-title="<?php echo t('dashboard.chart1.title'); ?>"
           data-meals="<?php echo t('dashboard.chart1.meals'); ?>"
           data-criteria="<?php echo t('dashboard.chart1.criteria'); ?>"
           data-axis-family="<?php echo t('dashboard.axis.family'); ?>"
           data-axis-count="<?php echo t('dashboard.axis.count'); ?>"
           data-description="<?php echo t('dashboard.chart1.description'); ?>"></div>
    </section>

    <section aria-labelledby="recipe-chart-heading">
      <h2 id="recipe-chart-heading" class="visually-hidden"><?php echo t('dashboard.chart2.heading'); ?></h2>
      <div class="chart" id="recipe-chart"
           data-title="<?php echo t('dashboard.chart2.title'); ?>"
           data-count="<?php echo t('dashboard.chart2.count'); ?>"
           data-axis-recipe="<?php echo t('dashboard.axis.recipe'); ?>"
           data-description="<?php echo t('dashboard.chart2.description'); ?>"></div>
    </section>

    <section aria-labelledby="pantry-chart-heading">
      <h2 id="pantry-chart-heading" class="visually-hidden"><?php echo t('dashboard.chart3.heading'); ?></h2>
      <div class="chart" id="pantry-chart"
           data-title="<?php echo t('dashboard.chart3.title'); ?>"
           data-items="<?php echo t('dashboard.chart3.items'); ?>"
           data-description="<?php echo t('dashboard.chart3.description'); ?>"
           data-empty="<?php echo t('dashboard.empty'); ?>"></div>
    </section>
<?php
require __DIR__ . '/views/footer.php';
