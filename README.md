# Installation notes
Some things you need to know
## config files & symlinks
we use a symlink for the following configs

 * app/etc/local.xml
 * app/etc/modules/ArtsOnIT_Autologin.xml

Make sure to symlinks exists to the correct config.

```
cd app/etc
ln -s local.dev.xml local.xml
cd app/etc/modules
ln -s ArtsOnIT_Autologin.dev.xml ArtsOnIT_Autologin.xml
```

In the above example change dev for live
