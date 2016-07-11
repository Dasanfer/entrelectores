<?php
namespace AppBundle\Services\Security;

use PHPassLib\Application\Context;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;

class PassEncoder implements PasswordEncoderInterface
{
    private $bcryptEncoder;
    private $prefixedSalt;

    public function __construct($cost = 10, $prefixedSalt = null)
    {
        $this->bcryptEncoder = new BCryptPasswordEncoder($cost);
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        if (preg_match('|^\$P\$|', $encoded)) {
            $context = new Context();
            $context->addConfig('portable');
            return $context->verify($raw, $encoded);
        }

        return $this->bcryptEncoder->isPasswordValid($encoded, $raw, $salt);
    }

    public function encodePassword($raw, $salt)
    {
        return $this->bcryptEncoder->encodePassword($raw, $salt);
    }
}
