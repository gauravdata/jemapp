<table border="0" width="300">
    <tr bgcolor="#d1d1d1">
        <th>##Name##</th>
        <th>##Email##</th>
        <th>##To pay##</th>
        <th>##Approved##</th>
        <th>##Pending##</th>
        <th>##Declined##</th>
        <th>##Minimum payout##</th>
        <th>##Payout method##</th>        
        <th>##Payout data##</th>        
    </tr>
    {foreach from=$payaffiliates item=payaffiliate}
        <tr>
            <td>{$payaffiliate->get("firstname")} {$payaffiliate->get("lastname")}</td> 
            <td>{$payaffiliate->get("username")}</td>
            <td>{$currency}{$payaffiliate->get("amounttopay")}</td>
            <td>{$currency}{$payaffiliate->get("commission")}</td>
            <td>{$currency}{$payaffiliate->get("pendingAmount")}</td>
            <td>{$currency}{$payaffiliate->get("declinedAmount")}</td>
            <td>{$payaffiliate->get("minimumpayout")}</td>
            <td>{$payaffiliate->get("payoutMethod")}</td>
            <td>{$payaffiliate->get("payoutData")}</td>                             
        </tr>
    {/foreach}
</table>
