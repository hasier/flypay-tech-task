<?php

class PaymentValidator {
    public static $postFilter = array(
      'amount' => FILTER_VALIDATE_INT,
      'tip' => FILTER_VALIDATE_INT,
      'tableNum' => FILTER_VALIDATE_INT,
      'restaurantId' => FILTER_VALIDATE_INT,
      'paymentRef' => FILTER_DEFAULT,
      'cardType' => FILTER_VALIDATE_INT
    );

    public static $getFilter = array(
      'location' => FILTER_VALIDATE_INT
    );

   public static function validatePost($data) {
        $filteredData = filter_var_array($data, PaymentValidator::$postFilter, false);
        if($filteredData['amount'] === false || $filteredData['amount'] < 0
             || $filteredData['tableNum'] === false || $filteredData['tableNum'] <= 0
             || $filteredData['restaurantId'] === false || $filteredData['restaurantId'] <= 0
             || $filteredData['paymentRef'] === false || empty($filteredData['paymentRef'])
             || $filteredData['cardType'] === false || $filteredData['cardType'] <= 0) {
          return false;
        }
        return $filteredData;
   }

   public static function validateGet($data) {
        $filteredData = filter_var_array($data, PaymentValidator::$getFilter, false);
        if($filteredData['location'] === false || $filteredData['location'] < 0) {
          return false;
        }
        return $filteredData;
   }
}
