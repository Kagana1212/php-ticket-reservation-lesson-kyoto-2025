<?php

declare(strict_types=1);

namespace App\Models;

use App\DbConnection;

class TicketTokenUsage
{
    // トークンが存在するかどうかを確認するメソッド
    public static function checkTokenUsage(string $token): bool
    {
        try {
            $dbh = DbConnection::get();
            $dbh->beginTransaction();

            $sql = 'SELECT * FROM ticket_token_usages WHERE token = :token;';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':token', $token, \PDO::PARAM_STR);
            $stmt->execute();
            $tokenUsage = $stmt->fetch();

            if (false !== $tokenUsage) {
                $dbh->rollBack();
                return false;
            }

            $sql = 'INSERT INTO ticket_token_usages (token, created_at, updated_at) 
                    VALUES (:token, :created_at, :updated_at);';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':token', $token, \PDO::PARAM_STR);
            $now = date('Y-m-d H:i:s');
            $stmt->bindValue(':created_at', $now, \PDO::PARAM_STR);
            $stmt->bindValue(':updated_at', $now, \PDO::PARAM_STR);

            $stmt->execute();
            $dbh->commit();
        } catch (\PDOException $e) {
            $dbh->rollBack();
            echo $e->getMessage();
            exit;
        }
        return true;
    }

    // トークンを消費するメソッド
    public static function consumeToken(string $token): bool
    {
        try {
            $dbh = DbConnection::get();
            $dbh->beginTransaction();

            $sql = 'SELECT * FROM ticket_token_usages WHERE token = :token;';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':token', $token, \PDO::PARAM_STR);
            $stmt->execute();
            $tokenUsage = $stmt->fetch();

            if (false === $tokenUsage) {
                $dbh->rollBack();
                return false;
            }

            // トークンを消費済みに更新
            $sql = 'UPDATE ticket_token_usages SET consumed = 1, updated_at = :updated_at WHERE token = :token;';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':token', $token, \PDO::PARAM_STR);
            $now = date('Y-m-d H:i:s');
            $stmt->bindValue(':updated_at', $now, \PDO::PARAM_STR);

            $stmt->execute();
            $dbh->commit();
        } catch (\PDOException $e) {
            $dbh->rollBack();
            echo $e->getMessage();
            exit;
        }
        return true;
    }
}
