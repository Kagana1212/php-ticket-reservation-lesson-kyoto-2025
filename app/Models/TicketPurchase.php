<?php

declare(strict_types=1);

namespace App\Models;

use App\DbConnection;
/*
 *本来とは違うが、今回はここに「チケット購入」に関する処理をまとめる 
 */
class TicketPurchase
{

    //全体取得
    public static function getAll(): array
    {
    // xxxここに「一覧取得」の処理を書く
        try {
            $dbh = DbConnection::get(); 

            $stmt = $dbh->prepare('SELECT * FROM ticket_purchases ORDER BY created_at DESC LIMIT 100');
            $stmt->execute();
            $list = $stmt->fetchAll();
        } catch (\PDOException $e) {
            // SQLエラー時の処理
            echo $e->getMessage();
            exit;
        }
        return $list;
    }
}