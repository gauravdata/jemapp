<!-- traffic_info -->
<table class="StatsSummaries">
<tbody>
        <tr class="gray">
            <td> </td>
            <td>##Used##</td>
            <td>##Limit##</td>           
        </tr>
        <tr class="light">
            <td>##Transactions## {widget id="transactionDates"}</td>
            <td>{widget id="transactionsUsed"}</td>
            <td>{widget id="transactionsLimit"}</td>            
        </tr>
        <tr class="dark">
            <td>##Bandwidth## {widget id="bandwidthDates"}</td>
            <td>{widget id="bandwidthUsed"}</td>
            <td>{widget id="bandwidthLimit"}</td>            
        </tr>
</tbody>        
</table>
<br/>
<table class="StatsSummaries">
<tbody>
        <tr class="gray">
            <td>##Bandwidth##</td>
            <td>##Transactions##</td>
        </tr>
        <tr class="gray">
            <td><div class="TrafficInfoGraph">
{widget id="bandwidthChart"}
</div></td>
            <td><div class="TrafficInfoGraph">
{widget id="transactionChart"}
</div>
</td>
        </tr>
</tbody>
</table>
