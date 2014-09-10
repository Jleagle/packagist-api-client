<?php
namespace Jleagle;

use \GuzzleHttp\Client as Guzzle;

class Packagist
{

  private $_apiUrl = '';

  public function __construct($apiUrl = 'https://packagist.org')
  {
    $this->_apiUrl = $apiUrl;
  }

  /**
   * @param string $author
   * @param string $package
   *
   * @return array
   * @throws \Exception
   */
  public function package($author, $package = null)
  {
    if (!$package)
    {
      list($author, $package) = explode('/', $author, 2);
    }

    if (!$author || !$package)
    {
      throw new \Exception('No package specified');
    }

    $fullName = $author.'/'.$package;
    $request = $this->_request('p/'.$fullName.'.json');
    $package = $request['packages'][$fullName];

    return $package;
  }

  /**
   * @param string   $search
   * @param string[] $tags
   * @param int      $page
   *
   * @return array
   * @throws \Exception
   */
  public function search($search = '', $tags = [], $page = 1)
  {
    if (!$search && !$tags && !$page)
    {
      throw new \Exception('Need to give at least one parameter');
    }

    $query = http_build_query([
        'q' => $search,
        'tags' => $tags,
        'page' => $page
      ]);

    $request = $this->_request('search.json?'.$query);
    $request['pages'] = (int)ceil($request['total'] / 15);

    return $request;
  }

  /**
   * Supports wildcards and ranges. eg.
   * *z[en]d*
   *
   * @param string $filter
   *
   * @return string[]
   */
  public function all($filter = null)
  {
    $return = [];
    $request = $this->_request('packages/list.json');
    foreach($request['packageNames'] as $package)
    {
      if ($filter && !fnmatch($filter, $package))
      {
        continue;
      }
      $return[] = $package;
    }
    return $return;
  }

  /**
   * @param string $path
   *
   * @return string
   * @throws \Exception
   */
  private function _request($path)
  {
    $client = new Guzzle();
    $client->setDefaultOption('verify', true);

    $url = $this->_apiUrl.'/'.$path;
    $res = $client->get($url);

    if ($res->getStatusCode() != 200)
    {
      throw new \Exception('Packagist did not respond correctly.');
    }

    return $res->json();
  }

}
