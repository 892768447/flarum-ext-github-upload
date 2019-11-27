import app from 'flarum/app';
import Component from "flarum/Component";
import icon from "flarum/helpers/icon";
import Alert from "flarum/components/Alert";
import LoadingIndicator from "flarum/components/LoadingIndicator";

export default class UploadButton extends Component {

    /**
     * Load the configured remote uploader service.
     */
    init() {
        // the service type handling uploads
        this.textAreaObj = null;

        // initial state of the button
        this.loading = false;
    }

    /**
     * Show the actual Upload Button.
     * 上传按钮
     *
     * @returns {*}
     */
    view() {
        return m('div', {className: 'Button hasIcon irony-github-upload-button Button--icon'}, [
            this.loading ? LoadingIndicator.component({className: 'Button-icon'}) : icon('far fa-file', {className: 'Button-icon'}),
            m('span', {className: 'Button-label'}, this.loading ? app.translator.trans('flarum-ext-github-upload.forum.states.loading') : app.translator.trans('flarum-ext-github-upload.forum.buttons.attach')),
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

        // set the button in the loading state (and redraw the element!)
        this.loading = true;
        m.redraw();

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
        setTimeout(function() {
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
        // todo show popup
        this.alertNotice("error", message);
    }

    /**
     * Appends the file's link to the body of the composer.
     * 上传成功添加链接到编辑器中
     *
     * @param response
     */
    success(response) {
        response.forEach((text) => {
          this.textAreaObj.insertAtCursor(text + '\n');
        })
        // reset the button for a new upload
        setTimeout(() => {
            document.getElementById("irony-github-upload-form").reset();
            this.loading = false;
        }, 1000);
    }
}
