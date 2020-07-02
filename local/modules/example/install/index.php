<?
use \Bitrix\Main\Loader;

Class example extends CModule
{
    public $MODULE_ID = 'example';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    private $COMPONENT_VENDOR = 'inwebium';
    private $arModuleComponents = array('example.detail', 'example.list', 'example');
    private $ModulePath = '/local/modules/example';
    private $installPath;

    // Конструктор класса модуля
    function example()
    {
        $arModuleVersion = array();

        $this->installPath = str_replace("\\", '/', __FILE__);
        $this->installPath = substr($this->installPath, 0, strlen($this->installPath) - strlen('/index.php'));
		include($this->installPath . '/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->MODULE_NAME = "Модуль " . $this->MODULE_ID;
		$this->MODULE_DESCRIPTION = "Модуль для просмотра элементов инфоблока со связанными пользователями и элементами.";
    }

    // Установка модуля, инфоблоков и компонентов
    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;

        $this->CreateIBlocks();
        $this->InstallFiles();
        RegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(
        	"Установка модуля",
            $DOCUMENT_ROOT . $this->ModulePath . '/install/step1.php'
        	);
        
        return true;
    }

    // Создание типа инфоблоков и инфоблоков
    function CreateIBlocks()
    {
    	global $APPLICATION, $errors;
    	Loader::includeModule('iblock');
    	$sIblockTypeId = $this->MODULE_ID;

		$resIblockType = \Bitrix\Iblock\TypeTable::getById($sIblockTypeId);

		if ($arIblockType = $resIblockType->fetch()) {
			print("IBlock type already exists." . PHP_EOL);
		    var_dump($arIblockType);
		} else {
			$arIblockTypeFields = array(
			    'ID' => $sIblockTypeId,
			    'SECTIONS' => 'N',
			    'IN_RSS' => 'N',
			    'SORT' => 100,
			    'LANG' => array(
			        'en' => array(
			            'NAME' => 'Example',
			            'ELEMENT_NAME' => 'Elements'
			            )
			        ),
			    	'ru' => array(
			    		'NAME' => 'Example',
			    		'ELEMENT_NAME' => 'Элементы'
			    		)
		    );

			$obIblockType = new CIBlockType;
			$resIblockType = $obIblockType->Add($arIblockTypeFields);
		}

		$arExampleIblockFields = array(
			"ACTIVE" => "Y",
			"NAME" => "Example",
			"CODE" => 'example',
			"LIST_PAGE_URL" => "#SITE_DIR#/example/index.php?",
			"DETAIL_PAGE_URL" => "#SITE_DIR#/example/index.php?ID=#ELEMENT_ID#",
			"IBLOCK_TYPE_ID" => $sIblockTypeId,
			"SITE_ID" => "s1",
			"SORT" => 100,
			"DESCRIPTION" => "",
			"GROUP_ID" => array("2" => "X")
		);
		$obExampleIblock = new CIBlock;
		$iExampleIblockId = $obExampleIblock->Add($arExampleIblockFields);


		$arLinkedIblockFields = array(
			"ACTIVE" => "Y",
			"NAME" => "Linked",
			"CODE" => 'linked',
			"LIST_PAGE_URL" => "#SITE_DIR#/linked/index.php?",
			"DETAIL_PAGE_URL" => "#SITE_DIR#/linked/index.php?ID=#ELEMENT_ID#",
			"IBLOCK_TYPE_ID" => $sIblockTypeId,
			"SITE_ID" => "s1",
			"SORT" => 200,
			"DESCRIPTION" => "",
			"GROUP_ID" => array("2" => "X")
		);
		$obLinkedIblock = new CIBlock;
		$iLinkedIblockId = $obLinkedIblock->Add($arLinkedIblockFields);


		$obUsersProperty = new CIBlockProperty;
		$arUsersPropertyFields = array(
		        "NAME" => "Пользователи",
		        "ACTIVE" => "Y",
		        "MULTIPLE" => "Y",
		        "SORT" => 10,
		        "CODE" => "USERS",
		        "MULTIPLE_CNT" => 3, 
		        "PROPERTY_TYPE" => "S", 
		        "USER_TYPE" => "UserID",
		        "IBLOCK_ID" => $iExampleIblockId,
		        "HINT" => "",
		);
		$iUsersPropertyId = $obUsersProperty->Add($arUsersPropertyFields);

		$obElementsProperty = new CIBlockProperty;
		$arElementsPropertyFields = array(
		        "NAME" => "Элементы",
		        "ACTIVE" => "Y",
		        "MULTIPLE" => "Y",
		        "SORT" => 20,
		        "CODE" => "ELEMENTS",
		        "MULTIPLE_CNT" => 3, 
		        "PROPERTY_TYPE" => "E", 
		        "IBLOCK_ID" => $iExampleIblockId,
		        "LINK_IBLOCK_ID" => $iLinkedIblockId,
		        "HINT" => "",
		);
		$iElementsPropertyId = $obElementsProperty->Add($arElementsPropertyFields);

		$arLinkedElementsNames = array(
				'Linked 1', 'Linked 2', 'Linked 3', 'Linked 4'
			);

		$obElement = new CIBlockElement;

		$arNewElementFields = Array(
				"MODIFIED_BY"    => 1,
				"IBLOCK_SECTION_ID" => false,
				"IBLOCK_ID"      => $iLinkedIblockId,
				"NAME"           => "Элемент",
				"ACTIVE"         => "Y",
				"PREVIEW_TEXT"   => "",
				"DETAIL_TEXT"    => ""
			);

		$arLinkedElementsId = array();
		foreach ($arLinkedElementsNames as $sLinkedElementName) {
			$arNewElementFields['NAME'] = $sLinkedElementName;
			$iNewElementId = $obElement->Add($arNewElementFields);
			$arLinkedElementsId[] = $iNewElementId;
			print('New linked element:' . $iNewElementId . PHP_EOL);
		}

		$arExampleElementsNames = array(
				'Example 1', 'Example 2', 'Example 3', 'Example 4', 'Example 5', 'Example 6', 'Example 7', 'Example 8'
			);

		$arNewElementFields['PROPERTY_VALUES'] = array('USERS' => array(1), 'ELEMENTS' => array($arLinkedElementsId[1], $arLinkedElementsId[3]));
		$arNewElementFields['IBLOCK_ID'] = $iExampleIblockId;
		foreach ($arExampleElementsNames as $sExampleElementName) {
			$arNewElementFields['NAME'] = $sExampleElementName;
			$iNewElementId = $obElement->Add($arNewElementFields);
			print('New linked element:' . $iNewElementId . PHP_EOL);
		}
    }

    // Удаление модуля, инфоблоков и компонентов
    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->DeleteIBlocks();
        $this->UnInstallFiles();
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(
        	"Удаление модуля", 
            $DOCUMENT_ROOT . $this->ModulePath . '/install/unstep1.php'
        	);

        return true;
    }

    // Копирование каталога с компонентами
    function InstallFiles()
	{
		global $DOCUMENT_ROOT;
		CheckDirPath($DOCUMENT_ROOT . '/local/components');
		CopyDirFiles(
			$DOCUMENT_ROOT . $this->ModulePath . '/install/components',
			$DOCUMENT_ROOT . '/local/components',
			true,
			true
			);

		CheckDirPath($DOCUMENT_ROOT . '/local/templates');
		CopyDirFiles(
			$DOCUMENT_ROOT . $this->ModulePath . '/install/templates',
			$DOCUMENT_ROOT . '/local/templates',
			true,
			true
			);
	}

	// Удаление каталога с компонентами
    function UnInstallFiles()
	{
		global $DOCUMENT_ROOT;
		foreach ($this->arModuleComponents as $key => $folder) {
			DeleteDirFilesEx($DOCUMENT_ROOT.'/local/components/' . $this->COMPONENT_VENDOR . '/' . $folder);
		}
		DeleteDirFilesEx($DOCUMENT_ROOT.'/local/templates/.default/components/bitrix/system.pagenavigation/infinite');
	}

	// Удаление типа инфоблоков с инфоблоками
    function DeleteIBlocks()
    {
    	global $APPLICATION, $DB, $errors;
    	if(CModule::IncludeModule("iblock")) {
    		CIBlockType::Delete($this->MODULE_ID);
    	}
    	return;
    }
}
?>