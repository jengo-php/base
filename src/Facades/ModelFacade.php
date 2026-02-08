<?php

namespace Jengo\Base\Facades;

use CodeIgniter\Model as CI4Model;
use RuntimeException;

/**
 * Class Model
 *
 * A static facade wrapper around CodeIgniter 4's base Model class, allowing for fluent and expressive query building via static calls.
 *
 * This class provides a clean, Laravel-like static interface to the underlying CI4 model methods,
 * making it easier to work with models without instantiating them manually. It also enhances `insert()` and `update()` 
 * by automatically capturing validation errors and storing them in the session flashdata, enabling easier error handling in views.
 *
 * ### Key Features:
 * - Static access to common query methods (`where()`, `find()`, `insert()`, etc.)
 * - Automatic instantiation via `__callStatic`
 * - Custom `insert()` and `update()` that store validation errors in session flashdata (`errors`)
 * - Useful for simplifying controller code and building expressive model interfaces
 *
 * ### Example:
 * ```php
 * $user = User::where('email', $email)->first();
 * User::insert(['name' => 'John']);
 * ```
 *
 * Note: Although this enables static usage, it still delegates execution to a live instance of the model behind the scenes.
 *
 * @package Jengo\Facades
 * 
 * @method static mixed find($id = null)
 * @method static mixed findAll(int $limit = 0, int $offset = 0)
 * @method static mixed first()
 * @method static mixed getCompiledSelect(bool $reset = true)
 * @method static static select(string $select = '*', bool $escape = null)
 * @method static static selectMax(string $select = '', string $alias = '')
 * @method static static selectMin(string $select = '', string $alias = '')
 * @method static static selectAvg(string $select = '', string $alias = '')
 * @method static static selectSum(string $select = '', string $alias = '')
 * @method static static join(string $table, string $cond, string $type = '', bool $escape = null)
 * @method static static where(string|array $key, mixed $value = null, bool $escape = null)
 * @method static static orWhere(string|array $key, mixed $value = null, bool $escape = null)
 * @method static static whereIn(string $key = null, array $values = null, bool $escape = null)
 * @method static static orWhereIn(string $key = null, array $values = null, bool $escape = null)
 * @method static static whereNotIn(string $key = null, array $values = null, bool $escape = null)
 * @method static static orWhereNotIn(string $key = null, array $values = null, bool $escape = null)
 * @method static static like(string|array $field, string $match = '', string $side = 'both', bool $escape = null)
 * @method static static orLike(string|array $field, string $match = '', string $side = 'both', bool $escape = null)
 * @method static static notLike(string|array $field, string $match = '', string $side = 'both', bool $escape = null)
 * @method static static orNotLike(string|array $field, string $match = '', string $side = 'both', bool $escape = null)
 * @method static static groupBy(string|string[] $by, bool $escape = null)
 * @method static static having(string|array $key, string $value = null, bool $escape = null)
 * @method static static orderBy(string $orderBy, string $direction = '', bool $escape = null)
 * @method static static set(mixed $key, string $value = '', bool $escape = null)
 * @method static static limit(int $value, int $offset = 0)
 * @method static static offset(int $offset)
 * @method static static countAllResults(bool $reset = true)
 * @method static static countAll()
 * @method static mixed insert(array|object|null $data = null, bool $returnID = true)
 * @method static bool insertBatch(array $set = null, bool $escape = null, int $batchSize = 100)
 * @method static bool update($id = null, $data = null)
 * @method static bool updateBatch(array $set = null, string $index = null, int $batchSize = 100)
 * @method static bool delete($id = null, bool $purge = false)
 * @method static array errors()
 * @method static string getLastQuery()
 * @method static bool save(array|object|null $data)
 * @method static array asArray()
 * @method static object asObject(string $className = 'stdClass')
 * @method static mixed with(string ...$associations)
 */

class ModelFacade
{
    protected static string $class;

    public function __construct(string $model)
    {
        static::$class = $model;
    }

    protected static function instance(): CI4Model
    {
        if (!class_exists(class: self::$class)) {
            throw new RuntimeException("Class is undefined");
        }

        $instance = new (self::$class)();

        if(!($instance instanceof CI4Model)) {
            throw new RuntimeException("Class must extend ci4's Model class");
        }

        return $instance;
    }

    public static function __callStatic($method, $args)
    {
        $instance = self::instance();

        return $instance->$method(...$args);
    }
}