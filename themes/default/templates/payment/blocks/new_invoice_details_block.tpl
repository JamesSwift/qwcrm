<!-- new_invoice_details_block.tpl -->
<table width="100%" cellpadding="4" cellspacing="0" border="0" class="olotable">
    <tr class="olotd4">
        <td class="menuhead2"><b>{$translate_payment_invoice_id}</b></td>
        <td class="menuhead2"><b>{$translate_payment_date}</b></td>
        <td class="menuhead2"><b>{$translate_payment_due_date}</b></td>
        <td class="menuhead2"><b>{$translate_payment_amount}</b></td>
        <td class="menuhead2"><b>{$translate_payment_workorder_id}</b></td>
        <td class="menuhead2"><b>{$translate_payment_balance}</b></td>
    </tr>
    <tr>        
        <td>{$invoice_id}</td>
        <td>{$invoice_date|date_format:$date_format}</td>
        <td>{$invoice_due|date_format:$date_format}</td>
        <td>{$currency_sym}{$invoice_total|string_format:"%.2f"}</td>
        <td>{$workorder_id}</td>
        <td><font color="#CC0000"><b>{$currency_sym}{$balance|string_format:"%.2f"}</b></font></td>      
    </tr>
    <tr>
        <td colspan="6" valign="top">
            {foreach item=customer_item from=$customer_details}
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td valign="top">
                            {$customer_item.CUSTOMER_DISPLAY_NAME}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {$customer_item.CUSTOMER_ADDRESS}<br>
                            {$customer_item.CUSTOMER_CITY}, {$customer_item.CUSTOMER_STATE} {$customer_item.CUSTOMER_ZIP}
                        </td>
                    </tr>
                    <tr>
                        <td><b>{$translate_payment_email}</b> {$customer_item.CUSTOMER_EMAIL}</td>
                    </tr>
                    <tr>
                        <td><b>{$translate_payment_phone}</b> {$customer_item.CUSTOMER_PHONE}</td>
                </table>
                {assign var="customer_id" value=$customer_item.CUSTOMER_ID}
            {/foreach}        
        </td>
    </tr>
</table>