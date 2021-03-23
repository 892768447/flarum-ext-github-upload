<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
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
    },
    'down' => function (Builder $schema) {
        $schema->drop('irony_github_files');
    },
];
