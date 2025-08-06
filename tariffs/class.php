<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Highloadblock as HL;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class TariffSelectorComponent extends CBitrixComponent
{
    const HL_BLOCK_NAME = 'UserTarif';

    private $hlblock;

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    /**
     * Handles the AJAX request to select a tariff.
     */
    protected function selectTariffAction()
    {
        global $USER, $APPLICATION;

        $request = Application::getInstance()->getContext()->getRequest();
        $tariffName = $request->getPost('tariff_name');

        $response = ['success' => false];

        if (!check_bitrix_sessid()) {
            $response['message'] = 'Invalid session.';
        } elseif (!$USER || !$USER->IsAuthorized()) {
            $response['message'] = 'User is not authorized.';
        } elseif (empty($tariffName)) {
            $response['message'] = 'Tariff name is not specified.';
        } else {
            try {
                $this->setupHighloadBlock();
                $entityDataClass = $this->hlblock['entity_data_class'];
                $userId = $USER->GetID();

                // Check if the user already has a selection
                $existing = $entityDataClass::getList([
                    'select' => ['ID'],
                    'filter' => ['=UF_USER_ID' => $userId],
                ])->fetch();

                if ($existing) {
                    // Update existing record
                    $result = $entityDataClass::update($existing['ID'], [
                        'UF_TARIFF_NAME' => $tariffName,
                        'UF_DATE_UPDATE' => new \Bitrix\Main\Type\DateTime(),
                    ]);
                } else {
                    // Add new record
                    $result = $entityDataClass::add([
                        'UF_USER_ID' => $userId,
                        'UF_TARIFF_NAME' => $tariffName,
                        'UF_DATE_UPDATE' => new \Bitrix\Main\Type\DateTime(),
                    ]);
                }

                if ($result->isSuccess()) {
                    $response['success'] = true;
                } else {
                    $response['message'] = 'Failed to save data.';
                    // In a real app, you would log $result->getErrorMessages()
                }
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();
            }
        }

        $APPLICATION->RestartBuffer();
        header('Content-Type: application/json');
        echo json_encode($response);
        die();
    }

    /**
     * Initializes the Highload Block configuration.
     * @throws Exception
     */
    protected function setupHighloadBlock()
    {
        if ($this->hlblock) {
            return;
        }

        if (!Loader::includeModule('highloadblock')) {
            throw new Exception('Highloadblock module is not installed.');
        }

        $hlblock = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => self::HL_BLOCK_NAME]
        ])->fetch();

        if (!$hlblock) {
            throw new Exception('Highload block "' . self::HL_BLOCK_NAME . '" not found.');
        }

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $this->hlblock = [
            'id' => $hlblock['ID'],
            'entity_data_class' => $entity->getDataClass(),
        ];
    }

    protected function getTariffs()
    {
        $tariffs = [];
        $tariffCodes = ['TRIAL', 'BASE', 'PREMIUM', 'UNLIMITED'];

        foreach ($tariffCodes as $code) {
            if (!empty($this->arParams[$code . '_NAME'])) {
                $features = !empty($this->arParams[$code . '_FEATURES'])
                    ? array_map('trim', explode(',', $this->arParams[$code . '_FEATURES']))
                    : [];

                $tariffs[] = [
                    'NAME'       => $this->arParams[$code . '_NAME'],
                    'PRICE'      => $this->arParams[$code . '_PRICE'],
                    'PERIOD'     => $this->arParams[$code . '_PERIOD'],
                    'SPEED'      => $this->arParams[$code . '_SPEED'],
                    'FEATURES'   => $features,
                    'IS_POPULAR' => ($this->arParams[$code . '_IS_POPULAR'] ?? 'N') === 'Y',
                ];
            }
        }
        return $tariffs;
    }

    protected function getSelectedTariff()
    {
        global $USER;
        if (!$USER || !$USER->IsAuthorized()) {
            return null;
        }

        try {
            $this->setupHighloadBlock();
            $entityDataClass = $this->hlblock['entity_data_class'];
            $selected = $entityDataClass::getList([
                'select' => ['UF_TARIFF_NAME'],
                'filter' => ['=UF_USER_ID' => $USER->GetID()],
            ])->fetch();

            return $selected ? $selected['UF_TARIFF_NAME'] : null;
        } catch (Exception $e) {
            // Log error in a real application
            return null;
        }
    }

    public function executeComponent()
    {
        $request = Application::getInstance()->getContext()->getRequest();
        if ($request->isPost() && $request->get('action') === 'select_tariff') {
            $this->selectTariffAction();
        } else {
            $this->arResult['TARIFFS'] = $this->getTariffs();
            $this->arResult['SELECTED_TARIFF'] = $this->getSelectedTariff();
            $this->includeComponentTemplate();
        }
    }
}
