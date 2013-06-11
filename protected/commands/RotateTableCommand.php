<?php
// Hívása Standard+7 Harkány esetén: ./yiic_dev RotateTable Import --turn_type_id=9 --hotel_id=2 --turn_nights=7 --rotate_cycle=4 --path="./standard+7_HS_porg_tabla.csv" --verbose=1 --test_only=1
// Hívása Standard+7 Sopron esetén: ./yiic_dev RotateTable Import --turn_type_id=9 --hotel_id=4 --turn_nights=7 --rotate_cycle=4 --path="./standard+7_HS_porg_tabla.csv" --verbose=1 --test_only=1
// Hívása  Standard+7 Mátra esetén: ./yiic_dev RotateTable Import --turn_type_id=9 --hotel_id=3 --turn_nights=7 --rotate_cycle=4 --path="./standard+7_M_porg_tabla.csv" --verbose=1 --test_only=1

// Hívása Hipp-Hopp7 Harkány esetén:  ./yiic_dev RotateTable Import --turn_type_id=10 --hotel_id=2 --turn_nights=7 --rotate_cycle=4 --path="./standard+7_HS_porg_tabla.csv" --verbose=1 --test_only=1
// Hívása Hipp-Hopp7 Sopron esetén:  ./yiic_dev RotateTable Import --turn_type_id=10 --hotel_id=4 --turn_nights=7 --rotate_cycle=4 --path="./standard+7_HS_porg_tabla.csv" --verbose=1 --test_only=1
// Hívása Hipp-Hopp7 Mátra esetén ./yiic_dev RotateTable Import --turn_type_id=10 --hotel_id=3 --turn_nights=7 --rotate_cycle=4 --path="./standard+7_M_porg_tabla.csv" --verbose=1 --test_only=1

// Hívása Barangoló7 SHSM pörgés esetén:  ./yiic_dev RotateTable Import --turn_type_id=11 --hotel_id= --turn_nights=7 --rotate_cycle=4 --path="./barangolo7_SHSM_porg_tabla.csv" --verbose=1 --test_only=1
// Hívása Barangoló7 HSMS pörgés esetén:  ./yiic_dev RotateTable Import --turn_type_id=12 --hotel_id= --turn_nights=7 --rotate_cycle=4 --path="./barangolo7_HSMS_porg_tabla.csv" --verbose=1 --test_only=1

// Hívása RCI7 Harkány esetén:  ./yiic_dev RotateTable Import --turn_type_id=13 --hotel_id=2 --turn_nights=7 --rotate_cycle=4 --path="./standard+7_HS_porg_tabla.csv" --verbose=1 --test_only=1
// Hívása RCI7 Sopron esetén:  ./yiic_dev RotateTable Import --turn_type_id=13 --hotel_id=4 --turn_nights=7 --rotate_cycle=4 --path="./standard+7_HS_porg_tabla.csv" --verbose=1 --test_only=1
// Hívása RCI7 Mátra esetén ./yiic_dev RotateTable Import --turn_type_id=13 --hotel_id=3 --turn_nights=7 --rotate_cycle=4 --path="./standard+7_M_porg_tabla.csv" --verbose=1 --test_only=1


// TODO: Ha kiderül, hogy a 2. RCI turnus típus is lesz, akkor arra is fel kell tölteni
// TODO: ECO Comfort turnus típusokra is, ha le lesz specifikálva

class RotateTableCommand extends CConsoleCommand
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

    private $h = array(
        'turn_number_1',
        'turn_number_2',
        'turn_number_3',
        'turn_number_4',
    );

    /**
    * ez a fő függvény
    */
    public function actionImport($turn_type_id, $hotel_id, $turn_nights, $rotate_cycle, $path) {

		//ini_set('memory_limit', '512M');
		//set_time_limit(2000);

        //$path="./porg_tabla.csv";
        $delimiter = ";";

        //$this->h = array_flip($this->h);

        $reader = new sfCsvReader($path, $delimiter);
        //$reader->setCharset('UTF-8');

        $execute_start = new sfDate();
        echo "START: ".$execute_start->dump()."\n\n";

        $reader->open();

        $conn = Yii::app()->db;
        $transaction = $conn->beginTransaction();


        try {
            $rotate_time_table = new RotateTimeTable();
            $rotate_time_table->turn_type_id = intval($turn_type_id);
            $rotate_time_table->hotel_id = $hotel_id ? intval($hotel_id) : null;
            $rotate_time_table->turn_nights = intval($turn_nights);
            $rotate_time_table->rotate_cycle = intval($rotate_cycle);
            $rotate_time_table->is_active = 1;
            $rotate_time_table->created_by = 1;

            if ($rotate_time_table->save()) {

                $row_number = 0;
                while ($data = $reader->read()) {
                    $this->processRow(++$row_number, $rotate_time_table->id, $data);
                }
            } else {
                if ($this->verbose) {
                    print_r($rotate_time_table->getErrors());
                }
            }

            if (!$this->test_only) $transaction->commit();
            else $transaction->rollback();

        } catch(Exception $e) {
            $transaction->rollback();
        }

        $reader->close();

        $execute_end = new sfDate();

        if ($this->verbose) {
            echo "\nEND: ".$execute_end->dump()."\n\n";
            echo "DURATION: ".$execute_end->diff($execute_start, sfTime::SECOND)."\n\n";
            echo "\n";
        }
    }

    protected function processRow($row_number, $rotate_time_table_id, $data) {
        //trim($data[$this->h['turn_number_4']]);
        $rotate_time_row = new RotateTimeRow();
        $rotate_time_row->rotate_time_table_id = $rotate_time_table_id;
        $success = $rotate_time_row->save();

        if ($success) {

            $year = 1;
            foreach($data as $turn_number) {
                $row_turn = new RotateTimeRowTurn();
                $row_turn->rotate_time_row_id = $rotate_time_row->id;
                $row_turn->year = $year;
                $row_turn->turn_number = $turn_number;
                $row_turn->created_by = 1;
                $success = $row_turn->save();

                if (!$success) break;

                $year++;
            }
        } else {
            if ($this->verbose) {
                print_r($rotate_time_row->getErrors());
                return;
            }
        }

        if ($this->verbose) {
            if ($success) {
                echo "$row_number. sor feldolgozva\n";
            } else {
                echo "$row_number. sor feldolgozása sikertelen:\n";
                print_r($rotate_time_row->getErrors());
                print_r($row_turn->getErrors());
                echo "\n";
            }
        }

    }

}

