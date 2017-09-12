<?php

abstract class BaseSerializer {
   public static function serializeList($data, callable $serializer) {
      $list = array();
      foreach ($data as $value) {
         $list[] = call_user_func($serializer, $value);
      }
      return $list;
   }
}

class PaymentSerializer extends BaseSerializer {
   public static function serializeGet($payment) {
        return array('id' => $payment->getId(), 
                     'created' => $payment->getCreated(), 
                     'amount' => $payment->getAmount(), 
                     'tip' => $payment->getTip(), 
                     'tableNum' => $payment->getTableNum(), 
                     'restaurantId' => $payment->getRestaurantId(), 
                     'paymentRef' => $payment->getPaymentRef(), 
                     'cardType' => $payment->getCardType());
   }

   public static function serializePost($payment) {
        return PaymentSerializer::serializeGet($payment);
   }
}
