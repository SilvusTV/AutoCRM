# Git Hooks with Husky

This directory contains Git hooks managed by [Husky](https://typicode.github.io/husky/).

## Pre-commit Hook

The pre-commit hook runs [Laravel Pint](https://laravel.com/docs/10.x/pint) on all staged PHP files before each commit.
This ensures that all code committed to the repository follows the Laravel coding standards.

### How it works

1. When you run `git commit`, the pre-commit hook is triggered
2. The hook runs `npx lint-staged`, which executes Laravel Pint on all staged PHP files
3. If Laravel Pint finds any style issues, it will automatically fix them
4. The fixed files are automatically added to the commit
5. If Laravel Pint cannot fix some issues, the commit will be aborted with an error message

Note: The pre-commit hook file has been updated to use the simplified format required by Husky v10.0.0, which only
contains the command to be executed.

### Setup

To set up the pre-commit hook, run the following commands:

```bash
# Install dependencies
npm install

# This will set up Husky
npm run prepare
```

### Skipping the hook

If you need to skip the pre-commit hook for a specific commit, you can use the `--no-verify` flag:

```bash
git commit --no-verify -m "Your commit message"
```

However, it's recommended to let the hook run to maintain code quality.

### Troubleshooting

If you encounter any issues with the pre-commit hook:

1. Make sure you have installed the npm dependencies with `npm install`
2. Make sure you have run `npm run prepare` to set up Husky
3. Check that Laravel Pint is installed with `composer install`
4. Try running `npm run lint` manually to see if there are any errors
