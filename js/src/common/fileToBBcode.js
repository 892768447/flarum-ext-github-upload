export default function fileToBBcode(file) {
    switch (file.type()) {
        // File
        case 'file':
            return `[${file.name()}](${file.url()})`;

        // Image template
        case 'image':
            return `[IMG]${file.url()}[/IMG]`;

        // video
        case 'video':
            return `[GITHUB-VIDEO]${file.url()}[/GITHUB-VIDEO]`;

        // 'just-url' or unknown
        default:
            return file.url();
    }
}
