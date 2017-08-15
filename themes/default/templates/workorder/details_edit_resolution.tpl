<!-- resolution.tpl -->
<script src="{$theme_js_dir}tinymce/tinymce.min.js"></script>
<script>{include file="`$theme_js_dir_finc`editor-config.js"}</script>}

<table width="100%" border="0" cellpadding="20" cellspacing="0">
    <tr>
        <td>
            <table width="700" cellpadding="5" cellspacing="0" border="0" >
                <tr>
                    <td class="menuhead2" width="80%">{t}Edit Work Order Resolution{/t}</td>
                    <td class="menuhead2" width="20%" align="right" valign="middle">
                        <a>
                            <img src="{$theme_images_dir}icons/16x16/help.gif" border="0" onMouseOver="ddrivetip('<div><strong>{t escape=tooltip}WORKORDER_DETAILS_EDIT_RESOLUTION_HELP_TITLE{/t}</strong></div><hr><div>{t escape=tooltip}WORKORDER_DETAILS_EDIT_RESOLUTION_HELP_CONTENT{/t}</div>');" onMouseOut="hideddrivetip();">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="menutd2" colspan="2">                        
                        <table width="100%" class="olotable" cellpadding="5" cellspacing="0" border="0" >
                            <tr>
                                <td width="100%" valign="top">                                    
                                    <form action="index.php?page=workorder:details_edit_resolution" method="post" name="close_workorder" id="close_workorder">
                                        <b>{t}Edit Work Order Resolution{/t}</b><br>
                                        <textarea class="olotd4" rows="15" cols="70" name="resolution">{$resolution}</textarea>
                                        <br>                                        
                                        <input name="workorder_id" value="{$workorder_id}" type="hidden">                                        
                                        <input name="submitchangesonly" value="{t}Submit Changes Only{/t}" type="submit">
                                        <input name="closewithoutinvoice" value="{t}Close Without Invoice{/t}" type="submit">
                                        <input name="closewithinvoice" value="{t}Close With Invoice{/t}" type="submit">
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