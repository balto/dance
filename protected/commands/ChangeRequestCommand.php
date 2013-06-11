<?php
// Hívása ./yiic_dev ChangeRequest ProcessOldRequests --verbose=1 --test_only=1
class ChangeRequestCommand extends CConsoleCommand
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

    public function actionProcessOldRequests()
    {
        Yii::import('application.modules.turn.components.*');

        $message = TurnManager::getInstance()->processHolidayChangeOldRequests($this->verbose, $this->test_only);
        Yii::app()->sendCustomerServiceMail('Automatikusan feldolgozott időpontcsere-kérelmek - '.date('Y-m-d'), $message);
        if ($this->verbose) echo "Értesítő e-mail kiküldve.\n";
    }

}
