<?php
/**
 * CDbHttpSession class felüldefiniálása
 *
 */

/**
 * CDbHttpSession extends {@link CHttpSession} by using database as session data storage.
 *
 * CDbHttpSession stores session data in a DB table named 'YiiSession'. The table name
 * can be changed by setting {@link sessionTableName}. If the table does not exist,
 * it will be automatically created if {@link autoCreateSessionTable} is set true.
 *
 * The following is the table structure:
 *
 * <pre>
 * CREATE TABLE YiiSession
 * (
 *     id CHAR(32) PRIMARY KEY,
 *     expire INTEGER,
 *     data TEXT
 * )
 * </pre>
 *
 * CDbHttpSession relies on {@link http://www.php.net/manual/en/ref.pdo.php PDO} to access database.
 *
 * By default, it will use an SQLite3 database named 'session-YiiVersion.db' under the application runtime directory.
 * You can also specify {@link connectionID} so that it makes use of a DB application component to access database.
 *
 * When using CDbHttpSession in a production server, we recommend you pre-create the session DB table
 * and set {@link autoCreateSessionTable} to be false. This will greatly improve the performance.
 * You may also create a DB index for the 'expire' column in the session table to further improve the performance.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CDbHttpSession.php 3167 2011-04-07 04:25:27Z qiang.xue $
 * @package system.web
 * @since 1.0
 */
class EDbHttpSession extends CDbHttpSession
{
	/**
	 * @var string the name of the DB table to store session content.
	 * Note, if {@link autoCreateSessionTable} is false and you want to create the DB table manually by yourself,
	 * you need to make sure the DB table is of the following structure:
	 * <pre>
	 * (id CHAR(32) PRIMARY KEY, expire INTEGER, data TEXT)
	 * </pre>
	 * @see autoCreateSessionTable
	 */
	//public $sessionTableName='sessions';
	/**
	 * @var boolean whether the session DB table should be automatically created if not exists. Defaults to true.
	 * @see sessionTableName
	 */
	//public $autoCreateSessionTable=false;

	/**
	* Updates the current session id with a newly generated one .
	* Please refer to {@link http://php.net/session_regenerate_id} for more details.
	* @param boolean $deleteOldSession Whether to delete the old associated session file or not.
	* @since 1.1.8
	*/
	public function regenerateID($deleteOldSession=false)
	{
	    $oldID=session_id();;
	    session_regenerate_id($deleteOldSession); // parent::regenerateID(false);
	    $newID=session_id();
	    $db=$this->getDbConnection();

	    $sql="SELECT * FROM {$this->sessionTableName} WHERE sess_id=:id";
	    $row=$db->createCommand($sql)->bindValue(':id',$oldID)->queryRow();
	    if($row!==false)
	    {
	        if($deleteOldSession)
	        {
	            $sql="UPDATE {$this->sessionTableName} SET sess_id=:newID WHERE sess_id=:oldID";
	            $db->createCommand($sql)->bindValue(':newID',$newID)->bindValue(':oldID',$oldID)->execute();
	        }
	        else
	        {
	            $row['sess_id']=$newID;
	            $db->createCommand()->insert($this->sessionTableName, $row);
	        }
	    }
	    else
	    {
	        // shouldn't reach here normally
	        $db->createCommand()->insert($this->sessionTableName, array(
					'sess_id'=>$newID,
					'sess_time'=>time()+$this->getTimeout(),
	        ));
	    }
	}

	/**
	* Creates the session DB table.
	* @param CDbConnection $db the database connection
	* @param string $tableName the name of the table to be created
	*/
	protected function createSessionTable($db,$tableName)
	{
	    // ne hozza létre, mert az adatbázisnak része
	}

	/**
	* Session open handler.
	* Do not call this method directly.
	* @param string $savePath session save path
	* @param string $sessionName session name
	* @return boolean whether session is opened successfully
	*/
	public function openSession($savePath,$sessionName)
	{
	    if($this->autoCreateSessionTable)
	    {
	        $db=$this->getDbConnection();
	        $db->setActive(true);
	        $sql="DELETE FROM {$this->sessionTableName} WHERE sess_time<".time();
	        try
	        {
	            $db->createCommand($sql)->execute();
	        }
	        catch(Exception $e)
	        {
	            $this->createSessionTable($db,$this->sessionTableName);
	        }
	    }
	    return true;
	}

	/**
	* Session read handler.
	* Do not call this method directly.
	* @param string $id session ID
	* @return string the session data
	*/
	public function readSession($id)
	{
	    $now=time();
	    $sql="
	        	SELECT sess_data FROM {$this->sessionTableName}
	        	WHERE sess_time>$now AND sess_id=:id
	        	";
	    $data=$this->getDbConnection()->createCommand($sql)->bindValue(':id',$id)->queryScalar();
	    return $data===false?'':$data;
	}

	/**
	* Session write handler.
	* Do not call this method directly.
	* @param string $id session ID
	* @param string $data session data
	* @return boolean whether session write is successful
	*/
	public function writeSession($id,$data)
	{
	    // exception must be caught in session write handler
	    // http://us.php.net/manual/en/function.session-set-save-handler.php
	    try
	    {
	        $expire = sfDate::getInstance()->get() + $this->getTimeout();
	        $db=$this->getDbConnection();
	        $sql="SELECT sess_id FROM {$this->sessionTableName} WHERE sess_id=:id";
	        if($db->createCommand($sql)->bindValue(':id',$id)->queryScalar()===false)
	        $sql="INSERT INTO {$this->sessionTableName} (sess_id, sess_data, sess_time) VALUES (:id, :data, $expire)";
	        else
	        $sql="UPDATE {$this->sessionTableName} SET sess_time=$expire, sess_data=:data WHERE sess_id=:id";
	        $db->createCommand($sql)->bindValue(':id',$id)->bindValue(':data',$data)->execute();
	    }
	    catch(Exception $e)
	    {
	        if(YII_DEBUG)
	        echo $e->getMessage();
	        // it is too late to log an error message here
	        return false;
	    }
	    return true;
	}

	/**
	* Session destroy handler.
	* Do not call this method directly.
	* @param string $id session ID
	* @return boolean whether session is destroyed successfully
	*/
	public function destroySession($id)
	{
	    $sql="DELETE FROM {$this->sessionTableName} WHERE sess_id=:id";
	    $this->getDbConnection()->createCommand($sql)->bindValue(':id',$id)->execute();
	    return true;
	}

	/**
	 * Session GC (garbage collection) handler.
	 * Do not call this method directly.
	 * @param integer $maxLifetime the number of seconds after which data will be seen as 'garbage' and cleaned up.
	 * @return boolean whether session is GCed successfully
	 */
	public function gcSession($maxLifetime)
	{
	    $sql="DELETE FROM {$this->sessionTableName} WHERE sess_time<".time();
	    $this->getDbConnection()->createCommand($sql)->execute();
	    return true;
	}


	/**
	* @return CDbConnection the DB connection instance
	* @throws CException if {@link connectionID} does not point to a valid application component.
	*/
	protected function getDbConnection()
	{
	    $conn = parent::getDbConnection();
	    return $conn;

	}
}
