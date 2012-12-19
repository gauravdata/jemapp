UPDATE qu_g_settings SET value = '<a href="{$targeturl}"><img src="{$image_src}" alt="{$alt}" title="{$alt}" width="{$width}" height="{$height}" /></a>{$impression_track}' 
WHERE name = 'BannerFormatImagebanner';
UPDATE qu_g_settings SET value = '<object type="application/x-shockwave-flash"
                            data="{$flashurl}?clickTAG={$targeturl_encoded}" width="{$width}" height="{$height}">
                            <param name="movie" value="{$flashurl}?clickTAG={$targeturl_encoded}" />
                            <param name="loop" value="{$loop}"/><param name="menu" value="false"/><param name="quality" value="medium"/><param name="wmode" value="{$wmode}"/>
                            </object>
                            {$impression_track}' 
WHERE name = 'BannerFormatFlash';