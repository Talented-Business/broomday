=== Drag and Drop Multiple File Upload - Contact Form 7 ===
Donate link : http://codedropz.com/donation
Tags: drag and drop, contact form 7, ajax uploader, multiple file, upload, contact form 7 uploader
Requires at least: 3.0.1
Tested up to: 5.2
Stable tag: 1.2.5.0
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Drag and Drop Uploader a simple Wordpress plugin extension for Contact Form 7, this will allow you to Upload multiple files using "Drag & Drop" or "Browse Multiple" in uploading area of your form.

Here's a little demo [here](http://codedropz.com/contact).

= Features =

* File Type Validation
* File Size Validation
* Ajax Upload
* Limit number of files Upload.
* Drag & Drop or Browse File - Multiple Upload
* Support Multiple Drag and Drop in One Form.
* Attach to email or send as links.
* Mobile Responsive
* Cool Progress Bar
* Browser Compatability

== Frequently Asked Questions ==

= How can I send feedback or get help with a bug? =

For any bug reports go to <a href="https://wordpress.org/support/plugin/drag-and-drop-multiple-file-upload-contact-form-7">Support</a> page.

= How can I limit file size? =

To limit file size in `multiple file upload` field generator under Contact Form 7, there's a field `File size limit (bytes)`. Please take note it should be `Bytes` you may use any converter just Google (MB to Bytes converter) default of this plugin is 5MB(5242880 Bytes).

= How can I limit the number of files in my Upload? =

You can limit the number of files in your file upload by adding this parameter `max-file:3` to your shortcode : 

Example: (limit - 3): `[mfile upload-file-344 max-file:3]`

= How can I Add or Limit file types =

You can add or change file types in cf7 Form-tag Generator Options by adding `jpeg|png|jpg|gif` in `Acceptable file types field`.

Example : [mfile upload-file-433 filetypes:jpeg|png|jpg|gif]

= How can I change text in Drag and Drop Uploading area? =

You can change text `Drag & Drop Files Here or Browse Files` text in Wordpress Admin menu under `Contact` > `Drag & Drop Upload`.

= How can I change email attachment as links? =

Go to WP Admin `Contact->Drag & Drop Upload` settings then check "Send Attachment as links?" option.

To manage mail template, go to Contact Forms edit specific form and Select `Mail` tab. In Message Body add generated code from mfile. ( Example Below )

Message Body : [your-message]

File Links 1 : [upload-file-754]

File Links2 : [upload-file-755]

Note : No need to add in `File Attachments` field.

== Installation ==

To install this plugin see below:

1. Upload the plugin files to the `/wp-content/plugins/drag-and-drop-multiple-file-upload-contact-form-7.zip` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

== Screenshots ==

1. Generate Upload Field - Admin
2. Form Field Settings - Admin
3. Uploader Settings - Admin
4. Email Attachment- Gmail
5. Email Attachment As links - Gmail
6. Multiple Drag and Drop Fields - Front

== Changelog ==

= 1.2.5.0 =
* Fixed - Please Update to 1.2.5.0 to fixed disable button issue.

= 1.2.5 =
* Fixed - Improved ( Disable button while upload is on progress )
* Fixes - Validate file size limit before uploading the file ( https://wordpress.org/support/topic/file-uploading-is-working-incorrect/ )

= 1.2.4 =
* Added - Support WPML using .po and .mo files
* Added - Added to support multilingual ( using Poedit )
* Fixed - Prevent attachment from sending to Mail(2) if field attachment is not set. (https://wordpress.org/support/topic/problem-with-2th-mail-attachment-2/)
* Added - Disable 'submit' button while upload is on progress...

= 1.2.3 =
* Added - Multiple Drag and Drop fields in a form
* Added - Options in admin for error message
* Added - Option that allow user to send attachment as links
* Added - Added new folder name `wp_dndcf7_uploads` to separate files from wpcf7_uploads ( When option 'Send Attachment as links?' is check ).

= 1.2.2 =
* Add - Create admin settings where you can manage or change text in your uploading area. It's under 'contacts' > 'Drag and Drop'.
* New - Empty or Clear attachment file when Contact Form successfully send. 
* Fixes - Fixed remove item bugs when file is greater than file limit.
* Fixes - Changed 'icon-moon' fonts to avoid conflict with the other themes.
* New - Added text domain for language translations.

= 1.2.2 =
* Issue - fixed bug when file is not required(*).
* Issue - fixed error on 'wpcf7_mail_components' components hooks when there's no file.

= 1.2.1 =
* Issue - fixed bug when file is not required(*).
* Issue - fixed error on 'wpcf7_mail_components' components hooks when there's no file.

= 1.2 =
- Add admin option to limit the number of files. (Maximum File Upload Limit)

= 1.1 =
- This version fixes on user drop validation.
- Optimized Javascript File

= 1.0 =
* Initial Release

== Upgrade Notice ==

= 1.2.3 =
This version fixed minor issues/bugs and add multiple drag and drop fields in a form.

= 1.2.1 =
This version fixed minor issues and bugs.

= 1.2.2 =
Added some usefull features.

= 1.2.4 =
Added new features and fixes.

== Donations ==

Would you like to support the advancement of this plugin? [Donate](http://codedropz.com/donation)