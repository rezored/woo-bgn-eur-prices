# Development Guidelines for WooCommerce BGN/EUR Prices Plugin

## 📋 **Version Management Protocol**

### **When to Update Version:**
- **Patch (1.4.3 → 1.4.4)**: Bug fixes, security improvements, minor enhancements
- **Minor (1.4.3 → 1.5.0)**: New features, WooCommerce Blocks support, performance improvements
- **Major (1.4.3 → 2.0.0)**: Breaking changes, major rewrites, new architecture

### **Files to Update for Every Change:**
1. `wc-multi-currency.php` - Main plugin file
2. `README.md` - GitHub documentation
3. `readme.txt` - WordPress.org plugin directory

---

## 🔧 **Code Update Checklist**

### **1. Main Plugin File (`wc-multi-currency.php`)**

#### **Header Updates:**
```php
/**
 * Plugin Name: Prices in BGN and EUR
 * Description: Displays product prices in BGN and EUR using the fixed exchange rate: 1 EUR = 1.95583 BGN.
 * Version: [NEW_VERSION]
 * Author: Rezored
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
```

#### **Admin Page Updates:**
```php
<p><strong><?php esc_html_e('Версия [NEW_VERSION]:', 'prices-in-bgn-and-eur'); ?></strong> 
<?php esc_html_e('[DESCRIPTION_OF_CHANGES]', 'prices-in-bgn-and-eur'); ?></p>
```

#### **Security Compliance:**
- ✅ Use `esc_js()` for JavaScript output
- ✅ Use `esc_html__()` for translatable strings
- ✅ Use `esc_url()` for URLs
- ✅ Use `esc_attr()` for HTML attributes
- ✅ Escape late (at output point)

---

### **2. GitHub README.md Updates**

#### **Version Badge:**
```markdown
![Version](https://img.shields.io/badge/version-[NEW_VERSION]-blue.svg)
```

#### **Features Section:**
- Add new features to both Bulgarian and English sections
- Use emojis for visual appeal
- Keep consistent formatting

#### **Changelog Section:**
```markdown
## Changelog

### Version [NEW_VERSION]
- [CHANGE_DESCRIPTION]
- [ANOTHER_CHANGE]

### Version [PREVIOUS_VERSION]
- [PREVIOUS_CHANGES]
```

---

### **3. WordPress.org readme.txt Updates**

#### **Stable Tag:**
```txt
Stable tag: [NEW_VERSION]
```

#### **Works On Section:**
```txt
Работи на: WooCommerce 3.0+ (включително WooCommerce Blocks)
```

#### **Changelog Section:**
```txt
= [NEW_VERSION] =
* [CHANGE_DESCRIPTION]
* [ANOTHER_CHANGE]

= [PREVIOUS_VERSION] =
* [PREVIOUS_CHANGES]
```

---

## 🚀 **Feature Implementation Guidelines**

### **WooCommerce Blocks Support:**
1. **JavaScript Approach**: Use client-side scripts for dynamic content
2. **Performance**: Optimize with `setInterval` (2000ms) and immediate execution
3. **Event Handling**: Listen for `updated_wc_block` and `updated_cart_totals`
4. **Disclaimer**: Always include the BNB exchange rate disclaimer

### **Security Implementation:**
1. **Escaping**: Always escape output using appropriate WordPress functions
2. **Internationalization**: Use `__()` and `_e()` for all user-facing text
3. **Validation**: Validate all data before processing
4. **Nonces**: Use nonces for any form processing (if added in future)

### **Performance Optimization:**
1. **Conditional Loading**: Only load scripts on relevant pages
2. **Caching**: Consider caching exchange rates (if dynamic in future)
3. **Minification**: Minify JavaScript and CSS for production
4. **Lazy Loading**: Load heavy resources only when needed

---

## 📝 **Documentation Standards**

### **Code Comments:**
- Use Bulgarian for business logic comments
- Use English for technical comments
- Include `@since` tags for new methods
- Document complex algorithms

### **User Documentation:**
- Provide both Bulgarian and English versions
- Include screenshots for UI changes
- Explain new features clearly
- Include troubleshooting sections

### **Developer Documentation:**
- Document all hooks and filters
- Provide code examples
- Include integration guides
- Maintain API documentation

---

