<?php
// src/Acme/DemoBundle/Tests/Utility/CalculatorTest.php
namespace AppBundl\Tests\Security;

use AppBundle\Services\Security\PassEncoder;

class PassEncoderTest extends \PHPUnit_Framework_TestCase
{
    public function testValidWordpressPass()
    {
        $encoder = new PassEncoder();
        $raw = 'entrelectores';
        $encoded = '$P$BGjfp3bcR.3jYXggi9k148Id/b6Gtw1';
        $this->assertTrue($encoder->isPasswordValid($encoded,$raw,uniqid()));
    }

    public function testEncodeDecode()
    {
        $encoder = new PassEncoder();
        $raw = 'entrelectores';
        $salt = sha1(uniqid());
        $encoded = $encoder->encodePassword($raw,$salt);
        $this->assertTrue($encoder->isPasswordValid($encoded,$raw,$salt));
    }
}
