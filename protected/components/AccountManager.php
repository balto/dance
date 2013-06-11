<?php

class AccountManager extends BaseModelManager
{
    private static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new AccountManager();
        }
        return self::$instance;
    }

    public function decreaseUndebitedInterest($member_id, $value){
        $account_interest_sql = "UPDATE account SET undebited_interest=(undebited_interest-:debited_interest) WHERE member_id=:member_id";
        $account_interest_command = Yii::app()->db->createCommand($account_interest_sql);
        $account_interest_command->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $account_interest_command->bindValue(':debited_interest', $value, PDO::PARAM_INT);

        $account_interest_command->execute();
    }

    public function increaseBalance($member_id, $value){
        $account_sql = "UPDATE account SET balance=(balance+:debit_balance) WHERE member_id=:member_id";
        $account_command = Yii::app()->db->createCommand($account_sql);
        $account_command->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $account_command->bindValue(':debit_balance', $value, PDO::PARAM_INT);
        $account_command->execute();
    }

    public function decreaseBalance($member_id, $value){
        $account_sql = "UPDATE account SET balance=(balance-:price) WHERE member_id=:member_id";
        $account_command = Yii::app()->db->createCommand($account_sql);
        $account_command->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $account_command->bindValue(':price', $value, PDO::PARAM_INT);
        $account_command->execute();
    }
}