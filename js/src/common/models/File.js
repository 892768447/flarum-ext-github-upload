import Model from 'flarum/Model';
import mixin from 'flarum/utils/mixin';
import fileToBBcode from '../fileToBBcode';

export default class File extends mixin(Model, {
    url: Model.attribute('url'),
    name: Model.attribute('name'),
    uuid: Model.attribute('sha'),
    type: Model.attribute('type'),
    created_at: Model.attribute('created_at'),
    path: Model.attribute('path'),
}) {
    /**
     * Generate bbcode for this file
     */
    bbcode() {
        return fileToBBcode(this);
    }
}
