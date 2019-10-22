# Tables

Idea: make statically typed what is possible and prevent typos.

## Priorities:

- keep it simple (no-relations support)
- keep it fast to use (Scaffolding)
- do not write it twice (sync database to database-access-layer classes)
- do not screw up database - support for rich types as UUID, arrays, ... (easy to add more)

## Installation

This is private repo, so you have to add repositories to you `composer.json` manually. This tells composer where to find our packages.

````json
{
    "repositories": [
		{
			"type": "vcs",
			"url": "git@gitlab.grifart.local:grifart/tables.git"
		},
        {
            "type": "vcs",
            "url": "git@gitlab.grifart.local:jkuchar1/assert-function-signature.git"
        },
        {
            "type": "vcs",
            "url": "git@gitlab.grifart.local:grifart/class-scaffolder.git"
        }
    ]
}
````

Then install dependencies.

````bash
# 1. install grifart/tables:

# use for latest stable version:
composer require grifart/tables

# use for latest dev version:
composer require grifart/tables:dev-master

# 2. install grifart/class-scaffolder for automatic defintions generation:
composer require grifart/class-scaffolder
````
