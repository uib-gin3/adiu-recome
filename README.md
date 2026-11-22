# Recome

Weekly meal planning and grocery list management for practice 1 of subject 21724, Aplicacions Distribuïdes a Internet i Interfícies d'Usuari (ADIU), Universitat de les Illes Balears.

## Description

Recome keeps your recipes, the food you have at home and the menu of the week. From the menu it builds a shopping list, and a dashboard compares the week against a balanced diet criteria. The interface is written in Catalan, Spanish and English, and it is strictly black and white.

The application follows the MVC paradigm with a small code base and server rendered templates, in a restful spirit. The classes in `app` are the models. Each page inside `www` is the controller of one resource and its own template below, sharing the layout of `views`. A GET request reads the list or the form, a POST request writes and redirects back. The menu grid and the dashboard ask for their data with the Fetch API and keep the JavaScript to the minimum that the practice requires.

## Technologies

- HTML, CSS and PHP rendered templates on the server
- Vanilla JavaScript only where it is needed, the menu grid and the dashboard
- Fetch API for the JSON of the menu and the dashboard
- PHP 8 with PDO and MySQL
- Highcharts with its accessibility module for the dashboard
- Docker Compose with a PHP Apache container and a MySQL container

## Structure

```
www/                                web root, mounted into the web container
  index.php                         the weekly menu with the summary squares
  ingredients.php, recipes.php      GET shows the list or the form, POST writes
  pantry.php, shopping-list.php     and redirects, one file per resource
  dashboard.php                     three Highcharts visualizations and the filter
  views/header.php, views/footer.php  shared layout with the details navigation
  lang/ca.php, lang/es.php, lang/en.php  interface texts, one file per language
  app/session.php                   session, language, flash message, escaping
  app/*.php                         models, one class per entity, PDO prepared statements
  api/dashboard.php, api/menu.php   JSON endpoints used with the Fetch API
  css/style.css, js/*.js            stylesheet and the small scripts
db/init.sql                         schema, units, criteria and example data
data/                               MySQL data of the bind volume, kept out of git
Dockerfile, docker-compose.yml      runtime with two containers
```

The navigation groups the sections with native `details` elements. Each group unfolds on click and shows the list entry and the add entry of the section, and the group of the current page renders already unfolded.

## How to run it with Docker

```
docker compose up --build
```

Open `http://localhost:8080` in your browser. The first start imports `db/init.sql` automatically, so the database is ready with example data.

The MySQL data lives in the `data/` folder of the project because it is a bind volume. Deleting that folder and starting the containers again gives a fresh database imported from `db/init.sql`.

## How to run it on XAMPP

1. Copy the contents of `www/` into a folder of `htdocs`, for example `htdocs/recome`.
2. Open `www/app/config.php` and change the host to `localhost`. Set the user to `root` and leave an empty password, or create the `recome` user yourself.
3. Import `db/init.sql` in phpMyAdmin.
4. Open `http://localhost/recome` in your browser.

## Languages

The three links CA, ES and EN in the header change the interface language. The choice is kept in the session and every page is rendered again in that language, so the `lang` attribute of the document always matches the text. The data itself, like ingredient and recipe names, stays in the language it was typed in.

## Accessibility

- Semantic HTML with landmarks, one heading per level and a skip link
- Labels on every field, table captions and scope attributes
- Black on white with high contrast, no color anywhere
- Visible keyboard focus, native buttons, links and inputs only
- A live status region that announces the result of each action
- Charts use the Highcharts accessibility module with descriptions
- The pages that need JavaScript announce it politely with `noscript`

## How the data travels

The pages that render forms talk to the models directly through PHP and show the result after a redirect, with a short message stored in the session. The menu grid and the dashboard do the round trip of the practice: JavaScript asks `api/menu.php` or `api/dashboard.php` with the Fetch API, PHP queries MySQL with PDO prepared statements and answers with `json_encode`, and the browser draws the grid or the Highcharts visualizations. The family filter of the dashboard starts the round trip again with the chosen value.
