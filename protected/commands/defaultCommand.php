<?php
// Hívása: ./yiic_dev Default test --verbose=0 --test_only=0
class DefaultCommand extends CConsoleCommand
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

    public function actionTest(){
    	$ct = new CronTest();
    	$ct->datetime = date('Y-m-d H:i:s');
    	$ct->save();
    	
    	echo '-----------lefut------------'.PHP_EOL;
    	
    }
}
