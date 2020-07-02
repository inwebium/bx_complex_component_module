<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Type\DateTime,
	Bitrix\Main\Loader,
	Bitrix\Iblock;

CPageOption::SetOptionString("main", "nav_page_in_session", "N");
if (!isset($arParams["CACHE_TIME"])) {
	$arParams["CACHE_TIME"] = 36000000;
}

if($this->startResultCache(false)) {

	$arSort = array('NAME' => 'ASC');
	$arFilter = array('IBLOCK_ID' => $arParams['IBLOCK_ID'], "IBLOCK_LID" => SITE_ID, 'ACTIVE' => 'Y', 'ID' => $_REQUEST['ID']);
	$arSelect = array('ID', 'IBLOCK_ID', 'NAME', 'TIMESTAMP_X', 'PROPERTY_*');

	$resIblockElements = \Bitrix\Iblock\ElementTable::getList(array(
			'select' => array(
				'ID', 
				'IBLOCK_ID', 
				'NAME', 
				'TIMESTAMP_X'),
			'filter' => array(
					'IBLOCK_ID' => $arParams['IBLOCK_ID'],
					'ACTIVE' => 'Y',
					'ID' => $_REQUEST['ID']
				),
			//'group' => array(),
			'order' => array(
					'NAME' => 'ASC'
				)
			//'count_total' => $arParams['ELEMENTS_PER_PAGE']
		));

	$arIblockElements = $resIblockElements->fetchAll();
	foreach ($arIblockElements as $arItem) {
		$arButtons = CIBlock::GetPanelButtons(
			$arItem["IBLOCK_ID"],
			$arItem["ID"],
			0,
			array("SECTION_BUTTONS"=>false, "SESSID"=>false)
		);
		$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
		$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

		$dbProperty = \CIBlockElement::getProperty(
			$arItem['IBLOCK_ID'],
			$arItem['ID']
		);

		$arItem['DETAIL_PAGE_URL'] = $APPLICATION->GetCurPage() . '?' . $arParams['DETAIL_PARAM_NAME'] . '=' . $arItem["ID"];

		while($arProperty = $dbProperty->Fetch()){  
			$arItem['PROPERTIES'][$arProperty["CODE"]]['VALUE'][$arProperty["VALUE"]] = $arProperty["VALUE"];
			
		}

		$arLinkedElementsId = array();
		foreach ($arItem['PROPERTIES']['ELEMENTS']['VALUE'] as $iElementId) {
			$arLinkedElementsId[] = $iElementId;
		}

		$resLinkedElements = \Bitrix\Iblock\ElementTable::getList(array(
			'select' => array(
				'ID', 
				'IBLOCK_ID', 
				'NAME'),
			'filter' => array(
					'ID' => $arLinkedElementsId
				),
			'order' => array(
					'NAME' => 'ASC'
				)
		));
		while ($arLinkedElement = $resLinkedElements->fetch()) {
			$arItem["PROPERTIES"]['ELEMENTS']['VALUE'][$arLinkedElement['ID']] = $arLinkedElement;
		}

		$arLinkedUsersId = array();
		foreach ($arItem['PROPERTIES']['USERS']['VALUE'] as $iUserId) {
			$arLinkedUsersId[] = $iUserId;
		}

		$resLinkedUsers = \Bitrix\Main\UserTable::getList(array(
				'select' => array(
						'ID',
						'NAME',
						'LAST_NAME',
						'SECOND_NAME'
					),
				'filter' => array(
						'ID' => $arLinkedUsersId
					),
				'order' => array(
						'LAST_NAME' => 'ASC'
					)
			));

		while ($arLinkedUser = $resLinkedUsers->fetch()) {
			$arItem["PROPERTIES"]['USERS']['VALUE'][$arLinkedUser['ID']] = $arLinkedUser;
		}

		$arItem["MODIFIED"] = DateTime::createFromUserTime($arItem["TIMESTAMP_X"]);

		$arItem['LIST_PAGE_URL'] = $APPLICATION->GetCurPage();

		$arResult['ITEMS'][] = $arItem;

	}

	$this->setResultCacheKeys(array(
			"ITEMS"
		));

	$this->IncludeComponentTemplate();
}

?>