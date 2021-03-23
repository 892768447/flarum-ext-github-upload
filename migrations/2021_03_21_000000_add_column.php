<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasColumn('irony_github_files', 'path')) {
            $schema->table('irony_github_files', function (Blueprint $table) {
                $table->string('path')->nullable(); // 本地路径
            });
        }
    },
    'down' => function (Builder $schema) {
        // Not doing anything but `down` has to be defined
    },
];
