<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Type\DateTime,
	Bitrix\Main\Application,
	Bitrix\Main\Loader,
	Bitrix\Iblock;

CPageOption::SetOptionString('main', 'nav_page_in_session', 'N');
if (!isset($arParams['CACHE_TIME'])) {
	$arParams['CACHE_TIME'] = 36000000;
}

$arParams['DISPLAY_TOP_PAGER'] = $arParams['DISPLAY_TOP_PAGER']=='Y';
$arParams['DISPLAY_BOTTOM_PAGER'] = $arParams['DISPLAY_BOTTOM_PAGER']!='N';
$arParams['PAGER_TITLE'] = trim($arParams['PAGER_TITLE']);
$arParams['PAGER_TEMPLATE'] = trim($arParams['PAGER_TEMPLATE']);

if($arParams['DISPLAY_TOP_PAGER'] || $arParams['DISPLAY_BOTTOM_PAGER']) {
	$arNavParams = array(
		'nPageSize' => $arParams['ELEMENTS_PER_PAGE'],
		'bShowAll' => $arParams['PAGER_SHOW_ALL']
	);
	$arNavigation = CDBResult::GetNavParams($arNavParams);
} else {
	$arNavParams = array(
		'nTopCount' => $arParams['ELEMENTS_PER_PAGE'],
	);
	$arNavigation = false;
}

$request = Application::getInstance()->getContext()->getRequest();

if(
	$this->startResultCache(
		$arParams["CACHE_TIME"], 
		array(
			$arNavigation
			)
		)) {

	$arSort = array('NAME' => 'ASC');
	$arFilter = array('IBLOCK_ID' => $arParams['IBLOCK_ID'], "IBLOCK_LID" => SITE_ID, 'ACTIVE' => 'Y');

	$arSelect = array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL', 'LIST_PAGE_URL', 'TIMESTAMP_X', 'PROPERTY_*');

	$arNavParams['iNumPage'] = $arNavigation['PAGEN'];

	$resIblock = \Bitrix\Iblock\IblockTable::getById($arParams['IBLOCK_ID']);
	$arResult['IBLOCK'] = $resIblock->fetch();

	$iOffset = $arNavParams['nPageSize'] * ($arNavParams['iNumPage'] - 1);
	$resElements = CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);
	$arLinkedElementsId = array();
	$arLinkedUsersId = array();

	while ($obIblockElement = $resElements->GetNextElement()) {
		$arItem = $obIblockElement->GetFields();

		$arButtons = CIBlock::GetPanelButtons(
				$arItem["IBLOCK_ID"],
				$arItem["ID"],
				0,
				array("SECTION_BUTTONS"=>false, "SESSID"=>false)
			);
		$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
		$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

		$arItemProperties = $obIblockElement->GetProperties();

		$arItem['PROPERTIES']['ELEMENTS'] = $arItemProperties['ELEMENTS'];

		foreach ($arItemProperties['ELEMENTS']['VALUE'] as $iElementId) {
			$arLinkedElementsId[] = $iElementId;
		}
		
		$arItem['PROPERTIES']['USERS'] = $arItemProperties['USERS'];

		foreach ($arItemProperties['USERS']['VALUE'] as $iUserId) {
			$arLinkedUsersId[] = $iUserId;
		}

		$arItem["MODIFIED"] = DateTime::createFromUserTime($arItem["TIMESTAMP_X"]);
		$arItem['DETAIL_PAGE_URL'] = $APPLICATION->GetCurPage() . '?' . $arParams['DETAIL_PARAM_NAME'] . '=' . $arItem["ID"];
		$arResult['ITEMS'][$arItem["ID"]] = $arItem;
	}

	$resLinkedElements = CIBlockElement::GetList(array(), array('ID' => $arLinkedElementsId));
	
	while ($arLinkedElement = $resLinkedElements->GetNext()) {
		$arResult["DISPLAY_PROPERTIES"]['ELEMENTS'][$arLinkedElement['ID']] = $arLinkedElement;
	}

	$resLinkedUsers = CUser::GetList(($by = 'last_name'), ($order = 'asc'), array('ID' => $arLinkedUsersId));
	
	while ($arLinkedUser = $resLinkedUsers->GetNext()) {
		$arResult["DISPLAY_PROPERTIES"]['USERS'][$arLinkedUser['ID']] = $arLinkedUser;
	}

	if ($arNavigation['PAGEN'] > 1 && $request->getQuery('AJAX') == 'Y') {
		$this->setResultCacheKeys(array(
				"ITEMS"
			));
		$this->IncludeComponentTemplate('ajax');
	} else {
		$arResult["NAV_STRING"] = $resElements->GetPageNavStringEx(
				$navComponentObject,
				$arParams["PAGER_TITLE"],
				$arParams["PAGER_TEMPLATE"]
			);

		$this->setResultCacheKeys(array(
				"NAV_STRING",
				"ITEMS",
			));
		$this->IncludeComponentTemplate();
	}
}
?>