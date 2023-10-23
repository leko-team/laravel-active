<?php declare(strict_types=1);

namespace LekoTeam\LaravelActive\Traits;

use Closure;
use LekoTeam\LaravelActive\Scopes\ActivityScope;

/**
 * Trait for active entity.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder withInactive()
 */
trait ActivityTrait
{
    /**
     * Boot the active trait for a model.
     *
     * @return void
     */
    public static function bootActivityTrait()
    {
        static::addGlobalScope(new ActivityScope);
    }

    /**
     * Perform the actual activate query on this model instance.
     *
     * @return void
     */
    public function activate(): void
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery());

        $columns = [$this->getIsActiveColumn() => true];
        $this->{$this->getIsActiveColumn()} = true;

        if (!is_null($this->getIsActiveColumn())) {
            $this->{$this->getIsActiveColumn()} = true;
            $columns[$this->getIsActiveColumn()] = true;
        }

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));

        $this->fireModelEvent('activated', false);
    }

    /**
     * Deactivate an active model instance.
     *
     * @return bool
     */
    public function deactivate(): bool
    {
        $this->{$this->getIsActiveColumn()} = false;

        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('deactivated', false);

        return $result;
    }

    /**
     * Determine if the model instance is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->{$this->getIsActiveColumn()};
    }

    /**
     * Register a “activated” model event callback with the dispatcher.
     *
     * @param Closure|string $callback
     * @return void
     */
    public static function activated($callback): void
    {
        static::registerModelEvent('activated', $callback);
    }

    /**
     * Register a “deactivated” model event callback with the dispatcher.
     *
     * @param Closure|string $callback
     * @return void
     */
    public static function deactivated($callback): void
    {
        static::registerModelEvent('deactivated', $callback);
    }

    /**
     * Activate model without raising any events.
     *
     * @return bool|null
     */
    public function activateQuietly(): ?bool
    {
        return static::withoutEvents(fn () => $this->activate());
    }

    /**
     * Deactivate model instance without raising any events.
     *
     * @return bool
     */
    public function deactivateQuietly(): bool
    {
        return static::withoutEvents(fn () => $this->deactivate());
    }

    /**
     * Get the name of the “is active” column.
     *
     * @return string
     */
    public function getIsActiveColumn(): string
    {
        return defined(static::class . '::IS_ACTIVE') ? static::IS_ACTIVE : 'is_active';
    }

    /**
     * Get the fully qualified “is active” column.
     *
     * @return string
     */
    public function getQualifiedIsActiveColumn(): string
    {
        return $this->qualifyColumn($this->getIsActiveColumn());
    }

    /**
     * Get the name of the “start at” column.
     *
     * @return string
     */
    public function getStartAtColumn(): string
    {
        return defined(static::class . '::START_AT') ? static::START_AT : 'start_at';
    }

    /**
     * Get the fully qualified “start at” column.
     *
     * @return string
     */
    public function getQualifiedStartAtColumn(): string
    {
        return $this->qualifyColumn($this->getStartAtColumn());
    }

    /**
     * Get the name of the “end at” column.
     *
     * @return string
     */
    public function getEndAtColumn(): string
    {
        return defined(static::class . '::END_AT') ? static::END_AT : 'end_at';
    }

    /**
     * Get the fully qualified “end at” column.
     *
     * @return string
     */
    public function getQualifiedEndAtColumn(): string
    {
        return $this->qualifyColumn($this->getEndAtColumn());
    }
}