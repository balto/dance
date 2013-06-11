<?php
// Hívása ./yiic_dev CondHolidays Free --verbose=1 --test_only=1
 /**
 *
 * Felszabadítja a config-ban megadott időn belüli (30 nap) feltételes foglalásokat, mert azokkal a napokkal már a szálloda rendelkezik
 */
class CondHolidaysCommand extends CConsoleCommand
{
    public $verbose=false;
    public $test_only=false;


    /**
    * Initializes the command object.
    * This method is invoked after a command object is created and initialized with configurations.
    * You may override this method to further customize the command before it executes.
    * @since 1.1.6
    */
    public function init()
    {
    }


    /**
     * ez a fő függvény
     */
    public function actionFree() {
        $sql = !$this->test_only ? 'DELETE h ' : 'SELECT count(id) ';
        $sql .= ' FROM holiday as h WHERE h.conditional = 1 and DATEDIFF(h.from, SYSDATE()) < '.Yii::app()->params['limit_to_free_cond_holidays'];

        $command = Yii::app()->db->createCommand($sql);

        $rows = $command->execute();

        if ($this->verbose)  echo $rows."db feltételes üdülési idő ". (!$this->test_only ? "került törlésre!" : " várakozik törlésre!\n");
    }


}