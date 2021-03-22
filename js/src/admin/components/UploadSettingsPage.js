import app from 'flarum/app';
import Button from 'flarum/components/Button';
import saveSettings from 'flarum/utils/saveSettings';
import Alert from 'flarum/components/Alert';
import Switch from 'flarum/components/Switch';
import withAttr from 'flarum/utils/withAttr';
import Stream from 'flarum/utils/Stream';
import ExtensionPage from 'flarum/components/ExtensionPage';

export default class UploadSettingsPage extends ExtensionPage {
    oninit(vnode) {
        super.oninit(vnode);
        // whether we are saving the settings or not right now
        this.loading = false;

        // the fields we need to watch and to save
        // 表单输入框字段
        this.fields = ['user', 'token', 'projects', 'maxsize'];

        // the checkboxes we need to watch and to save.
        // jsdelivr cdn 加速开关字段
        this.checkboxes = ['jsdelivrcdn'];

        // get the saved settings from the database
        const settings = app.data.settings;

        // Contains current values.
        this.values = {};
        // bind the values of the fields and checkboxes to the getter/setter functions
        this.fields.forEach((key) => (this.values[key] = Stream(settings[this.addPrefix(key)])));
        this.checkboxes.forEach((key) => (this.values[key] = Stream(settings[this.addPrefix(key)] === '1')));
    }

    content() {
        return [
            m('.UploadSettingsPage', [
                m('.container', [
                    m(
                        'form',
                        {
                            onsubmit: this.onsubmit.bind(this),
                        },
                        [
                            m('fieldset', [
                                m('legend', app.translator.trans('flarum-ext-github-upload.admin.settings.user_label')),
                                m('input', {
                                    className: 'FormControl',
                                    value: this.values.user() || '',
                                    oninput: withAttr('value', this.values.user),
                                }),
                            ]),
                            m('fieldset', [
                                m('legend', app.translator.trans('flarum-ext-github-upload.admin.settings.token_label')),
                                m('input', {
                                    className: 'FormControl',
                                    value: this.values.token() || '',
                                    oninput: withAttr('value', this.values.token),
                                }),
                            ]),
                            m('fieldset', [
                                m('legend', app.translator.trans('flarum-ext-github-upload.admin.settings.projects_label')),
                                m('input', {
                                    className: 'FormControl',
                                    value: this.values.projects() || '',
                                    oninput: withAttr('value', this.values.projects),
                                }),
                            ]),
                            m('fieldset', [
                                m('legend', app.translator.trans('flarum-ext-github-upload.admin.settings.maxsize_label')),
                                m('input.FormControl', {
                                    value: this.values.maxsize() || 1024,
                                    oninput: withAttr('value', this.values.maxsize),
                                    type: 'number',
                                    min: '0',
                                })
                            ]),
                            m('fieldset', [
                                m('legend', app.translator.trans('flarum-ext-github-upload.admin.settings.cdn_label1')),
                                Switch.component(
                                    {
                                        state: this.values.jsdelivrcdn() || false,
                                        onchange: this.values.jsdelivrcdn,
                                    },
                                    app.translator.trans('flarum-ext-github-upload.admin.settings.cdn_label2')
                                )
                            ]),

                            Button.component(
                                {
                                    type: 'submit',
                                    className: 'Button Button--primary',
                                    loading: this.loading,
                                    disabled: !this.changed(),
                                },
                                app.translator.trans('flarum-ext-github-upload.admin.settings.save_label')
                            ),
                        ]
                    ),
                ]),
            ]),
        ];
    }

    /**
     * Checks if the values of the fields and checkboxes are different from
     * the ones stored in the database
     * 检查表单值和原始值是否有改动，是则存入数据库
     *
     * @returns boolean
     */
    changed() {
        const fieldsCheck = this.fields.some((key) => this.values[key]() !== app.data.settings[this.addPrefix(key)]);
        const checkboxesCheck = this.checkboxes.some((key) => this.values[key]() !== (
            app.data.settings[this.addPrefix(key)] === '1'));
        return fieldsCheck || checkboxesCheck;
    }

    /**
     * Saves the settings to the database and redraw the page
     * 提交表单存入数据库并刷新页面
     *
     * @param e
     */
    onsubmit(e) {
        // prevent the usual form submit behaviour
        e.preventDefault();

        // if the page is already saving, do nothing
        if (this.loading) return;

        // prevents multiple savings
        this.loading = true;

        // remove previous success popup
        app.alerts.dismiss(this.successAlert);

        const settings = {};

        // gets all the values from the form
        this.fields.forEach((key) => (settings[this.addPrefix(key)] = this.values[key]()));
        this.checkboxes.forEach((key) => (settings[this.addPrefix(key)] = this.values[key]()));

        // actually saves everything in the database
        saveSettings(settings)
            .then(() => {
                // on success, show popup
                this.successAlert = app.alerts.show(
                    Alert, {type: 'success'},
                    app.translator.trans('flarum-ext-github-upload.admin.settings.success_label'));
            })
            .catch(() => {})
            .then(() => {
                // return to the initial state and redraw the page
                this.loading = false;
                m.redraw();
            });
    }

    /**
     * Adds the prefix `irony.github.upload.` at the beginning of `key`
     *
     * @returns string
     */
    addPrefix(key) {
        return 'irony.github.upload.' + key;
    }
}
