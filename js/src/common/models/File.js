import Model from 'flarum/Model';
import mixin from 'flarum/utils/mixin';
import fileToBBcode from '../fileToBBcode';

export default class File extends mixin(Model, {
    url: Model.attribute('url'),
    uuid: Model.attribute('sha'),
    type: Model.attribute('type'),
    createdAt: Model.attribute('createdAt'),
    path: Model.attribute('path'),
}) {
    /**
     * Generate bbcode for this file
     */
    bbcode() {
        return fileToBBcode(this);
    }
}
