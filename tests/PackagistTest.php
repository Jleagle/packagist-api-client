<?php
class PackagistTest extends PHPUnit_Framework_TestCase
{

  public function testPackagist()
  {

    $packagist = new \Jleagle\Packagist();

    $this->assertEquals('x', 'x');

  }

}
