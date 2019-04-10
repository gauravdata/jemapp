<?php
require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo "Application is not installed yet, please complete install wizard first.";
    exit;
}

// Only for urls
// Don't remove this
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);

Mage::app('admin')->setUseSessionInUrl(false);

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
    ini_set('display_errors', 1);
}

umask(0);

// ----------------------------------------------------------------------------------------------------------------

$tests = array(
    '--select test--',
	'getCarrier',
	'getServiceLevelTime',
	'getServiceLevelOther',
	'getCarrierProfile',
	'getShipmentLocation',
	'getEmailType',
	'getIncoterm',
	'getCostcenter',
	'getPackage',
	'getLocationSelect',
	'createDocument',
	'doBooking',
	'doLabel',
	'doBookAndPrint',
);
$test = isset($_GET['test']) ? $_GET['test'] : false;
if (empty($test) || !isset($tests[$test])) {
    $test = false;
}
$value = isset($_GET['val']) ? $_GET['val'] : false;

?>

<style type="text/css">
    blockquote {
        border: 1px solid #ccc;
        background-color: #e1f0f8;
        padding: 10px 15px;
        margin: 5px 0;
    }
    blockquote.exception {
        background-color: #f8e1f0;
    }
</style>

<h2>Transsmart Api Test</h2>

<form method="get">
    <select name="test">
        <?php foreach ($tests as $_key => $_label): ?>
        <option value="<?php echo $_key; ?>"<?php if ($_key == $test): ?> selected="selected"<?php endif; ?>>
            <?php echo $_label; ?>
        </option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="val" value="<?php echo htmlspecialchars($value); ?>" size="10" />
    <input type="submit" value="Test" />
</form>

<?php
if ($test) {

    $testCode = "/** @var Transsmart_Shipping_Model_Client \$client */\n"
                . "\$client = Mage::helper('transsmart_shipping')->getApiClient();\n";

    $result = null;
    try {
        /** @var Transsmart_Shipping_Model_Client $client */
        $client = Mage::helper('transsmart_shipping')->getApiClient();

        switch ($tests[$test]) {
            case 'getCarrier':
                $testCode .= "\$result = \$client->getCarrier();\n";
                $result = $client->getCarrier();
                break;
            case 'getServiceLevelTime':
                $testCode .= "\$result = \$client->getServiceLevelTime();\n";
                $result = $client->getServiceLevelTime();
                break;
            case 'getServiceLevelOther':
                $testCode .= "\$result = \$client->getServiceLevelOther();\n";
                $result = $client->getServiceLevelOther();
                break;
            case 'getCarrierProfile':
                $testCode .= "\$result = \$client->getCarrierProfile();\n";
                $result = $client->getCarrierProfile();
                break;
            case 'getShipmentLocation':
                $testCode .= "\$result = \$client->getShipmentLocation();\n";
                $result = $client->getShipmentLocation();
                break;
            case 'getEmailType':
                $testCode .= "\$result = \$client->getEmailType();\n";
                $result = $client->getEmailType();
                break;
            case 'getIncoterm':
                $testCode .= "\$result = \$client->getIncoterm();\n";
                $result = $client->getIncoterm();
                break;
            case 'getCostcenter':
                $testCode .= "\$result = \$client->getCostcenter();\n";
                $result = $client->getCostcenter();
                break;
            case 'getPackage':
                $testCode .= "\$result = \$client->getPackage();\n";
                $result = $client->getPackage();
                break;
            case 'getLocationSelect':
                $testCode .= "\$result = \$client->getLocationSelect('4814 DC', 'NL', 'EEX', 'Breda', 'Reduitlaan', '29');\n";
                $result = $client->getLocationSelect('4814 DC', 'NL', 'EEX', 'Breda', 'Reduitlaan', '29');
                break;
            case 'createDocument':
                $testCode .= "\$result = \$client->createDocument('TESTREF10', 746, 1452, 896, null, null, null, null, null, 2, null, 4);\n";
                $result = $client->createDocument('TESTREF10', 746, 1452, 896, null, null, null, null, null, 2, null, 4, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 'Techtwo', 'Reduitlaan', 29, '4814 DC', 'Breda', 'NB', 'NL');
                break;
            case 'doBooking':
                $testCode .= "\$result = \$client->doBooking(". var_export($value, true) . ");\n";
                $result = $client->doBooking($value);
                break;
            case 'doLabel':
                $testCode .= "\$result = \$client->doLabel(". var_export($value, true) . ", 'mitch@techtwo.nl');\n";
                $result = $client->doLabel($value, 'mitch@techtwo.nl');
                break;
            case 'doBookAndPrint':
                $testCode .= "\$result = \$client->doBookAndPrint(". var_export($value, true) . ", 'mitch@techtwo.nl');\n";
                $result = $client->doBookAndPrint($value, 'mitch@techtwo.nl');
                break;
        }
    }
    catch (Exception $exception) {
        $result = $exception;
    }

    echo "<h3>Code</h3>\n";
    echo '<blockquote>';
    highlight_string("<?php\n" . $testCode);
    echo '</blockquote>';

    echo "<h3>Result</h3>\n";
    if ($result instanceof Exception) {
        echo '<blockquote class="exception"><code>';
        echo '<strong>' . get_class($exception) . "</strong> -\n";
        echo htmlspecialchars($exception->getMessage());
        echo '</code></blockquote>';
    }
    else {
        echo '<blockquote>';
        highlight_string("<?php\n" . var_export($result, true));
        echo '</blockquote>';
    }

}
