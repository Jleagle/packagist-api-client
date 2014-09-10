<?php
use \Jleagle\Packagist;

class PackagistTest extends PHPUnit_Framework_TestCase
{

  public function testPackage()
  {

    $packagist = new Packagist('http://packagist.org');
    $package = $packagist->package('jleagle/packagist-api-wrapper');

    $this->assertArrayHasKey('dev-master', $package);
    $this->assertArrayHasKey('name', $package['dev-master']);

  }

  public function testSearch()
  {

    $packagist = new Packagist('http://packagist.org');
    $search = $packagist->search();

    $this->assertArrayHasKey('results', $search);
    $this->assertArrayHasKey('pages', $search);
    $this->assertEquals(ceil($search['total']/15), $search['pages']);

  }

  public function testAll()
  {

    $packagist = new Packagist('http://packagist.org');

    $all = $packagist->all();
    $this->assertTrue(in_array('jleagle/packagist-api-wrapper', $all));

    $filtered = $packagist->all('*zend*');
    $this->assertTrue(in_array('zendframework/zend-authentication', $filtered));
    $this->assertFalse(in_array('jleagle/packagist-api-wrapper', $filtered));



  }

  public function testPackageException()
  {
    $this->setExpectedException('Exception');

    $packagist = new Packagist('http://packagist.org');
    $package = $packagist->package('xxx');

  }


}
