import app from 'flarum/app';
import {extend} from 'flarum/extend';
import TextEditor from 'flarum/components/TextEditor';
import UploadButton from './components/UploadButton';
import DragAndDrop from './components/DragAndDrop';
import PasteClipboard from './components/PasteClipboard';
import Uploader from './handler/Uploader';

export default function () {
    extend(TextEditor.prototype, 'oninit', function () {
        this.uploader = new Uploader();
    });
    extend(TextEditor.prototype, 'controlItems', function (items) {
        if (!app.forum.attribute('canUploadToGithub')) return;

        // Add upload button
        items.add(
            'irony-github-upload',
            UploadButton.component({
                uploader: this.uploader,
            })
        );

    });

    extend(TextEditor.prototype, 'oncreate', function (f_, vnode) {
        if (!app.forum.attribute('canUploadToGithub')) return;

        this.uploader.on('success', ({file, addBBcode}) => {
            console.log(file);
            if (!addBBcode || !file.url()) return;

            this.attrs.composer.editor.insertAtCursor(file.bbcode() + '\n');

            // We wrap this in a typeof check to prevent it running when a user
            // is creating a new discussion. There's nothing to preview in a new
            // discussion, so the `preview` function isn't defined.
            if (typeof this.attrs.preview === 'function') {
                // Scroll the preview into view
                // preview() causes the composer to close on mobile, but we don't want that. We want only the scroll
                // We work around that by temporarily patching the isFullScreen method
                const originalIsFullScreen = app.composer.isFullScreen;

                app.composer.isFullScreen = () => false;

                this.attrs.preview();

                app.composer.isFullScreen = originalIsFullScreen;
            }
        });

        const dragAndDrop = new DragAndDrop((files) => this.uploader.upload(files), this.$().parents('.Composer')[0]);

        const unloadHandler = () => {
            dragAndDrop.unload();
        };

        this.$('textarea').bind('onunload', unloadHandler);

        //new PasteClipboard((files) => this.uploader.upload(files), this.$('textarea')[0]);
    });
}
