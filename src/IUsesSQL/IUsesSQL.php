<?php
/**
 * Interface for class that interacts with the database. Contains one method which encapsulates all SQL requests.
 *
 * @package MedesCore
 */
interface IUsesSQL {
  public static function SQL($id=null);
}

