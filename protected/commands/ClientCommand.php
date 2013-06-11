<?php
class ClientCommand extends CConsoleCommand
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

    // Hívása ./yiic_dev Client GenClients --count=1
    public function actionGenClients($count){
        Yii::app()->db->createCommand("TRUNCATE TABLE `client`")->execute();

        $command = Yii::app()->db->createCommand();
        $command->bulkInsertClearAll(); // ürítjük

        $settlement_ids = Yii::app()->db->createCommand('SELECT id FROM settlement WHERE is_active=1')->queryColumn();

        $date = sfDate::getInstance()->formatDbDateTime();

        for($i=1;$i<=$count;$i++){
            $data = array();
            $data['name'] = $data['notify_name'] = "Test Client ".$i;
            $data['sex'] = $data['notify_sex'] = 2;
            $data['identifier'] = $i;
            $data['firm'] = 0;
            $data['settlement_id'] = $data['notify_settlement_id'] = $settlement_ids[rand(1, count($settlement_ids))-1];
            $data['street'] = $data['notify_street'] = "Fő út";
            $data['zip'] = $data['notify_zip'] = 1111;
            $data['member'] = 1;
            $data['died'] = 0;
            $data['interest_suspended'] = 0;
            $data['member_status_id'] = 2;
            $data['created_by'] = 1;
            $data['created_at'] = $date;

            $command->bulkInsertCollect('client', $data);
        }

        try {
            if ($command->hasBulkInsertData('client')) {
                $command->bulkInsertExecute('client');
            }

        } catch (Exception $e) {
            $errors[] = Yii::t('msg','Nem sikerült a usert elmenteni!');
        }
    }

    public function actionGenAccounts() {
        Yii::app()->db->createCommand("TRUNCATE TABLE `account`")->execute();
        ini_set('memory_limit', '256M');
        set_time_limit(1800); // fél óra
        //Yii::app()->db->createCommand("TRUNCATE TABLE `account`")->execute();
        $query_params = array(
            array('select', 'c.id AS client_id'),
            array('from', 'client c'),
            array('leftJoin', array('account a', 'a.member_id = c.id')),
            array('where', array('c.member = 1 AND a.id IS NULL AND member_status_id=2'))
        );

        $results = DBManager::getInstance()->query($query_params);

        $command = Yii::app()->db->createCommand();
        $command->bulkInsertClearAll(); // ürítjük

        foreach($results['data'] as $client){

            $data = array();
            $data['member_id'] = $client["client_id"];
            $data['balance'] = 0;
            $data['undebited_interest'] = 0;

            $command->bulkInsertCollect('account', $data);
        }

        try {
            if ($command->hasBulkInsertData('account')) {
                $command->bulkInsertExecute('account');
            }

        } catch (Exception $e) {
            $errors[] = Yii::t('msg','Nem sikerült a usert elmenteni!');
        }
    }


