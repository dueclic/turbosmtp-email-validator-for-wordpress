module.exports = {
    // existing tags are plain "1.9.1", no "v" prefix
    'tag-prefix': '',
    releaseCommitMessageFormat: 'chore(release): {{currentTag}}',
    // source of truth for the current version
    packageFiles: [
        {filename: 'turbosmtp-email-validator.php', updater: 'scripts/version-updaters/php-plugin.js'},
    ],
    bumpFiles: [
        {filename: 'turbosmtp-email-validator.php', updater: 'scripts/version-updaters/php-plugin.js'},
        {filename: '.wordpress-org/readme/README.txt', updater: 'scripts/version-updaters/wp-readme.js'},
        {filename: 'package.json', type: 'json'},
    ],
};
