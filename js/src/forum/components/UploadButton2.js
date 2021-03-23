import app from 'flarum/app';
import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import icon from "flarum/common/helpers/icon";
import Alert from "flarum/common/components/Alert";
import classList from 'flarum/common/utils/classList';

export default class UploadButton extends Component {

    oninit(vnode) {
        super.oninit(vnode);
        // the service type handling uploads
        this.textAreaObj = this.attrs.textAreaObj;
    }

    oncreate(vnode) {
        super.oncreate(vnode);
    }


    view() {
        const versions = app.forum.attribute('version').split('.');
        if (parseInt(versions[versions.length - 1]) >= 15) {
            return (
                <Button
                    className={classList([
                        'Button',
                        'hasIcon',
                        'irony-github-upload-button',
                        'Button--icon'
                    ])}
                    icon={'fas fa-file-upload'}
                    onclick={this.uploadButtonClicked.bind(this)}
                    disabled={this.attrs.disabled}
                >
                    <span
                        className="Button-label">{app.translator.trans('flarum-ext-github-upload.forum.buttons.attach')}</span>}
                    <form>
                        <input type="file" multiple={true} onchange={this.process.bind(this)}/>
                    </form>
                </Button>
            );
        }

        return m('div', {className: 'Button hasIcon irony-github-upload-button Button--icon'}, [
            icon('fas fa-cloud-upload-alt', {className: 'Button-icon file-icon'}),
            m('span', {className: 'Button-label'}, app.translator.trans('flarum-ext-github-upload.forum.buttons.attach')),
            m('form#irony-github-upload-form', [
                m('input', {
                    type: 'file',
                    multiple: true,
                    onchange: this.process.bind(this)
                })
            ])
        ]);
    }

    /**
     * Process the upload event.
     * 解析上传事件
     *
     * @param e
     */
    process(e) {
        // get the file from the input field

        //var files = $(e.target)[0].files;
        const files = this.$('input').prop('files');
        if (files.length === 0) {
            // We've got no files to upload, so trying
            // to begin an upload will show an error
            // to the user.
            return;
        }

        // 添加loading图标
        $('.file-icon').removeClass('fas fa-cloud-upload-alt');
        $('.file-icon').addClass('fas fa-spinner fa-spin');

        this.uploadFiles(files, this.success, this.failure);
    }

    /**
     * Event handler for upload button being clicked
     *
     * @param {PointerEvent} e
     */
    uploadButtonClicked(e) {
        // Trigger click on hidden input element
        // (Opens file dialog)
        this.$('input').click();
    }

    uploadFiles(files, successCallback, failureCallback) {
        m.redraw(); // Forcing a redraw so that the button also updates if uploadFiles() is called from DragAndDrop or PasteClipboard

        console.log(files);

        const data = new FormData();

        for (let i = 0; i < files.length; i++) {
            data.append('files[]', files[i]);
        }

        console.log(data.keys());
        console.log(data.getAll("files[]"));

        // send a POST request to the api
        // 发送上传请求
        return app.request({
            method: 'POST',
            url: app.forum.attribute('apiUrl') + '/irony/github/upload',
            // prevent JSON.stringify'ing the form data in the XHR call
            serialize: (raw) => raw,
            data
        }).then(
            this.success.bind(this),
            this.failure.bind(this)
        ).catch((error) => {
            m.redraw();
            throw error;
        });
    }

    /**
     * 消息提示
     *
     * @param type
     * @param message
     */
    alertNotice(type, message) {
        let alert;
        app.alerts.show(
            (alert = new Alert({
                type: type,
                children: message
            }))
        );
        // 3秒后自动关闭
        setTimeout(function () {
            app.alerts.dismiss(alert);
        }, 3000);
    }

    /**
     * Handles errors.
     * 错误
     *
     * @param message
     */
    failure(message) {
        // 删除loading图标
        $('.file-icon').removeClass('fas fa-spinner fa-spin');
        $('.file-icon').addClass('fas fa-cloud-upload-alt');
        this.alertNotice("error", message);
    }

    /**
     * Appends the file's link to the body of the composer.
     * 上传成功添加链接到编辑器中
     *
     * @param response
     */
    success(response) {
        // 删除loading图标
        $('.file-icon').removeClass('fas fa-spinner fa-spin');
        $('.file-icon').addClass('fas fa-cloud-upload-alt');
        response.forEach((text) => {
            this.textAreaObj.insertAtCursor(text + '\n');
        });
        // reset the button for a new upload
        setTimeout(() => {
            document.getElementById("irony-github-upload-form").reset();
        }, 1000);
    }
}
