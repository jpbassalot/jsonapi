# JSON File API

The JSON File API is a simple read-only API that allows you to fetch and serialize data stored in JSON files. This API supports pagination and searching for files based on specific properties.

## Getting Started
To use this, you will need to have PHP >= 8.1 installed.

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

$all_files = $reports->getFiles();

$search = isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '';

/**
* JSON Example:
 * {
 *  "title": "My Title",
 *  "property1": "Property 1",
 *  "property2": "Property 2",
 *  "property3": "Property 3",
 *  "nested": {
 *      "property1": "Nested Property 1",
 *      "property2": "Nested Property 2"
 * }
 */
if ($search) {
    $result = $reports->searchFilesData($search, ['title', 'property1', 'property2', 'property3', 'nested.property1', 'nested.property2'], $all_files);
} else {
    $result = $reports->getFilesData($all_files);
}

echo $reports->sendJson($result, $all_files);
```