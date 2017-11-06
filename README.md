# ampinstant-cf
AMPInstant Contact Form (No JS, NO CSS added on html)


##Instalasi

* Download and extract ampinstant-cf.zip to wp-content/plugins/
* Activate the plugin through the ‘Plugins’ menu in WordPress.
* Add this Shortcode in your contact form page 
``` 
[ampinstant-contact-form]
```
##Problem Solving

Jika fungsi wp_mail tidak bekerja, coba gunakan plugin tambahan https://wordpress.org/plugins/wp-mail-smtp/ dan gunakan SMTP anda.

Saran saya, Anda bisa menggunakan penyedia SMTP seperti 
* Mailgun
* Mailjet
* Sendgrid
* dll 