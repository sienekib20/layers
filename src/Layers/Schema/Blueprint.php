<?php

namespace Sienekib\Layers\Schema;

class Blueprint
{
    protected $table;
    protected $columns = [];
    protected $primaryKeys = [];
    protected $uniqueKeys = [];
    protected $indexes = [];
    protected $foreignKeys = [];
    protected $checks = [];
    protected $columnModifiers = [];
    protected $currentColumn = '';

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    // Tipos de Coluna
    public function increments(string $column)
    {
        $this->columns[] = "{$column} INT AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    public function string(string $column, int $length = 255)
    {
        $this->columns[] = "{$column} VARCHAR({$length})";
        $this->currentColumn = $column;
        return $this;
    }

    public function integer(string $column)
    {
        $this->columns[] = "{$column} INT";
        $this->currentColumn = $column;
        return $this;
    }

    public function boolean(string $column)
    {
        $this->columns[] = "{$column} TINYINT(1)";
        $this->currentColumn = $column;
        return $this;
    }

    public function text(string $column)
    {
        $this->columns[] = "{$column} TEXT";
        $this->currentColumn = $column;
        return $this;
    }

    public function date(string $column)
    {
        $this->columns[] = "{$column} DATE";
        $this->currentColumn = $column;
        return $this;
    }

    public function dateTime(string $column)
    {
        $this->columns[] = "{$column} DATETIME";
        $this->currentColumn = $column;
        return $this;
    }

    public function decimal(string $column, int $precision = 8, int $scale = 2)
    {
        $this->columns[] = "{$column} DECIMAL({$precision}, {$scale})";
        $this->currentColumn = $column;
        return $this;
    }

    public function float(string $column, int $precision = 8, int $scale = 2)
    {
        $this->columns[] = "{$column} FLOAT({$precision}, {$scale})";
        $this->currentColumn = $column;
        return $this;
    }

    public function double(string $column, int $precision = 8, int $scale = 2)
    {
        $this->columns[] = "{$column} DOUBLE({$precision}, {$scale})";
        $this->currentColumn = $column;
        return $this;
    }

    public function time(string $column)
    {
        $this->columns[] = "{$column} TIME";
        $this->currentColumn = $column;
        return $this;
    }

    public function timestamp(string $column)
    {
        $this->columns[] = "{$column} TIMESTAMP";
        $this->currentColumn = $column;
        return $this;
    }

    public function enum(string $column, array $values)
    {
        $enumValues = implode("', '", $values);
        $this->columns[] = "{$column} ENUM('{$enumValues}')";
        $this->currentColumn = $column;
        return $this;
    }

    public function timestamps()
    {
        $this->columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    // Modificadores de Coluna
    public function nullable()
    {
        $this->columnModifiers[$this->currentColumn][] = "NULL";
        return $this;
    }

    public function notNullable()
    {
        $this->columnModifiers[$this->currentColumn][] = "NOT NULL";
        return $this;
    }

    public function default($value)
    {
        $this->columnModifiers[$this->currentColumn][] = "DEFAULT '{$value}'";
        return $this;
    }

    public function unsigned()
    {
        $this->columnModifiers[$this->currentColumn][] = "UNSIGNED";
        return $this;
    }

    public function after(string $column)
    {
        $this->columnModifiers[$this->currentColumn][] = "AFTER {$column}";
        return $this;
    }

    public function comment(string $comment)
    {
        $this->columnModifiers[$this->currentColumn][] = "COMMENT '{$comment}'";
        return $this;
    }

    public function first()
    {
        $this->columnModifiers[$this->currentColumn][] = "FIRST";
        return $this;
    }

    // Constraints
    public function unique()
    {
        $this->uniqueKeys[] = $this->currentColumn;
        return $this;
    }

    public function index(string $column)
    {
        $this->indexes[] = $column;
        return $this;
    }

    public function primary(string $column)
    {
        $this->primaryKeys[] = $column;
        return $this;
    }

    public function check(string $expression)
    {
        $this->checks[] = $expression;
        return $this;
    }

    public function foreign(string $column, string $references, string $on, string $onDelete = 'CASCADE', string $onUpdate = 'CASCADE')
    {
        $this->foreignKeys[] = [
            'column' => $column,
            'references' => $references,
            'on' => $on,
            'onDelete' => $onDelete,
            'onUpdate' => $onUpdate,
        ];
        return $this;
    }

    public function renameColumn(string $oldName, string $newName)
    {
        $this->columns[] = "RENAME COLUMN {$oldName} TO {$newName}";
        return $this;
    }

    public function dropColumn(string $column)
    {
        $this->columns[] = "DROP COLUMN {$column}";
        return $this;
    }

    public function changeColumn(string $column, callable $callback)
    {
        $blueprint = new Blueprint($this->table);
        $callback($blueprint);
        $definition = array_pop($blueprint->columns);
        $this->columns[] = "MODIFY COLUMN {$column} {$definition}";
        return $this;
    }

    public function renameTable(string $newName)
    {
        $this->columns[] = "RENAME TO {$newName}";
        return $this;
    }

    // Geração do SQL
    public function toSql(): string
    {
        $columnDefinitions = [];

        foreach ($this->columns as $column) {
            $modifiers = isset($this->columnModifiers[$this->currentColumn]) ? ' ' . implode(' ', $this->columnModifiers[$this->currentColumn]) : '';
            $columnDefinitions[] = "{$column}{$modifiers}";
        }

        $columns = implode(", ", $columnDefinitions);

        /*foreach ($this->columnModifiers as $modifiers) {
            foreach ($modifiers as $modifier)   
                $columns .= " " . $modifier;
        }*/

        if (!empty($this->primaryKeys)) {
            $columns .= ", PRIMARY KEY (" . implode(", ", $this->primaryKeys) . ")";
        }

        foreach ($this->uniqueKeys as $uniqueKey) {
            $columns .= ", UNIQUE ({$uniqueKey})";
        }

        foreach ($this->indexes as $index) {
            $columns .= ", INDEX ({$index})";
        }

        foreach ($this->foreignKeys as $foreignKey) {
            $columns .= ", FOREIGN KEY ({$foreignKey['column']}) REFERENCES {$foreignKey['on']}({$foreignKey['references']}) ON DELETE {$foreignKey['onDelete']} ON UPDATE {$foreignKey['onUpdate']}";
        }

        foreach ($this->checks as $check) {
            $columns .= ", CHECK ({$check})";
        }

        return "CREATE TABLE {$this->table} ({$columns})";
    }
}
