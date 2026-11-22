// Weekly menu grid, filled with the Fetch API and edited through the dialog

const gridBody = document.getElementById("menu-body");
const dialog = document.getElementById("meal-dialog");
const dialogSlot = document.getElementById("dialog-slot");
const recipeSelect = document.getElementById("meal-recipe");
const peopleInput = document.getElementById("meal-people");
const texts = JSON.parse(document.getElementById("menu-texts").textContent);

let slots = [];
let activeDay = 0;
let activeSlot = 0;

function slotAt(day, slot) {
  for (const item of slots) {
    if (Number(item.day) === day && Number(item.slot) === slot) {
      return item;
    }
  }
  return null;
}

// The grid is a table with the seven days across and five slots down
function renderGrid() {
  gridBody.innerHTML = "";
  for (let slot = 1; slot <= 5; slot++) {
    const row = document.createElement("tr");
    const header = document.createElement("th");
    header.scope = "row";
    header.textContent = texts["slot" + slot];
    row.appendChild(header);

    for (let day = 1; day <= 7; day++) {
      const cell = document.createElement("td");
      const button = document.createElement("button");
      button.type = "button";
      button.className = "meal-button";
      const meal = slotAt(day, slot);
      if (meal) {
        const name = document.createElement("strong");
        name.textContent = meal.recipe_name;
        button.appendChild(name);
        const people = document.createElement("span");
        people.className = "meal-people";
        people.textContent = meal.people + " " + texts.peopleWord;
        button.appendChild(people);
        button.setAttribute(
          "aria-label",
          meal.recipe_name + ", " + meal.people + " " + texts.peopleWord +
          ", " + texts["day" + day] + " " + texts["slot" + slot]
        );
      } else {
        button.textContent = "+";
        button.setAttribute(
          "aria-label",
          texts.addMeal + ", " + texts["day" + day] + " " + texts["slot" + slot]
        );
      }
      button.addEventListener("click", () => openDialog(day, slot));
      cell.appendChild(button);
      row.appendChild(cell);
    }
    gridBody.appendChild(row);
  }
}

function openDialog(day, slot) {
  activeDay = day;
  activeSlot = slot;
  dialogSlot.textContent = texts["day" + day] + ", " + texts["slot" + slot];
  const meal = slotAt(day, slot);
  recipeSelect.value = meal ? meal.recipe_id : "";
  peopleInput.value = meal ? meal.people : 2;
  dialog.showModal();
}

async function saveMeal(event) {
  event.preventDefault();
  if (!recipeSelect.value) {
    dialog.close();
    return;
  }
  try {
    await apiSend("api/menu.php", "POST", {
      day: activeDay,
      slot: activeSlot,
      recipe_id: recipeSelect.value,
      people: peopleInput.value
    });
    dialog.close();
    showStatus(texts.saved);
    await loadSlots();
  } catch (error) {
    showError(error);
  }
}

async function clearMeal() {
  try {
    await apiSend("api/menu.php?day=" + activeDay + "&slot=" + activeSlot, "DELETE");
    dialog.close();
    showStatus(texts.cleared);
    await loadSlots();
  } catch (error) {
    showError(error);
  }
}

async function loadSlots() {
  slots = await apiGet("api/menu.php");
  renderGrid();
}

document.getElementById("meal-form").addEventListener("submit", saveMeal);
document.getElementById("meal-clear").addEventListener("click", clearMeal);
document.getElementById("meal-cancel").addEventListener("click", () => dialog.close());

document.addEventListener("DOMContentLoaded", async () => {
  try {
    await loadSlots();
  } catch (error) {
    showError(error);
  }
});
