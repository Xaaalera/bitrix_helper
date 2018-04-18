<?php
use Bitrix\Sale;
/**
 * Created by PhpStorm.
 * User: Xaalera
 * Date: 2/7/2018
 * Time: 5:24 PM
 */
class BasketBitrix
{
    private $basket, $endPrice,$user,$id;

    public function __construct($ID)
    {
        try {
            $this->id =$ID;
            $this->getCurrentUser() ;
            $this->basket = $this->get_basket_array();
//            $obBasket = Sale\Basket::getList(array('filter' => array('ORDER_ID' => $ID)));
            $this->order = CSaleOrder::GetByID($ID);
//            var_dump($obBasket);
        }
        catch (Exception $e) {
            echo 'Выброшено исключение: ', $e->getMessage(), "\n";
        }

    }
    

    public  function getCurrentUser(){
        $rsUser = CUser::GetByID(CUser::GetID());
        $arUser = $rsUser->Fetch();
        $this->user =$arUser ;
        return $arUser ;
    }
    public function getUserEmail(){
        return $this->user['EMAIL'];
    }
    public  function  getUserName(){
        return $this->user['NAME'];
    }
    public  function  getUserPhone(){
        return $this->user['PERSONAL_PHONE'];
    }
    
    
    public function getBasketInString()
    {
        $product_string = '';
        foreach ($this->basket as $value) {
            $price = $value['PRICE'] *  $value['QUANTITY'] ;
                        $product_string .= <<<PRODUCT
 Name: {$value['NAME']} . quantity: {$value['QUANTITY']} . Price: {$price}
 
PRODUCT;
        }

        return $product_string;
    }
    
    /**
     * @return float|int - сумма заказа
     */
    public function getEndPrice()
    {
        return $this->order['PRICE'];

    }


    //платежная cистема
    
    /**
     * @return string - имя платежной системы
     */
    public function getPaySystemName()
    {
        try {
            $paysystem = $this->order['PAY_SYSTEM_ID'] ;
            $res = \Bitrix\Sale\PaySystem\Manager::getById($paysystem);
            return  $res['NAME'];
        }
        catch (Exception $e) {
            echo "<pre>";
            print_r($e->getMessage());
            echo "</br>";
        }
    }
    
    /**
     * @return string  - Тип доставки
     */
    //доставка
    public function getDeliveryName()
    {
        try {
            $delivery_id = $this->order['DELIVERY_ID'];
             $res = \Bitrix\Sale\Delivery\Services\Manager::getById($delivery_id) ;
            return $res['NAME'];
        }
        catch (Exception $e) {
            echo "<pre>";
            print_r($e->getMessage());
            echo "</br>";
        }

    }

    public  function  getPersonType(){
        try {
            $id = $_REQUEST['PERSON_TYPE'] ;
            $name = $id == 1 ? 'Физическое лицо' : 'Юридическое лицо';
            return $name ;
        }
        catch (Exception $e) {
            echo "<pre>";
            print_r($e->getMessage());
            echo "</br>";
        }
    }
	
	
	private function get_basket_array()
    {
        $arBasketItems = array();
        
        $dbBasketItems = CSaleBasket::GetList(array(
            "NAME" => "ASC",
            "ID"   => "ASC"
        ), array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID"      => SITE_ID,
                "ORDER_ID" => $this->id
            ));
    
        while ($arItems = $dbBasketItems->Fetch())
        {
            $arBasketItems[] = $arItems;
        }
        return $arBasketItems;
    }

public function getPropsOrder(){
      $obProps = Bitrix\Sale\Internals\OrderPropsValueTable::getList(array('filter' => array('ORDER_ID' => $this->id)));
        while($prop = $obProps->Fetch()){
            $props[$prop['NAME']]= $prop['VALUE'];
        }

	returm $props ;

}

	

}
