// Dashboard page, three Highcharts visualizations with one family filter

const filterSelect = document.getElementById("family-filter");
const weekBox = document.getElementById("week-chart");
const recipeBox = document.getElementById("recipe-chart");
const pantryBox = document.getElementById("pantry-chart");
const GRAY_COLORS = ["#000000", "#4d4d4d", "#767676", "#a6a6a6", "#cccccc", "#e5e5e5"];

// Everything is drawn in black and gray tones
function chartBase() {
  return {
    credits: { enabled: false },
    chart: { backgroundColor: "#ffffff", style: { fontFamily: "inherit" } },
    title: { style: { color: "#000000" } },
    legend: { itemStyle: { color: "#000000" } },
    xAxis: {
      lineColor: "#000000",
      tickColor: "#000000",
      labels: { style: { color: "#000000" } },
      title: { style: { color: "#000000" } }
    },
    yAxis: {
      gridLineColor: "#e5e5e5",
      lineColor: "#000000",
      labels: { style: { color: "#000000" } },
      title: { style: { color: "#000000" } }
    }
  };
}

// Meals of the week per family with the balanced criteria line
function drawWeekChart(data) {
  const categories = filterSelect.value ? [filterSelect.value] : data.families;
  const meals = categories.map(family => Number(data.week[family] || 0));
  const criteria = categories.map(family =>
    data.criteria[family] !== undefined ? Number(data.criteria[family]) : null
  );
  Highcharts.chart("week-chart", Object.assign(chartBase(), {
    chart: { type: "column", backgroundColor: "#ffffff", style: { fontFamily: "inherit" } },
    title: { text: weekBox.dataset.title, style: { color: "#000000" } },
    accessibility: { description: weekBox.dataset.description },
    xAxis: Object.assign(chartBase().xAxis, { categories: categories, title: { text: weekBox.dataset.axisFamily, style: { color: "#000000" } } }),
    yAxis: Object.assign(chartBase().yAxis, { min: 0, title: { text: weekBox.dataset.axisCount, style: { color: "#000000" } } }),
    series: [
      { name: weekBox.dataset.meals, type: "column", data: meals, color: "#000000" },
      { name: weekBox.dataset.criteria, type: "line", data: criteria, color: "#767676", dashStyle: "Dash", marker: { symbol: "diamond" } }
    ]
  }));
}

// Ingredients per recipe, limited to the chosen family
function drawRecipeChart(data) {
  const categories = data.recipes.map(item => item.name);
  const totals = data.recipes.map(item => item.total);
  Highcharts.chart("recipe-chart", Object.assign(chartBase(), {
    chart: { type: "bar", backgroundColor: "#ffffff", style: { fontFamily: "inherit" } },
    title: { text: recipeBox.dataset.title, style: { color: "#000000" } },
    accessibility: { description: recipeBox.dataset.description },
    xAxis: Object.assign(chartBase().xAxis, { categories: categories, title: { text: recipeBox.dataset.axisRecipe, style: { color: "#000000" } } }),
    yAxis: Object.assign(chartBase().yAxis, { min: 0, title: { text: recipeBox.dataset.count, style: { color: "#000000" } } }),
    series: [{ name: recipeBox.dataset.count, data: totals, color: "#4d4d4d" }]
  }));
}

// Pantry items per family
function drawPantryChart(data) {
  pantryBox.innerHTML = "";
  if (data.pantry.length === 0) {
    const message = document.createElement("p");
    message.textContent = pantryBox.dataset.empty;
    pantryBox.appendChild(message);
    return;
  }
  const points = data.pantry.map(item => ({ name: item.family, y: Number(item.items) }));
  Highcharts.chart("pantry-chart", Object.assign(chartBase(), {
    chart: { type: "pie", backgroundColor: "#ffffff", style: { fontFamily: "inherit" } },
    title: { text: pantryBox.dataset.title, style: { color: "#000000" } },
    accessibility: { description: pantryBox.dataset.description },
    plotOptions: {
      pie: {
        colors: GRAY_COLORS,
        borderColor: "#ffffff",
        dataLabels: { color: "#000000" },
        showInLegend: true
      }
    },
    series: [{ name: pantryBox.dataset.items, data: points }]
  }));
}

async function loadCharts() {
  // The filter sends a new request so PHP queries the database again
  const data = await apiGet("api/dashboard.php?family=" + encodeURIComponent(filterSelect.value));
  drawWeekChart(data);
  drawRecipeChart(data);
  drawPantryChart(data);
}

filterSelect.addEventListener("change", async () => {
  try {
    await loadCharts();
  } catch (error) {
    showError(error);
  }
});

document.addEventListener("DOMContentLoaded", async () => {
  try {
    await loadCharts();
  } catch (error) {
    showError(error);
  }
});
