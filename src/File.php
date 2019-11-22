<?php

namespace Irony\Github\Upload;

use Flagrow\Upload\Contracts\UploadAdapter;
use Flarum\Database\AbstractModel;
use Flarum\User\User;

class File extends AbstractModel
{
    protected $table = 'irony_github_files';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
