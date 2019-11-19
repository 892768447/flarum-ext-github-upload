<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->create('irony_github_files', function (Blueprint $table) {
            $table->increments('id');                               // 自增ID
            $table->integer('actor_id')->unsigned()->nullable();    // 用户ID
            $table->string('url');                                  // 绝对url路径
            $table->integer('md5');                                 // 文件原始md5，防止重复上传的相同文件
            $table->integer('sha');                                 // 文件在github上的sha值
            $table->timestamp('created_at');                        // 创建时间
        });
    },
    'down' => function (Builder $schema) {
        $schema->drop('irony_github_files');
    },
];
