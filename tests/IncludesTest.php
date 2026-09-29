<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../includes.php";

class IncludesTest extends TestCase
{
    public function testValidUsernameAccepted(): void
    {
        $this->assertTrue(isValidUsername("stefan"));
    }

    public function testShortUsernameRejected(): void
    {
        $this->assertFalse(isValidUsername("ab"));
    }

    public function testValidPasswordAccepted(): void
    {
        $this->assertTrue(isValidPassword("password123"));
    }

    public function testShortPasswordRejected(): void
    {
        $this->assertFalse(isValidPassword("12345"));
    }

    public function testPasswordHashAndVerify(): void
    {
        $hash = hashPassword("mysecret");
        $this->assertTrue(verifyPassword("mysecret", $hash));
        $this->assertFalse(verifyPassword("wrongpassword", $hash));
    }

    public function testHashIsNotPlaintext(): void
    {
        $hash = hashPassword("mysecret");
        $this->assertNotEquals("mysecret", $hash);
    }
}