// Hívása ./yiic_dev Client Genrights --active_from='2012-01-01' --verbose=1
// 1000db jog 30M memória 10perc alatt
/**
 *
 * Örökös jogokat generáló command action (a kiosztás generálás teljesítmény tesztjéhez készült)
 * @param integer $active_from ezzel a kezdődátummal generálja a jogot és az első évi kiosztást erre az évre teszi
 */
    public function actionGenRights($active_from) {
        Yii::app()->db->createCommand("TRUNCATE TABLE `allocation`")->execute();
        Yii::app()->db->createCommand("TRUNCATE TABLE `holiday`")->execute();
        Yii::app()->db->createCommand("TRUNCATE TABLE `right`")->execute();

        ini_set('memory_limit', '192M');
        set_time_limit(1800); // fél óra


        if ($this->verbose) {
            $execute_start = new sfDate();
            echo "START: ".$execute_start->dump()."\n\n";
        }

        $right_types = RightType::model()->with('lifetime')->findAll('value IS NULL');
        $client_ids = Yii::app()->db->createCommand('SELECT id FROM client WHERE member_status_id = :active')
            ->queryColumn(array(':active' => 2));
        $hotel_ids = Yii::app()->db->createCommand('SELECT id FROM hotel')->queryColumn();

        // foglalt turnusok gyűjtéséhez turnus típusonként, hotelenként(Csak azokat gyűjti, amiket a generálás során előállít, a db-be már meglevőket nem olvassa fel
        // de nem baj, mert a szabad szoba keresésnél úgy is még egyszer leellenőrizzük, hogy szabad-e abban az időben a szoba, és ha nem szabad, akkor lépünk tovább
        $reserved_turns = array();

        foreach($client_ids as $client_id){
            $counts = array(1,2);
            $right_count_key = array_rand($counts);

if ($this->verbose) {
    echo "Client id: ".$client_id."\n\n";
    echo "Right count: ".$counts[$right_count_key]."\n\n";
}

            for($i=0; $i<$counts[$right_count_key]; $i++){
                $right_type = $right_types[rand(1, count($right_types))-1];
                $turn_type = $right_type->turnType;

                // hotel kiválasztása
                /*if ($turn_type->rotateLocType->table) {
                    // ha a hely pörgetése táblázat alapján történik, akkor csak olyan hotelt választhatunk, amire van táblázat
                    $hotel_ids = Yii::app()->db->createCommand('SELECT DISTINCT hotel_id FROM rotate_loc_row WHERE rotate_loc_table_id = :rotate_loc_table_id')
                        ->queryColumn(array(':rotate_loc_table_id' => $turn_type->rotate_loc_table_id));
                }*/

                if(!empty($hotel_ids)){
                    $hotel_id_key = array_rand($hotel_ids);
                    $hotel_id = $hotel_ids[$hotel_id_key];
if ($this->verbose) {
    echo "Hotel id: ".$hotel_id."\n\n";
}
                    // jog generálása
                    $right = $this->genRight($client_id, $right_type, $hotel_id, $active_from, $reserved_turns);
if ($this->verbose) {
    echo "Right Ok"."\n\n";
}
                }
            }
if ($this->verbose) {
    echo "---------------------"."\n\n";
}

        }

        if ($this->verbose) {
            echo "MEMORY BEFORE END: ".memory_get_usage()."\n\n";
            $execute_end = new sfDate();
            echo "\nEND: ".$execute_end->dump()."\n\n";
            echo "DURATION: ".$execute_end->diff($execute_start, sfTime::SECOND)."\n\n";
            echo "\n";
        }

    }


    /**
     *
     * Adott örökös jogtípus alapján generál egy jogot az adott taghoz, adott kezdési idővel.
     * Gyűjti a már kiosztott turnusokat turnus típusonként hotelenként
     */
    private function genRight($client_id, RightType $right_type, $hotel_id, $active_from, &$reserved_turns) {
//print_r("\n\nclient id: ".$client_id.' right type: '.$right_type->name. ' hotel_id: '.$hotel_id);
        $conn = Yii::app()->db;
        $transaction=$conn->beginTransaction();

        try {
            $turn_type = $right_type->turnType;

            // jog generálása
            $right = new Right();
            $right->member_id = $client_id;
            $right->type_id = $right_type->id;
            $right->hotel_id = $hotel_id;

            $right->buy_reason_id = RightBuyDelReason::model()->find('name = :name', array(':name' => 'Jogvásárlás'))->id;
            $right->value = 100000;
            $right->buy_date = $active_from;
            $right->active_from = $active_from;
            $right->active_to = null; // örökös
            $right->right_relation_id = StatusManager::getInstance()->getId('RightRelation', 'owner');
            $right->owner_ratio = '1';
            $right->notif_client_id = $client_id;
            $right->status_id = StatusManager::getInstance()->getId('RightStatus', 'active');
            $right->created_by = 1;

            // szabad időpont meghatározása
            $year = sfDate::getInstance($active_from)->retrieve(sfTime::YEAR);
            //if(!$turn_type->rotateTimeType->table) { // fix, constant, kézi
                $turn_in_year = TimeGrid::model()->findByPk($turn_type->time_grid_id)->turn_in_year;
                $turn_number = 1;
                if (!isset($reserved_turns[$turn_type->id])) $reserved_turns[$turn_type->id] = array();
                if (!isset($reserved_turns[$turn_type->id][$hotel_id])) $reserved_turns[$turn_type->id][$hotel_id] = array();

                if (count($reserved_turns[$turn_type->id][$hotel_id]) < $turn_in_year) {
                    // turnus számot választunk
                    while (in_array($turn_number, $reserved_turns[$turn_type->id][$hotel_id])) {
                        $turn_number = rand(1, $turn_in_year);
                    }

                    // és megjegyezzük a foglaltak között
                    $reserved_turns[$turn_type->id][$hotel_id][] = $turn_number;

                } else {
                    // minden turnust eladtunk, nem tudunk ehhez a jogtípushoz jogot generálni
                    throw new Exception("Minden turnust eladtunk az id ={$turn_type->id} jogtípusból az id=$hotel_id szállodában, nem újabb jogot generálni");
                }

                $from = TurnManager::getInstance()->getTurn($turn_type->time_grid_id, $turn_number, $year);

                $nights = TimeGrid::model()->findByPk($turn_type->time_grid_id)->nights;
                $to = sfDate::getInstance($from)->addDay($nights)->formatDbDate();

            /*} else {
                // táblázatos idő pörgetés esetén a táblázat sorának rögzítése
                $rot_time_table_id = Yii::app()->db->createCommand('SELECT id FROM rotate_time_table WHERE turn_type_id = :turn_type_id AND (hotel_id = :hotel_id OR hotel_id IS NULL)')
                    ->queryScalar(array(':turn_type_id' => $turn_type->id, ':hotel_id' => $hotel_id));
                $rot_time_row_ids = Yii::app()->db->createCommand('SELECT id FROM rotate_time_row WHERE rotate_time_table_id = :rotate_time_table_id')
                    ->queryColumn(array(':rotate_time_table_id' => $rot_time_table_id));

                // kiválasztunk egy turnust, mintha ezt adtuk volna el neki
                $rot_time_row_id = rand(1, count($rot_time_row_ids)-1);
                if (!isset($reserved_turns[$turn_type->id])) $reserved_turns[$turn_type->id] = array();
                if (!isset($reserved_turns[$turn_type->id][$hotel_id])) $reserved_turns[$turn_type->id][$hotel_id] = array();

                if (count($reserved_turns[$turn_type->id][$hotel_id] < count($rot_time_row_ids))) {
                    // keresünk szabad turnust (a táblázat egy sorát)
                    while (in_array($rot_time_row_id, $reserved_turns[$turn_type->id][$hotel_id])) {
                        $rot_time_row_id = rand(1, count($rot_time_row_ids)-1);
                    }

                    // és megjegyezzük a foglaltak között
                    $reserved_turns[$turn_type->id][$hotel_id][] = $rot_time_row_id;
                } else {
                    // minden turnust eladtunk, nem tudunk ehhez a jogtípushoz jogot generálni
                    throw new Exception("Minden turnust eladtunk az id ={$turn_type->id} jogtípusból az id=$hotel_id szállodában, nem újabb jogot generálni");
                }

                $right->time_row_id = $rot_time_row_id;

                $turn_number = RotateTimeRowTurn::model()->find('rotate_time_row_id = :rotate_time_row_id AND year = 1', array('rotate_time_row_id' => $rot_time_row_id))->turn_number;
                $from = TurnManager::getInstance()->getTurn($turn_type->time_grid_id, $turn_number, $year);

                $nights = TimeGrid::model()->findByPk($turn_type->time_grid_id)->nights;
                $to = sfDate::getInstance($from)->addDay($nights)->formatDbDate();

            }*/

            // szabad hely meghatározása
            $apart = TurnManager::getInstance()->getFreeApart($from, $to, $hotel_id, $right_type->apart_size_id, $turn_type->apart_category_id,  $turn_type->apart_quality_id);
            if (!$apart) {
                $message  = "Jogtípus: ".$right_type->id. ' '.$right_type->name."\n";
                $message .= "VAN Szabad időpont az id=$hotel_id hotelben: from: ".$from. " to: ". $to;
                $message .= " DE NEM TALÁLT SZABAD SZOBÁT az id=$hotel_id hotelben ({$right_type->apart_size_id} méret, {$turn_type->apart_category_id} minőség,  {$turn_type->apart_quality_id} állapot)\n\n";
//print_r($reserved_turns[$turn_type->id][$hotel_id]);
                throw new Exception($message);
                // Esetleg ilyenkor újabb időpontot kellene keresni, ahol esetleg találunk szabad szobát, de nem lényeges, majd generálunk egy másikat
            }

            // fix apartmanos turnus típusnál a szobát lementjük a jogba is
            if ($turn_type->rotateLocType->fix_apart) {
                $right->apart_id = $apart['id'];

            // táblázatos hely pörgetés esetén a táblázat eladott sorát lementjük a jogba
            } else if ($turn_type->rotateLocType->table) {
                $rot_loc_row_ids = Yii::app()->db->createCommand('SELECT id FROM rotate_loc_row WHERE rotate_loc_table_id = :rotate_loc_table_id')
                        ->queryColumn(array(':rotate_loc_table_id' => $turn_type->rotate_loc_table_id));

                // véletlenszerűen választunk egy hotelt, ahol kezdi a barangolást
                $right->first_loc_row_id = rand(1, count($rot_loc_row_ids)-1);
            }

            if (!$right->save()) {
                $message = DBManager::getInstance()->logModelError($right);
                throw new Exception($message);
            }


            // kiosztás generálása
            $allocation = new Allocation();
            $allocation->from = $from;
            $allocation->to = $to;
            $allocation->right_id = $right->id;
            $allocation->apart_id = $apart['id'];
            if (!$allocation->save()) {
                $message = DBManager::getInstance()->logModelError($allocation);
                throw new Exception($message);
            }

            // üdülési időpont generálása
            $holiday = new Holiday();
            $holiday->allocation_id = $allocation->id;
            $holiday->client_id = $client_id;
            $holiday->from = $from;
            $holiday->to = $to;
            $holiday->apart_id = $apart['id'];
            $holiday->conditional = 1;
            $holiday->allocation_reason_id = StatusManager::getInstance()->getId('HolidayAllocationReason', 'member_fee_not_paid');
            if (!$holiday->save()) {
                $message = DBManager::getInstance()->logModelError($holiday);
                throw new Exception($message);
            }

            if ($this->test_only) $transaction->rollback();
            else $transaction->commit();

            return $right;

        } catch(Exception $e) {
            if ($this->verbose) {
                echo "\n".$e->getMessage()."\n";
            }
            $transaction->rollback();
        }
    }

    public function actionGenAparts() {
        Yii::app()->db->createCommand("TRUNCATE TABLE `apart`")->execute();

        $hotels = Hotel::model()->findAll();
        $apart_sizes = ApartSize::model()->findAll('is_active=1');
        $apart_qualities = ApartQuality::model()->findAll('is_active=1');
        $apart_categories = ApartCategory::model()->findAll('is_active=1');
        $time_grids = TimeGrid::model()->findAll('is_active=1');

        $date = sfDate::getInstance()->formatDbDateTime();

        $command = Yii::app()->db->createCommand();
        $command->bulkInsertClearAll(); // ürítjük

        foreach($hotels as $hotel){
            $num = 1;
            $hotel_id = $hotel->id;

            foreach($apart_sizes as $apart_size){
                $apart_size_id = $apart_size->id;

                foreach($apart_categories as $apart_category){
                    $apart_category_id = $apart_category->id;

                    foreach($apart_qualities as $apart_quality){
                        $apart_quality_id = $apart_quality->id;

                        foreach($time_grids as $time_grid){
                            $time_grid_id = $time_grid->id;

                            $data = array();
                            $data['numero'] = $num;
                            $data['hotel_id'] = $hotel_id;
                            $data['for_member'] = 1;
                            $data['for_member_sell'] = 0;
                            $data['time_grid_id'] = $time_grid_id;
                            $data['apart_size_id'] = $apart_size_id;
                            $data['apart_category_id'] = $apart_category_id;
                            $data['apart_quality_id'] = $apart_quality_id;
                            $data['is_active'] = 1;
                            $data['created_by'] = 1;
                            $data['created_at'] = $date;

                            $command->bulkInsertCollect('apart', $data);
                            $num++;
                        }
                    }

                }
            }
        }

        try {
            if ($command->hasBulkInsertData('apart')) {
                $command->bulkInsertExecute('apart');
            }

        } catch (Exception $e) {
            $errors[] = Yii::t('msg','Nem sikerült az apartot elmenteni!');
        }
    }

    public function actionGenDebitPretenseItemPrices() {
        Yii::app()->db->createCommand("TRUNCATE TABLE `debit_pretence_item_price`")->execute();

        $date = sfDate::getInstance()->formatDbDateTime();

        $right_types = RightType::model()->with('lifetime')->findAll('value IS NULL');
        $debit_pretense_items = DebitPretenseItem::model()->with('debitPretense')->findAll('t.is_active=1');

        $command = Yii::app()->db->createCommand();
        $command->bulkInsertClearAll(); // ürítjük

        foreach($debit_pretense_items as $debit_pretense_item){
            $debit_pretense_item_id = $debit_pretense_item->id;

            $data = array();
            $data['debit_pretence_item_id'] = $debit_pretense_item_id;
            $data['price'] = rand(1,100)*1000;
            $data['right_type_id'] = null;
            $data['is_active'] = 1;
            $data['created_by'] = 1;
            $data['created_at'] = $date;

            if(!$debit_pretense_item->debitPretense->rigt_related){
                $command->bulkInsertCollect('debit_pretence_item_price', $data);
            }
            else{
                foreach($right_types as $right_type){
                    $right_type_id = $right_type->id;

                    $data['price'] = rand(1,100)*1000;
                    $data['right_type_id'] = $right_type_id;
                    $command->bulkInsertCollect('debit_pretence_item_price', $data);
                }
            }
        }

        try {
            if ($command->hasBulkInsertData('debit_pretence_item_price')) {
                $command->bulkInsertExecute('debit_pretence_item_price');
            }

        } catch (Exception $e) {
            $errors[] = Yii::t('msg','Nem sikerült az apartot elmenteni!');
        }
    }

    public function actionReset() {
        $this->actionGenAccounts();
        Yii::app()->db->createCommand("TRUNCATE TABLE `credit`")->execute();
        Yii::app()->db->createCommand("TRUNCATE TABLE `credit_item`")->execute();
        Yii::app()->db->createCommand("TRUNCATE TABLE `debit`")->execute();
        Yii::app()->db->createCommand("TRUNCATE TABLE `debit_item`")->execute();
        Yii::app()->db->createCommand("TRUNCATE TABLE `voucher`")->execute();
    }

}

/*Tesztelés folyamata*/
/*
 * actionGenAparts()
 * actionGenClients($count)
 * actionGenAccounts()
 * actionGenRights($active_from)
 * actionGenDebitPretenseItemPrices()
 */