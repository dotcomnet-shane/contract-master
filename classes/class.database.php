<?php

class database
{
    /**
     * Created by PhpStorm.
     * User: ali
     * Date: 2021/07/27
     *
     * Description: This class will instantiate the db connection and be used for all db related functions.
     *              Only valid for use with mysql databases.
     *              Every query, except for special and support functions, will make use of PDO queries
     *              Configuration can be set in the appropriate config and settings files or instantiate the class with the required parameters
     */

    /**
     * The mysqli connection
     *
     * @var PDO
     * @access private
     */
    private static $_con;

    /**
     * @var string  The username of the user of the database we are connecting to
     */
    private $username;
    /**
     * @var string  The password for the user of the database we are connecting to
     */
    private $password;
    /**
     * @var string The IP Address / Hostname of the database server we are connecting to
     */
    private $address;
    /**
     * @var string The name of the database we are connecting to
     */
    private $database;
    /**
     * @var int The connection port of the database we are connecting to
     */
    private $port = 3306;


    /**
     * Required Functions
     */

    /**
     * database constructor.
     * This function will assign the contents on $dbParams to the appropriate object variables.
     *
     * @param $dbParams
     *
     * @return void
     */
    public function __construct($dbParams)
    {
        //Check all required params hae been given
        $requiredFields = array('username', 'password', 'address', 'database');
        $requirements = functions::checkRequirements($requiredFields, $dbParams);

        if(!$requirements['success'])
        {
            //We are missing required fields
            trigger_error('Not all required params have been set! - CDB1');
            return;
        }

        foreach($dbParams as $field => $value)
        {
            $this->{$field} = $value;
        }

        $result = $this->connect();

        if(!$result)
        {
            //We failed to connect to the database
            trigger_error('Unable to successfully connect! - CDB2');
        }
    }

    /**
     * This function will set the object connection variable to null
     */
    public function __destruct()
    {
        self::$_con = null;
    }

    /**
     * This function will create the connection to the mysql db using the PDO constructor
     *
     * @return bool The success of the function
     */
    private function connect()
    {
        //Set mysqli error reporting
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try
        {
            self::$_con = new PDO("mysql:host=" . $this->address . ";port=" . $this->port . ";dbname=" . $this->database . ';charset=utf8', $this->username, $this->password);

            self::$_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        }
        catch(mysqli_sql_exception $ex)
        {
            error_log("Unable to connect to database [$this->database] with the error [$ex]");
        }
        catch(exception $exception)
        {
            error_log("Unknown error occurred while trying to connect to DB [$this->database] [$exception]");
        }

        return false;
    }

    /**
     * @return PDO
     */
    public function get_connection()
    {
        return self::$_con;
    }

    /**
     * This function will execute the given query with it's parameters
     *
     * @param string $query The query to be executed
     * @param array $params optional The array of params to be executed with the query
     * @param bool $logger
     * @param string $statementReturnMode
     *
     * @return  array|bool|string
     */
    private function executePDO($query, $params = array(), $logger = DEBUG, $statementReturnMode = PDO::FETCH_ASSOC)
    {
        $con = $this->get_connection();

        $stmt = $con->prepare($query);

        if($logger)
        {
            error_log($query);
        }

        try
        {
            if($stmt->execute($params))
            {
                $callingFunction = functions::getCallingFunction();
                $returnInsertFunctions = array('replace', 'insert', 'autoReplace', 'autoMultiInsert', 'autoImport');

                if($returnInsertFunctions == $callingFunction)
                {
                    return $con->lastInsertId();
                }

                $result = $stmt->fetchAll($statementReturnMode);

                if($logger)
                {
                    error_log(json_encode($result));
                }

                if(!empty($result))
                {
                    return $result;
                }
            }

            return false;
        }
        catch(mysqli_sql_exception $ex)
        {
            $paramString = $this->createParamsString($params);
            error_log("Invalid Query: [$query] Params: [$paramString] Error: $ex");
        }
        catch(exception $exception)
        {
            $paramString = $this->createParamsString($params);
            error_log("Unknown error occurred! Invalid Query: [$query] Params: [$paramString] Error: $exception");
        }

        return false;
    }

