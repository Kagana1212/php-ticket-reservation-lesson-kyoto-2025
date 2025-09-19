<?php

declare(strict_types=1);

namespace App\Models;

use App\DbConnection;

class TicketPurchase
{
    /**
     * トークンでチケット購入情報を取得
     */
    public static function getByToken(string $token): array|false
    {
        try {
            $dbh = DbConnection::get();
            // トークンに基づいてデータを取得
            $sql = 'SELECT * FROM ticket_purchases WHERE token = :token;';
            $pre = $dbh->prepare($sql);
            $pre->bindValue(':token', $token, \PDO::PARAM_STR);
            $pre->execute();
            $datum = $pre->fetch();

            // トークンが見つからなければ null を返す
            return $datum ?: null;
        } catch (\PDOException $e) {
            // エラーが発生した場合にログを表示する
            echo "Error: " . $e->getMessage();
            exit;
        }
    }

    /**
     * チケット購入情報の一覧を取得
     */
    public static function getAll(): array
    {
        try {
            $dbh = DbConnection::get();
            // 購入情報の一覧取得
            $sql = 'SELECT * FROM ticket_purchases ORDER BY created_at DESC LIMIT 100';
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);  // \PDO を明示的に指定
        } catch (\PDOException $e) {
            // エラーが発生した場合にログを表示する
            echo "Error: " . $e->getMessage();
            exit;
        }
        return $list;
    }
}
