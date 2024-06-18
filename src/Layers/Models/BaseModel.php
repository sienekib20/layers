<?php

namespace Sienekib\Layers\Models;

use Sienekib\Layers\Factory\Layer;
use Sienekib\Layers\Utils\Inflector;

/**
 * Class BaseModel
 * @package Sienekib\Layers\Models
 *
 * This class serves as a base model for other models to extend.
 */
class BaseModel
{
    /**
     * @var string Nome da tabela associada ao modelo.
     */
    protected static $table;

    /**
     * @var array Lista de campos que podem ser preenchidos em operações de create e update.
     */
    protected static $fillable = [];

    /**
     * Retorna todos os registros da tabela.
     *
     * @return array Array de registros
     */
    public static function all(): array
    {
        return Layer::table(static::$table)->select("1", []);
    }

    /**
     * Busca um registro pelo ID.
     *
     * @param int $id ID do registro a ser buscado
     * @return array|null Registro encontrado ou null se não encontrado
     */
    public static function find(int $id): ?array
    {
        $tableIdFieldName = Inflector::singularize(static::$table, 'pt') . '_id';
        $results = Layer::table(static::$table)->select("{$tableIdFieldName} = ?", [$id]);
        return $results[0] ?? null;
    }

    /**
     * Cria um novo registro na tabela.
     *
     * @param array $attributes Atributos a serem preenchidos no novo registro
     * @param bool $returnLastId Se true, retorna o ID do último registro inserido
     * @return bool|int Retorna true se a inserção for bem sucedida, ou o ID do último registro inserido
     */
    public static function create(array $attributes, bool $returnLastId = false)
    {
        $fillableAttributes = array_intersect_key($attributes, array_flip(static::$fillable));
        return Layer::table(static::$table)->insert($fillableAttributes, $returnLastId);
    }

    /**
     * Atualiza um registro pelo ID.
     *
     * @param array $attributes Novos valores dos atributos a serem atualizados
     * @param int $id ID do registro a ser atualizado
     * @return bool Retorna true se a atualização for bem sucedida, false caso contrário
     */
    public static function update(array $attributes, int $id): bool
    {
        $tableIdFieldName = Inflector::singularize(static::$table, 'pt') . '_id';
        $fillableAttributes = array_intersect_key($attributes, array_flip(static::$fillable));
        return Layer::table(static::$table)->update($fillableAttributes, "{$tableIdFieldName} = ?", [$id]);
    }

    /**
     * Deleta um registro pelo ID.
     *
     * @param int $id ID do registro a ser deletado
     * @return bool Retorna true se a deleção for bem sucedida, false caso contrário
     */
    public static function delete(int $id): bool
    {
        $tableIdFieldName = Inflector::singularize(static::$table, 'pt') . '_id';
        return Layer::table(static::$table)->delete("{$tableIdFieldName} = ?", [$id]);
    }

    /**
     * Define os campos que podem ser preenchidos em operações de create e update.
     *
     * @param array $fillable Campos permitidos para fillable
     */
    public static function fillable(array $fillable)
    {
        static::$fillable = $fillable;
    }

    /**
     * Obtém os campos fillable do modelo.
     *
     * @return array Campos fillable
     */
    public static function getFillable(): array
    {
        return static::$fillable;
    }

    /**
     * Salva ou atualiza um registro baseado nos atributos fornecidos.
     * Se o atributo 'id' estiver presente, tenta atualizar o registro correspondente;
     * caso contrário, cria um novo registro.
     *
     * @param array $attributes Atributos a serem preenchidos no registro
     * @return bool|int Retorna true se a operação for bem sucedida, ou o ID do último registro inserido
     */
    public static function save(array $attributes)
    {
        if (isset($attributes['id'])) {
            $id = $attributes['id'];
            unset($attributes['id']);
            return static::update($attributes, $id);
        } else {
            return static::create($attributes, true);
        }
    }

    /**
     * Busca um registro pelo nome de usuário.
     *
     * @param string $username Nome de usuário a ser buscado
     * @return array|null Registro encontrado ou null se não encontrado
     */
    public static function findByUsername(string $username): ?array
    {
        $results = Layer::table(static::$table)->select("username = ?", [$username]);
        return $results[0] ?? null;
    }

    /**
     * Busca registros com base em condições fornecidas.
     *
     * @param string $conditions Condições de seleção (ex: "username = ? AND email = ?")
     * @param array $params Parâmetros para substituir na condição
     * @return array Array de registros encontrados
     */
    public static function where(string $conditions, array $params = []): array
    {
        return Layer::table(static::$table)->select($conditions, $params);
    }

    /**
     * Conta o número total de registros na tabela.
     *
     * @return int Número total de registros na tabela
     */
    public static function count(): int
    {
        return Layer::table(static::$table)->count();
    }

    /**
     * Obtém o primeiro registro da tabela.
     *
     * @return array|null Primeiro registro da tabela ou null se não houver registros
     */
    public static function first(): ?array
    {
        $results = Layer::table(static::$table)->select("1", [], 1);
        return $results[0] ?? null;
    }

    /**
     * Obtém o último registro da tabela.
     *
     * @return array|null Último registro da tabela ou null se não houver registros
     */
    public static function last(): ?array
    {
        return Layer::table(static::$table)->select("1", [], 1, true)[0] ?? null;
    }

    /**
     * Busca todos os registros que correspondem às condições fornecidas.
     *
     * @param string $conditions Condições de seleção (ex: "active = 1")
     * @param array $params Parâmetros para substituir na condição
     * @return array Array de registros encontrados
     */
    public static function findAll(string $conditions = '', array $params = []): array
    {
        return Layer::table(static::$table)->select($conditions ?: "1", $params);
    }

    /**
     * Deleta registros com base nas condições fornecidas.
     *
     * @param string $conditions Condições de seleção (ex: "active = 0")
     * @param array $params Parâmetros para substituir na condição
     * @return bool Retorna true se a deleção for bem sucedida, false caso contrário
     */
    public static function deleteWhere(string $conditions, array $params = []): bool
    {
        return Layer::table(static::$table)->delete($conditions, $params);
    }

    /**
     * Trunca a tabela, deletando todos os seus registros.
     *
     * @return bool Retorna true se a operação de truncar for bem sucedida, false caso contrário
     */
    public static function truncate(): bool
    {
        return Layer::table(static::$table)->truncate();
    }

    /**
     * Verifica se um registro existe com base nas condições fornecidas.
     *
     * @param string $conditions Condições de seleção (ex: "username = ? AND email = ?")
     * @param array $params Parâmetros para substituir na condição
     * @return bool Retorna true se o registro existir, false caso contrário
     */
    public static function exists(string $conditions, array $params = []): bool
    {
        return Layer::table(static::$table)->exists($conditions, $params);
    }
}
