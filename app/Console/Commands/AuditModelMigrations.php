<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use Throwable;

class AuditModelMigrations extends Command
{
    protected $signature = 'migrate:audit-models';

    protected $description = 'List Eloquent model tables that have no create-table migration';

    public function handle(): int
    {
        $createdTables = $this->createdTables();
        $missing = [];

        foreach (File::allFiles(app_path('Models')) as $file) {
            $class = 'App\\Models\\'.str_replace(
                ['/', '.php'],
                ['\\', ''],
                $file->getRelativePathname()
            );

            try {
                if (! class_exists($class)) {
                    continue;
                }

                $reflection = new ReflectionClass($class);

                if ($reflection->isAbstract()
                    || ! $reflection->isSubclassOf(EloquentModel::class)
                    || $class === \App\Models\Model::class) {
                    continue;
                }

                $table = $reflection->newInstanceWithoutConstructor()->getTable();
            } catch (Throwable $exception) {
                $this->warn("Could not inspect {$class}: {$exception->getMessage()}");

                continue;
            }

            if (! isset($createdTables[strtolower($table)])) {
                $missing[] = [$class, $table];
            }
        }

        if ($missing === []) {
            $this->info('Every Eloquent model table has a create-table migration.');

            return self::SUCCESS;
        }

        usort($missing, fn (array $left, array $right) => $left[1] <=> $right[1]);

        $this->error(count($missing).' model table(s) have no create-table migration:');
        $this->table(['Model', 'Expected table'], $missing);

        return self::FAILURE;
    }

    /**
     * Find table names passed to Schema::create() in every application migration.
     *
     * @return array<string, true>
     */
    private function createdTables(): array
    {
        $tables = [];

        foreach (File::allFiles(database_path('migrations')) as $migration) {
            $contents = $migration->getContents();

            preg_match_all(
                '/Schema::create\(\s*[\'\"]([^\'\"]+)[\'\"]/',
                $contents,
                $matches
            );

            foreach ($matches[1] as $table) {
                $tables[strtolower($table)] = true;
            }
        }

        return $tables;
    }
}
