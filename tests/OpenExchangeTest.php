<?php
use Gufy\Currency\OpenExchange;
use Gufy\Currency\Exception\ApiException;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
class OpenExchangeTest extends \PHPUnit_Framework_TestCase{
  public $api;
  public function setUp(){
    parent::setUp();
    $this->api = new OpenExchange("536c3aa91c03431d948583d0983a6eac");
    $data = json_decode(file_get_contents(dirname(__FILE__)."/openexchange.json"),true);
    $content = $data["contents"];
    $mock = new MockHandler([
      new Response(200, ["Content-Type"=>"text/json"], json_encode($content[0])),
      new Response(200, ["Content-Type"=>"text/json"], json_encode($content[1])),
      new Response(200, ["Content-Type"=>"text/json"], json_encode($content[2])),
      new Response(200, ["Content-Type"=>"text/json"], json_encode($content[3])),
    ]);
    $handler = HandlerStack::create($mock);
    $this->api->client([
      'handler'=>$handler,
    ]);
  }
  public function testFunctions(){
    $response = $this->api->rates();
    $request = $this->api->getRequest();
    $api = $this->api->getAppId();
    $this->assertEquals("/api/latest.json", $request->getUri()->getPath());
    $this->assertEquals("base=USD&app_id={$api}", $request->getUri()->getQuery());
    $this->assertArrayHasKey("base", $response);
    $this->assertEquals("USD", $response["base"]);
    $this->assertArrayHasKey("rates", $response);
  }

  public function testConvert(){

    $response = $this->api->convert($value = 10, $base = "EUR", $target = "IDR");
    $request = $this->api->getRequest();
    $this->assertEquals("/api/convert/{$value}/{$base}/{$target}", $request->getUri()->getPath());
    $this->assertEquals(27673.975864, $response);
  }
  public function testGetCurrencies(){
    $response = $this->api->currencies();
    $this->assertArrayHasKey("USD", $response);
  }
  public function testHistory(){
    $date = "2016-05-16";
    $response = $this->api->rates("USD", $date);
    $request = $this->api->getRequest();
    $this->assertEquals("/api/historical/{$date}.json", $request->getUri()->getPath());
    $this->assertArrayHasKey("base", $response);
    $this->assertEquals("USD", $response["base"]);
    $this->assertEquals(strtotime("2016-05-16 23:00:00"), $response["timestamp"]);
    $this->assertArrayHasKey("rates", $response);
  }
  public function testErrorException(){
    $date = "2016-05-16";
    $this->api->rates("IDR", $date);
    $this->expectException(ApiException::class);
  }
}
