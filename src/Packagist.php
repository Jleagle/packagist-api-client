<?php
namespace Jleagle;

use \GuzzleHttp\Client as Guzzle;
use Jleagle\Models\Package;

class Packagist
{

  private $_apiUrl = '';

  // Cache
  private $_all = [];
  private $_packages = [];

  public function __construct($apiUrl = 'https://packagist.org')
  {
    $this->_apiUrl = $apiUrl;
  }

  public function package($package)
  {


  }

  public function search($search, $tags)
  {
    return http_build_query([$search]);
  }

  /**
   * @param string $filter
   *
   * @return Package[]
   */
  public function all($filter = null)
  {
    if (!$this->_all)
    {
      $request = $this->_request('packages/list.json');
      $this->_all = $request['packageNames'];
    }

    $return = [];
    foreach($this->_all as $package)
    {
      if ($filter && !fnmatch($filter, $package))
      {
        continue;
      }
      $return[] = new Package(['name' => $package]);
    }
    return $return;
  }

  private function _request($path)
  {
    $client = new Guzzle();
    $client->setDefaultOption('verify', false);
//    $client->setDefaultOption('verify', '/Websites/cacert.pem');

    $url = $this->_apiUrl.'/'.$path;

    $res = $client->get($url);

    if ($res->getStatusCode() != 200)
    {

    }

    $json = $res->json();

    return $json;

  }

  /**
   * @param $apiUrl
   *
   * @return $this
   */
  public function setApiUrl($apiUrl)
  {
    $this->_apiUrl = $apiUrl;
    return $this;
  }

}
?>



<?php
//if(!function_exists('fnmatch')) {
//  function fnmatch($pattern, $string) {
//    return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.'))."$#i", $string);
//  }
//}
?>