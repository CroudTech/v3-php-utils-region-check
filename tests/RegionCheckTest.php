<?php

use PHPUnit\Framework\TestCase;
use CroudTech\RegionCheck;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class RegionCheckTest extends TestCase
{

    protected $client;

    public function validClient()
    {
        $response = new Response(200, [], json_encode([
            'IsMaster' => true
        ]));
        $handler = HandlerStack::create(new MockHandler([$response]));
        
        return new Client(['handler' => $handler]);
    }

    public function invalidClient()
    {
        $response = new Response(200, [], json_encode([
            'IsMaster' => false
        ]));
        $handler = HandlerStack::create(new MockHandler([$response]));
        
        return new Client(['handler' => $handler]);
    }

    public function testNoConfig()
    {
        $check = new RegionCheck($this->validClient());
        $this->assertTrue($check->isMaster());  
    }

    public function testEmptyConfig()
    {
        $check = new RegionCheck($this->validClient());
        $this->assertTrue($check->isMaster());
    }


    public function testValidClient()
    {
        $check = new RegionCheck($this->validClient(), 'dummy-route');
        $this->assertTrue($check->isMaster());
    }

    public function testInvalidClient()
    {
        $check = new RegionCheck($this->invalidClient(), 'dummy-route');
        $this->assertFalse($check->isMaster());
    }


    public function testInvalidResults()
    {

        $this->expectException('Exception');
        $this->expectExceptionMessage('Invalid results');

        $handler = HandlerStack::create(
            new MockHandler([
                new Response(200),
            ])
        );

        $client = new Client(['handler' => $handler]);

        $check = new RegionCheck($client, 'dummypath');
        $check->isMaster();
    }

    public function testInvalidResponse()
    {

        $this->expectException('Exception');
        $this->expectExceptionMessage('Invalid response');

        $handler = HandlerStack::create(
            new MockHandler([
                new Response(400),
            ])
        );

        $client = new Client(['handler' => $handler]);

        $check = new RegionCheck($client, 'dummypath');
        $check->isMaster();
    }

}