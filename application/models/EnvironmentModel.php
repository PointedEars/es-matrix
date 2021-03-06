<?php

require_once 'models/databases/es-matrix/tables/EnvironmentTable.php';

/**
 * Model class for test environments
 */
class EnvironmentModel extends \PointedEars\PHPX\Model
{
  /* ORM */
	/**
	 * (non-PHPdoc)
	 * @see \PointedEars\PHPX\Model::$_persistentTable
	 */
  protected static $_persistentTable = 'EnvironmentTable';

	/**
	 * (non-PHPdoc)
	 * @see \PointedEars\PHPX\Model::$_persistentId
	 */
  protected static $_persistentId = 'id';

  /**
	 * (non-PHPdoc)
	 * @see \PointedEars\PHPX\Model::$_persistentProperties
	 */
  protected static $_persistentProperties = array(
    'version_id', 'sortorder', 'name', 'user_agent', 'tested'
  );

  /**
   * Environment ID
   * @var int|null
   */
  protected $_id;

  /**
   * ID of the associated version of an implementation
   * @var int|null
   */
  protected $_version_id;

  /**
   * Sort order
   * @var int
   */
  protected $_sortorder;

  /**
   * @var string
   */
  protected $_name;

  /**
   * Value of the User-Agent header field
   * @var string
   */
  protected $_user_agent;

  /**
   * <code>true</code> if this environment has been tested,
   * <code>false</code> otherwise.
   * @var bool
   */
  protected $_tested;

  /**
   * @param int|null $value
   * @return EnvironmentModel
   */
  public function setId ($value)
  {
    $this->_id = ($value === null) ? $value : (int) $value;
    return $this;
  }

  /**
   * @return int|null
   */
  public function getId ()
  {
    return $this->_id;
  }

  /**
   * @param int|null $value
   * @return EnvironmentModel
   */
  public function setVersion_id ($value)
  {
    $this->_version_id = ($value === null) ? $value : (int) $value;
    return $this;
  }

  /**
   * @return int|null
   */
  public function getVersion_id ()
  {
    return $this->_version_id;
  }

  /**
   * @param int $value
   * @return EnvironmentModel
   */
  public function setSortorder ($value)
  {
    $this->_sortorder = (int) $value;
    return $this;
  }

  /**
   * @return int
   */
  public function getSortorder ()
  {
    return $this->_sortorder;
  }

  /**
   * @param string $name
   * @return EnvironmentModel
   */
  public function setName ($value)
  {
    $this->_name = trim((string) $value);
    return $this;
  }

  /**
   * @return string
   */
  public function getName ()
  {
    return $this->_name;
  }

  /**
   * @param string $value
   * @return EnvironmentModel
   */
  public function setUser_agent ($value)
  {
    $this->_user_agent = trim((string) $value);
    return $this;
  }

  /**
   * @return string
   */
  public function getUser_agent()
  {
    return $this->_user_agent;
  }

  /**
   * @param bool $value
   * @return EnvironmentModel
   */
  public function setTested ($value)
  {
    $this->_tested = (bool) $value;
    return $this;
  }

  /**
   * @return bool
   */
  public function getTested()
  {
    return $this->_tested;
  }

  /**
   * @return EnvironmentModel
   */
  public function findByUserAgent ()
  {
    $result = $this->persistentTable->select(
      null,
      array(
        'user_agent' => $this->user_agent,
      )
    );

    if ($result)
    {
      $this->map($result[0]);
    }
    else
    {
      $this->{self::$_persistentId} = null;
    }

    return $this;
  }
}