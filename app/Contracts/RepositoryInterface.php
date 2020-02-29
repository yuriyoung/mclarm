<?php
namespace App\Contracts;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    /**
     * Query scope
     *
     * @param Closure $scope
     * @return $this
     */
    public function scopeQuery(Closure $scope);

    /**
     *  Rset query scope
     * @return $this
     */
    public function resetScope();

    /**
     * Stop repository update functions from returning a fresh
     * model when changes are committed.
     *
     * @return $this
     */
    public function withoutFreshModel();

    /**
     * Return a fresh model with a repository updates a model.
     *
     * @return $this
     */
    public function withFreshModel();

    /**
     * Set whether or not the repository should return a fresh model
     * when changes are committed.
     *
     * @param bool $fresh
     * @return $this
     */
    public function setFreshModel(bool $fresh = true);

    /**
     * Return first model or new one
     *
     * @param array $attributes
     * @return mixed
     */
    public function firstOrNew(array $attributes = []);

    /**
     * Get first model or create new one
     *
     * @param array $attributes
     * @return mixed
     */
    public function firstOrCreate(array $attributes = []);

    /**
     * Find a model that has the specific ID passed.
     *
     * @param int $id
     * @param array $columns
     * @return mixed
     *
     */
    public function find(int $id, array $columns = ['*']);

    /**
     * Find a modal by field and value
     *
     * @param string $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findBy(string $attribute, $value, array $columns = ['*']);

    /**
     * Find a model matching an array of where clauses.
     *
     * @param array $fields
     * @param array $columns
     * @return mixed
     */
    public function findWhere(array $fields, array $columns = ['*']);

    /**
     * Find a result set by multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return mixed
     */
    public function findWhereIn($field, array $values, array $columns = ['*']);

    /**
     * Find a result set by excluding multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return mixed
     */
    public function findWhereNotIn($field, array $values, array $columns = ['*']);

    /**
     * Find a result set by between values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return mixed
     */
    public function findWhereBetween($field, array $values, array $columns = ['*']);

    /**
     * Find and return the first matching instance for the given fields.
     *
     * @param array $fields
     * @param array $columns
     * @return mixed
     *
     */
    public function findFirstWhere(array $fields, array $columns = ['*']);

    /**
     * Return a count of records matching the passed arguments.
     *
     * @param array $fields
     * @param string $column
     * @return int
     */
    public function findCountWhere(array $fields, $column = '*'): int;

    /**
     * Get a model which was soft deleted that has the specific ID passed.
     * @param int $id
     * @param array $columns
     * @return mixed
     *
     */
    public function findTrashed(int $id, array $columns = ['*']);

    /**
     * Return a result set only soft deleted.
     *
     * @param array $columns
     * @return Collection
     */
    public function allTrashed(array $columns = ['*']): Collection;

    /**
     * Create a new model instance and persist it to the database.
     * This does not perform any model data validation.
     *
     * @param array $attributes
     * @param bool $force
     * @return mixed
     */
    public function create(array $attributes, bool $force = false);

    /**
     * Update a given ID with the passed array of fields.
     * This does not perform any model data validation.
     *
     * @param int $id
     * @param array $attributes
     * @param bool $force
     * @return mixed
     */
    public function update($id, array $attributes, bool $force = false);

    /**
     * @param $id
     * @param array $relations
     * @param array $attributes
     * @param bool $force
     * @return mixed
     */
    public function updateWithRelations($id, array $relations, array $attributes, bool $force = false);

    /**
     * Perform a mass update where matching records are updated using whereIn.
     * This does not perform any model data validation.
     *
     * @param string $column
     * @param array $values
     * @param array $fields
     * @return int
     */
    public function updateWhereIn(string $column, array $values, array $fields): int;

    /**
     * Update a record if it exists in the database, otherwise create it.
     * This does not perform any model data validation.
     *
     * @param array $where
     * @param array $attributes
     * @param bool $force
     * @return mixed
     */
    public function updateOrCreate(array $where, array $attributes, bool $force = false);

    /**
     * Delete a given record from the database.
     *
     * @param int $id
     * @return int
     */
    public function destroy(int $id): int;

    /**
     * Delete records matching the given attributes.
     *
     * @param array $attributes
     * @return int
     */
    public function destroyWhere(array $attributes): int;

    /**
     * Sync relations
     *
     * @param string|int $id
     * @param string $relation
     * @param array $attributes
     * @param bool $detaching
     * @return mixed
     */
    public function sync($id, $relation, array $attributes, bool $detaching = true);

    /**
     * Sync relations without detaching
     *
     * @param string|int $id
     * @param string $relation
     * @param array $attributes
     * @return mixed
     */
    public function syncWithoutDetaching($id, string $relation, array $attributes);

    /**
     * Return all records associated with the given model.
     *
     * @param array $columns
     * @return Collection
     */
    public function all($columns = ['*']): Collection;

    /**
     * Return a paginated result set.
     *
     * @param int $limit
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $limit = null, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Get the amount of entries in the database.
     *
     * @param array $where
     * @param string $column
     * @return int
     */
    public function count(array $where = [], $column = '*'): int;

    /**
     * Return a result set fro populate fields select
     *
     * @param string $column
     * @param string|null $key
     *
     * @return Collection
     */
    public function pluck(string $column, $key = null): Collection;

    /**
     * Order collection by a given column
     *
     * @param string $column
     * @param string $direction
     * @return mixed
     */
    public function orderBy(string $column, $direction = 'ASC');

    /**
     * Load relations
     *
     * @param array|string $relations
     * @return $this
     */
    public function with($relations);

    /**
     * Load relation with closure
     * @param $relation
     * @param Closure $closure
     * @return $this
     */
    public function whereHas($relation, Closure $closure);

    /**
     * @param $relations
     *
     * @return $this
     */
    public function withCount($relations);

    /**
     * Set hidden fields
     *
     * @param array $fields
     * @return $this
     */
    public function hidden(array $fields);

    /**
     * Set visible fields
     *
     * @param array $fields
     * @return $this
     */
    public function visible(array $fields);
}
