<?php
namespace Jleagle\Models;

class Package
{

  private $_name = '';
  private $_description = '';
  private $_url = '';
  private $_downloads = '';
  private $_favers = '';

  function __construct(array $data = [])
  {

    // Hydrate
    foreach($data as $field => $value)
    {
      if (property_exists($this, '_'.$field))
      {
        $this->{'_'.$field} = $value;
      }
    }
  }

  /**
   * @return mixed
   */
  public function getName()
  {
    return $this->_name;
  }

  /**
   * @return mixed
   */
  public function getDescription()
  {
    return $this->_description;
  }

  /**
   * @return mixed
   */
  public function getUrl()
  {
    return $this->_url;
  }

  /**
   * @return mixed
   */
  public function getDownloads()
  {
    return $this->_downloads;
  }

  /**
   * @return mixed
   */
  public function getFavers()
  {
    return $this->_favers;
  }
}
