<!-- Developed by: Sameer Waseem -->
<!-- Email: sameerwaseem17@gmail.com -->
<!-- Website: http://sameerwasim.github.io/ -->

<?php

/**
 * Database Connection
 */
class Database
{

  private $host;
  private $username;
  private $password;
  public $conn;

  function __construct($host, $username, $password) {
    $this->host = $host;
    $this->username = $username;
    $this->password = $password;
    // Create Connection
    $this->conn = new PDO("mysql:host=$this->host", $this->username, $this->password);
  }

  /* Create Database -
    Checks if database with that name exists or not,
    if not than it creates one.
  */
  public function createDB($dbName) {
    $sql = "CREATE DATABASE IF NOT EXISTS $dbName";
    $sql = $this->conn->prepare($sql);
    if ($sql->execute())
      return true;
  }

  /* Use Database -
    Checks if database with that name exists or not,
    if not than it creates one and uses it.
  */
  public function useDB($dbName) {
    $sql = "USE $dbName";
    $sql = $this->conn->prepare($sql);
    if (!($sql->execute())) {
      $this->createDB($dbName);
      $sql->execute();
    }
  }

  /* Delete Database -
    Checks if database with that name exists or not,
    if it does than delete it.
  */
  public function dropDB($table) {
    $sql = "DROP DATABASE IF EXISTS $table";
    $sql = $this->conn->prepare($sql);
    if ($sql->execute())
      return true;
  }

  /* Create Table -
    Checks if table with that name exists or not,
    if not than it creates one.
  */
  public function createTbl($table, $schema, $debug=false) {
    $sql = "CREATE TABLE $table (".implode(',',$schema).")";
    if ($debug==true) {echo $sql;}
    $sql = $this->conn->prepare($sql);
    if ($sql->execute())
      return true;
  }

  /* Delete Table -
    Checks if table with that name exists or not,
    if it does than delete it.
  */
  public function dropTbl($table) {
    $sql = "DROP TABLE IF EXISTS $table";
    $sql = $this->conn->prepare($sql);
    if ($sql->execute())
      return true;
  }

  /* Insert to Table -
    Insert the values with columns name to the
    specified table and also make sure all values
    are safe before inserting.
  */
  public function insertTbl($table, $columns, $values) {
    $sql = "INSERT INTO `$table` (".implode(', ', $columns).")
            VALUES (:".implode(', :', $columns).")";
    $sql = $this->conn->prepare($sql);
    foreach ($columns as $key => $value) {
      $sql->bindParam(":$columns[$key]", $values[$key]);
    }
    if ($sql->execute()) {
      return $this->conn->lastInsertId();
    } else {
      return $sql->errorinfo();
    }
  }

  /* Update Columns -
    Update the values with columns name to the
    specified table and also make sure all values
    are safe before inserting.
  */
  public function updateColumns($table, $values, $where) {
    foreach ($values as $key => $value)
      $columns[] = "`$key` = '$value'";
    foreach ($where as $key => $value)
      $condition[] = "`$key` = '$value'";
    $sql = "UPDATE `$table` SET ".implode(', ', $columns)." WHERE ".implode('', $condition)."";
    $sql = $this->conn->prepare($sql);
    $sql->execute();
  }

  /* Update Columns -
    Update the values with columns name to the
    specified table and also make sure all values
    are safe before inserting wuth multiple conditions.
  */
  public function updateWhere($table, $values, $where) {
    foreach ($values as $key => $value)
      $columns[] = "`$key` = '$value'";
    $sql = "UPDATE `$table` SET ".implode(', ', $columns)." WHERE ".$where."";
    $sql = $this->conn->prepare($sql);
    $sql->execute();
  }

  /* Delete Columns -
    delete the values with specified
    table wuth multiple conditions.
  */
  public function deleteWhere($table, $where) {
    $sql = "DELETE FROM `$table` WHERE ".$where."";
    $sql = $this->conn->prepare($sql);
    $sql->execute();
  }

