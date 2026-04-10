# Contributing to Socrates Blade

Thank you for your interest in contributing to Socrates Blade!

This guide will help you understand how to contribute to our project.

---

## What You Can Help With

- **Reporting bugs** - Found something that doesn't work correctly?
- **Suggesting features** - Have an idea to make the tool better?
- **Writing code** - Want to add new features or fix issues?
- **Improving documentation** - Help make the docs clearer for everyone!
- **Testing** - Run tests and verify the tool works correctly

---

## How to Contribute

### 1. Fork the Repository

Click the "Fork" button on the GitHub page to create your own copy of the repository.

### 2. Clone Your Fork

```bash
git clone https://github.com/YOUR-USERNAME/socrates-blade.git
cd socrates-blade
```

### 3. Create a Branch

Create a new branch for your changes:

```bash
git checkout -b my-feature-or-fix
```

### 4. Make Your Changes

Make your changes to the code, documentation, or tests.

### 5. Test Your Changes

Make sure your changes work correctly:

```bash
# Run the test suite
bats tests/bash/run-scan.sh.test.bats
./tests/bash/test_url_validator.sh
```

### 6. Commit Your Changes

Write a clear commit message:

```bash
git add .
git commit -m "Add clear description of your changes"
```

### 7. Push to GitHub

```bash
git push origin my-feature-or-fix
```

### 8. Create a Pull Request

Go to the original repository and click "New Pull Request".

---

## Pull Request Guidelines

### Writing a Good Pull Request

- **Clear title** - Briefly describe what you're changing
- **Detailed description** - Explain why this change is needed
- **Screenshots** - If applicable, show before/after
- **Test results** - Confirm tests pass

### Pull Request Template

```markdown
## Description
Briefly describe your changes.

## Why This Change is Needed
Explain why this change is important.

## Testing
- [ ] I have tested my changes locally
- [ ] All tests pass

## Screenshots (if applicable)
Add screenshots to show the changes.
```

---

## Code Style Guidelines

### Bash Scripts
- Use `#!/bin/bash` shebang
- Use `set -euo pipefail` for error handling
- Add comments for complex logic
- Use meaningful variable names

### Python Code
- Follow PEP 8 style guide
- Use type hints where helpful
- Add docstrings for functions

### General
- Keep lines under 100 characters
- Use consistent indentation (spaces, not tabs)
- Add comments for non-obvious code

---

## Commit Message Guidelines

### Good Commit Messages

- Start with a verb: "Add", "Fix", "Update", "Remove"
- Be specific: "Add XSS payload for stored attacks"
- Keep it short (under 72 characters for the first line)

### Example

```
Add new SQL injection payloads for time-based blind attacks

Added 10 new time-based SQL injection payloads targeting
MySQL and PostgreSQL databases.
```

---

## Reporting Bugs

### Before Reporting

1. Check if the issue already exists
2. Try to reproduce the bug yourself
3. Check the documentation

### Bug Report Template

```markdown
## Bug Description
Clear description of the bug.

## Steps to Reproduce
1. Go to '...'
2. Click on '...'
3. See error

## Expected Behavior
What should happen.

## Actual Behavior
What actually happens.

## Environment
- OS: 
- Python version:
- PHP version:
```

---

## Feature Requests

### Before Requesting

1. Check if the feature already exists
2. Think about how the feature would work
3. Consider if it fits the project scope

### Feature Request Template

```markdown
## Feature Description
Clear description of the feature.

## Why This Feature is Needed
Explain why this feature would be useful.

## Suggested Solution
How you think it could be implemented.

## Alternatives Considered
Other approaches you considered.
```

---

## License

By contributing to Socrates Blade, you agree that your contributions will be licensed under the MIT License.

---

## Questions?

- Open an issue on GitHub
- Check existing issues and discussions

We appreciate all contributions, big or small!