<!-- memo.tpl -->
<script src="{$theme_js_dir}tinymce/tinymce.min.js"></script>
<script src="{$theme_js_dir}editor-config.js"></script>

<table width="100%" border="0" cellpadding="20" cellspacing="0">
    <tr>
        <td>
            <table width="700" cellpadding="5" cellspacing="0" border="0" >
                <tr>
                    <td class="menuhead2" width="80%">New Memo</td>
                </tr>
                <tr>
                    <td class="menutd2">                        
                        <table width="100%" class="olotable" cellpadding="5" cellspacing="0" border="0" >
                            <tr>
                                <td width="100%" valign="top" >                                  
                                    <form method="post" action="index.php?page=customer:memo_new&customer_id={$customer_id}">                                        
                                        <table class="olotable" width="100%" border="0" >
                                            <tr>
                                                <td class="olohead"></td>
                                            </tr>
                                            <tr>
                                                <td class="olotd"><textarea name="customer_note" class="olotd4 mceCheckForContent" rows="15" cols="70"></textarea></td>
                                            </tr>
                                        </table>
                                        <br>
                                        <input class="olotd4" name="submit" value="submit" type="submit">    
                                    </form>
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>