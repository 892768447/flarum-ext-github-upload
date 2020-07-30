import app from 'flarum/app';
import Component from "flarum/Component";
import icon from "flarum/helpers/icon";
import Alert from "flarum/components/Alert";

export default class UploadButton extends Component {

    /**
     * Load the configured remote uploader service.
     */
    init() {
        // the service type handling uploads
        this.textAreaObj = null;
    }

    /**
     * Show the actual Upload Button.
     * 上传按钮
     *
     * @returns {*}
     */
    view() {
        return m('div', {className: 'Button hasIcon irony-github-upload-button Button--icon'}, [
            icon('far fa-file', {className: 'Button-icon file-icon'}),
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

        var files = $(e.target)[0].files;

        // 添加loading图标
        $('.file-icon').removeClass('far fa-file');
        $('.file-icon').addClass('fas fa-spinner fa-spin');

        this.uploadFiles(files, this.success, this.failure);
    }

    uploadFiles(files, successCallback, failureCallback) {
        const data = new FormData;

        for (var i = 0; i < files.length; i++) {
            data.append('files[]', files[i]);
        }

        // send a POST request to the api
        // 发送上传请求
        return app.request({
            method: 'POST',
            url: app.forum.attribute('apiUrl') + '/irony/github/upload',
            // prevent JSON.stringify'ing the form data in the XHR call
            serialize: raw => raw,
            data
        }).then(
            this.success.bind(this),
            this.failure.bind(this)
        );
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
        // 5秒后自动关闭
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
        $('.file-icon').addClass('far fa-file');
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
        $('.file-icon').addClass('far fa-file');
        response.forEach((text) => {
            this.textAreaObj.insertAtCursor(text + '\n');
        })
        // reset the button for a new upload
        setTimeout(() => {
            document.getElementById("irony-github-upload-form").reset();
        }, 1000);
    }
}