    /**
     * This function will convert the given formatted string of values into a PDO params array
     *
     * @param $data $values The values to convert from
     *
     * @return array
     */
    private function convertArrayToPDOParams($data)
    {
        $values = '';
        $cols = '';
        $cnt = 1;
        $params = array();

        foreach($data as $key => $value)
        {
            if(is_array($value))
            {
                foreach($value as $colName => $record)
                {
                    if($key == 0)
                    {
                        $cols .= "`$colName`, ";;
                    }

                    $values .= "$colName = :p$cnt, ";

                    $params[':p' . $cnt] = $record;
                    $cnt++;
                }
            }
            else
            {
                $cols .= "$key, ";
                $values .= "$key = :p$cnt, ";

                if($value === null)
                {
                    $params[':p' . $cnt] = null;
                }
                else
                {
                    $params[':p' . $cnt] = $value;
                }

                $cnt++;
            }
        }

        //Remove trailing comma and space
        $values = substr($values, 0, -2);
        $cols = substr($cols, 0, -2);

        return array('values' => $values, 'cols' => $cols, 'params' => $params);
    }

    /**
     * This function will create a params string using the provided params array
     *
     * @param array $params
     *
     * @return string
     */
    private function createParamsString($params)
    {
        $paramString = '';

        foreach($params as $key => $value)
        {
            $paramString .= "$key => $value, ";
        }

        return substr($paramString, 0, -2);
    }


    /**
     * Common Functions
     */

    /**
     * This function will insert provided values, in arrays, into the specified table or pass the values to update if primary is found
     *
     * Params:
     *
     * @param string $table The table to be inserted into
     * @param array $data   The column names and their values
     * @param bool $logger  optional. Error_logs the query for debugging purposes.
     *
     * @return mixed The function will return the last inserted id on success or return false and trigger an error on failure
     */
    public function autoImport($table, $data, $params = array(), $logger = false)
    {
        $convertedValues = $this->convertArrayToPDOParams($data);

        $params = $params ? $params : $convertedValues['params'];

        $result = $this->insert($table, $convertedValues['cols'], $convertedValues['values'], $params, $logger);

        return $result;
    }

    /**
     * This function will insert provided values, in arrays, into the specified table or pass the values to update if primary is found
     *
     * Params:
     *
     * @param string $table The table to be inserted into
     * @param array $data   The column names and their values
     * @param bool $logger  optional. Error_logs the query for debugging purposes.
     *
     * @return mixed The function will return the last inserted id on success or return false and trigger an error on failure
     */
    public function autoInsert($table, $data, $primary = 'id', $params = array(), $logger = DEBUG)
    {
        $result = '';

        if(isset($data[$primary]))
        {
            $id = $data[$primary];
            unset($data[$primary]);

            $convertedValues = $this->convertArrayToPDOParams($data);

            $where = "$primary = $id";

            $params = $params ? $params : $convertedValues['params'];

            $result = $this->update($table, $convertedValues['values'], $where, $params, $logger);
        }
        else
        {
            $convertedValues = $this->convertArrayToPDOParams($data);

            $params = $params ? $params : $convertedValues['params'];

            $result = $this->insert($table, $convertedValues['cols'], $convertedValues['values'], $params, $logger);
        }

        return $result;
    }

    /**
     * This is the same as autoInsert but does not create an update, only insert, so that we are able to specify the primary id
     *
     * This function will insert provided values, in arrays, into the specified table.
     *
     * Params:
     *
     * @param $table   string    The table to be inserted into
     * @param $data    array     The column names and their values
     * @param $logger  bool        optional. Error_logs the query for debugging purposes.
     *
     * @return mixed The function will return the last inserted id on success or return false and trigger an error on failure
     */
    public function autoJustInsert($table, $data, $logger = DEBUG)
    {
        $convertedValues = $this->convertArrayToPDOParams($data);

        return $this->insert($table, $convertedValues['cols'], $convertedValues['values'], $convertedValues['params'], $logger);
    }

    public function autoMagicReplace($table, $data, $logger = false)
    {
        $convertedValues = $this->convertArrayToPDOParams($data);
        $onUpdate = '';

        foreach($data as $col => $val)
        {
            $onUpdate .= "$col = VALUES($col), ";
        }

        //Remove trailing space and comma
        $onUpdate = substr($onUpdate, 0, -2);

        $query = "INSERT INTO $table (" . $convertedValues['cols'] . ') VALUES (' . $convertedValues['values'] . ')';

        $query .= " ON DUPLICATE KEY UPDATE $onUpdate";

        $result = $this->executePDO($query, $convertedValues['params'], $logger);

        return $result;
    }

