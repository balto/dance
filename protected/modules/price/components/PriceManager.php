<?php
class PriceManager extends BaseModelManager
{
    private static $instance = null;

    private function __construct() {

    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new PriceManager();
        }
        return self::$instance;
    }
	
	
	public function payIn($ticketId, $price){
		$error = array();	
		$message = '';
			
		$ticket = Ticket::model()->findByPk($ticketId);
		
		//tartozas
		$debit = (int)$ticket->price - (int)$ticket->payed_price;
		
		if($debit){
			if($price > $debit){
				$error[] = 'Több a befizetett összeg, mint amennyivel tartozik!<br />Befizetés : '.$price.'<br />Tartozás:'.$debit;
			}
			else{
				$ticket->payed_price = $ticket->payed_price + $price;
		
				if($ticket->save()){
					$success = true;
				}
			}
			
		}
		else{
			$error[] = 'Nincs tartozása!';
		}
		
		if(!empty($error)){
			$message = 'Az adatokat nem sikerült rögzíteni a következők miatt: ';
		}
		
		return $response = array(
                'success'=>true,
                'message'=>$message,
        		'error' => $error,
                'id' => $ticketId,
        		'is_edit' => 1,
        );
	}
	
}