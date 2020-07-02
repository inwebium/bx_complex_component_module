<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->IncludeComponent(
	"inwebium:example.list",
	"",
	Array(
		"CACHE_TIME" => $arParams['CACHE_TIME'],
		"CACHE_TYPE" => $arParams['CACHE_TYPE'],
		"DISPLAY_BOTTOM_PAGER" => $arParams['DISPLAY_BOTTOM_PAGER'],
		"DISPLAY_TOP_PAGER" => $arParams['DISPLAY_TOP_PAGER'],
		"ELEMENTS_PER_PAGE" => $arParams['ELEMENTS_PER_PAGE'],
		'DETAIL_PARAM_NAME' => $arParams['DETAIL_PARAM_NAME'],
		"IBLOCK_ID" => $arParams['IBLOCK_ID'],
		"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
		"LINKED_IBLOCK_ID" => $arParams['LINKED_IBLOCK_ID'],
		"PAGER_SHOW_ALWAYS" => $arParams['PAGER_SHOW_ALWAYS'],
		"PAGER_TEMPLATE" => $arParams['PAGER_TEMPLATE'],
		"PAGER_TITLE" => $arParams['PAGER_TITLE'],
	),
	$component
);
?>