    public function autoMultiDelete($table, $dataset, $logger = DEBUG)
    {
        $params = array();

        foreach($dataset as $data)
        {
            foreach($data as $key => $value)
            {
                $where = "$key=:$key";
                $params[":$key"] = $value;

                $this->delete($table, $where, $params, $logger);
            }
        }

        return true;
    }

    /**
     * This function will insert multiple provided values, in an array, into the specified table.
     *
     * Params:
     *
     * @param string $table The table to be inserted into
     * @param array $data   The column names and their values
     * @param bool $logger  optional. Error_logs the query for debugging purposes.
     *
     * @return string|bool The function will return the last inserted id as a string on success or return false on failure
     */
    public function autoMultiInsert($table, $data, $params = array(), $logger = DEBUG)
    {
        $convertedValues = $this->convertArrayToPDOParams($data);

        $query = "INSERT INTO $table (" . $convertedValues['cols'] . ') VALUES ' . $convertedValues['values'];

        return $this->executePDO($query, $convertedValues['params'], $logger);
    }

    public function autoMultiMagicReplace($table, $data, $logger = false)
    {
        foreach($data as $dataset)
        {
            $check = $this->autoMagicReplace($table, $dataset, $logger);

            if(!$check)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * This function will perform a replace with multiple provided values, in an array, into the specified table.
     *
     * Params:
     *
     * @param string $table The table to be replaced into
     * @param array $data   The column names and their values
     * @param bool $logger  optional. Error_logs the query for debugging purposes.
     *
     * @return string|bool The function will return the last inserted id as a string on success or return false on failure
     */
    public function autoMultiReplace($table, $data, $params = array(), $logger = DEBUG)
    {
        $convertedValues = $this->convertArrayToPDOParams($data);

        $query = "REPLACE INTO $table (" . $convertedValues['cols'] . ') VALUES ' . $convertedValues['values'];

        return $this->executePDO($query, $convertedValues['params'], $logger);
    }

    /**
     * This function will insert or update provided values, in arrays, into the specified table.
     *
     * Params:
     *
     * @param string $table The table to be inserted / updated into
     * @param array $data   The column names and their values
     * @param bool $logger  optional. Error_logs the query for debugging purposes.
     *
     * @return string|bool The function will return the last inserted id as a string on success or return false  on failure
     */
    public function autoReplace($table, $data, $logger = DEBUG)
    {
        $convertedValues = $this->convertArrayToPDOParams($data);

        $query = "REPLACE INTO $table (" . $convertedValues['cols'] . ') VALUES (' . $convertedValues['values'] . ')';

        return $this->executePDO($query, $convertedValues['params'], $logger);
    }

    /**
     * This function will update any table entries, in arrays, matching the where statement provided (column + value)
     *
     * Params:
     *
     * @param $table   string    The table to be inserted into
     * @param $data    array     The column names and their values
     * @param $where   string    The where statement to identify row to be updated
     * @param $logger  bool        optional. Error_logs the query for debugging purposes.
     *
     * @return mixed The function will return true on success or return false and trigger an error on failure
     */
    public function autoUpdate($table, $data, $where, $params = array(), $logger = DEBUG)
    {
        $convertedValues = $this->convertArrayToPDOParams($data);

        $query = "UPDATE $table SET " . $convertedValues['values'];

        if($where)
        {
            $query .= " WHERE $where";
        }

        $params = $params ? $params : $convertedValues['params'];

        return $this->executePDO($query, $params, $logger);
    }

    public function callProcedure($procedureName, $params = '', $logger = DEBUG)
    {
        $query = 'CALL ' . $procedureName;

        if($params)
        {
            $query .= "($params)";
        }
        else
        {
            $query .= '()';
        }

        return $this->executePDO($query);
    }

    /**
     * This function will delete any table entries matching the provided where statement (column + value)
     *
     * Params:
     *
     * @param string $table The table to be deleted from
     * @param string $where
     * @param array $params
     * @param bool $logger  optional. Error_logs the query for debugging purposes.
     *
     * @return bool     The function will return the success of the function as a bool
     */
    public function delete($table, $where, $params = array(), $logger = DEBUG)
    {
        $query = "DELETE FROM $table WHERE $where";

        $dbRes = $this->executePDO($query, $params, $logger);

        if($dbRes)
        {
            return true;
        }

        return false;
    }

    /**
     * This function will insert provided values into the specified table.
     *
     * Params:
     *
     * @param string $table  The table to be inserted into
     * @param string $cols   The columns to be inserted into, comma separated
     * @param string $values The values to be inserted, comma separated
     * @param bool $logger   optional. Error_logs the query for debugging purposes.
     *
     * @return string|bool     The function will return the last inserted id on success or return false on failure
     */
    public function insert($table, $cols, $values, $params = array(), $logger = DEBUG)
    {
        $query = "INSERT INTO $table ($cols) VALUES ($values)";

        return $this->executePDO($query, $params, $logger);
    }

    /**
     * This function will replace a row in the provided table matching the provided where statement (column + value)
     *
     * Params:
     *
     * @param $table              string    The table to replace into
     * @param $cols               string    The columns to be replace
     * @param $values             string    The value(s) to be replaced with.
     *                            Multiple values may be passed but must be comma separated.
     * @param $logger             bool        optional. Error_logs the query for debugging purposes.
     *
     * @return string|bool     The function will return the row id of the updated row as a string on success
     *                          or return false on failure
     */
    public function replace($table, $cols, $values, $params = array(), $logger = DEBUG)
    {
        $query = "REPLACE INTO $table ($cols) VALUES ($values)";

        $dbRes = $this->executePDO($query, $params, $logger);

        if($dbRes)
        {
            return $dbRes;
        }

        return false;
    }

    /**
     * This function is a standard sql select.
     *
     * @param string $columns The desired column(s). i.e. 'fname' || 'fname, lname'
     * @param string $table   The table to be queried.
     * @param string $where   optional where clause to include in query.
     * @param array $params
     * @param bool $logger    optional. Error_logs the query for debugging purposes.
     *
     * @return array
     */
    public function select($columns, $table, $where = '', $params = array(), $logger = DEBUG)
    {
        $con = $this->get_connection();

        $query = "SELECT $columns FROM $table";

        if($where)
        {
            $query .= " WHERE $where";
        }

        $dbRes = $this->executePDO($query, $params);

        if($dbRes)
        {
            return $dbRes;
        }

        return array();
    }

    /**
     * This function if a standard sql select but will only return the first row.
     *
     * Params:
     *
     * @param string $columns The desired column(s). i.e. 'fname' || 'fname, lname'
     * @param string $table   The table to be queried.
     * @param string $where   optional where clause to include in query.
     * @param bool $logger    optional. Error_logs the query for debugging purposes.
     *
     * @return array
     */
    public function selectRow($columns, $table, $where = '', $params = array(), $logger = DEBUG)
    {
        $query = "SELECT $columns FROM $table";

        if($where)
        {
            $query .= " WHERE $where";
        }

        $query .= ' LIMIT 1';

        $dbResult = $this->executePDO($query, $params, $logger);

        if(!empty($dbResult))
        {
            return $dbResult[0];
        }

        return null;
    }

    /**
     * This function queries the database for a specific field and returns only one entry.
     *
     * Params:
     *
     * @param string $field field in table to be queried.
     * @param string $table table to be queried.
     * @param string $where optional where clause to include in query.
     * @param bool $logger  optional. Error_logs the query for debugging purposes.
     *
     * @return string
     */
    public function selectScalar($field, $table, $where = '', $params = array(), $logger = DEBUG)
    {
        $query = "SELECT $field FROM $table";

        if($where)
        {
            $query .= " WHERE $where";
        }

        $query .= ' LIMIT 1';

        $dbResult = $this->executePDO($query, $params, $logger);

        if(!empty($dbResult))
        {
            if(($curPos = strpos($field, ' as ')) !== false)
            {
                $field = substr($field, $curPos + 4);
            }
            elseif(($curPos = strpos($field, '.')) !== false)
            {
                $field = substr($field, $curPos + 1);
            }

            if(array_key_exists($field, $dbResult[0]))
            {
                return $dbResult[0][$field];
            }
        }

        return false;
    }

    /**
     * This function will update any table entries matching the where statement provided (column + value)
     *
     * Params:
     *
     * @param string $table  The table to be updated
     * @param string $values The values to be updated
     * @param string $where  The where statement to identify row to be updated
     * @param bool $logger   optional. Error_logs the query for debugging purposes.
     *
     * @return bool     The function will return true on success or return false on failure
     */
    public function update($table, $values, $where, $params = array(), $logger = DEBUG)
    {
        $query = "UPDATE $table SET $values";

        if($where)
        {
            $query .= " WHERE $where";
        }

        return $this->executePDO($query, $params, $logger);
    }


    /**
     * Special Functions
     */

    public function doSql($query, $logger = DEBUG)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $con = mysqli_connect($this->address, $this->username, $this->password, $this->database, $this->port);

        if($logger)
        {
            error_log($query);
        }

        try
        {
            $result = $con->query($query);

            error_log(json_decode($result));

            if($result)
            {
                $result = $result->fetch_all(MYSQLI_ASSOC);

                return $result;
            }

            return false;
        }
        catch(mysqli_sql_exception $ex)
        {
            error_log("Invalid Query: [$query] Error: $ex");
        }
        catch(exception $exception)
        {
            error_log("Unknown error occurred! Invalid Query: [$query] Error: $exception");
        }

        return false;
    }

    public function doMultiSql($query, $logger = DEBUG)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $con = mysqli_connect($this->address, $this->username, $this->password, $this->database, $this->port);

        if($logger)
        {
            error_log($query);
        }

        try
        {
            $result = $con->multi_query($query);

            error_log(json_decode($result));

            return $result;
        }
        catch(mysqli_sql_exception $ex)
        {
            error_log("Invalid Query: [$query] Error: $ex");
        }
        catch(exception $exception)
        {
            error_log("Unknown error occurred! Invalid Query: [$query] Error: $exception");
        }

        return false;
    }

