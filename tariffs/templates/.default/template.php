<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */
?>

<div class="tariff-selector-container">
    <?php foreach ($arResult['TARIFFS'] as $tariff): ?>
        <?php
            $isSelected = (isset($arResult['SELECTED_TARIFF']) && $arResult['SELECTED_TARIFF'] === $tariff['NAME']);
            $cardClasses = 'tariff-card';
            if ($tariff['IS_POPULAR']) {
                $cardClasses .= ' popular';
            }
            if ($isSelected) {
                $cardClasses .= ' selected';
            }
        ?>
        <div class="<?php echo htmlspecialchars($cardClasses); ?>" data-tariff-name="<?php echo htmlspecialchars($tariff['NAME']); ?>">
            <?php if ($tariff['IS_POPULAR']): ?>
                <div class="popular-badge">Popular</div>
            <?php endif; ?>

            <h2 class="tariff-name"><?php echo htmlspecialchars($tariff['NAME']); ?></h2>

            <div class="tariff-price">
                <span class="price-amount">$<?php echo htmlspecialchars($tariff['PRICE']); ?></span>
                <span class="price-period"><?php echo htmlspecialchars($tariff['PERIOD']); ?></span>
            </div>

            <div class="tariff-speed"><?php echo htmlspecialchars($tariff['SPEED']); ?></div>

            <ul class="tariff-features">
                <?php foreach ($tariff['FEATURES'] as $feature): ?>
                    <li><?php echo htmlspecialchars($feature); ?></li>
                <?php endforeach; ?>
            </ul>

            <a href="#" class="choose-tariff-btn">
                <?php echo $isSelected ? 'Current Plan' : 'Choose Plan'; ?>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?php
// In a real Bitrix site, we would use $this->GetFolder() to get the path to the template folder.
// Since our mock component doesn't have this, we'll construct the path manually for the simulation.
$scriptPath = '/tariffs/templates/.default/script.js';
?>
<script src="<?php echo htmlspecialchars($scriptPath); ?>"></script>
