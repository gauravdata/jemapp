<table border="0" width="300">
    <tr bgcolor="#d1d1d1">
        <th>##ID##</th>
        <th>##Commission##</th>
        <th>##Total cost##</th>
        <th>##Order ID##</th>
        <th>##Product ID##</th>
        <th>##Created##</th>
        <th>##Campaign name##</th>
        <th>##Type##</th>        
        <th>##Status##</th>
        <th>##Paid##</th>
        <th>##Affiliate##</th>
        <th>##Channel##</th>
    </tr>
    {foreach from=$sales item=sale}
        <tr>
            <td>{$sale->get("id")}</td> 
            <td>{$sale->get("commission")|currency}</td>
            <td>{$sale->get("totalcost")|currency}</td>
            <td>{$sale->get("orderid")}</td>
            <td>{$sale->get("productid")}</td>
            <td>{$sale->get("dateinserted")|date}</td>
            <td>{$sale->get("name")}</td>
            <td>{$sale->get("rtype")}</td>            
            <td>{$sale->get("rstatus")}</td>
            <td>{$sale->get("payoutstatus")}</td>
            <td>{$sale->get("firstname")} {$sale->get("lastname")}</td>
            <td>{$sale->get("channel")}</td>                                 
        </tr>
    {/foreach}
</table>
