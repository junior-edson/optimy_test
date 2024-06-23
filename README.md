# PHP test

## 1. Installation

  - Install docker (https://www.docker.com/products/docker-desktop/)
  - Inside project folder, run these commands:
    - To build php and mysql containers: ```docker-compose up -d --build```
    - To import the database dump file: ```docker-compose exec php php import.php```
    - To fetch data from the database: ```docker-compose exec php php index.php```
  - Ps. 1: As no specific PHP version was mentioned here I am considering PHP 8.3, but it should run well since version 7.4.
  - Ps. 2: If you need to update the database credentials, do so in the file located at ```config/db.php```.


## 2. Changes made

### General changes

- Ensured compliance with PSRs (PHP Standards Recommendations).
- Improved documentation with PHPDoc standard.
- Improved code readability and maintainability.
- Added error handling and exception throwing where necessary.

### File-Specific Changes

#### `DB.php`

- Credentials loaded from an external configuration file (config/db.php).
- Added error handling and PDO attributes for better error management.
- Added query prepare to prevent SQL Injection.
- Added methods for managing transactions (beginTransaction, commit, rollBack), providing control over SQL transactions.

#### `Comment.php`

- Properties, params and function responses are now typed.
- Constructor initializes object properties directly upon instantiation.

#### `CommentManager.php`

- Introduced dependency injection for DB instance, enhancing flexibility and testability.
- Method ```getCommentsByNewsId``` will allow news to be fetched from the database with your comments in a more efficient way than the previous one.

#### `News.php`

- Properties, params and function responses are now typed.
- Constructor initializes object properties directly upon instantiation.
- Added ```getComments``` and ```setComments``` methods to include an array of comments objects within the news object.

#### `NewsManager.php`

- Fetches news and their associated comments together in the ```listNews``` method. This enhances efficiency and improves code organization.
- Added a TODO in the ```deleteNews``` method where if we consider the change of including an FK for ```news_id``` with ```ON DELETE CASCADE```, we will reduce the complexity of the code, and thus it will not be necessary to use transactions.

#### `index.php`

- Changed to const ```ROOT = __DIR__;``` for defining the root directory constant, maintaining functionality while using modern PHP syntax.
- Utilizes the ```getComments()``` method of each News object to retrieve comments directly associated with that news item. This approach improves efficiency by reducing redundant database queries.
- Checks if there are comments ```(count($news->getComments()) > 0)``` associated with a news item before iterating and displaying them. If no comments are found, it displays a message indicating so (No comments yet.), improving clarity.
