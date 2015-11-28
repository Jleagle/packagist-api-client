<?php
namespace Jleagle;

use Illuminate\Support\Collection;
use Jleagle\CurlWrapper\Curl;
use Jleagle\CurlWrapper\Enums\ContentTypeEnum;
use Jleagle\CurlWrapper\Exceptions\CurlException;
use Jleagle\Exceptions\PackagistException;
use Jleagle\Exceptions\PackagistPackageNotFoundException;
use Jleagle\Exceptions\PackagistServiceHookException;
use Vinelab\Rss\Rss;

class Packagist
{
  protected static $_apiUrl = 'https://packagist.org';

  /**
   * @param string $url
   */
  public static function setApiUrl($url)
  {
    static::$_apiUrl = $url;
  }

  /**
   * @param string $author
   * @param string $package
   *
   * @return array
   *
   * @throws PackagistPackageNotFoundException
   */
  public static function package($author, $package = null)
  {
    if(!$package)
    {
      $explode = explode('/', $author, 2);
      if(count($explode) != 2)
      {
        throw new PackagistPackageNotFoundException('No package specified.');
      }
      list($author, $package) = $explode;
    }

    $fullName = $author . '/' . $package;
    try
    {
      $request = static::_get('packages/' . $fullName . '.json');
    }
    catch(PackagistException $e)
    {
      throw new PackagistPackageNotFoundException('Package does not exist.');
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
  public static function search($search = '', $tags = [], $page = 1)
  {
    $query = http_build_query(
      [
        'q'    => $search,
        'tags' => (array)$tags,
        'page' => $page
      ]
    );

    $request = static::_get('search.json?' . $query);
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
  public static function all($filter = null)
  {
    $request = static::_get('packages/list.json');

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

  /**
   * @param string $username
   * @param string $apiToken
   * @param string $packageUrl
   *
   * @return bool
   *
   * @throws PackagistServiceHookException
   */
  public static function serviceHook($username, $apiToken, $packageUrl)
  {
    $url = static::$_apiUrl . '/api/update-package?username=' . $username . '&apiToken=' . $apiToken;

    $data = json_encode(
      [
        'repository' => [
          'url' => $packageUrl
        ]
      ]
    );

    $json = Curl
      ::post($url, $data)
      ->setContentType(ContentTypeEnum::JSON)
      ->run()
      ->getJson();

    if($json['status'] == 'error')
    {
      throw new PackagistServiceHookException($json['message']);
    }

    return true;
  }

  /**
   * @return Collection
   */
  public static function latestAdded()
  {
    $rss = new Rss();
    $feed = $rss->feed(static::$_apiUrl . '/feeds/packages.rss');
    return $feed->articles();
  }

  /**
   * @return Collection
   */
  public static function latestReleased()
  {
    $rss = new Rss();
    $feed = $rss->feed(static::$_apiUrl . '/feeds/releases.rss');
    return $feed->articles();
  }

  /**
   * @param string $path
   *
   * @return array
   *
   * @throws PackagistException
   */
  protected static function _get($path)
  {
    $exception = new PackagistException('Packagist did not respond correctly.');

    try
    {
      $response = Curl::get(static::$_apiUrl . '/' . $path)->run();
    }
    catch(CurlException $e)
    {
      throw $exception;
    }

    if($response->getHttpCode() != 200)
    {
      throw $exception;
    }

    return $response->getJson();
  }
}
