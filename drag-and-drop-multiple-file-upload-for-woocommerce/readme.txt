=== Drag and Drop Multiple File Upload for WooCommerce ===
Contributors: glenwpcoder
Tags: drag and drop, woocommerce, ajax uploader, multiple file, upload
Requires at least: 3.0.1
Tested up to: 6.8
Stable tag: 1.1.7
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Drag and Drop Multiple File Uploader is a simple, straightforward WordPress plugin extension for WooCommerce.

== Description ==

**Drag and Drop Multiple File Uploader** is a simple, straightforward WordPress plugin extension for WooCommerce that transforms your standard upload interface into a visually appealing file uploader. it allows users to upload multiple files using either the **drag-and-drop** feature or the common file browsing option on your product page.

Plugin requires at least v3.5.0 of WooCommerce.

Here's a little [DEMO](https://woo-commerce.codedropz.com/product/cap/).

### Features

* File Type Validation
* File Size Validation
* Ajax Uploader
* Limit number of files Upload.
* Limit files size for each field
* Can specify custom file types or extension
* Manage Text and Error message in admin settings
* Drag & Drop or Browse File - Multiple Upload
* Display Uploader in WooCommerce - Single Product Page
* Option to display in "Add to Cart Form", "Variations Form", "Add To Cart Button", "Single Variation".
* Able to delete uploaded file before adding to cart
* Support multiple languages
* Mobile Responsive
* Compatible with any browser

### ⭐ Premium Features
1. **Upload Large File** - Supports uploading large files.
2. **Image Preview** - Displays thumbnails for images.
3. **Text & Style** - Color options, borders, uploader icon, and more.
4. **Parallel Upload** - Limit simultaneous uploads to optimize server performance.
5. **Custom Filename** - Define custom filename patterns: *(Filename, Username, User ID, IP Address, Random, etc)*
6. **Change Upload Directory** - Customize the default WordPress upload directory.
7. **Upload Folder** - 📂 Choose a custom folder to store files:
	✅ Order No - Customer Order Number
	✅ Random - Auto-generated  Numbers
	✅ Date - Date formmat *(e.g., 04-31-2020)*
	✅ Time - Timestamp
	✅ Name - Users customer Firstname
	✅ Customer ID - Users customer ID
8. **Custom Fees** - Basic conditional fees.
	✅ Charge the user based on the **no. of files** *(e.g., 20 files ≥ 2 → add $20)*.
	✅ Charge the user based on **PDF pages** *(e.g., 20 pages ≥ 10 → multiply $2)*.
9. **Remove/Reject Files** - Able to remove or delete files in admin orders.
10. **Chunked Uploads** - Upload large files in smaller chunks to avoid timeouts.
11. **Uploader Visibility** - Show based on (Categories, Products, Tags, Attributes).
12. **Uploader Position** - Show **before** or **after** Add to Cart, Form, or Variations.
13. **Uploader Display** - Show on either the **"Checkout"** or **"Product"** page.
14. **ZIP Files** - Compress uploaded files into a ZIP archive
16. **Seamless Remote Storage Integration** (New)
	🔥 Supports: **Google Drive**, **Amazon S3**, **Dropbox**, **FTP**.
17. **Attach Files to Email** (New) - Include uploaded files in order confirmation emails.
	📝 **Note:** Works only with **standard storage** *(not compatible with remote storage)*.
18. **Ajax Upload** - Upload files without page reload for a seamless experience.
19. **Unlimited Uploads** - Users can upload as many files as needed.
20. **Security** - Ensure security with regular updates, vulnerability scans, and threat protection.
21. **Optimized Code & Performance** – Improve speed and efficiency.
23. **Unlimited Sites** - Use on any number of websites without restrictions.
24. **1 Month Premium Support** - Get priority assistance for one month.
25. **Multilingual Support** - Compatible with **WPML** and **Polylang** for translations.

