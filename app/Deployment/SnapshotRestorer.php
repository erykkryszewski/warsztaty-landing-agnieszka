<?php

declare(strict_types=1);

namespace App\Deployment;

use PDO;
use RuntimeException;

class SnapshotRestorer
{
    public function restore(PDO $pdo, array $snapshot): void
    {
        $tables = $snapshot['tables'] ?? [];

        if (!is_array($tables)) {
            throw new RuntimeException('Invalid deployment snapshot.');
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            $tableName = (string) ($table['name'] ?? '');

            if ($tableName === '') {
                continue;
            }

            $pdo->exec('DROP TABLE IF EXISTS `' . str_replace('`', '``', $tableName) . '`');
            $pdo->exec((string) ($table['create_sql'] ?? ''));
            $this->insertRows($pdo, $tableName, is_array($table['rows'] ?? null) ? $table['rows'] : []);
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    private function insertRows(PDO $pdo, string $tableName, array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $columns = array_keys($rows[0]);
        $columnSql = implode(', ', array_map(
            static fn (string $column): string => '`' . str_replace('`', '``', $column) . '`',
            $columns
        ));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $statement = $pdo->prepare(
            'INSERT INTO `' . str_replace('`', '``', $tableName) . '` (' . $columnSql . ') VALUES (' . $placeholders . ')'
        );

        foreach ($rows as $row) {
            $values = [];

            foreach ($columns as $column) {
                $values[] = $row[$column] ?? null;
            }

            $statement->execute($values);
        }
    }
}
