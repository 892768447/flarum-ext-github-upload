<?php

namespace Irony\Github\Upload;

use Flagrow\Upload\Contracts\UploadAdapter;
use Flarum\Database\AbstractModel;
use Flarum\Discussion\Discussion;
use Flarum\Post\Post;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $actor_id
 * @property string $url
 * @property string $sha
 * @property string $type
 * @property Carbon $created_at
 * @property string $name
 * @property string $path
 */
class File extends AbstractModel
{
    protected $table = 'irony_github_files';

    /**
     * @return BelongsTo
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * @return BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return BelongsTo
     */
    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }
}
