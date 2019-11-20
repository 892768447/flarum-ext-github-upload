import SettingsModal from "flarum/components/SettingsModal";
import Switch from "flarum/components/Switch";

export default class UploadSettingsModal extends SettingsModal {
    className() {
        return "Modal--small";
    }

    title() {
        return app.translator.trans("flarum-ext-github-upload.admin.settings.title");
    }

    form() {
        return [
            <div className="Form-group">
                <label>
                    {app.translator.trans("flarum-ext-github-upload.admin.settings.token_label")}
                </label>
                <input
                    required
                    className="FormControl"
                    bidi={this.setting("irony.github.upload.token")}
                />
            </div>,

            <div className="Form-group">
                <label>
                    {app.translator.trans(
                        "flarum-ext-github-upload.admin.settings.projects_label"
                    )}
                </label>
                <input
                    required
                    className="FormControl"
                    bidi={this.setting("irony.github.upload.projects")}
                />
            </div>,

            <div className="Form-group">
                <label>
                    {app.translator.trans(
                        "flarum-ext-github-upload.admin.settings.maxsize_label"
                    )}
                </label>
                <input
                    required
                    className="FormControl"
                    bidi={this.setting("irony.github.upload.maxsize")}
                />
            </div>,

            <div className="Form-group">
                <label>
                    {app.translator.trans(
                        "flarum-ext-github-upload.admin.settings.watermark_label1"
                    )}
                </label>
                {Switch.component({
                    state: !!Number(this.setting("irony.github.upload.watermark")()),
                    children: app.translator.trans("flarum-ext-github-upload.admin.settings.watermark_label2"),
                    onchange: this.setting("irony.github.upload.watermark")
                })}
            </div>
        ];
    }
}
