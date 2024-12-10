<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Drivers\MySql;

use PDO;
use PDOStatement;
use SamMcDonald\Norm\Drivers\PlatformDriver;

use function sprintf;

abstract class AbstractMySqlPlatformDriver extends PlatformDriver
{
    protected const DEFAULT_PORT = 3306;
    protected const DEFAULT_CHARSET = 'utf8mb4';
    protected const DEFAULT_COLLATION = 'utf8mb4_unicode_ci';

    protected const LENGTH_MAX_BINARY_FIELD = 65535;
    protected const LENGTH_MAX_VARCHAR_FIELD = 65535;

    protected const KEYWORD_TEMPORARY = 'TEMPORARY';


    protected const SQL_GET_DEFAULT_DATABASE = 'SELECT DATABASE()';
    protected const SQL_SELECT_ALL_FROM = 'SELECT * FROM %s';
    protected const SQL_GET_ALL_BY_ID = 'SELECT * FROM %s WHERE %s = ';
    protected const SQL_CHECK_ROW_EXIST = 'SELECT %s FROM %s WHERE %s = ';
    protected const SQL_SELECT_ALL_LIMIT_OFFSET = 'SELECT * FROM %s %s %s';
    protected const SQL_SET_FK_CHECKS = 'SET FOREIGN_KEY_CHECKS = %s';
    protected const SQL_DROP_TABLE = 'DROP %s TABLE %s';
    protected const SQL_TRUNCATE_TABLE = 'TRUNCATE %s';
    protected const SQL_DELETE_WHERE = 'DELETE FROM %s WHERE %s';
    protected const SQL_UPDATE_WHERE = 'UPDATE %s SET %s WHERE %s';
    protected const SQL_GET_ALL_BASE_TABLES = "SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'";

    // dont expose this, use getObjectQuoteCharacter
    private const QUOTE_IDENTIFIER = '`';


    public function __construct(
        protected readonly PDO $pdo
    ){
    }

    public function startTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    protected function getObjectQuoteCharacter(): string
    {
        return self::QUOTE_IDENTIFIER;
    }

    protected function quoteIdentifier(string $objectName): string
    {
        if (
            str_starts_with($objectName, $this->getObjectQuoteCharacter()) &&
            str_ends_with($objectName, $this->getObjectQuoteCharacter())
        ) {
            return $objectName;
        }

        return $this->getObjectQuoteCharacter() . $objectName . $this->getObjectQuoteCharacter();
    }

    protected function qualifiedIdentifier(string $objectName, string $fieldName): string
    {
        return sprintf(
            '%s.%s',
            $this->quoteIdentifier($objectName),
            $this->quoteIdentifier($fieldName),
        );
    }

    protected function getDropTableSql(string $tableName, bool $isTempTable = false): string
    {
        return sprintf(
            static::SQL_DROP_TABLE,
            $isTempTable ? static::KEYWORD_TEMPORARY : '',
            $this->quoteIdentifier($tableName)
        );
    }

    protected function getSetForeignKeyChecksSql(bool $enable = false): string
    {
        return sprintf(
            static::SQL_SET_FK_CHECKS,
            $enable ? '1' : '0',
        );
    }

    protected function getListOfTableObjectsSql(): string
    {
        return static::SQL_GET_ALL_BASE_TABLES;
    }

    protected function getDefaultDatabase(): string
    {
        return static::SQL_GET_DEFAULT_DATABASE;
    }

    /**
     * This will return:
     *          SELECT *
     *          FROM `tableName`
     */
    protected function getAllRowsFromSql(string $tableName): string
    {
        return sprintf(
            static::SQL_SELECT_ALL_FROM,
            $this->quoteIdentifier($tableName)
        );
    }

    /**
     * This will return:
     *          SELECT *
     *          FROM `tableName`
     *          WHERE `tableName`.`fieldName` =
     */
    protected function getSqlGetRowByIdSql(
        string $tableName,
        string $fieldIdentifier,
    ): string {
        $tableName = $this->quoteIdentifier($tableName);
        $fieldIdentifier = $this->quoteIdentifier($fieldIdentifier);

        return sprintf(
            static::SQL_GET_ALL_BY_ID,
            $tableName,
            $this->qualifiedIdentifier($tableName, $fieldIdentifier),
        );
    }

    /**
     * This will return:
     *          SELECT `fieldName`
     *          FROM `tableName`
     *          WHERE `tableName`.`fieldName` =
     */
    protected function getSqlCheckRowExistSql(
        string $tableName,
        string $fieldIdentifier,
    ): string {
        $tableName = $this->quoteIdentifier($tableName);
        $fieldIdentifier = $this->quoteIdentifier($fieldIdentifier);

        return sprintf(
            static::SQL_CHECK_ROW_EXIST,
            $fieldIdentifier,
            $tableName,
            $this->qualifiedIdentifier($tableName, $fieldIdentifier),
        );
    }

    protected function getSelectAllFromTableLimitOffsetSql(
        string $tableName,
        int|null $limit = null,
        int|null $offset = null
    ): string{
        return sprintf(
            static::SQL_SELECT_ALL_LIMIT_OFFSET,
            $this->quoteIdentifier($tableName),
            $limit !== null ? "LIMIT {$limit}" : '',
            $offset !== null ? "OFFSET {$limit}" : '',
        );
    }

    protected function getTruncateTableSql(string $tableName): string
    {
        return sprintf(
            static::SQL_TRUNCATE_TABLE,
            $this->quoteIdentifier($tableName),
        );
    }

    protected function getUpdateByIdSql(string $tableName, array $ids, array $setValues): string
    {
        $idSql = implode(' AND ', array_map(
            static fn($id) => $this->quoteIdentifier($id) . ' = :' . $id, array_keys($ids)
        ));
        $whereCond = implode(', ', array_map(
            static fn($id) => $this->quoteIdentifier($id) . ' = :' . $id, array_keys($setValues)
        ));

        return sprintf(
            static::SQL_UPDATE_WHERE,
            $this->quoteIdentifier($tableName),
            $idSql,
            $whereCond,
        );
    }

    protected function getDeleteByIdSql(string $tableName, array $ids): string
    {
        $idSql = implode(' AND ', array_map(
            static fn($id) => $this->quoteIdentifier($id) . ' = :' . $id, array_keys($ids))
        );

        return sprintf(
                static::SQL_DELETE_WHERE,
                $this->quoteIdentifier($tableName),
                $idSql
            );
    }

    protected function execWhileDisableForeignKeyChecks(PDOStatement $statement): void
    {
        $this->pdo->exec($this->getSetForeignKeyChecksSql(false));
        $statement->execute();
        $this->pdo->exec($this->getSetForeignKeyChecksSql(true));
    }
}
