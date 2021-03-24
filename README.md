# Github Upload by Irony

An extension that upload file to github, which supports auto embedding images and videos

### Features
- video and audio bbcode `[audio][/audio]` `[video][/video]`
- keep local files

### Installation

for beta 15 and latter
```sh
composer require irony/flarum-ext-github-upload:dev-master
```

### Notice
Flarum is beautiful, but I don't use it anymore, and it changes so often that it's difficult to maintain plug-ins

if not work, check database table `irony_github_files`
if not found column `name` and `path` in `irony_github_files`

```
alter table irony_github_files add column name varchar(255) default null;
alter table irony_github_files add column path varchar(255) default null;
```

thanks for [FriendsOfFlarum/upload](https://github.com/FriendsOfFlarum/upload)

* It's best not to use it with a `fof/upload`