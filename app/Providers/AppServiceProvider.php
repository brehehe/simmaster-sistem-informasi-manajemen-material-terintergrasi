<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\Spatie\Permission\PermissionRegistrar::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent lazy loading in development to detect accidental N+1 queries
        \Illuminate\Database\Eloquent\Model::preventLazyLoading(! $this->app->isProduction());

        \Illuminate\Support\Facades\Gate::policy(\App\Models\MenuPolda\MaterialShipment\MaterialShipment::class, \App\Policies\MaterialShipmentPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\StockOpname\StockOpname::class, \App\Policies\StockOpnamePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\MenuPolda\MaterialUsage\MaterialUsage::class, \App\Policies\MaterialUsagePolicy::class);

        Event::listen('eloquent.booted: *', function ($eventName, array $data) {
            $model = $data[0];
            $modelClass = get_class($model);

            if (str_starts_with($modelClass, 'App\\')) {
                $modelClass::addGlobalScope('default_order', function (Builder $builder) {
                    $query = $builder->getQuery();
                    if (empty($query->orders) && empty($query->groups) && empty($query->aggregate)) {
                        // Check if any custom selected columns contain aggregate functions
                        $hasAggregate = false;
                        if (! empty($query->columns)) {
                            foreach ($query->columns as $column) {
                                $colStr = $column instanceof Expression
                                    ? $column->getValue(DB::connection()->getQueryGrammar())
                                    : (string) $column;

                                if (preg_match('/\b(count|sum|avg|min|max|stddev|variance|string_agg|array_agg|json_agg|jsonb_agg)\s*\(/i', $colStr)) {
                                    $hasAggregate = true;
                                    break;
                                }
                            }
                        }

                        if (! $hasAggregate) {
                            $model = $builder->getModel();
                            if ($model->getKeyName()) {
                                $builder->orderBy($model->getTable() . '.' . $model->getKeyName(), 'asc');
                            }
                        }
                    }
                });
            }
        });
    }
}
