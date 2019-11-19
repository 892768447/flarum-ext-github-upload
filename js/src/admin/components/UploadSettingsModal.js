import SettingsModal from "flarum/components/SettingsModal";

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
                    bidi={this.setting("flarum-ext-github-upload.token")}
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
                    bidi={this.setting("flarum-ext-github-upload.projects")}
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
                    bidi={this.setting("flarum-ext-github-upload.maxsize")}
                />
            </div>,

            <div className="Form-group">
                <label>
                    {app.translator.trans(
                        "flarum-ext-github-upload.admin.settings.watermark_label"
                    )}
                </label>
                <input
                    type="checkbox"
                    className="FormControl"
                    bidi={this.setting("flarum-ext-github-upload.watermark")}
                />
            </div>
        ];
    }
}
