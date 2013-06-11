<?php
class AllocationCommand extends CConsoleCommand
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

/*$aparts = AllocationManager::getInstance()->getApartsByRightType(34,4);
print_r($aparts); exit;*/

    }


    // Hívása ./yiic_dev Allocation Gen --verbose=1
     /**
     *
     * Szükség esetén legenerálja az utolsó generált évet követő év kiosztásait és leteszi a kiosztások mellé az üdülési időket is feltételes foglalással
     * 3200db jog esetén 3,5 percig tart 35M memória
     */
    public function actionGen() {
        if ($this->verbose) {
            $execute_start = new sfDate();
            echo "START: ".$execute_start->dump()."\n\n";
        }


        $response = AllocationManager::getInstance()->gen();
//print_r($response);
        if ($this->verbose) {
            switch($response['result']) {
                case AllocationManager::RUNNING:
                    $message = "Nem generáltunk semmit, mert éppen fut a kiosztás generálás!";
                    break;
                case AllocationManager::NO_NEED:
                    $message = "Nincs szükség generálásra!";
                    break;
                case AllocationManager::ERROR_BEFORE_START:
                    $message = "Nem generáltunk semmit, mert épp a gen lementése hasalt el az elején!";
                    break;
                case AllocationManager::ERROR_BEFORE_END:
                    $message = "Mindent legeneráltunk, csak a lock-ot nem sikerült kiszedni!";
                    break;
                case AllocationManager::OK:
                    $message = "Sikeresen kigenerálásra kerültek a következő évi kiosztások!";
                    break;
            }


            echo $message."\n";
            if (!empty($response['warnings'])) {
                echo "\nAz email kiküldése a tapasztalt rendellenességekről". ($response['email_sent'] ? " sikeresen megtörtént!" :" sikertelen volt");
                print_r($response['warnings']);
            }
            if (!empty($response['errors'])) print_r($response['errors']);

            echo "MEMORY BEFORE END: ".memory_get_usage()."\n\n";

            $execute_end = new sfDate();
            echo "\nEND: ".$execute_end->dump()."\n\n";
            echo "DURATION: ".$execute_end->diff($execute_start, sfTime::SECOND)."\n\n";
            echo "\n";
        }


    }











// Hívása ./yiic_dev Allocation Genrights --count=1 --active_from='2012-01-01' --verbose=1
// 1000db jog 30M memória 10perc alatt
    /**
     *
     * Örökös jogokat generáló command action (a kiosztás generálás teljesítmény tesztjéhez készült)
     * @param integer $count	ennyi jogot próbál generálni
     * @param integer $active_from ezzel a kezdődátummal generálja a jogot és az első évi kiosztást erre az évre teszi
     */
    public function actionGenRights($count, $active_from) {
        ini_set('memory_limit', '192M');
        set_time_limit(1800); // fél óra


        if ($this->verbose) {
            $execute_start = new sfDate();
            echo "START: ".$execute_start->dump()."\n\n";
        }

        $right_types = RightType::model()->with('lifetime')->findAll('value IS NULL');
        $client_ids = Yii::app()->db->createCommand('SELECT id FROM client WHERE member_status_id = :active')
            ->queryColumn(array(':active' => StatusManager::getInstance()->getId('MemberStatus', 'active')));
        $hotel_ids = Yii::app()->db->createCommand('SELECT id FROM hotel')->queryColumn();

        // foglalt turnusok gyűjtéséhez turnus típusonként, hotelenként(Csak azokat gyűjti, amiket a generálás során előállít, a db-be már meglevőket nem olvassa fel
        // de nem baj, mert a szabad szoba keresésnél úgy is még egyszer leellenőrizzük, hogy szabad-e abban az időben a szoba, és ha nem szabad, akkor lépünk tovább
        $reserved_turns = array();

        for($i = 0; $i<$count; $i++) {
            // jogtípus véletlenszerű kiválasztása
            $right_type = $right_types[rand(1, count($right_types))-1];
            $turn_type = $right_type->turnType;

            // member véletlenszerű kiválasztása
            $client_id = $client_ids[rand(1, count($client_ids))-1];

            // hotel kiválasztása
            if ($turn_type->rotateLocType->table) {
                // ha a hely pörgetése táblázat alapján történik, akkor csak olyan hotelt választhatunk, amire van táblázat
                $hotel_ids = Yii::app()->db->createCommand('SELECT DISTINCT hotel_id FROM rotate_loc_row WHERE rotate_loc_table_id = :rotate_loc_table_id')
                    ->queryColumn(array(':rotate_loc_table_id' => $turn_type->rotate_loc_table_id));
            }
            $hotel_id = $hotel_ids[rand(1, count($hotel_ids))-1];

            // jog generálása
            $right = $this->genRight($client_id, $right_type, $hotel_id, $active_from, $reserved_turns);
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
            if(!$turn_type->rotateTimeType->table) { // fix, constant, kézi
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

            } else {
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

            }

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


}