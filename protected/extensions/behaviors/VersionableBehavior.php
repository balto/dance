<?php
class VersionableBehavior extends CActiveRecordBehavior
{

    /**
     * Responds to {@link CActiveRecord::onBeforeSave} event.
     * Overrides this method if you want to handle the corresponding event of the {@link CBehavior::owner owner}.
     * You may set {@link CModelEvent::isValid} to be false to quit the saving process.
     * @param CModelEvent $event event parameter
     */
    public function beforeSave($event)
    {
        if($this->owner->isNewRecord) {
            // insert: a fő tábla verzió számát 1-re állítjuk
            // $this->owner->version = 1; beforeValidate már beállította
        } else {
            // update: a fő tábla verzió számát megnöveljük
            $this->owner->version++;
        }
    }

    /**
     * Responds to {@link CActiveRecord::onAfterSave} event.
     * Overrides this method if you want to handle the corresponding event of the {@link CBehavior::owner owner}.
     * @param CModelEvent $event event parameter
     */
    public function afterSave($event)
    {
            // a verzió táblába is leteszünk egy recordot ugyanazokkal az adatokkal, amire a főtábla módosult
            $model_name = get_class($this->owner) . 'Version';
            $version_record = new $model_name;
            $version_record->setAttributes($this->owner->getAttributes());
            $version_record->save();
    }

    /**
     * Responds to {@link CActiveRecord::onBeforeDelete} event.
     * Overrides this method if you want to handle the corresponding event of the {@link CBehavior::owner owner}.
     * You may set {@link CModelEvent::isValid} to be false to quit the deletion process.
     * @param CEvent $event event parameter
     */
    public function beforeDelete($event)
    {
    }

	/**
	 * Responds to {@link CActiveRecord::onAfterDelete} event.
	 * Overrides this method if you want to handle the corresponding event of the {@link CBehavior::owner owner}.
	 * @param CEvent $event event parameter
	 */
	public function afterDelete($event)
	{
        // a verzió táblába is leteszünk egy recordot ha van deleted_at mező és kitöltjük a törlés idejével
	    $model_name = get_class($this->owner) . 'Version';
        $version_record = new $model_name;

	    if ($version_record->hasAttribute('deleted_at')) {
            $version_record->setAttributes($this->owner->getAttributes());

            $version_record->deleted_at = sfDate::getInstance()->formatDbDateTime();

            $version_record->save();
	    }
	}

    public function beforeValidate($event)
    {
        if($this->owner->isNewRecord){
            // insert: a fő tábla verzió számát 1-re állítjuk
            $this->owner->version = 1;
        }
    }
}
