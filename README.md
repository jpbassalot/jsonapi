# JSON File API

The JSON File API is a simple read-only API that allows you to fetch and serialize data stored in JSON files. This API supports pagination and searching for files based on specific properties.

## Getting Started
To use this, you will need to have PHP >= 7.4 installed. Clone the repository and run `composer install` to install the dependencies.

## Usage
### Fetching with Pagination

Example: `GET /path/to/index.php?page=1`

Query Parameters:

* `page` (optional): The page number to fetch. If not provided, it defaults to 1.

### Searching within JSON properties
To search within specific properties, make a GET request with the search query parameter.

Example: `GET /path/to/index.php?search=data`

Query Parameters:

* `search` (optional): The search query to filter files.

## Example Usage

```php
require_once 'vendor/autoload.php';

use Jpbassalot\JsonApi;

// A directory containing JSON files
$dir = 'json';

$reports = new JsonApi($dir, 9);

$files = $reports->getFiles();

$search = isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '';

if ($search) {
    $data = $reports->searchFilesData($search, ['title', 'property1', 'property2', 'property3'], $files);
} else {
    $data = $reports->getFilesData($files);
}

echo $reports->sendJson($data, $files);
```