  /* Select from Table -
    selects all values from the all
    columns from the specified table.
  */
  public function selectAll($table) {
    $sql = "SELECT * FROM `$table`";
    $sql = $this->conn->prepare($sql);
    $sql->execute();
    return $sql->fetchAll(PDO::FETCH_ASSOC);
  }

  /* Select count from Table -
    selects all values from the all
    columns from the specified table
    and returns the count.
  */
  public function selectCount($table) {
    $sql = "SELECT count(*) as count FROM `$table`";
    $sql = $this->conn->prepare($sql);
    $sql->execute();
    return $sql->fetchAll(PDO::FETCH_ASSOC);
  }

  /* Select count from Table -
    selects all values from the all
    columns from the specified table
    and returns the count with the
    specified condition.
  */
  public function selectCountWhere($table, $where) {
    $sql = "SELECT count(*) as count FROM `$table` WHERE $where";
    $sql = $this->conn->prepare($sql);
    $sql->execute();
    return $sql->fetchAll(PDO::FETCH_ASSOC)[0]['count'];
  }

  /* Select from Table -
    selects all values from the all
    columns from the specified table
    with specified order.
  */
  public function selectOrder($table, $order) {
    $sql = "SELECT * FROM `$table` ORDER BY $order";
    $sql = $this->conn->prepare($sql);
    $sql->execute();
    return $sql->fetchAll(PDO::FETCH_ASSOC);
  }

  /* Select columns Table -
    selects all values from these
    specified columns from the specified table.
  */
  public function selectColumns($table, $columns) {
    $sql = "SELECT `".implode('`,`', $columns)."` FROM `$table`";
    $sql = $this->conn->prepare($sql);
    $sql->execute();
    return $sql->fetchAll(PDO::FETCH_ASSOC);
  }

  /* Select columns Table -
    selects limited values from these
    specified columns from the specified table
    up to specified limit.
  */
  public function selectLimit($table, $columns, $limit) {
    $sql = "SELECT `".implode('`,`', $columns)."` FROM `$table` LIMIT $limit";
    $sql = $this->conn->prepare($sql);
    $sql->execute();
    return $sql->fetchAll(PDO::FETCH_ASSOC);
  }

  /* Select with condition -
    selects all values from these
    specified columns from the specified
    table where your conditions satisfies.
  */
  public function selectWhere($table, $columns, $where) {
    $sql = "SELECT `".implode('`,`', $columns)."` FROM `$table` WHERE $where";
    $sql = $this->conn->prepare($sql);
    $sql->execute();
    return $sql->fetchAll(PDO::FETCH_ASSOC);
  }

  /* Select with condition -
    selects all values from
    all these columns from the specified
    table where your conditions satisfies.
  */
  public function selectAllWhere($table, $where) {
    $sql = "SELECT * FROM `$table` WHERE $where";
    $sql = $this->conn->prepare($sql);
    $sql->execute();
    return $sql->fetchAll(PDO::FETCH_ASSOC);
  }

  /* Sum with condition -
    selects all values from the
    specified column from the specified
    table where your conditions satisfies
    and return the sum of that column.
  */
  public function selectSum($table, $column, $where) {
    $sql = "SELECT sum($column) as `total` FROM `$table` WHERE $where";
    $sql = $this->conn->prepare($sql);
    $sql->execute();
    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    return $result[0]['total'];
  }

  /* ID Value -
    selects id value from
    the foreign table id falls.
  */
  public function idValue($table, $column, $id) {
    $sql = "SELECT `$column` FROM `$table` WHERE `id` = '$id'";
    $sql = $this->conn->prepare($sql);
    $sql->execute();
    $value = $sql->fetch(PDO::FETCH_ASSOC);
    return $value[$column];
  }


}

$database = new Database(dbHOST, dbUSERNAME, dbPASSWORD);
$database->useDB(database); # Specify what database to use here.

?>
