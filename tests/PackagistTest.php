<?php
use \Jleagle\Packagist;

class PackagistTest extends PHPUnit_Framework_TestCase
{

  /**
   * @group medium
   *
   * @throws Exception
   */
  public function testPackage()
  {
    $packagist = new Packagist('http://packagist.org');
    $package = $packagist->package('jleagle/packagist-api-client');

    $this->assertArrayHasKey('versions', $package);
    $this->assertArrayHasKey('name', $package);
  }

  /**
   * @group medium
   */
  public function testSearch()
  {
    $packagist = new Packagist('http://packagist.org');
    $search = $packagist->search();

    $this->assertArrayHasKey('results', $search);
    $this->assertArrayHasKey('pages', $search);
    $this->assertEquals(ceil($search['total'] / 15), $search['pages']);
  }

  /**
   * @group medium
   */
  public function testAll()
  {
    $packagist = new Packagist('http://packagist.org');

    $all = $packagist->all();
    $this->assertTrue(in_array('jleagle/packagist-api-client', $all));

    $filtered = $packagist->all('*zend*');
    $this->assertTrue(in_array('zendframework/zend-authentication', $filtered));
    $this->assertFalse(in_array('jleagle/packagist-api-client', $filtered));
  }

  /**
   * @group medium
   *
   * @throws Exception
   */
  public function testPackageException()
  {
    $this->setExpectedException('Exception');

    $packagist = new Packagist('http://packagist.org');
    $package = $packagist->package('xxx');
  }
}
