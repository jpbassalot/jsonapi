<?php

namespace Jpbassalot;

/**
 * JsonApi class to process and return data from JSON files in a directory
 */
class JsonApi
{
    protected string $dir;
    protected int $per_page;

    /**
     * JsonApi constructor.
     *
     * @param string $dir Directory where JSON files are stored
     * @param int $per_page Number of reports per page
     */
    public function __construct(string $dir, int $per_page = 9)
    {
        $this->dir = rtrim($dir, '/');
        $this->per_page = $per_page;
    }

    /**
     * Get value from an array using dot notation
     *
     * @param array $json
     * @param string $property
     * @return mixed|null
     */
    private function getValueByDotNotation(array $json, string $property): mixed
    {
        $keys = explode('.', $property);
        $value = $json;

        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return null;
            }

            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Recursively search for the query within the specified properties using dot notation
     *
     * @param array $json
     * @param string $query
     * @param array $properties
     * @return bool
     */
    private function searchProperties(array $json, string $query, array $properties): bool
    {
        foreach ($properties as $prop) {
            $value = $this->getValueByDotNotation($json, $prop);
            if ($value !== null && stripos($value, $query) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all JSON files in the directory
     *
     * @return array
     */
    public function getFiles(): array
    {
        $files = glob($this->dir . '/*.json');
        sort($files);
        return $files;
    }

    /**
     * Get the JSON data for a given page
     *
     * @param array $files
     * @return array Array of reports data for the given page
     */
    public function getFilesData(array $files): array
    {
        $max_page = $this->getTotalPages($files);
        $page = filter_var(filter_input(INPUT_GET, 'page'), FILTER_VALIDATE_INT, [
            'options' => ['default' => 1, 'min_range' => 1, 'max_range' => $max_page]
        ]);

        $files = array_slice($files, ($page - 1) * $this->per_page, $this->per_page);

        return $this->parseFiles($files);
    }

    /**
     * Search for files with a given query in specified properties
     *
     * @param string $query
     * @param array $properties
     * @param array $files
     * @return array
     */
    public function searchFilesData(string $query, array $properties, array $files): array
    {
        $data = [];

        foreach ($files as $file) {
            $json = json_decode(file_get_contents($file), true);

            if ($this->searchProperties($json, $query, $properties)) {
                $data[] = $json;
            }
        }

        return $data;
    }

    /**
     * Send JSON response
     *
     * @param array $filtered_data
     * @param array $all_files
     * @return string
     */
    public function sendJson(array $filtered_data, array $all_files): string
    {
        $total_pages = $this->getTotalPages($all_files);
        $result = [
            'data' => $filtered_data,
            'total_pages' => $total_pages,
            'total_reports' => count($all_files),
        ];
        header('Content-Type: application/json');

        return json_encode($result);
    }

    /**
     * Get the total number of pages based on the number of files per page
     *
     * @param array $files
     * @return int Total number of pages
     */
    public function getTotalPages(array $files): int
    {
        return (int)ceil(count($files) / $this->per_page);
    }

    /**
     * Parse JSON files and return their contents as an array
     *
     * @param false|array $files
     * @return array
     */
    protected function parseFiles(false|array $files): array
    {
        $data = [];

        foreach ($files as $file) {
            $json = json_decode(file_get_contents($file), true);
            $data[] = $json;
        }

        return $data;
    }
}
