## How to update MinePeon WebUI

Be careful! The latest version is not compatible with version 2.2 and below! That is because settings names have been semantically renamed. ($devices => $miningExpDev) I'm working on a way to migrate old settings to new.

Apart from losing settings, updating the WebUI can do no harm.

### MinePeonWebUI: Pull latest version and set permissions

```Shell
cd /opt/minepeon/http/
git pull
touch /opt/minepeon/etc/minepeon.conf
chmod 660 /opt/minepeon/etc/minepeon.conf
chown minepeon.http /opt/minepeon/etc/minepeon.conf
mkdir /opt/minepeon/http/rrd/ /opt/minepeon/http/phpliteAdmin/
chmod 775 /opt/minepeon/http/rrd/ /opt/minepeon/http/phpliteAdmin/
chown minepeon.http /opt/minepeon/http/rrd/ /opt/minepeon/http/phpliteAdmin/
```

## How to update MinePeon core

### MinePeon: Pull latest version and set permissions

```Shell
cd /opt/minepeon/
git pull
touch /opt/minepeon/etc/minepeon.conf
chmod 660 /opt/minepeon/etc/minepeon.conf
chown minepeon.http /opt/minepeon/etc/minepeon.conf
```

## What to expect in the next release

* New page: advanced.html, SPA built in Angular.js
* New option: E-mail alerts
* New option: Automatically attempt recovery
* New function: Backup files and folders locally

## Changed

* Pools: only url and user are required, pass will be set to the string "none" if empty
* Settings: help-text might be incorrect
* added variables donateEnable & alertEnable 
* variables renamed, but still compatible
* 
