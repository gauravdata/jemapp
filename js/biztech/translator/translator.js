function getAdminTranslation()
{

    document.getElementById("translate_error_msg").innerHTML = '';
    var url = document.getElementById("translate_url").value
    var langto = document.getElementById("locale").value;
    var formdata = new Object();
    formdata["langto"] = document.getElementById("locale").value;
    formdata["langfrom"] = "";
    formdata["value"] = document.getElementById("original_translation").value;
    new Ajax.Request(url, {
        method: "post",
        parameters: formdata,
        onComplete: function(data) {
            var result = data.responseText.evalJSON(true);
            var value = result.text;
            if (result.status != "fail") {
                if (document.getElementById("string").tagName == "TEXTAREA") {
                    document.getElementById("string").innerHTML = value;
                } else {
                    document.getElementById("string").value = value;
                }
            } else {
                document.getElementById("translate_error_msg").innerHTML = value;
            }
        }
    });
}


Event.observe(window, 'load', function() {
    if ($('search_translate_form')) {
        $('search_translate_form').observe('submit', function(event) {
            $('form_search_submit').fire('click');
            Event.stop(event);
        });
    }
});

function matchSearchString(url) {

    var parameters = $('search_translate_form').serialize();
    new Ajax.Request(url, {
        method: 'post',
        parameters: parameters,
        onSuccess: function(transport) {
            result = transport.responseText.evalJSON();
            $('searchResult').update('<div class="hor-scroll">' + result.data + '</div>');
        }
    });
}

function translateSearchReset() {
    $('searchResult').update('<div class="hor-scroll"></div>');
}

function editStringUrl(row) {
    location.href = row.select(':last-child a');
}

function calcchar(url)
{
    new Ajax.Request(url, {
        method: 'POST',
        onSuccess: successFunc,
    });


    }

function successFunc(response){
    var result = JSON.parse(response.responseText);

    if (result) {
        html = '<p> Catalog character count with above selected attribute including html : ' + result.withhtml + '</p>';
        html += '<p> Catalog character count with above selected attribute without html : ' + result.withouthtml + '</p>';
        $('charcount').insert(html);
        win = new Window({ title: "Character Count", zIndex:3000, destroyOnClose: true, recenterAuto:false, resizable: false, width:450, height:200, minimizable: false, maximizable: false, draggable: false});
        win.setContent('charcount', false, false);
        win.showCenter();
    }
}