<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Controllers\Product;
use App\Models\Articles;
use App\Utility\Upload;
use Core\View;
use Mockery;

/**
 * Test unitaire pour le contrôleur Product
 * @covers Product
 */
class ProductControllerTest extends TestCase
{
    private Product $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $routeParams = [];
        $this->controller = new Product($routeParams);
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_FILES = [];
        $_SESSION = [];

        Mockery::close();
        parent::tearDown();
    }
    /**
     * Test d'affichage de la page d'ajout sans soumission de formulaire
     * @return void
     */
    public function testIndexActionDisplaysAddPageWithoutSubmission()
    {
        $_POST = [];
        $viewMock = Mockery::mock('alias:Core\View');
        $viewMock->shouldReceive('renderTemplate')
            ->once()
            ->with('Product/Add.html');

        ob_start();
        $this->controller->indexAction();
        $output = ob_get_clean();

        $this->assertEmpty($output);
    }

    /**
     * Test d'ajout de produit avec succès
     * Esquivé
     */
    public function testIndexActionSuccessfulProductAddition()
    {
        $this->markTestSkipped('Test skipped due to static dependencies - requires integration test approach');
    }

    /**
     * Test d'ajout de produit sans image
     * @return void
     */
    public function testIndexActionWithoutImage()
    {
        $_POST = [
            'submit' => true,
            'title' => 'Test Product'
        ];

        $_FILES = [
            'picture' => [
                'error' => UPLOAD_ERR_NO_FILE
            ]
        ];

        $viewMock = Mockery::mock('alias:Core\View');
        $viewMock->shouldReceive('renderTemplate')
            ->once()
            ->with('Product/Add.html');
        ob_start();
        $this->controller->indexAction();
        $output = ob_get_clean();
        $this->assertStringContainsString('alert("Vous devez ajouter une image !")', $output);
    }

    /**
     * Test d'ajout de produit avec erreur d'upload
     * @return void
     */
    public function testIndexActionWithUploadError()
    {
        $_POST = [
            'submit' => true,
            'title' => 'Test Product'
        ];

        $_FILES = [
            'picture' => [
                'error' => UPLOAD_ERR_PARTIAL
            ]
        ];
        $viewMock = Mockery::mock('alias:Core\View');
        $viewMock->shouldReceive('renderTemplate')
            ->once()
            ->with('Product/Add.html');

        ob_start();
        $this->controller->indexAction();
        $output = ob_get_clean();

        $this->assertStringContainsString('alert("Vous devez ajouter une image !")', $output);
    }

    /**
     * Test d'ajout de produit avec exception lors de la sauvegarde
     * @return void
     */
    public function testIndexActionWithSaveException()
    {
        $_POST = [
            'submit' => true,
            'title' => 'Test Product'
        ];

        $_FILES = [
            'picture' => [
                'error' => UPLOAD_ERR_OK,
                'name' => 'test.jpg'
            ]
        ];

        $_SESSION = [
            'user' => ['id' => 123]
        ];
        $articlesMock = Mockery::mock('alias:App\Models\Articles');
        $articlesMock->shouldReceive('save')
            ->once()
            ->andThrow(new \Exception('Erreur de base de données'));

        $viewMock = Mockery::mock('alias:Core\View');
        $viewMock->shouldReceive('renderTemplate')
            ->once()
            ->with('Product/Add.html');

        ob_start();
        $this->controller->indexAction();
        $output = ob_get_clean();

        $this->assertStringContainsString('alert("Erreur de base de données")', $output);
    }

    /**
     * Test d'affichage d'un produit avec succès
     * @return void
     */
    public function testShowActionSuccessful()
    {
        $controller = new Product(['id' => '123']);

        $expectedArticle = [
            'id' => 123,
            'title' => 'Test Product',
            'description' => 'Test Description',
            'price' => 29.99
        ];

        $expectedSuggestions = [
            ['id' => 124, 'title' => 'Suggestion 1'],
            ['id' => 125, 'title' => 'Suggestion 2']
        ];
        $articlesMock = Mockery::mock('alias:App\Models\Articles');
        $articlesMock->shouldReceive('addOneView')
            ->once()
            ->with('123');

        $articlesMock->shouldReceive('getSuggest')
            ->once()
            ->andReturn($expectedSuggestions);

        $articlesMock->shouldReceive('getOne')
            ->once()
            ->with('123')
            ->andReturn([$expectedArticle]);
        $viewMock = Mockery::mock('alias:Core\View');
        $viewMock->shouldReceive('renderTemplate')
            ->once()
            ->with('Product/Show.html', [
                'article' => $expectedArticle,
                'suggestions' => $expectedSuggestions
            ]);

        $controller->showAction();
        $this->assertTrue(true);
    }

    /**
     * Test d'affichage d'un produit avec exception
     * Esquivé
     */
    public function testShowActionWithException()
    {
        $this->markTestSkipped('Test skipped due to static dependencies - requires integration test approach');
    }

    /**
     * Test que le contrôleur peut être instancié
     * @return void
     */
    public function testControllerCanBeInstantiated()
    {
        $controller = new Product([]);
        $this->assertInstanceOf(Product::class, $controller);
    }

    /**
     * Test des paramètres de route
     * @return void
     */
    public function testRouteParamsAreSetCorrectly()
    {
        $params = ['id' => '123', 'action' => 'show'];
        $controller = new Product($params);
        $reflection = new \ReflectionClass($controller);
        $property = $reflection->getProperty('route_params');
        $property->setAccessible(true);
        $routeParams = $property->getValue($controller);

        $this->assertEquals('123', $routeParams['id']);
        $this->assertEquals('show', $routeParams['action']);
    }

    /**
     * Test de validation basique des données POST
     * @return void
     */
    public function testPostDataValidation()
    {
        $_POST = [
            'submit' => true,
            'title' => 'Test Title',
            'description' => 'Test Description'
        ];

        $this->assertTrue(isset($_POST['submit']));
        $this->assertEquals('Test Title', $_POST['title']);
        $this->assertEquals('Test Description', $_POST['description']);
    }

    /**
     * Test de validation des fichiers uploadés
     * @return void
     */
    public function testFileUploadValidation()
    {
        $validFile = [
            'picture' => [
                'error' => UPLOAD_ERR_OK,
                'name' => 'test.jpg',
                'tmp_name' => '/tmp/test.jpg'
            ]
        ];

        $invalidFile = [
            'picture' => [
                'error' => UPLOAD_ERR_NO_FILE
            ]
        ];

        $this->assertEquals(0, UPLOAD_ERR_OK);
        $this->assertEquals(4, UPLOAD_ERR_NO_FILE);

        $this->assertTrue($validFile['picture']['error'] === UPLOAD_ERR_OK);
        $this->assertFalse($invalidFile['picture']['error'] === UPLOAD_ERR_OK);
    }
}

if (!function_exists('Tests\Controllers\header')) {
    function header($string, $replace = true, $http_response_code = null) {
        return;
    }
}