
**System/Context:**
Act as an expert full-stack developer. Your task is to build a complete, minimalist web application for weekly meal planning and grocery list management.

**Technology Stack:**

* **Backend:** PHP 8+ (strictly acting as a REST API).
* **Database:** MySQL.
* **Frontend:** Static HTML files consuming the API via Fetch. Use strictly Vanilla JavaScript and Vanilla CSS (heavy use of Flexbox). Do NOT use any frontend or CSS frameworks (no React, Vue, Tailwind, Bootstrap, etc.).
* **Infrastructure:** The entire application must be containerized using Docker (docker-compose with PHP-Apache/FPM and MySQL containers).

**Design & UI/UX Constraints:**

* **E-ink Optimized & Ultra-Minimalist:** The UI must be strictly black and white. Use only typography, simple lines, and high contrast. No gradients, shadows, or unnecessary styling.
* **Responsive:** Must look great on mobile. For the Weekly Menu view, assume the user might need to rotate their phone to landscape, or implement a clean horizontal scroll.
* **Authentication:** None. This is a single-user personal tool.

**Core Entities & Business Logic:**

1. **Units (Unitats):**
* Must include predefined units: Tablespoon (Cullerada), Teaspoon (Culleradeta), Pinch (Polsim), Glass (Tassó), Cup (Tassa), Small Cup (Tasseta), grams (g), liters (l), milliliters (ml), centiliters (cl), kilograms (kg), and unit/piece (unitat).
* *Logic:* Every volume/weight unit must have a base conversion rate to either grams, liters, or units to calculate accurate shopping quantities.


2. **Ingredients:**
* Properties: Name, Category/Family, Default Purchasing Quantity, and Default Purchasing Unit.
* *Flags:*
* `Infinite`: Items like salt or tap water. Never add to the shopping list, even if required by a recipe.
* `Always Buy`: Items that must always appear on the shopping list regardless of pantry stock.


* Views: Create, Read, Update. Show a list of recipes using the ingredient and current pantry stock.


3. **Pantry (Rebost):**
* Inventory of what is currently at home (Ingredient, Quantity, Unit).
* Example: "Extra Virgin Olive Oil | 1L Bottle | Qty: 1".


4. **Recipes (Receptes):**
* Properties: Name, Base Number of People (Pax), Preparation Instructions.
* List of recipe ingredients (Ingredient, Quantity, Unit). Example: "1 Tablespoon of Extra Virgin Olive Oil".
* Views: Create, Read, Update (modify pax, ingredients, description).


5. **Weekly Menu (Menu Setmanal):**
* A grid of 7 days, with 5 meal slots per day.
* Users can click any meal slot and assign one or multiple recipes, specifying the number of people for that specific meal.
* *UI Toggle:* Show only recipe names by default. Allow toggling to display main ingredients or ingredient families to visually check diet balance.
* *History:* Keep a record of all past weeks.
* *Generation:* Allow creating a new week by cloning a past week, or auto-generating a random week based on existing recipes and a "Balanced Diet Criteria".
* *Dietary Balance:* Include a default balanced diet criteria (e.g., X portions of fish, Y portions of vegetables per week). Allow the user to customize this criteria.
* *Stats View:* Show statistics comparing the current week against the active criteria. Allow auto-filling empty slots in the week to meet the balanced criteria.


6. **Shopping List (Llista de la Compra):**
* Generated automatically from a Weekly Menu.
* *Generation Modes:*
1. Only main ingredients (Default).
2. Only main ingredients, ignoring the Pantry stock.
3. All ingredients (main + secondary).


* *List States & Modes:*
* **Supermarket Mode:** Checkboxes to tick off items as they are placed in the physical cart.
* **Pantry Transfer Mode:** Once bought, transfer items to the Pantry. Allow modifying the quantity transferred (e.g., wanted 2, only found 1).
* **Discard:** Mark items to be ignored (neither bought nor pending).
* **Carry-over:** Any item not transferred to the pantry or discarded remains pending and rolls over to the next week's list.
* **Ad-hoc Items:** Allow adding manual items to the shopping list that don't belong to any recipe.





**Deliverables Requested from the Agent:**

1. `docker-compose.yml` and `Dockerfile`s to spin up the PHP and MySQL environment effortlessly.
2. `init.sql` script to create the database schema and insert the default Units and balanced diet criteria.
3. The PHP REST API files (routing, database connection, and CRUD controllers for the entities).
4. The static frontend files (`index.html`, `recipes.html`, `pantry.html`, `ingredients.html`, `shopping-list.html`, `stats.html`, `app.js`, `style.css`) implementing the views and fetching data from the API using Vanilla JS. Ensure the CSS follows the B&W e-ink constraints.

Please provide the code in clear, well-structured blocks.
