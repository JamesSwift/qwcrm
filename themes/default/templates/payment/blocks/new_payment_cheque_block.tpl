<!-- new_payment_cheque_block.tpl -->
<form method="post" action="index.php?page=payment:new&invoice_id={$invoice_id}">
    <table width="100%" cellpadding="4" cellspacing="0" border="0" >
        <tr>
            <td class="menuhead2">&nbsp;{t}Cheque{/t}</td>
        </tr>
        <tr>
            <td class="menutd2">
                <table width="100%" cellpadding="4" cellspacing="0" border="0" width="100%" cellpadding="4" cellspacing="0" border="0" class="olotable">
                    <tr class="olotd4">
                        <td class="row2"></td>
                        <td class="row2"><b>{t}Cheque Number{/t}:</b></td>
                        <td class="row2"><b>{t}Amount{/t}</b></td>
                    </tr>
                    <tr class="olotd4">
                        <td></td>                        
                        <td><input name="cheque_number" class="olotd5" type="text" maxlength="15" required onkeydown="return onlyNumbers(event);"></td>                        
                        <td>{$currency_sym}<input name="amount" class="olotd5" size="10" value="{$invoice_details.BALANCE|string_format:"%.2f"}" type="text" maxlength="10" required pattern="{literal}[0-9]{1,7}(.[0-9]{0,2})?{/literal}" required onkeydown="return onlyNumbersPeriod(event);"/></td>
                    </tr>
                    <tr>
                        <td valign="top"><b>{t}Note{/t}</b></td>
                        <td colspan="2" ><textarea name="note" cols="60" rows="4" class="olotd4"></textarea></td>
                    </tr>
                </table>
                <p>
                    <input type="hidden" name="type" value="2">                    
                    <button type="submit" name="submit" value="submit">{t}Submit Cheque Payment{/t}</button>
                </p>
            </td>
        </tr>
    </table>
</form>