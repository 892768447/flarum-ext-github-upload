export default function fileToBBcode(file) {
    switch (file.type()) {
        // File
        case 'file':
            return `[${file.name()}](${file.url()})`;

        // Image template
        case 'image':
            return `[IMG]${file.url()}[/IMG]`;

        // video
        case 'audio':
            return `[AUDIO]${file.url()}[/AUDIO]`;

        // video
        case 'video':
            return `[VIDEO]${file.url()}[/VIDEO]`;

        // 'just-url' or unknown
        default:
            return `[${file.name()}](${file.url()})`;
    }
}
