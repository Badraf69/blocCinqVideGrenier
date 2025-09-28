<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\User;

class UserControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        // On passe un tableau vide au constructeur du parent Core\Controller
        $this->controller = $this->getMockBuilder(User::class)
            ->setConstructorArgs([[]])   // <-- tableau attendu par __construct()
            ->onlyMethods(['register'])
            ->getMock();
    }

    public function testRegisterActionWithValidData()
    {
        // Simuler un POST valide
        $_POST = [
            'submit'         => true,
            'username'       => 'badraf',
            'email'          => 'badraf@example.com',
            'password'       => 'secret123',
            'password-check' => 'secret123'
        ];
        $_SESSION = [];

        // On simule le comportement de register() -> retourne un ID utilisateur
        $this->controller->expects($this->once())
            ->method('register')
            ->willReturn(42);

        // Empêcher exit de stopper le test
        $this->expectOutputRegex('/.*/');
        try {
            $this->controller->registerAction();
        } catch (\Throwable $e) {
            // ignore l'appel à exit
        }

        // Vérifier que la session contient les infos utilisateur
        $this->assertArrayHasKey('user', $_SESSION);
        $this->assertEquals(42, $_SESSION['user']['id']);
        $this->assertEquals('badraf', $_SESSION['user']['username']);
        $this->assertEquals('badraf@example.com', $_SESSION['user']['email']);

    }
    public function testRegisterActionWithPasswordMismatch()
    {
        $_POST = [
            'submit'         => true,
            'username'       => 'badraf',
            'email'          => 'badraf@example.com',
            'password'       => 'secret123',
            'password-check' => 'differentPassword'
        ];
        $_SESSION = [];

        // On s’assure que register() ne doit PAS être appelé
        $this->controller->expects($this->never())
            ->method('register');

        // On capture la sortie (echo)
        $this->expectOutputString("Les mots de passe ne correspondent pas");

        $this->controller->registerAction();

        // La session ne doit pas contenir "user"
        $this->assertArrayNotHasKey('user', $_SESSION);
    }

}