Pro version [DEMO](https://www.codedropz.com/woo-commerce-pro/shop/).

You can get [PRO Version here!](https://www.codedropz.com/woocommerce-drag-drop-multiple-file-upload/)

### Other Plugin You May Like

* [Drag & Drop Multiple File Upload - WPForms](https://www.codedropz.com/drag-drop-file-uploader-wpforms/)
An extension for **WPForms**
* [Drag & Drop Multiple File Upload - Contact Form 7](https://wordpress.org/plugins/drag-and-drop-multiple-file-upload-contact-form-7/)
An extension for **Contact Form 7**

== Frequently Asked Questions ==

= How can I send feedback or get help with a bug? =

For any bug reports go to <a href="https://wordpress.org/support/plugin/drag-and-drop-multiple-file-upload-for-woocommerce">Support</a> page.

= How can I change File Upload Name? =

Go to "WooCommerce > Settings > File Uploads" in "Upload Restriction - Options" section there's a field "Name" where you can add/change of the uploader name.

= How can I change "File Upload" Label =

Go to "WooCommerce > Settings > File Uploads" in "Uploader Info" there's a field "File Upload Label" where you can change/add a custom label.

= How can I limit Max File Size? =

To limit file size, go to "WooCommerce > Settings > File Uploads" scroll down and find "Upload Restriction" section.

On that section there's a Text field name "Max File Size (Bytes)" that you specify File Size limit of each file. (if this field empty, default: 10MB)

Please also take note it should be `Bytes` you may use any converter just Google (MB to Bytes converter).

= How can I set "Max" Number of Files in my Upload? =

To limit the Num of files go to "WooCommerce > Settings > File Uploads" find the "Upload Restriction" section and then add number in "Max File Upload" field. (default : 10)

= How can I set a "Minimum" File Upload? =

To set Minimum Num of files go to "WooCommerce > Settings > File Uploads" find the "Upload Restriction" section and then add number in "Min File Upload" field.

= How can I Add or Limit File Types? =

To add file types restriction, in "WooCommerce > Settings > File Uploads" scroll down and find the "Upload Restriction" section.

In 'Supported File Types' field, add File types/extensions you want to accept, this should be separated by (,) comma.

Example: jpg, png, jpeg, gif

= How can I change text in my Uploader? =

You can change text `Drag & Drop Files Here or Browse Files` text in Wordpress Admin, it's under "WooCommerce > Settings > File Uploads".

= How to Disable Uploader in Specific Product? =

Go to "Products" then "Edit" specific products.

In "Product Data" box/widget click "File Uploads" tab then there's an option that allow you disable the uploader.

= How to change label for individual product? =

Go to "Products" then "Edit" specific products.

In "Product Data" box/widget click "File Uploads" tab then there's a field name "Label" where you can add custom label for individual product.

= How can I change Error Messages? =

All error message can be managed here "WooCommerce > Settings > File Uploads" 'Error Message' section.

== Installation ==

To install this plugin see below:

1. Upload the plugin files to the `/wp-content/plugins/drag-and-drop-multiple-file-upload-for-woocommerce.zip` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Configure plugin in "WooCommerce > Settings > File Uploads".

== Screenshots ==

1. Product Single Page - Front-end
2. Upload in Progress - Front-end
3. Shopping Cart - Front-end
4. Order Details - Front-end
5. Order Details - Admin
6. File Upload (Product Settings) - Admin
7. Uploader Settings - Admin
8. Upload Display - Front-end

== Changelog ==

= 1.1.7 =
- Securty - Fixed security issue reported by WordFence via RIN MIYACHI (Unauthenticated Arbitrary File Upload via upload Function)

= 1.1.6 =
- Tested on Wordpress 6.8
- Fixed warning on text domain (_load_textdomain_just_in_time was called incorrectly)

= 1.1.5 =
- Fixed - vulnerability issues reported by Phat RiO - BlueRock via (WordFence).
- Changes - Tweak readme.txt plugin description and features.

= 1.1.4 =
- Fixed load_plugin_textdomain warning
- WooCommerce 9.6.0 compatibility check

= 1.1.3 =
* Check WooCommerce 9.1.4 compatibility
* WordPress compatibility check 6.6.1

= 1.1.2 =
* Check WooCommerce 8.2.1 compatibility
* Declared compatibility for HPOS

= 1.1.1 =
* Security - Addressed and resolved security vulnerabilities that were reported (Thanks to "Marc Montpas")

= 1.1.0 =
* Bug Fix - Overwrite the existing file if a file with the same name already exists

= 1.0.10 =
* Fixes - Bug fixes
* Fixes - Added alternative solution for cache nonce
* Checking Wordpress 6.2 compatibility & WooCommerce 7.5.1

= 1.0.9 =
* Fixes - Security Fixes
* Added - Security nonce for upload and delete (Ajax Request)

= 1.0.8 =
* Bug - Css fixes font Conflict
* Check - Test with latest version of Wordpress 6.1.1 and WooCommerce 7.3.0

= 1.0.7 =
* New - French Translation Updated (Thanks to @dleroux61 / Dominique Le Roux)
* Check - Tested with latest version of Wordpress 5.9.3 & WooCommerce 6.4.1

= 1.0.6 =
* Fixes - Disable File Upload not working.
* Tested - In Wordpress 5.8.2 & Latest WooCommerce version

= 1.0.5 =
* Fixes - Custom text/message issue.

= 1.0.4 =
* Add accept attributes to display specific file types when browsing files - https://wordpress.org/support/topic/restrict-upload-in-browse-files/
  - use 'dndmfu_wc_all_types' filter (bolean) to show all types.
* Translate “deleting”, “of” & “remove” text.
* Added compatibility plugin for polylang & wpml multilingual.

= 1.0.3 =
* Bug - Fixes
* Fixed - Conflict with "Drag & Drop Multiple Upload For CF7"
* Fixed - Option error message not showing
* Note - You need to go to "WooCommerce -> Settings -> File Uploads" and re-save options.

= 1.0.2 =
* Bug - Fixes
* Fixed - Minimum file validation error message not showing.

= 1.0.1 =
* Bug - Fixes
* New - Added new option to disable file upload (globally).
* New - Added option in "Product Data" to enable/disable file upload of individual product.

= 1.0 =
* Initial Release

== Upgrade Notice ==

== Donations ==
Would you like to support the advancement of this plugin? [Donate](http://codedropz.com/donation)