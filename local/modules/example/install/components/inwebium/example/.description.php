<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Тестовый список",
	"DESCRIPTION" => "Тестовый список",
	"COMPLEX" => "Y",
	"PATH" => array(
		"ID" => "inwebium",
		"NAME" => "inwebium",
		"CHILD" => array(
			"ID" => "example",
			"NAME" => "Example",
			"CHILD" => array(
				"ID" => "example_cmpx"
			),
		)
	),
);
?>