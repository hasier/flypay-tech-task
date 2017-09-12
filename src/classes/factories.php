<?php

require_once __DIR__ . '/dbloader.php';

class PaymentFactory extends DBLoader {
    public function save($paymentData) {
        $sql = "INSERT INTO payment
               (amount, tip, tableNum, restaurantId, paymentRef, cardType) VALUES
               (:amount, :tip, :tableNum, :restaurantId, :paymentRef, :cardType)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            "amount" => $ticket->getAmount(),
            "tip" => $ticket->getTip(),
            "tableNum" => $ticket->getTableNum(),
            "restaurantId" => $ticket->getRestaurantId(),
            "paymentRef" => $ticket->getPaymentRef(),
            "cardType" => $ticket->getCardType(),
        ]);
        if(!$result) {
            throw new Exception("Oops, an error occurred saving the payment");
        }
        $paymentData['id'] = $this->db->lastInsertId();
        return new Payment($paymentData);
    }
}
