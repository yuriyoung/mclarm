<?php


namespace App\Repositories;

use App\Exceptions\RepositoryException;
use Closure;
use Exception;
use App\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class Repository implements RepositoryInterface
{
    /**
     * @var \Illuminate\Foundation\Application;
     */
    protected $app;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $columns = ['*'];

    /**
     * @var Closure
     */
    protected $scopeQuery = null;

    /**
     * @var bool
     */
    protected $withFresh = true;

    /**
     * AbstractRepository constructor.
     *
     * @param Application; $application
     */
    public function __construct(Application $application)
    {
        $this->app = $application;
        $this->initializeModel($this->model());
        $this->boot();
    }

    /**
     * Return an identifier or Model object to be used by the repository.
     * Specify Model class name. eg: User::class
     *
     * @return string
     */
    abstract public function model();

    /**
     * eg: push criteria for repository
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Return an instance of the eloquent model bound to this
     * repository instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Return an instance of the builder to use for this repository.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getBuilder()
    {
//        if ($this->model instanceof Builder) {
//            return $this->model;
//        } else {
//            $results = $this->model->newQuery();
//        }

        return $this->model->newQuery();
    }

    /**
     * Query scope
     *
     * @param Closure $scope
     * @return $this
     */
    public function scopeQuery(Closure $scope)
    {
        $this->scopeQuery = $scope;
        return $this;
    }

    /**
     *  Rset query scope
     * @return $this
     */
    public function resetScope()
    {
        $this->scopeQuery = null;
        return $this;
    }

    /**
     * Stop repository update functions from returning a fresh
     * model when changes are committed.
     *
     * @return $this
     */
    public function withoutFreshModel()
    {
        return $this->setFreshModel(false);
    }

    /**
     * Return a fresh model with a repository updates a model.
     *
     * @return $this
     */
    public function withFreshModel()
    {
        return $this->setFreshModel(true);
    }

    /**
     * Set whether or not the repository should return a fresh model
     * when changes are committed.
     *
     * @param bool $fresh
     * @return $this
     */
    public function setFreshModel(bool $fresh = true)
    {
        $clone = clone $this;
        $clone->withFresh = $fresh;
        return $clone;
    }

    /**
     * @param array $columns
     * @return Collection
     * @throws RepositoryException
     */
    public function get($columns = ['*'])
    {
        return $this->all($columns);
    }

    /**
     * Get first model of repository
     *
     * @param array $columns
     * @return Builder|\Illuminate\Database\Eloquent\Model
     * @throws RepositoryException
     */
    public function first($columns = ['*'])
    {
        $this->applyScope();
        $result = $this->getBuilder()->first($columns);

        $this->resetModel();

        return $result;
    }

    /**
     * @param int $id
     * @param array $columns
     * @return Builder|Model|mixed
     * @throws RepositoryException
     */
    public function firstOrFail(int $id, array $columns = ['*'])
    {
        $this->applyScope();
        $result = $this->getBuilder()->firstOrFail($columns);

        $this->resetModel();

        return $result;
    }

    /**
     * @param array $wheres
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     * @throws RepositoryException
     */
    public function firstWhere(array $wheres, array $columns = ['*'])
    {
        $this->applyScope();
        $this->applyCondition($wheres);
        $model = $this->getBuilder()->first($columns);

        $this->resetModel();

        return $model;
    }

    /**
     * @param array $wheres
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     * @throws \App\Exceptions\RepositoryException
     */
    public function firstOrWhere(array $wheres, array $columns = ['*'])
    {
        $this->applyScope();
        $this->applyOrCondition($wheres);
        $model = $this->getBuilder()->first($columns);

        $this->resetModel();

        return $model;
    }

    /**
     * Return first model or new one
     *
     * @param array $attributes
     * @return Builder|\Illuminate\Database\Eloquent\Model|mixed
     * @throws RepositoryException
     */
    public function firstOrNew(array $attributes = [])
    {
        $this->applyScope();
        $result = $this->getBuilder()->firstOrNew($attributes);

        $this->resetModel();

        return $result;
    }

    /**
     * Get first model or create new one
     *
     * @param array $attributes
     * @return Builder|\Illuminate\Database\Eloquent\Model|mixed
     * @throws RepositoryException
     */
    public function firstOrCreate(array $attributes = [])
    {
        $this->applyScope();
        $result = $this->getBuilder()->firstOrCreate($attributes);

        $this->resetModel();

        return $result;
    }

    /**
     * Find a model that has the specific ID passed.
     *
     * @param int $id
     * @param array $columns
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     * @throws RepositoryException
     */
    public function find(int $id, array $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->getBuilder()->find($id, $columns);
        $this->resetModel();

        return $model;
    }

    /**
     * @param int $id
     * @param array $columns
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|Model
     * @throws RepositoryException
     */
    public function findOrFail(int $id, array  $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->getBuilder()->findOrFail($id, $columns);

        $this->resetModel();

        return $model;
    }

    /**
     * Find a modal by field and value
     *
     * @param string $attribute
     * @param $value
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|mixed
     * @throws RepositoryException
     */
    public function findBy(string $attribute, $value, array $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->getBuilder()->where($attribute, '=', $value)->get($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Find a model matching an array of where clauses.
     *
     * @param array $where
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|mixed
     * @throws RepositoryException
     */
    public function findWhere(array $where, array $columns = ['*'])
    {
        $this->applyScope();
        $this->applyCondition($where);

        $model = $this->getBuilder()->get($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Find a result set by multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|mixed
     * @throws RepositoryException
     */
    public function findWhereIn($field, array $values, array $columns = ['*'])
    {
        $this->applyScope();

        $model = $this->getBuilder()->whereIn($field, $values)->get($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Find a result set by excluding multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Collection|mixed
     * @throws RepositoryException
     */
    public function findWhereNotIn($field, array $values, array $columns = ['*'])
    {
        $this->applyScope();

        $model = $this->getBuilder()->whereNotIn($field, $values)->get($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Find a result set by between values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|mixed
     * @throws RepositoryException
     */
    public function findWhereBetween($field, array $values, array $columns = ['*'])
    {
        $this->applyScope();

        $model = $this->getBuilder()->whereBetween($field, $values)->get($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Find a result set by not between values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Collection
     * @throws RepositoryException
     */
    public function findWhereNotBetween($field, array $values, array $columns = ['*'])
    {
        $this->applyScope();

        $model = $this->getBuilder()->whereNotBetween($field, $values)->get($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Find and return the first matching instance for the given fields.
     *
     * @param array $where
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed
     * @throws RepositoryException
     */
    public function findFirstWhere(array $where, array $columns = ['*'])
    {
        $this->applyScope();

//        $model = $this->getBuilder()->firstOrFail($where)->get($columns);
        $model = $this->getBuilder()->firstWhere($where)->get($columns);

        $this->resetModel();

        return $model;
    }

    /**
     * Return a count of records matching the passed arguments.
     *
     * @param array $where
     * @param string $column
     * @return int
     * @throws RepositoryException
     */
    public function findCountWhere(array $where, $column = '*'): int
    {
        $this->applyScope();
        $this->applyCondition($where);

        $result = $this->getBuilder()->count($column);
        $this->resetModel();

        return $result;
    }

    /**
     * Get a model which was soft deleted that has the specific ID passed.
     *
     * @param int $id
     * @param array $columns
     * @return Model|mixed
     * @throws RepositoryException
     */
    public function findTrashed(int $id, array $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->model->onlyTrashed()->findOrFail($id, $columns);
//        $model = $this->getBuilder()->onlyTrashed()->findOrFail($id, $columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Return a result set only soft deleted.
     *
     * @param array $columns
     * @return Collection
     * @throws RepositoryException
     */
    public function allTrashed(array $columns = ['*']): Collection
    {
        $this->applyScope();
        $results = $this->model->onlyTrashed()->get($columns);
//        $results = $this->getBuilder()->onlyTrashed()->get($columns);
        $this->resetModel();

        return $results;
    }

    /**
     * Create a new model instance and persist it to the database.
     * This does not perform any model data validation.
     *
     * @param array $attributes
     * @param bool $force
     * @return Builder|\Illuminate\Database\Eloquent\Model|mixed
     * @throws RepositoryException
     */
    public function create(array $attributes, bool $force = false)
    {
        $model = $this->getBuilder()->newModelInstance();
        ($force) ? $model->forceFill($attributes) : $model->fill($attributes);

        $model->save();
        if ($this->withFresh)
            $model->fresh();

        $this->resetModel();

        return $model;
    }

    /**
     * Update a given ID with the passed array of fields.
     * This does not perform any model data validation.
     *
     * @param int $id
     * @param array $attributes
     * @param bool $force
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     * @throws RepositoryException
     */
    public function update($id, array $attributes, bool $force = false)
    {
        $model = $this->getBuilder()->findOrFail($id);
        ($force) ? $model->forceFill($attributes) : $model->fill($attributes);

        // use the push() to update relation
        $relations = array_keys($model->getRelations()); // ['relation' => relationModel, ...]
        foreach ($relations as $relation) {
            if($model->{$relation}) {
                $model->{$relation}->fill($attributes["{$relation}"] ?? $attributes);
            }
        }

        (empty($relations)) ? $model->save() : $model->push();
        if ($this->withFresh)
            $model->fresh();

        $this->resetModel();

        return $model;
    }

    /**
     * update the model and relations
     * This does not perform any model data validation.
     * using the push() method for update relations
     *
     * @param $id
     * @param array $relations
     * @param array $attributes
     * @param bool $force
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @throws RepositoryException
     */
    public function updateWithRelations($id, array $relations, array $attributes, bool $force = false)
    {
        $model = $this->getBuilder()->findOrFail($id);
        ($force) ? $model->forceFill($attributes) : $model->fill($attributes);

        foreach ($relations as $relation) {
            if($model->{$relation}) {
                $model->{$relation}->fill($attributes["{$relation}"] ?? $attributes);
            }
        }

        $model->push();
        if ($this->withFresh)
            $model->fresh();

        $this->resetModel();

        return $model;
    }

    /**
     * Perform a mass update where matching records are updated using whereIn.
     * This does not perform any model data validation.
     *
     * @param string $where
     * @param array $values
     * @param array $fields
     * @return int
     * @throws RepositoryException
     */
    public function updateWhereIn(string $where, array $values, array $fields): int
    {
        assert(empty($column), 'First argument passed to updateWhereIn must be a non-empty string.');

        $result = $this->getBuilder()->whereIn($where, $values)->update($fields);
        $this->resetModel();

        return $result;
    }

    /**
     * Update a record if it exists in the database, otherwise create it.
     *
     * @param array $attributes
     * @param array $values
     * @param bool $force
     * @return Builder|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function updateOrCreate(array $attributes, array $values, bool $force = false)
    {
        $this->applyScope();

        $model = $this->getBuilder()->updateOrCreate($attributes, $values);
        if ($this->withFresh)
            $model->fresh();

        return $model;
    }

    /**
     * Delete a given record from the database.
     *
     * @param int $id
     * @param bool $force
     * @return int
     * @throws Exception
     */
    public function destroy(int $id, $force = false): int
    {
        $this->applyScope();

        $model = $this->find($id);
        // $originalModel = clone $model;

        return ($force) ? $model->forceDelete() : $model->delete();
    }

    /**
     * Delete records matching the given attributes.
     *
     * @param array $where
     * @return int
     * @throws RepositoryException
     */
    public function destroyWhere(array $where): int
    {
        $this->applyScope();
        $this->applyCondition($where);

        $deleted = $this->getBuilder()->delete();

        $this->resetModel();

        return $deleted;
    }

    /**
     * Sync relations
     *
     * @param string|int $id
     * @param string $relation
     * @param array $attributes
     * @param bool $detaching
     * @return mixed
     * @throws Exception
     */
    public function sync($id, $relation, array $attributes, bool $detaching = true)
    {
        return $this->find($id)->{$relation}()->sync($attributes, $detaching);
    }

    /**
     * Sync relations without detaching
     *
     * @param int|string $id
     * @param string $relation
     * @param array $attributes
     * @return mixed
     * @throws Exception
     */
    public function syncWithoutDetaching($id, string $relation, array $attributes)
    {
        return $this->sync($id, $relation, $attributes, false);
    }

    /**
     * Insert a single or multiple records into the database at once skipping
     * validation and mass assignment checking.
     *
     * @param array $data
     * @return bool
     */
    public function insert(array $data): bool
    {
        return $this->getBuilder()->insert($data);
    }

    /**
     * Insert multiple records into the database and ignore duplicates.
     *
     * @param array $values
     * @return bool
     */
    public function insertIgnore(array $values): bool
    {
        if (empty($values)) {
            return true;
        }

        foreach ($values as $key => $value) {
            ksort($value);
            $values[$key] = $value;
        }

        $bindings = array_values(array_filter(Arr::flatten($values, 1), function ($binding) {
            return ! $binding instanceof RepositoryException;
        }));

        $grammar = $this->getBuilder()->toBase()->getGrammar();
        $table = $grammar->wrapTable($this->model->getTable());
        $columns = $grammar->columnize(array_keys(reset($values)));

        $parameters = collect($values)->map(function ($record) use ($grammar) {
            return sprintf('(%s)', $grammar->parameterize($record));
        })->implode(', ');

        $statement = "insert ignore into $table ($columns) values $parameters";

        return $this->getBuilder()->getConnection()->statement($statement, $bindings);
    }

    /**
     * Return all records associated with the given model.
     *
     * @param array $columns
     * @return \Illuminate\Support\Collection
     * @throws RepositoryException
     */
    public function all($columns = ['*']): Collection
    {
        $this->applyScope();
        $results = $this->getBuilder()->get($columns);

        $this->resetScope();
        $this->resetModel();

        return $results;
    }

    /**
     * Return a paginated result set.
     *
     * @param int $limit
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws RepositoryException
     */
    public function paginate(int $limit = null, array $columns = ['*']): LengthAwarePaginator
    {
        $this->applyScope();
        $limit = is_null($limit) ? 15 : $limit;
        $results = $this->getBuilder()->paginate($limit, $columns);

        $this->resetScope();
        $this->resetModel();

        return $results;
    }

    /**
     *
     * @param $limit
     * @return Builder
     * @throws RepositoryException
     */
    public function limit($limit)
    {
        $this->applyScope();
        $results = $this->getBuilder()->limit($limit);

        $this->resetModel();

        return $results;
    }

    /**
     * Get the amount of entries in the database.
     *
     * @param array $where
     * @param string $column
     * @return int
     * @throws RepositoryException
     */
    public function count(array $where = [], $column = '*'): int
    {
        if ($where) {
            $this->applyCondition($where);
        }
        $result = $this->getBuilder()->count($column);

        $this->resetModel();
        $this->resetScope();

        return $result;
    }

    /**
     * Return a result set fro populate fields select
     *
     * @param string $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function pluck(string $column, $key = null): Collection
    {
        return $this->getBuilder()->pluck($column, $key);
    }

    /**
     * Order collection by a given column
     *
     * @param string $column
     * @param string $direction
     * @return mixed
     */
    public function orderBy(string $column, $direction = 'ASC')
    {
        $this->model = $this->model->orderBy($column, $direction);
        return $this;
    }

    /**
     * Load relations
     *
     * @param array|string $relations
     * @return $this
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);
        return $this;
    }

    /**
     * Load relation with closure
     *
     * @param $relation
     * @param Closure $closure
     * @return $this
     */
    public function whereHas($relation, Closure $closure)
    {
        $this->model = $this->model->whereHas($relation, $closure);
        return $this;
    }

    /**
     * @param $relations
     *
     * @return $this
     */
    public function withCount($relations)
    {
        $this->model = $this->model->withCount($relations);
        return $this;
    }

    /**
     * Check if entity has relation
     *
     * @param $relation
     * @return $this
     */
    public function has($relation)
    {
        $this->model = $this->model->has($relation);
        return $this;
    }

    /**
     * Set hidden fields
     * TODO: has a bug(can not hidden field)
     *
     * @param array $fields
     * @return $this
     */
    public function hidden(array $fields)
    {
        $this->model->setHidden($fields);
        return $this;
    }

    /**
     * Set visible fields
     * TODO: BUG - Can not visible fields
     *
     * @param array $fields
     * @return $this
     */
    public function visible(array $fields)
    {
        $this->model->setVisible($fields);
        return $this;
    }

    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    protected function applyScope()
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
            $this->model = $callback($this->model);
        }

        return $this;
    }

    /**
     * Applies the given where conditions to the model.
     *
     * @param array $where
     * @return void
     */
    protected function applyCondition(array $where): void
    {
        foreach ($where as $field => $value) {
            if(is_array($value)) {
                list($field, $condition, $val) = $value;
                $this->model = $this->model->where($field, $condition, $val);
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }

    /**
     * @param array $where
     */
    protected function applyOrCondition(array $where): void
    {
        foreach ($where as $field => $value) {
            if(is_array($value)) {
                list($field, $condition, $val) = $value;
                $this->model = $this->model->orWhere($field, $condition, $val);
            } else {
                $this->model = $this->model->orWhere($field, '=', $value);
            }
        }
    }

    /**
     * Take the provided model and make it accessible to the rest of the repository.
     *
     * @param array $model
     * @return mixed
     */
    protected function initializeModel(...$model)
    {
        switch (count($model)) {
            case 1:
                return $this->model = $this->app->make($model[0]);
            case 2:
                return $this->model = call_user_func([$this->app->make($model[0]), $model[1]]);
            default:
                throw new InvalidArgumentException('Model must be an instance of Illuminate\\Database\\Eloquent\\Model or an array with a count of two.');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|mixed
     * @throws RepositoryException
     */
    protected function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * @throws RepositoryException
     */
    protected function resetModel()
    {
        $this->makeModel();
    }
}
