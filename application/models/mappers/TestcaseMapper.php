<?php

require_once '../lib/features.class.php';

require_once 'models/TestcaseModel.php';
require_once 'models/FeatureModel.php';
require_once 'models/mappers/ImplementationMapper.php';
require_once 'models/mappers/EnvironmentMapper.php';

/**
 * Mapper class for testcases
 *
 * @author Thomas Lahn
 */
class TestcaseMapper extends \PointedEars\PHPX\Db\Mapper
{
  private static $_instance = null;

  /*
   * (non-PHPDoc) see Mapper::$_table
   */
  protected $_table = 'TestcaseTable';

  protected function __construct()
  {
    /* Singleton */
  }

  /**
   * Returns the instance of this mapper
   *
   * @return TestcaseMapper
   */
  public static function getInstance()
  {
    if (is_null(self::$_instance))
    {
      self::$_instance = new self();
    }

    return self::$_instance;
  }

	/**
   * Saves a testcase in the testcase table
   *
   * @param TestcaseModel $testcase
   */
  public function save(TestcaseModel $testcase)
  {
    $id = $testcase->id;
    $data = array(
      'feature_id'  => $testcase->feature_id,
      'title'       => $testcase->title,
      'code'        => $testcase->code,
      'quoted'      => $testcase->quoted,
      'alt_type'    => $testcase->alt_type
    );

    if (defined('DEBUG') && DEBUG > 0)
    {
      debug($data);
    }

    $this->getDbTable()->updateOrInsert($data, array('id' => $id));
  }

  /**
   * Saves the testcases for a feature
   *
   * @param FeatureModel $feature
   */
  public function saveForFeature (FeatureModel $feature)
  {
    $table = $this->getDbTable();
    $table->beginTransaction();

    /*
     * NOTE: Must _delete_ saved testcases for that feature to avoid
     * invalid results (ON DELETE CASCADE)
     */
    $table->delete(null, array('feature_id' => $feature->id));

    /* Assign new testcases to feature */
    $testcases = $feature->testcases;

    if ($testcases)
    {
      foreach ($testcases as $testcase)
      {
        /* DEBUG */
        if (defined('DEBUG') && DEBUG > 0)
        {
          debug($testcase);
        }

        $testcase->insert();
      }
    }

    $success = $table->commit();
    if ($success)
    {
      if ($testcases)
      {
        $feature->testcases = $testcases;
      }

      EnvironmentMapper::getInstance()->setAllUntested();
    }
    else
    {
      $table->rollBack();
    }

    return $success ? $feature : null;
  }

  public function importAll(FeatureList $featureList)
  {
    $testcases = array();

    foreach ($featureList->items as $key => $featureData)
    {
      $versions = $featureData->versions;
      $code = isset($versions['']) ? $versions[''] : null;
      if (!is_null($code))
      {
        $testcase = new TestcaseModel(array(
        	'id' => $key + 1,
          'feature_id' => $key + 1,
        ));

        $testcase->setCode($code, true);

        $this->save($testcase);

        $testcases[] = $testcase;
      }
    }

//     function mapper($el)
//     {
//       return strlen(trim($el->code));
//     }
//     debug(max(array_map('mapper', $testcases)));

//     debug($testcases);
  }

  /**
   * Copies the testcases for a feature from another
   *
   * @param int $sourceId
   * @param FeatureModel $target
   */
  public function copy($sourceId, $target)
  {
    $testcases = $target->testcases;
    if (!is_array($testcases))
    {
      $testcases = array();
    }

    $testcases = array_merge($testcases, $this->findByFeatureId($sourceId));
    foreach ($testcases as $key => $value)
    {
      if (!is_int($key))
      {
        unset($testcases[$key]);
      }
    }

    $target->testcases = $testcases;
  }

  /**
   * Returns the testcases for a feature specified by its ID
   *
   * @param int $id
   * @return array
   */
  public function findByFeatureId($id)
  {
    $resultSet = $this->getDbTable()->select(null, array('feature_id' => $id));
    $testcases = array();
    foreach ($resultSet as $row)
    {
      $testcase = new TestcaseModel($row);
//         ->setCreated($row->created)
      ;
      $testcases[] = $testcase;
    }

    if (defined('DEBUG') && DEBUG > 0)
    {
      debug(array('testcases' => $testcases));
    }

    return $testcases;
  }
}

