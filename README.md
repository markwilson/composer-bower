# composer-bower

Runs `bower install` as a post-install or post-update script.

## Usage

```` json
{
    "scripts": {
        "post-update-cmd": [
            "ComposerBower\\DependencyInstall::execute"
        ],
        "post-install-cmd": [
            "ComposerBower\\DependencyInstall::execute"
        ]
    },
    "extra": {
        "composer-bower": {
            "working-directory": "<a custom working directory based on current working directory>",
            "package": "<a package name to use as the working directory (optional)>"
        }
    }
}
````

Also possible to define `composer-bower` as an array of bower dependencies.
