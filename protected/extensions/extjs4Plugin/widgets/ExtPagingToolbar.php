<?php

class ExtPagingToolbar extends ExtCustomWidget
{
	protected function init()
	{
		$this
			->displayInfo(true)
			->displayMsg(Yii::t('msg','Találat: {0} - {1} összesen: {2}'))
			->emptyMsg(Yii::t('msg','Nincs megjeleníthető adat'))
			->beforePageText(Yii::t('msg','Oldal'))
			->afterPageText(Yii::t('msg',' / {0}'))
			->firstText(Yii::t('msg','Első'))
			->lastText(Yii::t('msg','Utolsó'))
			->nextText(Yii::t('msg','Következő'))
			->prevText(Yii::t('msg','Előző'))
			->refreshText(Yii::t('msg','Frissítés'))
			->plugins(array(
				array(
					'ptype' => 'pagesize',
					'beforeText' => '',
					'afterText' => Yii::t('msg','/oldal')
			)))
		;
	}
}