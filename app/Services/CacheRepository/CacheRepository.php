<?php

namespace App\Services\CacheRepository;

use App\Services\CacheRepository\Contract\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CacheRepository implements RepositoryInterface
{
    protected $model;

    protected $key;

    /**
     * CacheRepository class constructor
     *
     * @param Illuminate\Database\Eloquent\Builder | Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(object $model)
    {
        $this->model = $model;
        $this->key = $this->tableKey($this->model);
    }

    protected function tableKey($model)
    {
        if ($model instanceof Builder) {
            return $this->model->getModel()->getTable();
        } elseif ($model instanceof Model) {
            return $this->model->getTable();
        } else {
            return 'common_' . date('dmY');
        }
    }

    public static function init($model)
    {
        $model = is_string($model) ? new $model() : $model;
        return new self($model);
    }

    public function all()
    {
        return Cache::remember($this->cacheKey('all'), self::DURATION, function () {
            return $this->model->all();
        });
    }

    public function find(int $id)
    {
        return Cache::remember($this->cacheKey($id), self::DURATION, function () use ($id) {
            return $this->model->where('id', $id)->first();
        });
    }

    public function findBy(array $filter, string $key = '')
    {
        return Cache::remember($this->cacheKey($filter, $key), self::DURATION, function () use ($filter) {
            return $this->model->where($filter)->first();
        });
    }

    public function findAll(array $filter, string $key = '')
    {
        return Cache::remember($this->cacheKey($filter, $key), self::DURATION, function () use ($filter) {
            return $this->model->where($filter)->get();
        });
    }

    public function update(array $data, int $id)
    {
        $this->model->where('id', $id)->update($data);

        $result = $this->model->where('id', $id)->first();

        Cache::put($this->cacheKey($id), $result, self::DURATION);
        return $result;
    }

    public function updateBy(array $data, array $filter, string $key = '')
    {
        $this->model->where($filter)->update($data);

        $result = $this->model->where($filter)->get();

        Cache::put($this->cacheKey($filter, $key), $result, self::DURATION);

        return $result;
    }

    public function delete(int $id = null)
    {
        if ($id) {
            $this->model = $this->model->where('id', $id);
        }
        $this->model->delete();
        Cache::forget($this->cacheKey($id));
        return;
    }

    public function deleteBy(array $filter)
    {
        Cache::forget($this->cacheKey($filter));
        return;
    }

    protected function cacheKey($filter = null, string $override = '')
    {
        if (!empty($override)) {
            return $override;
        } elseif (empty($filter)) {
            return $this->key;
        } elseif (is_array($filter)) {
            $tmp = $this->_createKey($filter);
            return empty($tmp) ? $this->key : "{$this->key}.{$tmp}";
        } else {
            return "{$this->key}.{$filter}";
        }
    }

    private function _createKey(array $arr): string
    {
        $tmpKey = '';
        if (!empty($arr)) {
            foreach ($arr as $key => $value) {
                if (is_string($value) || is_int($value)) {
                    $tmpKey .= "{$key}_{$value}.";
                }
            }
        }

        $tmpKey = ltrim($tmpKey, '_');
        $tmpKey = substr_replace($tmpKey, "", -1);

        return $tmpKey;
    }
}