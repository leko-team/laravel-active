<?php

namespace LekoTeam\LaravelActive\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Class of the scopes for a builder.
 */
class ActivityScope implements Scope
{
    /**
     * All the extensions to be added to the builder.
     *
     * @var string[]
     */
    protected $extensions = ['WithInactive'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $time = $model->fromDateTime($model->freshTimestamp());
        $builder->withoutGlobalScope($this);
        $builder->where($model->getQualifiedIsActiveColumn(), true)
            ->whereRaw('
                (' . $model->getQualifiedStartAtColumn() . ' IS NULL OR ' . $model->getQualifiedStartAtColumn() . ' <= ' . "'" . $time . "'" . ')
                AND 
                (' . $model->getQualifiedEndAtColumn() . ' IS NULL OR ' . $model->getQualifiedEndAtColumn() . ' >= ' . "'" . $time . "'" . ')
            ');
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param Builder $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Add the with-inactive extension to the builder.
     *
     * @param Builder $builder
     * @return void
     */
    protected function addWithInactive(Builder $builder): void
    {
        $builder->macro('withInactive', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
