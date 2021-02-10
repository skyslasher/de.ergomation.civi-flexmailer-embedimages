# CiviCRM Flexmailer Embed Images Plugin

This plugin embeds images in HTML e-mails (HTML inline images) instead of linking to them.
CiviCRM trackers will be left untouched.
This increases the e-mail size, but images are displayed images in the e-mail client right away,
even when autoloading images is turned off on the client side (what is the new default setting nowadays).
It also has the option to embed only locally hosted images, what can be useful for copyright reasons.

This plugin is one of the successors of the
[Wordpress integration for CiviMail with Mosaico plugin](https://github.com/skyslasher/de.ergomation.wp-civi-mosaico)
that is now split into three separate plugins:
* [CiviCRM Mosaico Plugin Interface](https://github.com/skyslasher/de.ergomation.civi-mosaico-plugininterface)
* CiviCRM Flexmailer Embed Images (this plugin)
* [WordPress CivCRM Mosaico Integration](https://github.com/skyslasher/wp-civi-mosaico)

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.2+
* CiviCRM 5.x
* CiviCRM FlexMailer plugin (included in CiviCRM 5.28+, older versions need to download the extension)

## Installation (Web UI)

Learn more about installing CiviCRM extensions in the [CiviCRM Sysadmin Guide](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/).

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl de.ergomation.civi-flexmailer-embedimages@https://github.com/skyslasher/de.ergomation.civi-flexmailer-embedimages/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/skyslasher/de.ergomation.civi-flexmailer-embedimages.git
cv en civi_flexmailer_embedimages
```

## Getting Started

Install the dependencies and afterwards the plugin. The settings can be reached in
the Mailings menu under *Advanced E-Mail settings*.

## Known Issues

Currently there are no known issues.