    public function sanitize($data)
    {
        $con = $this->get_connection();

        if(is_array($data))
        {
            //We are dealing with an array
            $array = array();

            foreach($data as $k => $v)
            {
                if(is_array($v))
                {
                    $array[$con->quote($k)] = $this->sanitize($v);
                }
                elseif(is_bool($v))
                {
                    $array[$con->quote($k)] = $v;
                }
                else
                {
                    $array[$con->quote($k)] = $con->quote($v);
                }
            }

            return $array;
        }
        else
        {
            //$data is not an array
            $data = $con->quote($data);

            return $data;
        }
    }


    /**
     * Support Functions
     */


    public function optimizeAllTables($logger = DEBUG)
    {
        return;
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $con = mysqli_connect($this->address, $this->username, $this->password, $this->database, $this->port);

        $query = 'SHOW TABLES';

        if($logger)
        {
            error_log($query);
        }

        $result = $con->query($query);

        if($result)
        {
            $optimizationResults = array();

            while($table = ($result->fetch_assoc()))
            {
                foreach($table as $db => $tableName)
                {
                    $optimizationQuery = "OPTIMIZE TABLE $tableName";

                    if($logger)
                    {
                        error_log($optimizationQuery);
                    }

                    $res = $con->query($optimizationQuery);

                    if($res)
                    {
                        $optimizationResults[$tableName] = $res->fetch_assoc();
                    }
                    else
                    {
                        var_dump($optimizationQuery);
                        die($con->error);
                    }
                }
            }

            return $optimizationResults;
        }
        else
        {
            //Errors were encountered
            error_log('Invalid Queries : [' . $query . ']');
        }

        return false;
    }

    public function showVars($logger = DEBUG)
    {
        $query = 'SHOW VARIABLES';

        $result = $this->executePDO($query, array(), $logger);

        return $result;
    }

    public function tableExists($table, $logger = DEBUG)
    {
        $query = 'SHOW TABLES LIKE :table';
        $params = array(':table' => $table);

        if($this->executePDO($query, $params, $logger))
        {
            return true;
        }

        return false;
    }

    public function truncate($table)
    {
        return;


        $query = "TRUNCATE TABLE `$table`";

        return $this->executePDO($query);
    }

    public function updateIni($field, $value, $logger = DEBUG)
    {
        $params = array(':value' => $value);
        $query = "SET session $field=:value";

        $result = $this->executePDO($query, $params, $logger);

        return $result;
    }


}