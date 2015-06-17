<?php
namespace Jleagle;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Packagist
{
  protected $_apiUrl;

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
        'tags' => (array)$tags,
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
    $request = $this->_request('packages/list.json');

    $return = [];
    if(is_array($request['packageNames']))
    {
      foreach($request['packageNames'] as $package)
      {
        if(!$filter || fnmatch($filter, $package, FNM_CASEFOLD))
        {
          $return[] = $package;
        }
      }
    }

    return $return;
  }

  public function latestAdded()
  {
    $rss = \Feed::loadRss($this->_apiUrl . '/feeds/packages.rss');
    return $this->_handleRss($rss);
  }

  public function latestReleased()
  {
    $rss = \Feed::loadRss($this->_apiUrl . '/feeds/releases.rss');
    return $this->_handleRss($rss);
  }

  protected function _handleRss(\Feed $rss)
  {
    $return = [];
    foreach($rss->item as $item)
    {
      $return[] = [
        'title'       => (string)$item->title,
        'description' => (string)$item->description,
        'link'        => (string)$item->link,
        'guid'        => (string)$item->guid,
        'author'      => (string)$item->author,
        'creator'     => (string)$item->{'dc:creator'},
        'comments'    => (string)$item->{'slash:comments'},
        'time'        => (string)$item->timestamp,
      ];
    }

    return $return;
  }


  /**
   * @param string $path
   *
   * @return array
   * @throws \Exception
   */
  protected function _request($path)
  {
    $client = new Client();

    $url = $this->_apiUrl . '/' . $path;
    $res = $client->get($url);

    if($res->getStatusCode() != 200)
    {
      throw new \Exception('Packagist did not respond correctly.');
    }

    return $res->json();
  }
}
