<!--	invoice_data_panel	-->

<div class="left">##Invoice number##{widget id="number"}</div> 
<div class="right">##Amount##{widget id="amount"}</div>
<div style="clear: both"></div>
<hr/>
<br/>
<div class="left">##Date from##{widget id="datefrom"}</div>
<div class="right">##Due date##{widget id="duedate"}</div>
<div class="left">##Date to##{widget id="dateto"}</div>
<div class="right">##Status##{widget id="rstatus"}</div>
<div style="clear: both"></div>
<br/>
##Invoice note##{widget id="merchantnote"}<br/>
{widget id="proformatext" class="Invoice"}{widget id="invoicetext" class="Invoice"}
<br/>
{widget id="print"}{widget id="close"}{widget id="send_mail"}
