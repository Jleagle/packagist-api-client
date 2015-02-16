<?php
namespace Jleagle;

use \GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;

class Packagist
{

  private $_apiUrl = '';
  private $_all = [];

  /**
   * @param string $apiUrl
   */
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
    if(!$package)
    {
      $explode = explode('/', $author, 2);
      if(count($explode) != 2)
      {
        throw new \Exception('No package specified.');
      }
      list($author, $package) = $explode;
    }

    $fullName = $author . '/' . $package;
    try
    {
      $request = $this->_request('packages/' . $fullName . '.json');
    }
    catch(ClientException $e)
    {
      throw new \Exception('Package does not exist.');
    }
    $package = $request['package'];

    return $package;
  }

  /**
   * @param string   $search
   * @param string[] $tags
   * @param int      $page
   *
   * @return array
   */
  public function search($search = '', $tags = [], $page = 1)
  {
    $query = http_build_query(
      [
        'q'    => $search,
        'tags' => $tags,
        'page' => $page
      ]
    );

    $request = $this->_request('search.json?' . $query);
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
    if(!$this->_all)
    {
      $request = $this->_request('packages/list.json');
      $this->_all = $request['packageNames'];
    }

    $return = [];
    if(is_array($this->_all))
    {
      foreach($this->_all as $package)
      {
        if(!$filter || fnmatch($filter, $package, FNM_CASEFOLD))
        {
          $return[] = $package;
        }
      }
    }

    return $return;
  }

  /**
   * @param string $path
   *
   * @return array
   * @throws \Exception
   */
  private function _request($path)
  {
    $client = new Guzzle();
    $client->setDefaultOption('verify', true);

    $url = $this->_apiUrl . '/' . $path;
    $res = $client->get($url);

    if($res->getStatusCode() != 200)
    {
      throw new \Exception('Packagist did not respond correctly.');
    }

    return $res->json();
  }
}
