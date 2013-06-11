<?php
// Hívása: ./yiic_dev Apart Gen --count_per=3 --verbose=1 --test_only=1
/**
 *
 * Apartmanokat generálás az összes hotelhez, mindenfajta méretben, minőségben.
 * Azt lehet megadni, hogy egy hotelben egy apart méret egy apart minőség egy apart állapot és egy rácson belül hány szoba jöjjön létre.
 * Összesen hotelek száma * apart méteretk száma * apart minőségek száma * apart állapotok száma * rácsok száma db szobát hoz létre
 *
 * @author Bene
 *
 */
class apartCommand extends CConsoleCommand
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
        Yii::import('application.modules.turn.components.*');
    }


    /**
    * ez a fő függvény
    */
    public function actionGen($count_per) {

		//ini_set('memory_limit', '512M');
		//set_time_limit(2000);

        $execute_start = new sfDate();
        echo "START: ".$execute_start->dump()."\n\n";

        $hotel_ids = Yii::app()->db->createCommand()
            ->select('id')
            ->from('hotel h')
            ->queryColumn();

        $time_grid_ids = Yii::app()->db->createCommand()
            ->select('id')
            ->from('time_grid tg')
            ->queryColumn();

        $apart_size_ids = Yii::app()->db->createCommand()
            ->select('id')
            ->from('apart_size as')
            ->queryColumn();

        $apart_category_ids = Yii::app()->db->createCommand()
            ->select('id')
            ->from('apart_category ac')
            ->queryColumn();

        $apart_quality_ids = Yii::app()->db->createCommand()
            ->select('id')
            ->from('apart_quality aq')
            ->queryColumn();

/*print_r($hotel_ids);
print_r($time_grid_ids);
print_r($apart_size_ids);
print_r($apart_category_ids);
print_r($apart_quality_ids);*/

        $conn = Yii::app()->db;
        $transaction = $conn->beginTransaction();


        try {

            foreach($hotel_ids as $hotel_id) {

                $max_numero_in_hotel = Yii::app()->db->createCommand("select max(numero) from apart where hotel_id = :hotel_id")
                    ->queryScalar(array(':hotel_id' => $hotel_id));
                if (!$max_numero_in_hotel) $max_numero_in_hotel = 0;


                foreach($time_grid_ids as $time_grid_id) {
                    foreach ($apart_size_ids as $apart_size_id) {
                        foreach($apart_category_ids as $apart_category_id) {
                            foreach($apart_quality_ids as $apart_quality_id) {
                                for($i=1; $i <= $count_per; $i++) {
                                    $apart = new Apart();
                                    $apart->numero = ++$max_numero_in_hotel;
                                    $apart->hotel_id = $hotel_id;
                                    $apart->for_member = 1;
                                    $apart->for_member_sell = 0;
                                    $apart->time_grid_id = $time_grid_id;
                                    $apart->turn_type_id = null;
                                    $apart->apart_size_id = $apart_size_id;
                                    $apart->apart_category_id = $apart_category_id;
                                    $apart->apart_quality_id = $apart_quality_id;
                                    $apart->is_active = 1;
                                    $apart->created_by = 1;
                                    $success = $apart->save();

                                    if (!$success) {
                                        if ($this->verbose) {
                                            print_r($apart->getErrors());
                                        }
                                        return;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!$this->test_only) $transaction->commit();
            else $transaction->rollback();

            $execute_end = new sfDate();

            if ($this->verbose) {
                echo "END: ".$execute_end->dump()."\n\n";
                echo "DURATION: ".$execute_end->diff($execute_start, sfTime::SECOND)."\n\n";
                echo "\n\n".count($hotel_ids)*count($time_grid_ids)*count($apart_size_ids)*count($apart_category_ids)*count($apart_quality_ids)*$count_per." db szoba ".(!$this->test_only?"sikeresen":"lenne")." létrehozva!";
                echo "\n";
            }

        } catch(Exception $e) {
            if ($this->verbose) print_r($e->getMessage());
            $transaction->rollback();
        }
    }

}

