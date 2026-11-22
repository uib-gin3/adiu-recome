# Practice 1 – Web Applications

Subject 21724 – Aplicacions Distribuïdes a Internet i Interfícies d'Usuari (ADIU), Universitat de les Illes Balears.

## Objective

The goal of this practice is for the student to develop a complete web application that combines front-end and back-end, applying knowledge of HTML, CSS, JavaScript, PHP, and databases. The project is a single website on a topic of your choice, and one of its pages is a Data Dashboard that shows data from the website's own database.

## Project description

Design a website on a topic of your choice, applying principles of structure, style, and responsive design. The website must include a Dashboard page, reachable from the main navigation menu, that presents data related to the topic.

The data is stored in a MySQL database. The Dashboard requests it from PHP with the Fetch API and draws it with Highcharts.

### Topic ideas (any other topic is also welcome)

- Restaurant or café – most ordered dishes, reservations per day
- Product store or local business – sales by category, stock levels
- Gym / nutrition blog – class attendance, calories per recipe
- Page with articles or publications – articles per category, views per month
- Personal portfolio or CV – projects per technology, skills by level

## Technologies

- **HTML, CSS, and JavaScript** — structure, style, and behavior of every page
- **Fetch API** — to request the data from PHP, and request it again when the user changes a filter
- **PHP and MySQL on XAMPP (Apache + MySQL)** — to serve the whole website and query the database
- **Highcharts (and Highmaps if needed)** — to draw the charts and maps, and redraw them with the new data when a filter changes

## Website requirements

- **Structure**: minimum 3 pages, one of which is the Dashboard (e.g. Home, Products, Contact, Dashboard)
- **Consistency**: all pages, including the Dashboard, share the same header, navigation menu, footer, and styles. The Dashboard must look like part of the website, not a separate application
- **Original code**: the HTML and CSS are written by the student, not copied from a website template. The text and images can be anything that fits the topic
- **Design**:
  - Use of semantic tags (`header`, `nav`, `main`, `section`, `footer`)
  - Custom CSS styles (colors, typography, spacing, borders, etc.)
  - Responsive design using media queries, Flexbox, or Grid

## Dashboard page requirements

- At least three visualizations (charts or tables) using data from the website's database, related to the topic
- Data is loaded with the Fetch API from PHP files that query the database and return JSON (`json_encode`)
- At least one filter (e.g. a select, a date range, or a search box) that sends a value to PHP with fetch and redraws a visualization with the new data. Protect that query against SQL injection
- Client-server separation: frontend pages request JSON data from backend PHP scripts rather than connecting to the database directly
- Optional: one visualization with data from an external API

## Submission

- Project folder in .zip or .rar format, containing the whole website ready to run in XAMPP (`htdocs`)
- Database exported as .sql
- Video of 5–10 minutes showing functionality and how the project was built (if the file is too large, it can be uploaded to YouTube or OneDrive and the link shared in the ReadMe)
- ReadMe document including:
  - Project description
  - Technologies used
  - Project structure
  - Instructions to run the project (import the .sql file, URL to open)

## Evaluation criteria

The project will be evaluated on the following aspects:

| Aspect | Points |
| --- | --- |
| Website (front-end) | 2.5 |
| Dashboard and back-end | 2.5 |
| Code organization and client/server separation | 1.5 |
| Documentation and video | 1.5 |
| Accessibility | 1.0 |
| Extra | 1.0 |
| **Total** | **10** |

### 1. Website (front-end): 2.5 points

- Minimum 3 pages including the Dashboard, all reachable from a shared navigation menu
- Semantic HTML, custom CSS, and responsive design (media queries, Flexbox, or Grid)
- Consistent look across all pages, including the Dashboard

### 2. Dashboard and back-end: 2.5 points

- At least 3 visualizations with data loaded from the database through PHP (JSON) and the Fetch API (1.5 points)
- At least one filter that re-queries the database safely, protected against SQL injection (1 point)

### 3. Code organization and client/server separation: 1.5 points

- Client-server separation: frontend pages request JSON data from backend PHP scripts rather than connecting to the database directly (1 point)
- Descriptive variable names and proper indentation (0.5 points)

### 4. Documentation and video: 1.5 points

- Complete ReadMe: project description, technologies, execution instructions, code and database structure (0.5 points)
- Video (5–10 minutes) showing the application working and explaining the code: the separation between client-side and server-side code, and how the data travels from the database → PHP (JSON) → Fetch API → chart (1 point)

### 5. Accessibility: 1 point

- Semantic HTML on all pages, with clear and descriptive headings
- Charts accessible using the Highcharts accessibility module (<https://www.highcharts.com/docs/accessibility/accessibility-module>)

### 6. Extra: 1 point

- Well-designed and visually appealing interface
- Use of an external API in one of the visualizations (optional)
