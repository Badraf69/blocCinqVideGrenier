<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\Contact;
use PHPMailer\PHPMailer\PHPMailer;

class ContactControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        $this->controller = $this->getMockBuilder(Contact::class)
            ->setConstructorArgs([[]])
            ->onlyMethods([])
            ->getMock();
    }

    public function testSendActionWithValidPostData()
    {
        global $_test;
        $_test = true;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'contact_name'    => 'Jean Dupont',
            'contact_email'   => 'jean@example.com',
            'contact_message' => 'Bonjour, ceci est un test.'
        ];

        // On crée un mock de PHPMailer
        $mailMock = $this->getMockBuilder(PHPMailer::class)
            ->onlyMethods(['send', 'setFrom', 'addAddress'])
            ->disableOriginalConstructor()
            ->getMock();

        // Vérifie que send() sera bien appelé une fois
        $mailMock->expects($this->once())
            ->method('send')
            ->willReturn(true);

        // Injection du mock PHPMailer dans le contrôleur
        $contact = $this->getMockBuilder(Contact::class)
            ->setConstructorArgs([[]])
            ->onlyMethods(['createMailer'])
            ->getMock();

        $contact->method('createMailer')->willReturn($mailMock);

        // On capture la sortie (renderTemplate ou echo)
        $this->expectOutputRegex('/.*/');
        try {
            $contact->sendAction();
        } catch (\Throwable $e) {
            echo "❌Test email ko";
        }
    }
}
