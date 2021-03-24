import app from 'flarum/app';

import UploadSettingsModal from './components/UploadSettingsModal';
import UploadSettingsPage from './components/UploadSettingsPage';

app.initializers.add('irony-github-upload', app => {
    app.extensionData
        .for('irony-github-upload')
        .registerPage(UploadSettingsPage)
});
