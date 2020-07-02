<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}
?>
<div id="b_navigation-ajax_<?=$arResult["NavNum"];?>">
<?
$sNextHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'=';
?>
	<div id="navigation-ajax_<?=$arResult["NavNum"];?>">
		<a href="<?=$sNextHref;?>" class="load-more_<?=$arResult["NavNum"];?>">
			Загрузить еще
		</a>
	</div>
</div>
<script type="text/javascript">
	var currentPage = <?=$arResult["NavPageNomer"];?>;
	var pageCount = <?=$arResult["NavPageCount"];?>;
	function RequestPage() {
		BX.ajax.get(
				document.getElementsByClassName('load-more_<?=$arResult["NavNum"];?>')[0].href + (currentPage+1), 
				{URL: document.getElementsByClassName('load-more_<?=$arResult["NavNum"];?>')[0].href + (currentPage+1), AJAX: "Y"}, 
				ResponseCallback
			);
	}

	function ResponseCallback (response) {
		console.log(response);
		var targetDiv = document.getElementById('ajax_elements-list');
		var loadedPage = document.createElement('div');
		loadedPage.innerHTML = response
		targetDiv.appendChild(loadedPage);
		currentPage++;

		if (currentPage >= pageCount) {
			document.getElementById('b_navigation-ajax_<?=$arResult["NavNum"];?>').removeChild(document.getElementById('navigation-ajax_<?=$arResult["NavNum"];?>'));
		}
	}

	BX.ready(function(){
		BX.bindDelegate(
				document.body, 'click', {className: 'load-more_<?=$arResult["NavNum"];?>'},
				function(event) {
					if(!event) event = window.event;
					RequestPage();
					return BX.PreventDefault(event);
				}
			);
	});
</script>