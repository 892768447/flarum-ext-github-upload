import app from 'flarum/app';

import UploadSettingsModal from './components/UploadSettingsModal';

app.initializers.add('irony-github-upload', app => {
    app.extensionSettings['irony-github-upload'] = () => app.modal.show(new UploadSettingsModal());
});