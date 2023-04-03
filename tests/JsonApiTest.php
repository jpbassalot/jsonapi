<?php

use PHPUnit\Framework\TestCase;
use Faker\Factory;

use Jpbassalot\JsonApi;

class JsonApiTest extends TestCase
{
    private ?JsonApi $json_api;
    private string $testDir = __DIR__ . '/json_samples';
    private int $perPage;
    private array $searchItem;

    private int $sampleFilesCount;

    protected function setUp(): void
    {
        $this->perPage = rand(1, 20);
        $this->json_api = new JsonApi($this->testDir, $this->perPage);

        $this->sampleFilesCount = 50;

        $faker = Factory::create();

        for ($i = 1; $i <= $this->sampleFilesCount; $i++) {
            $sampleData = [
                'title' => $faker->sentence(3),
                'age' => $faker->numberBetween(18, 65),
                'date' => $faker->date(),
                'country' => $faker->country(),
                'content' => [
                    'body' => $faker->paragraph(),
                    'url' => $faker->url(),
                ]
            ];

            file_put_contents("$this->testDir/sample_$i.json", json_encode($sampleData));
        }

        $this->searchItem = [
            'title' => $faker->sentence(3),
            'age' => $faker->numberBetween(18, 65),
            'date' => $faker->date(),
            'country' => $faker->country(),
            'content' => [
                'body' => $faker->paragraph(),
                'url' => $faker->url(),
            ]
        ];

        $this->sampleFilesCount++;

        file_put_contents("$this->testDir/sample_$this->sampleFilesCount.json", json_encode($this->searchItem));

    }

    protected function tearDown(): void
    {
        for ($i = 1; $i <= $this->sampleFilesCount; $i++) {
            unlink("$this->testDir/sample_$i.json");
        }

        $this->json_api = null;
    }

    public function testGetFiles()
    {
        $files = $this->json_api->getFiles();
        $this->assertIsArray($files);
    }

    public function testGetJsonFiles()
    {
        $files = $this->json_api->getFiles();
        $json_files = $this->json_api->getFilesData($files);
        $this->assertIsArray($json_files);
        $this->assertCount($this->perPage, $json_files);
    }

    public function testSearchJson()
    {
        $files = $this->json_api->getFiles();
        $search = $this->searchItem['title'];
        $properties = ['title', 'age', 'date', 'country', 'content.body', 'content.url'];

        $result = $this->json_api->searchFilesData($search, $properties, $files);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('title', $result[0]);
        $this->assertArrayHasKey('age', $result[0]);
        $this->assertArrayHasKey('date', $result[0]);
        $this->assertArrayHasKey('country', $result[0]);
        $this->assertArrayHasKey('content', $result[0]);
        $this->assertArrayHasKey('body', $result[0]['content']);
        $this->assertArrayHasKey('url', $result[0]['content']);

        $this->assertEquals($this->searchItem['title'], $result[0]['title']);
    }

    public function testGetTotalPages()
    {
        $files = $this->json_api->getFiles();
        $totalPages = $this->json_api->getTotalPages($files);
        $this->assertIsInt($totalPages);
    }
}

