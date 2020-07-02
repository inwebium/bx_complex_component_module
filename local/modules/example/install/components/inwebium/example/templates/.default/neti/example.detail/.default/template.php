<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->AddHeadScript('https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js');
$APPLICATION->SetAdditionalCSS("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css");
$APPLICATION->SetAdditionalCSS("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css");
$APPLICATION->AddHeadScript('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
CJSCore::Init(array('ajax'));
?>

<div class="elements-list">
	<? foreach ($arResult['ITEMS'] as $key => $arItem): ?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => 'Удалить элемент ' . $arItem['NAME'] . '?'));
		?>
		<div class="element" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<h3><?=$arItem['NAME'];?></h3>
			<p class="date-modified"><?=$arItem['MODIFIED'];?></p>
			<ul>
				<? foreach ($arItem["PROPERTIES"]['USERS']['VALUE'] as $iUserId => $arUser): ?>
					<li>
						<? print($arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME']); ?>
					</li>
				<? endforeach ?>
			</ul>
			<ul>
				<? foreach ($arItem["PROPERTIES"]['ELEMENTS']['VALUE'] as $iElementId => $arElement): ?>
					<li>
						<?=$arElement['NAME'];?>
					</li>
				<? endforeach ?>
			</ul>
		</div>
	<? endforeach ?>
	<a href="<?=$arItem['LIST_PAGE_URL'];?>">К списку</a>
</div>