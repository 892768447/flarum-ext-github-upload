<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        // 创建数据库
        if (!$schema->hasTable('irony_github_files')) {
            $schema->create('irony_github_files', function (Blueprint $table) {
                $table->increments('id');                               // 自增ID
                $table->integer('actor_id')->unsigned()->nullable();    // 用户ID
                $table->string('name')->nullable();    // 文件名
                $table->string('path')->nullable();    // 文件本地路径
                $table->string('url');                                  // 绝对url路径
                $table->char('sha', 40);                         // 文件在github上的sha值
                $table->string('type', 5);                       // 文件类型
                $table->timestamp('created_at');                        // 创建时间
            });
        }
        // 兼容升级
        if (!$schema->hasColumn('irony_github_files', 'name')) {
            $schema->table('irony_github_files', function (Blueprint $table) {
                $table->string('name')->nullable();// 文件名
            });
        }
        if (!$schema->hasColumn('irony_github_files', 'path')) {
            $schema->table('irony_github_files', function (Blueprint $table) {
                $table->string('path')->nullable();// 文件本地路径
            });
        }
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('irony_github_files');
    },
];
