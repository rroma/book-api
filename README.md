### Objective

Bookstore REST API using PHP and Symfony.

### Tasks

-   Implement assignment using:
    -   Language: **PHP**
    -   Framework: **Symfony**
-   Implement a REST API returning JSON or XML based on the `Content-Type` header
-   Implement a custom user model with a "author pseudonym" field
-   Implement a book model. Each book should have a title, description, author (your custom user model), cover image and price
    -   Choose the data type for each field that makes the most sense
-   Provide an endpoint to authenticate with the API using username, password and return a JWT
-   Implement REST endpoints for the `/books` resource
    -   No authentication required
    -   Allows only GET (List/Detail) operations
    -   Make the List resource searchable with query parameters
-   Provide REST resources for the authenticated user
    -   Implement the typical CRUD operations for this resource
    -   Implement an endpoint to unpublish a book (DELETE)
-   Implement API tests for all endpoints

### Project Setup

- Clone the repo
- Fill out your .env file for example .env.local and .env.test.local for local setup
- Run **composer install**
- Setup dev and test databases by running
  - bin/console doctrine:database:create --env=dev
  - bin/console doctrine:database:create --env=test
  - bin/console doctrine:schema:create --env=dev
  - bin/console doctrine:schema:create --env=test
- Load fixtures with user's data for testing purposes  
  - bin/console doctrine:fixtures:load --env=dev
  - User credentials can be found in src/DataFixtures/UserFixtures.php
  - There you also can find Darth Wader user that's not able to publish books
- Run symfony local web server with **symfony server:start -d**
- The API docs will be accessible with http://localhost:8000/api url
- Run tests with bin/phpunit
