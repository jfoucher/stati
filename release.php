#!/usr/bin/env php
<?php

if (!isset($argv[1])) {
    echo 'Please specify the tag for this release';
    exit(1);
}

$tag = $argv[1];

shell_exec('git tag '.$tag);
shell_exec('./build.sh');
shell_exec('git tag -d '.$tag);


//update manifest.json

$manifest = json_decode(file_get_contents('./docs/manifest.json'));
$manifest[] = [
    "name" => "stati.phar",
    "sha1" => sha1(file_get_contents('./build/stati.phar')),
    "url" => "https://raw.githubusercontent.com/jfoucher/stati/".$tag."/build/stati.phar",
    "version" => $tag,
    "plugins" => [
        [
            "name" => "paginator",
            "url" => "https://raw.githubusercontent.com/jfoucher/stati/".$tag."/build/paginate.phar"
        ],
        [
            "name" => "related",
            "url" => "https://raw.githubusercontent.com/jfoucher/stati/".$tag."/build/related.phar"
        ]
    ]
];

file_put_contents('./docs/manifest.json', json_encode($manifest));

shell_exec('git commit -am "Creating release with tag '.$tag.'" && git push');
shell_exec('git tag '.$tag.' && git push --tags');
