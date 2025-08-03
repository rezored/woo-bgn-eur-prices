# Development Workflow for WooCommerce BGN/EUR Plugin

## Version Update Process

### 1. Before Making Changes
- [ ] Create a new branch for your changes
- [ ] Note the current version number
- [ ] Plan what changes will be made

### 2. After Making Code Changes
- [ ] Test the plugin functionality
- [ ] Update version number using the script: `php update-version.php 1.4.8`
- [ ] Manually update changelog with specific changes
- [ ] Update any new features in documentation

### 3. Files That Need Version Updates

#### Main Plugin File (`prices-in-bgn-and-eur.php`)
- [ ] Plugin header version
- [ ] CSS/JS asset versions
- [ ] Admin page version display

#### Documentation (`README.md`)
- [ ] Version badges (Bulgarian and English sections)
- [ ] Changelog entries (both languages)
- [ ] Any new features or changes

#### WordPress Readme (`readme.txt`)
- [ ] Stable tag version
- [ ] Changelog entries
- [ ] Any new features

### 4. Automated Version Update

Use the provided script to update version numbers:

```bash
php update-version.php 1.4.8
```

This will automatically update:
- Plugin header version
- CSS/JS asset versions  
- README.md version badges
- readme.txt stable tag

### 5. Manual Updates Required

After running the script, you still need to manually update:

1. **Changelog Details**: Replace the generic "Bug fixes and improvements" with specific changes
2. **New Features**: Update documentation for any new functionality
3. **Screenshots**: Update if UI changes were made
4. **Testing**: Verify all changes work correctly

### 6. Commit and Release

- [ ] Commit all changes with descriptive message
- [ ] Create git tag for the new version
- [ ] Test the plugin one more time
- [ ] Prepare release notes

## Version Numbering Convention

- **Major.Minor.Patch** (e.g., 1.4.7)
- **Major**: Breaking changes
- **Minor**: New features, backward compatible
- **Patch**: Bug fixes, backward compatible

## Checklist for Each Release

### Code Changes
- [ ] All functionality tested
- [ ] No console errors
- [ ] Works with different themes
- [ ] Works with different WooCommerce versions

### Documentation Updates
- [ ] Version numbers updated everywhere
- [ ] Changelog entries added
- [ ] New features documented
- [ ] Screenshots updated (if needed)

### Release Preparation
- [ ] Plugin tested in WordPress admin
- [ ] ZIP file created
- [ ] Release notes prepared
- [ ] Git tag created

## Quick Commands

```bash
# Update version to 1.4.8
php update-version.php 1.4.8

# Create git tag
git tag -a v1.4.8 -m "Version 1.4.8"

# Push tag
git push origin v1.4.8
```

## Notes for AI Assistant

When making changes to the plugin, please:

1. **Always update version numbers** in all relevant files
2. **Add changelog entries** with specific details about changes
3. **Update documentation** for any new features
4. **Test functionality** before considering changes complete
5. **Follow the workflow** outlined in this document

This ensures consistency and proper version management across all plugin files. 