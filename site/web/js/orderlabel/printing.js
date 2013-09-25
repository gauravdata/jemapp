function createPrintersTable() {
    var printers = dymo.label.framework.getPrinters();
    var table = document.createElement("table");
    var thead = document.createElement("thead");
    var header = document.createElement("tr");

    var createTableHeader = function (name) {
        var cell = document.createElement("th");
        cell.appendChild(document.createTextNode(name));
        header.appendChild(cell);
    };

    createTableHeader("Printer Name");
    createTableHeader("Printer Model");
    createTableHeader("Is Connected");
    thead.appendChild(header);
    table.appendChild(thead);

    var tbody = document.createElement("tbody");
    table.appendChild(tbody);

    var createPrinterRow = function (printer, row, propertyName) {
        var cell = document.createElement("td");
        if (typeof printer[propertyName] != "undefined") {
            var text = '';
            if (printer[propertyName] == true) {
                text = 'Yes';
            } else if (printer[propertyName] == false) {
                text = 'No';
            } else {
                text = printer[propertyName];
            }
            cell.appendChild(document.createTextNode(text));
        } else {
            cell.appendChild(document.createTextNode("n/a"));
        }
        row.appendChild(cell);
    };

    for (var r = 0; r < printers.length; r++) {
        var printer = printers[r];
        var row = document.createElement("tr");
        createPrinterRow(printer, row, "name");
        createPrinterRow(printer, row, "modelName");
        createPrinterRow(printer, row, "isConnected");
        tbody.appendChild(row);
    }

    return table;
}

function updatePrintersTable() {
    var container = document.getElementById("printer-info");

    while (container.firstChild) {
        container.removeChild(container.firstChild);
    }

    container.appendChild(createPrintersTable());
}

function loadPrinters() {
    var printersSelect = document.getElementById('printersSelect');
    var printers = dymo.label.framework.getPrinters();

    if (printers.length == 0) {
        //alert("No DYMO printers are installed. Install DYMO printers.");
        var elem = $('printButton');
        elem.addClassName('disabled');
        elem.update("No Printer");
        elem.setAttribute('disabled', 'disabled');
        $('printersSelect').addClassName('no-display');
        return;
    }

    for (var i = 0; i < printers.length; i++) {
        var printerName = printers[i].name;

        var option = document.createElement('option');
        option.value = printerName;
        option.appendChild(document.createTextNode(printerName));
        printersSelect.appendChild(option);
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
    var printButton = document.getElementById('printButton');
    var printersSelect = document.getElementById('printersSelect');

    printButton.onclick = function () {
        printLabels(printersSelect.value);
    }

    updatePrintersTable();
    loadLabelXml();
    loadPrinters();
}

document.observe("dom:loaded", function() {
  orderLabelLoad();
});
