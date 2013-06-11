<?php
// Hívása: ./yiic_dev Finance calcDailyInterest --verbose=1 --test_only=1
class FinanceCommand extends CConsoleCommand
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
        Yii::import('application.modules.finance.components.*');
    }

    /**
     * Minden számlázott terhelésre egy napi kamat kiszámítása a napi kamatláb alapján,
     * figyelembe véve a pillanatnyi tőketartozást, a kamatszámítás felfüggesztését.
     *
     * A ki nem terhelt kamatot kell update-elni a napi kamattal.
     * Ha esetleg kimarad a job, akkor minden kimaradt napra az aktuális kamatláb
     * és aktuális egyenleg alapján kell kamatot számolni.
     *
     * Ha esetleg közben már kiterhelték a kamatot, akkor előrdulhat, hogy a negatívba megy
     * a nem kiterhelt kamat. Ezt a negatív kamatot is engedni kellene kiterhelni,
     * ami csökkenti a számla összegét.
     *
     * Részletfizetési tételeket is figyelembe kell venni, és az ott nyilvántartott jóváírások
     * és fizetési határidők alapján kell kamatot számolni a terheléshez.
     *
     * A késedelmi kamat kerekítve legyen 0-ra, vagy 5-re.
     */
    public function actionCalcDailyInterest() {
        $today = sfDate::getInstance()->formatDbDate();
        $grace_period = Yii::app()->params['default_interest_calc_grace_time'];
        $finance_man = FinanceManager::getInstance();

        // kiszámítjuk a mai napra esedékes kamatszázalékot az Interest táblában levő adatok alapján
        $daily_interest_percent = $finance_man->getDailyInterestPercent($today);

        // összeszedjük azokat a számlákat és terhelési értesítőket, amelyek payment_date-je
        // a config-ban meghatározott grace_time-ot is figyelembe véve lejárt,
        // és nincsenek felfüggesztve vagy törölve, és ma még nem számoltunk rá kamatot
        $sql = "
            SELECT d.id, d.interest_calc_at, d.interest, d.debit_balance, v.payment_date
            FROM debit d
            JOIN voucher v ON d.voucher_id = v.id
            JOIN voucher_type vt ON v.voucher_type_id = vt.id
            WHERE (vt.invoice = 1 OR vt.note = 1)
              AND d.deleted = 0
              AND d.interest_suspended = 0
              AND (d.interest_calc_at < :today OR d.interest_calc_at IS NULL)
              AND v.payment_date < :today_minus_grace_period
        ";

        $command = Yii::app()->db->createCommand($sql);
        $dataReader = $command->query(array(
            ':today' => $today,
            ':today_minus_grace_period' => sfDate::getInstance()->subtractDay($grace_period)->formatDBDate(),
        ));

        // végigmegyünk a számlákon
        foreach ($dataReader as $row) {
            if ($this->verbose) echo "===============\nTerhelés ID=".$row['id']."\n";
            // ellenőrizzük, hogy tegnap számítottunk-e kamatot
            if (sfDate::getInstance($row['interest_calc_at'])->addDay(1)->formatDbDate() == $today) {
                // ha igaz, akkor a debit.interest-et megnöveljük a (debit.balance * a mai kamat%)-kal
                if ($this->verbose) echo 'Tegnap is számítottunk kamatot, ma csak növelni kell ';

                $debit = Debit::model()->findByPk($row['id']);
                $daily_interest = $debit->balance * $daily_interest_percent / 100;
                if ($this->verbose) echo $daily_interest . ' Ft-tal (' . $daily_interest_percent . "%)\n";
                $debit->interest += $daily_interest;
                $debit->interest_calc_at = sfDate::getInstance()->formatDbDateTime();
                if (!$this->test_only) {
                    if (!$debit->save()) {
                        DBManager::getInstance()->logModelError($debit);
                        if ($this->verbose) echo 'Nem sikerült rögzíteni a kamat-többletet, a hiba oka: '.DBManager::getInstance()->getModelErrors($debit);
                    }
                }

            } else {
                // Mióta késedelmes? Mikor számoltunk utoljára kamotot?
                $delay_start = sfDate::getInstance($row['payment_date'])->addDay($grace_period);
                $interest_calc_at = sfDate::getInstance($row['interest_calc_at'] ? $row['interest_calc_at'] : '2000-01-01');
                if ($this->verbose) {
                    echo "Tegnap nem számítottunk kamatot.\n";
                    echo 'Késedelem kezdete: '.$delay_start->formatDbDate()."\n";
                    echo 'Utolsó kamatszámítás: '.$interest_calc_at->formatDbDate()."\n";
                }

                // Az első nap, amikortól számolni kell a kamatot az az utolsó kamatszámítást követő nap lehet
                $interest_calc_at->addDay(1);

                // a kettő közül a nagyobbik dátumtól kezdve
                $calc_interest_start = $delay_start->cmp($interest_calc_at) < 0 ? $interest_calc_at : $delay_start;
                if ($this->verbose) echo $calc_interest_start->formatDbDate() . "-tól minden napra kamatot kell számítani.\n";

                // visszamenőleg minden napra számolni kell a kamatot a mai napig
                $debit = Debit::model()->findByPk($row['id']);

                for($date = $calc_interest_start; $date->cmp(sfDate::getInstance()) <= 0; $date->addDay(1)) {
                    // erre a napra meg kell határozni, hogy mennyi volt a balance
                    $balance = $finance_man->getDebitBalanceAt($row['id'], $date->formatDbDate());
                    $interest_percent = $finance_man->getDailyInterestPercent($date->formatDbDate());

                    $daily_interest = $balance * $interest_percent / 100;
                    $debit->interest += $daily_interest;
                    if ($this->verbose) echo '- '.$date->formatDbDate().": $balance * $interest_percent% = $daily_interest Ft\n";
                    $debit->interest_calc_at = $date->formatDbDate();
                    if (!$this->test_only) {
                        if (!$debit->save()) {
                            DBManager::getInstance()->logModelError($debit);
                            if ($this->verbose) echo 'Nem sikerült rögzíteni a kamat-többletet, a hiba oka: '.DBManager::getInstance()->getModelErrors($debit);
                            break;
                        }
                    }
                }

                $debit->interest_calc_at = sfDate::getInstance()->formatDbDateTime();
                if (!$this->test_only) {
                    if (!$debit->save()) {
                        DBManager::getInstance()->logModelError($debit);
                        if ($this->verbose) echo 'Nem sikerült rögzíteni az utolsó kamatszámítás pontos idejét, a hiba oka: '.DBManager::getInstance()->getModelErrors($debit);
                    }
                }
            }
        }
    }
}
