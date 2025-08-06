<?php
// --- Bitrix Environment Emulation ---
define("B_PROLOG_INCLUDED", true);
// Set the document root for path calculations
$_SERVER["DOCUMENT_ROOT"] = __DIR__;

// Mock the core CBitrixComponent class, as it won't exist in this standalone environment.
if (!class_exists('CBitrixComponent')) {
    abstract class CBitrixComponent
    {
        public $arParams = [];
        public $arResult = [];
        protected $componentPath;

        // In a real environment, the path is determined automatically. We'll set it manually.
        public function __construct($component = null) {
            // Extract the component name from the child class file path
            $reflector = new ReflectionClass(get_class($this));
            $this->componentPath = dirname(str_replace($_SERVER['DOCUMENT_ROOT'], '', $reflector->getFileName()));
        }

        public function onPrepareComponentParams($arParams) {
            return $arParams;
        }

        abstract public function executeComponent();

        protected function includeComponentTemplate($templateName = '') {
            if (empty($templateName)) {
                $templateName = '.default';
            }

            // Make component data available to the template file.
            $arResult = $this->arResult;
            $arParams = $this->arParams;

            $templateFile = $_SERVER["DOCUMENT_ROOT"] . $this->componentPath . '/templates/' . $templateName . '/template.php';

            if (file_exists($templateFile)) {
                // In a real Bitrix site, CSS is included via core methods. Here, we'll link it directly.
                $styleFile = $this->componentPath . '/templates/' . $templateName . '/style.css';
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . $styleFile)) {
                    echo '<link href="' . htmlspecialchars($styleFile) . '" type="text/css" rel="stylesheet" />';
                }

                include $templateFile;
            } else {
                trigger_error("Cannot find template '" . htmlspecialchars($templateName) . "'", E_USER_WARNING);
            }
        }
    }
}

// --- Page Display Logic ---

// Include the component's class file
require_once($_SERVER["DOCUMENT_ROOT"] . '/tariffs/class.php');

// Instantiate the component
$component = new TariffSelectorComponent();

// Prepare and execute the component logic
$component->arParams = []; // We have no parameters to pass
$component->executeComponent(); // This will prepare $arResult and call includeComponentTemplate
?>
