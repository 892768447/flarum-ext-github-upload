import app from 'flarum/app';

import UploadSettingsModal from './components/UploadSettingsModal';
import UploadSettingsPage from './components/UploadSettingsPage';

app.initializers.add('irony-github-upload', app => {
    try {
        // 老版本
        app.extensionSettings['irony-github-upload'] = () => app.modal.show(new UploadSettingsModal());
    } catch (err) {
        // 新版本
        app.extensionData
            .for('irony-github-upload')
            .registerPage(UploadSettingsPage)
    }
});
