import app from 'flarum/app';

import File from '../common/models/File';
import addUploadButton from './addUploadButton';

export * from './components';

app.initializers.add('irony-github-upload', app => {
    addUploadButton();
    // File model
    app.store.models.files = File;
});