## 🔍 **Testing Protocol**

### **Pre-Release Testing:**
1. **WordPress Compatibility**: Test on latest WordPress version
2. **WooCommerce Compatibility**: Test on latest WooCommerce version
3. **Theme Compatibility**: Test with popular themes
4. **Plugin Conflicts**: Test with common WooCommerce plugins
5. **Security Scan**: Run WordPress security scanner
6. **Performance Test**: Check for performance impact

### **Testing Checklist:**
- [ ] Traditional WooCommerce cart/checkout
- [ ] WooCommerce Blocks cart/checkout
- [ ] Product pages
- [ ] Email templates
- [ ] Admin interface
- [ ] Mobile responsiveness
- [ ] Different currencies (BGN only)
- [ ] Exchange rate accuracy

---

## 🎯 **Quality Assurance**

### **Code Quality:**
- [ ] WordPress Coding Standards compliance
- [ ] PHP syntax validation
- [ ] No linter errors (except false positives)
- [ ] Proper error handling
- [ ] Memory usage optimization

### **Security Quality:**
- [ ] All output properly escaped
- [ ] No SQL injection vulnerabilities
- [ ] No XSS vulnerabilities
- [ ] Proper capability checks
- [ ] Input validation (if applicable)

### **User Experience:**
- [ ] Intuitive interface
- [ ] Fast loading times
- [ ] Responsive design
- [ ] Clear error messages
- [ ] Accessibility compliance

---

## 📦 **Release Process**

### **1. Development Phase:**
- [ ] Implement new features/fixes
- [ ] Update all three files
- [ ] Test thoroughly
- [ ] Security review

### **2. Documentation Phase:**
- [ ] Update changelog
- [ ] Update version numbers
- [ ] Review documentation
- [ ] Create release notes

### **3. Release Phase:**
- [ ] Create GitHub release
- [ ] Update WordPress.org plugin
- [ ] Announce on social media
- [ ] Monitor for issues

---

## 🔄 **Maintenance Schedule**

### **Weekly:**
- Check for WordPress/WooCommerce updates
- Monitor user feedback
- Review security advisories

### **Monthly:**
- Update dependencies
- Performance review
- Code quality audit

### **Quarterly:**
- Major feature planning
- User feedback analysis
- Market research

---

## 📞 **Support Guidelines**

### **User Support:**
- Respond within 24 hours
- Provide clear, step-by-step solutions
- Include code examples when needed
- Escalate complex issues

### **Developer Support:**
- Maintain detailed documentation
- Provide integration examples
- Support custom implementations
- Regular community engagement

---

## 🎨 **Branding Guidelines**

### **Visual Identity:**
- Use consistent color scheme
- Maintain professional appearance
- Include plugin logo when possible
- Follow WordPress design patterns

### **Communication:**
- Use friendly, professional tone
- Provide both Bulgarian and English
- Include emojis for visual appeal
- Maintain consistent messaging

---

## 📊 **Analytics & Monitoring**

### **Usage Tracking:**
- Monitor plugin activation rates
- Track feature usage
- Monitor error rates
- User feedback collection

### **Performance Monitoring:**
- Page load times
- Memory usage
- Database queries
- Server response times

---

## 🔮 **Future Planning**

### **Short-term Goals (3 months):**
- Enhanced WooCommerce Blocks support
- Performance optimizations
- Additional currency support
- User interface improvements

### **Long-term Goals (1 year):**
- Dynamic exchange rates
- Advanced customization options
- Multi-language support
- Premium features

---

## 📚 **Resources & References**

### **WordPress Development:**
- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Security](https://developer.wordpress.org/apis/security/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)

### **WooCommerce Development:**
- [WooCommerce Developer Docs](https://docs.woocommerce.com/)
- [WooCommerce Blocks](https://github.com/woocommerce/woocommerce-blocks)
- [WooCommerce Hooks](https://docs.woocommerce.com/wc-apidocs/hook-docs.html)

### **Tools & Services:**
- [WordPress Plugin Directory](https://wordpress.org/plugins/)
- [GitHub](https://github.com/)
- [WordPress Security Scanner](https://wordpress.org/plugins/wordfence/)

---

*Last Updated: [CURRENT_DATE]*
*Version: 1.0*
*Maintainer: Rezored* 