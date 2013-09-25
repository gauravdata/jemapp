var activePrinter = null;

function loadPrinters() {
    var printers = dymo.label.framework.getPrinters();

    if (printers.length == 0) {
        // alert("No DYMO printers are installed. Install DYMO printers.");
        var elements = $$('[name=labelConfirm]');
        for (i = 0; i < elements.length; i++) {
            elements[i].addClassName('disabled');
        }
        return;
    }

    for (var i in printers) {
        activePrinter = printers[i].name;
        break;
    }
}

function loadLabelXml() {
    new Ajax.Request(labelUrl,
        {
            method:'get',
            onSuccess:function (data) {
                labelXml = data.responseText;
            },
            onFailure:function () {
                alert('Error loading from: ' + labelUrl);
            }
        }
    );
}

function printLabels(printerName) {
    var printers = dymo.label.framework.getPrinters();
    var printer = printers[printerName];

    if (!printer) {
        alert("Printer '" + printerName + "' not found");
        return;
    }

    if (printer.printerType != "LabelWriterPrinter") {
        alert("Unsupported printer type");
        throw "Unsupported printer type";
    }

    var labelSetBuilder = new dymo.label.framework.LabelSetBuilder();
    for (var i = 0; i < addressData.length; i++) {
        var record = labelSetBuilder.addRecord();
        var data = addressData[i];

        var info = "<font family='Arial' size='12'>"; // default font
        if (data.company != null) {
            info = info + "<b>" + data.company + "</b>\n";
            if(data.prefix != null){
                info = info + data.prefix + " ";
            }
            info = info + data.name + "\n";
        }
        else {
            info = info + "<b>" + data.name + "</b>\n";
        }
        info = info + data.street + "\n";
        info = info + data.cpc;
        if (data.region != null) {
            info = info + "\n" + data.region;
        }
        if (data.country != null) {
            info = info + "\n" + data.country;
        }
        if (data.phone != null) {
            info = info + "\n" + data.phone;
        }
        info = info + "</font>";

        record.setTextMarkup("Text", info);
    }

    dymo.label.framework.printLabel(printerName, "", labelXml, labelSetBuilder);

    if (senderData != null) {
        printSenderLabels(printerName);
    }
}

function printSenderLabels(printerName) {
    var senderLabelSetBuilder = new dymo.label.framework.LabelSetBuilder();
    var senderDetails = "<font family='Arial' size='12'>";
    senderDetails = senderDetails + "<b>" + senderText + ":</b>\n";
    senderDetails = senderDetails + senderData.name + "\n";
    senderDetails = senderDetails + senderData.street + "\n";
    senderDetails = senderDetails + senderData.postcode + " " + senderData.city + "\n";
    senderDetails = senderDetails + senderData.country;
    senderDetails = senderDetails + "</font>";

    for (var i = 0; i < addressData.length; i++) {
        var record = senderLabelSetBuilder.addRecord();
        record.setTextMarkup("Text", senderDetails);
    }

    dymo.label.framework.printLabel(printerName, "", labelXml, senderLabelSetBuilder);
}

function orderLabelLoad() {
    if (moduleReady) {
        loadLabelXml();
        loadPrinters();

        var printButtons = document.getElementsByName('labelConfirm');
        if (activePrinter != null) {
            for (var i in printButtons) {
                printButtons[i].innerHTML = '<span>Print Label</span>';
                printButtons[i].onclick = function () {
                    printLabels(activePrinter);
                }
            }
        } else {
            for (var i in printButtons) {
                printButtons[i].innerHTML = '<span>No Printer</span>';
            }
        }
    }
}

document.observe("dom:loaded", function() {
  orderLabelLoad();
});
