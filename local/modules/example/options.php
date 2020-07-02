<h1>Настройка модуля</h1>

<?
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
$moduleId = "example";
$arIblocks = array();

if (CModule::IncludeModule("iblock")) {
	
	$resIBlocks = CIBlock::GetList(
			array('NAME'=>'ASC'), 
			array('SITE_ID'=>'s1')
		);
	
	while($arIBlock = $resIBlocks->GetNext()) {
		$arIblocks[$arIBlock["ID"]] = $arIBlock["NAME"];
	}
}

$arOptions = array(
    array(
	    	"EXAMPLE_IBLOCK_ID", 
	    	"Основной инфоблок", 
	    	array(
	    		"select",
	    		$arIblocks
	    		), 
	    	""
		),
    array(
	    	"LINKED_IBLOCK_ID", 
	    	"Связанный инфоблок", 
	    	array(
	    		"select",
	    		$arIblocks
	    		), 
	    	""
		)
);

$arTabs = array(
	    array(
	    	"DIV" => "componentsSettings", 
	    	"TAB" => "Настройки компонентов", 
	    	"ICON" => "perfmon_settings", 
	    	"TITLE" => "Настройки компонентов"
		),
	);
$tabControl = new CAdminTabControl($moduleId . "_tabControl", $arTabs);

CModule::IncludeModule($moduleId);

if($REQUEST_METHOD=="POST" && strlen($Update.$Apply) > 0 && check_bitrix_sessid())
{

    foreach($arOptions as $arOption)
    {
        $name=$arOption[0];
        $val=$_REQUEST[$name];
        $setOptionResult = COption::SetOptionString($moduleId, $name, $val);
    }
}

?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($moduleId)?>&amp;lang=<?=LANGUAGE_ID?>">
    <?
    $tabControl->Begin();
    $tabControl->BeginNextTab();

    foreach($arOptions as $arOption) {
    	$optionValue = COption::GetOptionString($moduleId, $arOption[0], $arOption[3]);
    	$optionType = $arOption[2];
    ?>
    	<tr>
    		<td width="40%" nowrap <?if($optionType[0]=="textarea") echo 'class="adm-detail-valign-top"'?>>
                <label for="<?echo htmlspecialcharsbx($arOption[0])?>"><?echo $arOption[1]?>:</label>
    		</td>
    		<td width="60%">
    			<?
    			switch ($optionType[0]) {
    				case 'text':
    					?>
    					<input 
    						type="text"
    						size="<?echo $optionType[1]?>" 
    						maxlength="255" 
    						value="<?echo htmlspecialcharsbx($optionValue)?>" 
    						name="<?echo htmlspecialcharsbx($arOption[0])?>" 
    						id="<?echo htmlspecialcharsbx($arOption[0])?>">
    					<?
    					break;
    				case 'select':
    					?>
    					<select
    						name="<?echo htmlspecialcharsbx($arOption[0])?>"
    						id="<?echo htmlspecialcharsbx($arOption[0])?>">
    						<?
    						foreach ($optionType[1] as $keyOption => $valueOption) {
    							$selected = ($keyOption == $optionValue) ? 'selected="selected"' : '' ;
    						?>
    							<option value="<?=$keyOption;?>" <?=$selected;?>><?=$valueOption;?></option>
    						<?
    						}
    						?>
    					</select>
    					<?
    					break;
    				default:
    					break;
    			}
    			?>
            </td>
    	</tr>
    <?
    }
    $tabControl->Buttons();
    ?>
    <input 
    	type="submit"
    	name="Update"
    	value="<?=GetMessage("MAIN_SAVE")?>"
    	title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>"
    	class="adm-btn-save">
    <input
    	type="submit"
    	name="Apply"
    	value="<?=GetMessage("MAIN_OPT_APPLY")?>"
    	title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
    <?if(strlen($_REQUEST["back_url_settings"])>0):?>
        <input 
        	type="button"
        	name="Cancel"
        	value="<?=GetMessage("MAIN_OPT_CANCEL")?>"
        	title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>"
        	onclick="window.location='<?echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
        <input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
    <?endif?>
    <?=bitrix_sessid_post();?>
    <?$tabControl->End();
    ?>
</form>