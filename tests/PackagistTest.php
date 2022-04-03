<?php
use Jleagle\Packagist;
use PHPUnit\Framework\TestCase;

class PackagistTest extends TestCase
{

  /**
   * @group medium
   *
   * @throws Exception
   */
  public function testPackage()
  {
    $packagist = new Packagist();
    $package = $packagist->package('jleagle/packagist-api-client');

    $this->assertArrayHasKey('versions', $package);
    $this->assertArrayHasKey('name', $package);
  }

  /**
   * @group medium
   */
  public function testSearch()
  {
    $packagist = new Packagist();
    $search = $packagist->search('packagist-api-client');

    $this->assertArrayHasKey('results', $search);
    $this->assertArrayHasKey('pages', $search);
    $this->assertEquals(ceil($search['total'] / 15), $search['pages']);
  }

  /**
   * @group medium
   */
  public function testAll()
  {
    $packagist = new Packagist();

    $all = $packagist->all();
    $this->assertContains('jleagle/packagist-api-client', $all);

    $filtered = $packagist->all('*zend*');
    $this->assertContains('zendframework/zend-authentication', $filtered);
    $this->assertNotContains('jleagle/packagist-api-client', $filtered);
  }

  /**
   * @group medium
   *
   * @throws Exception
   */
  public function testPackageException()
  {
    $this->expectException('Exception');

    $packagist = new Packagist();
    $package = $packagist->package('xxx');
  }
}
