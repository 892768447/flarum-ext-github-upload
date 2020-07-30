import {extend} from "flarum/extend";
import app from 'flarum/app';
import TextEditor from "flarum/components/TextEditor";
import UploadButton from "./components/UploadButton";
import DragAndDrop from "./components/DragAndDrop";
import PasteClipboard from "./components/PasteClipboard";

app.initializers.add('irony-github-upload', app => {
    let uploadButton,
        drag,
        clipboard;

    extend(TextEditor.prototype, 'controlItems', function (items) {
        // check whether the user can upload images. If not, returns.
        // 检查是否可以上传
        if (!app.forum.attribute('canUploadToGithub')) return;

        // create and add the button
        // 创建上传按钮
        uploadButton = new UploadButton;
        uploadButton.textAreaObj = this;
        items.add('irony-github-upload', uploadButton, 0);

        // animate the button on hover: shows the label
        // 鼠标悬停动画
        // $('.Button-label', '.item-irony-github-upload > div').hide();
        // $('.item-irony-github-upload > div').hover(
        //     function () {
        //         $('.Button-label', this).show();
        //         $(this).removeClass('Button--icon')
        //     },
        //     function () {
        //         $('.Button-label', this).hide();
        //         $(this).addClass('Button--icon')
        //     }
        // );
    });

    extend(TextEditor.prototype, 'configTextarea', function () {
        // check whether the user can upload images. If not, returns.
        // 检查是否可以上传
        if (!app.forum.attribute('canUploadToGithub')) return;

        if (!drag) {
            // 拖拽支持
            drag = new DragAndDrop(uploadButton);
        }
        if (!clipboard) {
            // 剪贴板支持
            clipboard = new PasteClipboard(uploadButton);
        }
    });
});
