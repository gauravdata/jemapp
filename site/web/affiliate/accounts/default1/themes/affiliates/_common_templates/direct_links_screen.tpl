<!-- direct_links_screen -->
##Read more about DirectLinks## <a href='#Custom-Page;%7B"template":"custom/directlink_explained"%7D' style="text-decoration: underline; font-weight: bold; color:#135fab">##here##</a>.
<div>
  <div style="float: left">##You don't need to enter each and every URL address of your pages, you can use star convention.##<br/>
##So for example pattern## <strong>*yoursite.com*</strong> ##will match:##<br/>
www.yoursite.com<br/>
subdomain.yoursite.com<br/>
www.yoursite.com/something.html<br/>
www.yoursite.com/dir/something.php?parameters<br/>
  </div>
  <div style="float: left">
  
<fieldset>
    <legend>##Test URL matching##</legend>
    
    <div class="HintText">##You can test if your pattern matches the given URL.##</div>
        
    <div class="Inliner">##Pattern##</div>
    {widget id="pattern" class="FormFieldBigInline FormFieldOnlyInput"}
    <div class="clear"></div>    
    <div class="Inliner">##Real url##</div>
    {widget id="realUrl" class="FormFieldBigInline FormFieldOnlyInput"}
     <div class="clear"></div>
    {widget id="checkButton"}
    {widget id="message"}
</fieldset>
  </div>
</div>
<div class="clear"></div> 
<br/>
##Links may not be changed. If you want to change link, you can delete the old link and create new, which must merchant again approve##
{widget id="UrlsGrid"}
