<?php namespace Gufy\Currency;
use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Middleware;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Gufy\Currency\Exception\ApiException;
use Closure;
class OpenExchange{
  const API_URL = "https://openexchangerates.org/api/";
  private $appId;
  static $CLIENT;
  private $request;
  public function __construct($appId){
    $this->appId = $appId;
  }
  public function rates($base = "USD", $date = null){
    if ($date == null){
      return $this->request("latest.json", ["base"=>$base]);
    }
    else{
      return $this->request("historical/{$date}.json", ["base"=>$base]);
    }
  }
  public function client($config = []){
    if(self::$CLIENT == null){
      $config = array_merge($config, [
        "base_uri" => self::API_URL,
        "query"=>[
          "app_id"=>$this->getAppId()
        ]
      ]);
      self::$CLIENT = new Client($config);
    }
    return self::$CLIENT;
  }
  public function convert($value, $base, $target){
    $data = $this->request("convert/".$value."/".$base."/".$target);
    return $data["response"];
  }
  public function currencies(){
    return $this->request("currencies.json");
  }
  private function request($url, $data = []){
    $class = $this;
    $tapHandler = Middleware::tap(function(RequestInterface $request) use($class){
      $class->setRequest($request);
    });
    $client = $this->client();
    $clientHandler = $client->getConfig("handler");
    $data["app_id"] = $this->getAppId();
    try{
      $response = $client->request("GET", $url, ["query"=>$data, "handler"=>$tapHandler($clientHandler)]);
      // $this->request = $response->getRequest();
      return json_decode($response->getBody()->getContents(), true);
    }
    catch(ClientException $e){
      $response = json_decode($e->getResponse()->getBody()->getContents(), true);
      throw new ApiException($response["description"]);
    }
  }
  public function setRequest(RequestInterface $request){
    $this->request = $request;
  }
  public function getRequest(){
    return $this->request;
  }
  public function getAppId(){
    return $this->appId;
  }

}
