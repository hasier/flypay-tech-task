<?php

require_once __DIR__ . '/dbloader.php';

class PaymentFinder extends DBLoader {
    public function getPaymentsFromDate($date, $location) {
        $sql = "SELECT *
                FROM payment p
                WHERE p.created > :date";
        if ($location != null) {
          $sql = $sql . ' AND p.location = :location';
        }
        $stmt = $this->db->prepare($sql);

        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new Payment($row);
        }
        return $results;
    }
}

class UserFinder extends DBLoader {
    public function getUserByToken($token) {
        $sql = "SELECT *
                FROM user u
                WHERE u.token = :token";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(["token" => $token]);
        if($result) {
            return new User($stmt->fetch());
        }
    }
}
