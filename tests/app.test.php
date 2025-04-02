<?php

use PHPUnit\Framework\TestCase;
use PDO;

class SimpleTest extends TestCase
{
    private $pdo;

    // 🔹 Setup: Establish database connection before tests
    protected function setUp(): void
    {
        $this->pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=my_friend_system', 'myuser', 'mypassword');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Basic asertion
    public function testBasicAssertions()
    {
        $this->assertTrue(true);
        $this->assertEquals(2, 1 + 1);
    }

    // Check database connection
    public function testDatabaseConnection()
    {
        $this->assertInstanceOf(PDO::class, $this->pdo);
    }

    //Check If 'friends' table exists
    public function testFriendsTableExists()
    {
        $query = $this->pdo->query("SHOW TABLES LIKE 'friends'");
        $this->assertNotFalse($query->fetch());
    }

     //Check If 'myfriends' table exists
     public function testMyFriendsTableExists()
     {
         $query = $this->pdo->query("SHOW TABLES LIKE 'myfriends'");
         $this->assertNotFalse($query->fetch());
     }

    //Close database connection after tests
    protected function tearDown(): void
    {
        $this->pdo = null;
    }
}
