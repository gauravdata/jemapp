<!-- signup_fields -->
<div class="SignupForm">
    <fieldset>
        <legend>##Personal Info##</legend>
        {widget id="username"}
        {widget id="firstname"}
        {widget id="lastname"}
        {widget id="refid" class="Refid"}
        {widget id="parentuserid"}            
    </fieldset>
    
    <fieldset>
        <legend>##Additional info##</legend>
        {widget id="data1"}{*Web Url*}
        {widget id="data2"}{*Company name*}
        {widget id="data3"}{*Street*}
        {widget id="data4"}{*City*}
        {widget id="data5"}{*State*}
        {widget id="data6"}{*Country*}
    </fieldset>
    
    {widget id="payoutMethods"}
    {widget id="termsAndConditions" class="TermsAndConditions"}
    {widget id="agreeWithTerms"}
    {widget id="FormMessage"}
    {widget id="SignupButton"}
</div>
