<?php

/**
 * EDBConnection
 * Расширение стандартного класс соединения с БД
 *
 * $Id: DbConnection.php 15 2010-10-20 11:24:00Z board $
 */
class EDbConnection extends CDbConnection {

	/**
	 * Creates a command for execution.
	 * @param mixed параметры построения запроса (массив типа $arr[name_param]=array('bind'=>true/false/text, 'value'=>value)
	 * @return CDbCommand the DB command
	 * @throws CException if the connection is not active
	 */
	public function createCommand($query=null)
	{//echo 'createcommand'; exit;
		if($this->getActive())
			return new EDbCommand($this,$query);
		else
			throw new CDbException('CDbConnection is inactive and cannot perform any DB operations.');
	}
}
