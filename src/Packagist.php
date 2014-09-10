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

  public function __construct($apiUrl = 'http://packagist.org')
  {
    $this->_apiUrl = $apiUrl;
  }

  public function package($author, $package = null)
  {

  }

  public function search($search, $tags = [], $page = 1)
  {
    $query = http_build_query([
      'q' => $search,
      'tags' => $tags,
      'page' => $page
    ]);
    $request = $this->_request('search.json?'.$query);

    $return = [];
    foreach($request['results'] as $package)
    {
      $return[] = new Package($package);
    }

    return [
      'results' => $return,
      'total' => $request['total'],
      'pages' => ceil($request['total'] / 15),
    ];
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
    $client->setDefaultOption('verify', true);

    $url = $this->_apiUrl.'/'.$path;
    $res = $client->get($url);

    if ($res->getStatusCode() != 200)
    {
      // Exception
    }

    return $res->json();
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
