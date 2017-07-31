# How to contribute

You can contribute to BEdita in many ways:

 * reporting a bug
 * write patches for open bug or feature issues
 * contribute to the [documentation](https://github.com/bedita/docs)


## Reporting a bug

 * Ensure the bug was not already reported on GitHub [issues](https://github.com/bedita/bedita/issues).
 * If you don't find an open issue then [open a new one](https://github.com/bedita/bedita/issues/new) following these rules:
    * fill the title with a clear (and short) description of the issue
    * describe the steps to reproduce the bug
    * give us your enviroment information as OS, BEdita version, PHP version, web server, database, etc...

## Write patches for open bug or feature issues

 * Fork the [BEdita repository](https://github.com/bedita/bedita)
 * Clone your fork locally
 * Add the upstream repository as remote
 * Choose the right branch on which you would work and checkout it
 * Create a topic branch from where you want to base your work.
 * Work on `YOUR-TOPIC-BRANCH` adding commits following our [commit conventions](https://github.com/bedita/bedita/wiki/Commit-conventions)
 * Core test cases should continue to pass. To run the test cases locally use the following command:
    ```shell
    vendor/bin/phpunit
    ```
 * You should always add test cases for your changes.
 * Follow our [coding styles rules](https://github.com/bedita/bedita/wiki/%5BBE4%5D-Coding-styles).
 * When you're done, push `YOUR-TOPIC-BRANCH` in your fork of the repository.
 * Submit a [pull request](https://github.com/bedita/bedita/compare) selecting the correct target branch (base) and your `YOUR-TOPIC-BRANCH` as source.
