<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock")) {
	return;
}

$iblocksTypes = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocks=array();
$resIBlock = CIBlock::GetList(
	array("SORT"=>"ASC"), 
	array(
		"SITE_ID"=>$_REQUEST["site"], 
		"TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")
		)
	);


while($arIBlock = $resIBlock->Fetch()) {
	$arIBlocks[$arIBlock["ID"]] = $arIBlock["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => "Тип инфоблоков",
			"TYPE" => "LIST",
			"VALUES" => $iblocksTypes,
			"DEFAULT" => "",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => "Инфоблок с элементами",
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => COption::GetOptionString("example", "EXAMPLE_IBLOCK_ID"),
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"LINKED_IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => "Связанный инфоблок",
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => COption::GetOptionString("example", "LINKED_IBLOCK_ID"),
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"DETAIL_PARAM_NAME" => array(
			"PARENT" => "BASE",
			"NAME" => "Имя параметра с ID элемента",
			"TYPE" => "STRING",
			"DEFAULT" => "ID",
		),
		"ELEMENTS_PER_PAGE" => array(
			"PARENT" => "BASE",
			"NAME" => "Количество элементов на страницу",
			"TYPE" => "STRING",
			"DEFAULT" => "3",
		),
		"CACHE_TIME"  =>  array("DEFAULT"=>36000000),
	)
);

CIBlockParameters::AddPagerSettings(
	$arComponentParameters,
	"Постраничная навигация", //$pager_title
	false, //$bDescNumbering
	false, //$bShowAllParam
	false, //$bBaseLink
	$arCurrentValues["PAGER_BASE_LINK_ENABLE"]==="N" //$bBaseLinkEnabled
);
?>