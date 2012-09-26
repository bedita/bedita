# How to contribute

BEdita needs your contribution to make it even better, so we ask you to follow the below guidelines to help us to maintain BEdita always at high level.

This is a summary of contribution workflow, if you haven't read the detailed document go at http://docs.bedita.com/about/contributing and read it, **it's important!**

## Reporting a bug

### Before submitting a bug

Before submitting a bug you should
 * have a look at [Official Documentation](http://docs.bedita.com) to check if you are misusing BEdita
 * ask for help on [Google Gruoup](https://groups.google.com/forum/#!forum/bedita) if you are not sure that the issue is really a bug

### Submitting a bug

If your problem seems a bug then you should open an issue on the official [bug tracker](https://github.com/bedita/bedita/issues) and try to follow these rules:
 * fill the title with a clear (and short) description of the issue
 * describe the steps to reproduce the bug
 * give us your enviroment information as OS, BEdita version, PHP version, etc...

## Submitting a Patch

 * Make sure you have a [GitHub account](https://github.com/signup/free)
 * Fork the [BEdita repository](https://github.com/bedita/bedita)
 * Clone your fork locally
 * Add the upstream repository as remote
 * Choose the right branch on which you would work and checkout it
 * Create a topic branch for the issue you want to work on
 * Work on YOUR-TOPIC-BRANCH making commits of logical units and writing good commit messages
 * Once finish your changes, update YOUR-TOPIC-BRANCH with the changes happened in BEdita since you started:
  * update to upstream the branch from which you have created YOUR-TOPIC-BRANCH
  * rebase your changes in YOUR-TOPIC-BRANCH on top of the updated code
 * Submit a [pull request](https://help.github.com/articles/using-pull-requests)